@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- ヘッダー：タイトルとボタン --}}
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">User Management</h1>
        {{-- buttonからaタグに修正して、routeをつなげたにょ！ --}}
        <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm inline-block">
            + Add New User
        </a>
    </div>

    {{-- 成功メッセージの表示 (UserControllerのwith('success', ...)を受け取るにょ) --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-bold shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- テーブルカード --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">User / Account</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Birth Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold shadow-inner">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800">{{ $user->name }}</span>
                                    <span class="text-[10px] text-blue-500 font-medium">@ {{ $user->account_name }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                            {{ $user->email }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                            {{ $user->phone ?? '---' }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                            @if($user->birth_date)
                                {{-- 一旦Carbonインスタンスに変換してからフォーマットするにょ --}}
                                {{ \Carbon\Carbon::parse($user->birth_date->toString())->format('M d, Y') }}
                            @else
                                ---
                            @endif
                        </td>

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
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection