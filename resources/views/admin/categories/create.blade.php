@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Create New Category</h1>
            <p class="text-gray-500 mt-1 font-medium">Select an alert type and configure its theme.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-500 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm">
            &larr; Back to List
        </a>
    </div>

    {{-- Main Form Card --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="p-10 space-y-10">
            @csrf

            {{-- Row 1: Category Name --}}
            <div class="space-y-3">
                <label for="name" class="block text-xs font-black text-blue-600 uppercase tracking-[0.2em] ml-1">Category Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-500 focus:bg-white rounded-2xl transition-all font-bold text-gray-800 outline-none placeholder-gray-300"
                       placeholder="e.g. Heavy Rain">
            </div>

            {{-- Row 2: Theme Color --}}
            <div class="space-y-3">
                <label for="color_code" class="block text-xs font-black text-blue-600 uppercase tracking-[0.2em] ml-1">Theme Color</label>
                <div class="flex items-center gap-4 p-2 bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-blue-500 focus-within:bg-white transition-all max-w-sm">
                    <input type="color" id="color_code" name="color_code" value="{{ old('color_code', '#3B82F6') }}" 
                           class="w-12 h-12 border-none rounded-xl cursor-pointer bg-transparent">
                    <input type="text" id="color_code_text" value="#3B82F6" 
                           class="flex-1 bg-transparent border-none font-mono font-bold text-gray-500 uppercase outline-none" readonly>
                </div>
            </div>

            {{-- Row 3: Icon Selection (The "React-Style" Grid) --}}
            <div class="space-y-6">
                <label class="block text-xs font-black text-blue-600 uppercase tracking-[0.2em] ml-1">Select Alert Type</label>
                
                {{-- カードグリッド --}}
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @php
                        $presets = [
                            'heavy_rain' => '🌧️', 'tsunami' => '🌊', 'road_closure' => '🚧', 'fire' => '🔥',
                            'lightning' => '⚡', 'water_outage' => '🚰', 'power_outage' => '💡', 'unstable_internet' => '📶', 'flood' => '⚠️'
                        ];
                    @endphp
                    @foreach($presets as $key => $emoji)
                        <button type="button" onclick="setIcon('{{ $key }}', '{{ $emoji }}')" 
                                id="btn-{{ $key }}"
                                class="icon-card flex flex-col items-center justify-center p-8 bg-white border-2 border-gray-100 rounded-[2.5rem] transition-all duration-300 hover:shadow-2xl hover:shadow-blue-500/10 group active:scale-95">
                            <span class="text-6xl mb-4 transition-transform duration-300 group-hover:scale-110 drop-shadow-sm">{{ $emoji }}</span>
                            <span class="text-sm font-bold text-gray-400 group-hover:text-blue-600 capitalize transition-colors">
                                {{ str_replace('_', ' ', $key) }}
                            </span>
                        </button>
                    @endforeach
                </div>

                {{-- 選択された値を保持する隠しフィールド --}}
                <input type="hidden" name="icon" id="icon" value="{{ old('icon') }}" required>
                
                {{-- エラー表示用 --}}
                @error('icon')
                    <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Row 4: Description --}}
            <div class="space-y-3">
                <label for="description" class="block text-xs font-black text-blue-600 uppercase tracking-[0.2em] ml-1">Description (Optional)</label>
                <textarea id="description" name="description" rows="3" 
                          class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-500 focus:bg-white rounded-2xl transition-all font-medium text-gray-600 outline-none"
                          placeholder="What should users know about this alert?">{{ old('description') }}</textarea>
            </div>

            {{-- Action Button --}}
            <div class="pt-6">
                <button type="submit" class="w-full bg-blue-600 text-white py-6 rounded-[2rem] font-black text-xl shadow-2xl shadow-blue-600/30 hover:bg-blue-700 hover:-translate-y-1 transition-all duration-300 uppercase tracking-[0.3em]">
                    Create New Category
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* 選択時のアニメーションとスタイル */
    .icon-card.active {
        border-color: #3B82F6;
        background-color: #f0f7ff;
        transform: translateY(-8px);
        box-shadow: 0 25px 50px -12px rgba(59, 130, 246, 0.25);
    }
    .icon-card.active span:last-child {
        color: #2563eb;
    }
</style>

<script>
    function setIcon(name, emoji) {
        // 1. 全カードの「active」を解除
        document.querySelectorAll('.icon-card').forEach(card => {
            card.classList.remove('active');
        });

        // 2. 選択されたカードを「active」にする
        const selectedCard = document.getElementById('btn-' + name);
        if (selectedCard) {
            selectedCard.classList.add('active');
        }

        // 3. inputに値をセット
        document.getElementById('icon').value = name;
    }

    // カラーピッカー連動
    const colorPicker = document.getElementById('color_code');
    const colorText = document.getElementById('color_code_text');
    colorPicker.addEventListener('input', (e) => {
        colorText.value = e.target.value.toUpperCase();
    });

    // 初期選択の反映 (バリデーションエラーで戻ってきた時用)
    window.onload = () => {
        const initialIcon = document.getElementById('icon').value;
        if (initialIcon) {
            const btn = document.getElementById('btn-' + initialIcon);
            if (btn) btn.classList.add('active');
        }
    };
</script>
@endsection