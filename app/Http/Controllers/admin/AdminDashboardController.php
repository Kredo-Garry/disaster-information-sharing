<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\PhivolcsEarthquake;
use App\Models\PhivolcsTsunamiBulletin;
use App\Models\PhivolcsVolcanoAlert;
use Carbon\CarbonInterface;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // --- PHIVOLCS: last fetched_at ---
        $latestEq = PhivolcsEarthquake::query()
            ->orderByDesc('fetched_at')
            ->orderByDesc('id')
            ->first();

        $latestTsu = PhivolcsTsunamiBulletin::query()
            ->orderByDesc('fetched_at')
            ->orderByDesc('id')
            ->first();

        $latestVol = PhivolcsVolcanoAlert::query()
            ->orderByDesc('fetched_at')
            ->orderByDesc('id')
            ->first();

        $eqFetchedAt  = $latestEq?->fetched_at;
        $tsuFetchedAt = $latestTsu?->fetched_at;
        $volFetchedAt = $latestVol?->fetched_at;

        // --- derive freshness status ---
        $eqFresh  = $this->freshness($eqFetchedAt);
        $tsuFresh = $this->freshness($tsuFetchedAt);
        $volFresh = $this->freshness($volFetchedAt);

        return view('admin.dashboard', [
            'userCount'     => User::count(),
            'postCount'     => Post::count(),
            'categoryCount' => Category::count(),

            'latestUsers'   => User::latest()->limit(5)->get(),
            'latestPosts'   => Post::with('user')->latest()->limit(5)->get(),

            // PHIVOLCS summary
            'eqFetchedAt'   => $eqFetchedAt,
            'tsuFetchedAt'  => $tsuFetchedAt,
            'volFetchedAt'  => $volFetchedAt,

            'eqCount'       => PhivolcsEarthquake::count(),
            'tsuCount'      => PhivolcsTsunamiBulletin::count(),
            'volCount'      => PhivolcsVolcanoAlert::count(),

            // Freshness (status + hours)
            'eqFresh'       => $eqFresh,
            'tsuFresh'      => $tsuFresh,
            'volFresh'      => $volFresh,
        ]);
    }

    /**
     * Determine freshness:
     * - ok: < 24h
     * - warn: 24h - < 48h
     * - danger: >= 48h OR never fetched
     */
    private function freshness(?CarbonInterface $fetchedAt): array
    {
        if (!$fetchedAt) {
            return [
                'status' => 'danger',
                'hours' => null,
                'label' => 'Never fetched',
            ];
        }

        $hours = $fetchedAt->diffInHours(now());

        if ($hours >= 48) {
            return [
                'status' => 'danger',
                'hours' => $hours,
                'label' => 'Stale (48h+)',
            ];
        }

        if ($hours >= 24) {
            return [
                'status' => 'warn',
                'hours' => $hours,
                'label' => 'Over 24h',
            ];
        }

        return [
            'status' => 'ok',
            'hours' => $hours,
            'label' => 'Fresh',
        ];
    }
}
