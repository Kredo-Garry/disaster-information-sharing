<?php

namespace App\Http\Controllers\Admin; // ← ここが Admin になっているか確認

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // ユーザー一覧を取得して表示
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // 他のメソッド（show, editなど）が必要な場合はここに追加していきます
}