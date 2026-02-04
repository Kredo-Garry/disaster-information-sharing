@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Disaster Categories</h1>
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
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
            {{-- アイコン表示エリア --}}
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-3xl bg-gray-50 border border-gray-100 flex-shrink-0">
                @php
                    // スペースとアンダースコアを除去して判定（これでSeederデータも完璧！）
                    $type = str_replace([' ', '_'], '', strtolower(trim($category->icon ?? $category->name)));
                @endphp

                @switch($type)
                    @case('heavyrain') 🌧️ @break
                    @case('tsunami') 🌊 @break
                    @case('roadclosure') 🚧 @break
                    @case('fire') 🔥 @break
                    @case('lightning') ⚡ @break
                    @case('wateroutage') 🚰 @break
                    @case('poweroutage') 💡 @break
                    @case('unstableinternet') 📶 @break
                    @case('flood') ⚠️ @break
                    {{-- 予備判定 --}}
                    @case('tap') @case('water') 🚰 @break
                    @case('lightbulb') 💡 @break
                    @case('wifi') 📶 @break
                    @case('flame') 🔥 @break
                    @case('waves') 🌊 @break
                    @default ⚠️
                @endswitch
            </div>
            
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-gray-800 truncate">{{ $category->name }}</h3>
                <div class="flex items-center gap-2 mt-1">
                    <span class="w-3 h-3 rounded-full shadow-sm" style="background-color: {{ $category->color_code ?? '#ccc' }}"></span>
                    <span class="text-xs font-mono text-gray-400 uppercase">{{ $category->color_code ?? 'No Color' }}</span>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <a href="{{ route('admin.categories.edit', $category) }}" class="p-2 text-gray-300 hover:text-blue-500 transition-colors" title="Edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                </a>

                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure?');" class="inline">
                    @csrf 
                    @method('DELETE')
                    <button class="p-2 text-gray-300 hover:text-red-500 transition-colors" title="Delete">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 py-20 text-center bg-white rounded-2xl border border-dashed border-gray-200">
            <p class="text-gray-400 font-medium">No categories found. Click "Add Category" to start.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection