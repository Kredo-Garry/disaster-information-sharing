@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    {{-- ヘッダー：戻るボタンとタイトル --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.users.index') }}" class="p-2 bg-white rounded-full shadow-sm border border-gray-200 text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
    </div>

    {{-- フォーム本体：白カード --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-10">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- Name入力：ラベルとインプットを横並び --}}
            <div class="grid grid-cols-3 items-center gap-4">
                <label for="name" class="text-sm font-bold text-gray-500">Name</label>
                <div class="col-span-2">
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                        class="w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition p-3">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Email入力 --}}
            <div class="grid grid-cols-3 items-center gap-4">
                <label for="email" class="text-sm font-bold text-gray-500">Email</label>
                <div class="col-span-2">
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                        class="w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition p-3">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- フッター：ボタンエリア --}}
            <div class="pt-8 border-t border-gray-50 flex items-center justify-end gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all transform active:scale-95">
                    Update Profile
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-100 text-gray-500 rounded-xl font-bold hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection