@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Disaster Categories</h1>
        {{-- 後ほど作成する新規登録画面へのリンク --}}
        <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-bold shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition">
            + Add Category
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl font-bold text-sm shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($categories as $category)
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl bg-gray-50 border border-gray-100">
                {{-- アイコンの出し分け --}}
                @switch($category->icon_type)
                    @case('tap') 🚰 @break
                    @case('lightbulb') 💡 @break
                    @case('wifi') 📶 @break
                    @default ⚠️
                @endswitch
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-800">{{ $category->name }}</h3>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $category->color_code }}"></span>
                    <span class="text-xs text-gray-400">{{ $category->color_code }}</span>
                </div>
            </div>
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                @csrf @method('DELETE')
                <button class="text-gray-300 hover:text-red-500 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>
        </div>
        @empty
        <div class="col-span-3 py-20 text-center bg-white rounded-2xl border border-dashed border-gray-300">
            <p class="text-gray-400">カテゴリがまだ登録されていません。「Add Category」から追加してください。</p>
        </div>
        @endforelse
    </div>
</div>
@endsection