<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MyPageApiController extends Controller
{
    /**
     * 期限切れなら「表示上 neutral」に変換して返す
     * - DBは更新しない（B案）
     * - neutral表示時：message/updated_at はレスポンス上 null
     */
    private function presentUser(User $user): array
    {
        $isExpired = false;

        if ($user->status !== 'neutral' && $user->status_expires_at) {
            $isExpired = $user->status_expires_at->lte(now());
        }

        if ($isExpired) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'account_name' => $user->account_name,
                'family_id' => $user->family_id,
                'status' => 'neutral',
                'status_message' => null,
                'status_updated_at' => null,
            ];
        }

        // active or already neutral
        return [
            'id' => $user->id,
            'name' => $user->name,
            'account_name' => $user->account_name,
            'family_id' => $user->family_id,
            'status' => $user->status ?? 'neutral',
            'status_message' => ($user->status === 'help') ? $user->status_message : null,
            'status_updated_at' => ($user->status === 'neutral') ? null : optional($user->status_updated_at)?->toIso8601String(),
        ];
    }

    /**
     * GET /api/me
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => $this->presentUser($user),
        ]);
    }

    /**
     * PATCH /api/me/status
     * - safe: message不要（messageはnull化）
     * - help: message必須
     * - updated_at = now, expires_at = now+24h
     */
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['safe', 'help'])],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['status'] === 'help') {
            $request->validate([
                'message' => ['required', 'string', 'max:1000'],
            ]);
        }

        $user = $request->user();

        $user->status = $validated['status'];
        $user->status_updated_at = now();
        $user->status_expires_at = now()->addHours(24);

        if ($validated['status'] === 'help') {
            $user->status_message = $validated['message'];
        } else {
            $user->status_message = null;
        }

        $user->save();

        return response()->json([
            'user' => $this->presentUser($user),
        ]);
    }

    /**
     * PATCH /api/me/family
     * - Family ID登録（英数字OK）
     */
    public function updateFamily(Request $request)
    {
        $validated = $request->validate([
            'family_id' => ['required', 'string', 'alpha_num', 'max:50'],
        ]);

        $user = $request->user();
        $user->family_id = $validated['family_id'];
        $user->save();

        return response()->json([
            'user' => $this->presentUser($user),
        ]);
    }

    /**
     * GET /api/family
     * - 自分と同一 family_id のユーザー一覧（自分は除外）
     * - family_id未登録なら検索しない（未登録者同士で出ないように）
     * - 0件なら notice を返す
     */
    public function family(Request $request)
    {
        $me = $request->user();
        $familyId = $me->family_id;

        // ✅ 未登録（null/''）は「検索しない」が最重要
        if (!$familyId || trim($familyId) === '') {
            return response()->json([
                'members' => [],
                'notice' => 'No Family ID is registered, or no users with the same Family ID were found.',
            ]);
        }

        $members = User::query()
            ->whereNotNull('family_id')
            ->where('family_id', '!=', '')
            ->where('family_id', $familyId)
            ->where('id', '!=', $me->id)
            ->orderByRaw("
                CASE
                    WHEN status = 'help' THEN 0
                    WHEN status = 'safe' THEN 1
                    ELSE 2
                END
            ")
            ->orderByDesc('status_updated_at')
            ->get();

        $presented = $members->map(fn ($u) => $this->presentUser($u))->values();

        return response()->json([
            'members' => $presented,
            'notice' => $presented->isEmpty()
                ? 'No Family ID is registered, or no users with the same Family ID were found.'
                : null,
        ]);
    }
}
