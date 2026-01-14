<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // ★ この edit メソッドが足りないためエラーが出ています
    public function edit(User $user)
    {
        // 編集画面を表示する
        return view('admin.users.edit', compact('user'));
    }

    // 更新処理もセットで必要になるはずなので追加しておきます
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            // パスワード変更が必要な場合などはここに追加
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }
}