@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- ヘッダー部分：タイトルと追加ボタン --}}
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Users</h1>
        {{-- 今後、ユーザー追加機能を作る時用のボタン --}}
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm">
            + Add New User
        </button>
    </div>

    {{-- テーブルを包む白カード --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    {{-- 名前とアイコン --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold shadow-inner">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800">{{ $user->name }}</span>
                                <span class="text-[10px] text-gray-400 italic">ID: {{ $user->id }}</span>
                            </div>
                        </div>
                    </td>
                    {{-- メールアドレス --}}
                    <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                        {{ $user->email }}
                    </td>
                    {{-- 操作ボタン --}}
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-500 hover:text-blue-700 font-bold text-sm">Edit</a>
                        
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('本当に削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 font-bold text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ページネーションのエリア --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection