@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Create New Category</h1>
            <p class="text-gray-500 mt-1 font-medium">Configure a new disaster category and its visual identifiers.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-500 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm">
            ← Back to List
        </a>
    </div>

    {{-- Main Form Card --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="p-10 space-y-10">
            @csrf

            {{-- Row 1: Name & Color --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-3">
                    <label for="name" class="block text-xs font-black text-blue-600 uppercase tracking-[0.2em]">Category Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                           class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-500 focus:bg-white rounded-2xl transition-all font-bold text-gray-800 outline-none"
                           placeholder="e.g. Heavy Rain" required>
                </div>

                <div class="space-y-3">
                    <label for="color_code" class="block text-xs font-black text-blue-600 uppercase tracking-[0.2em]">Theme Color</label>
                    <div class="flex items-center gap-4 p-2 bg-gray-50 rounded-2xl border-2 border-transparent focus-within:border-blue-500 focus-within:bg-white transition-all">
                        <input type="color" id="color_code" name="color_code" value="{{ old('color_code', '#3B82F6') }}" 
                               class="w-12 h-12 border-none rounded-xl cursor-pointer bg-transparent">
                        <input type="text" name="color_code_text" id="color_code_text" value="#3B82F6" 
                               class="flex-1 bg-transparent border-none font-mono font-bold text-gray-500 uppercase outline-none" readonly>
                    </div>
                </div>
            </div>

            {{-- Row 2: Icon Selection (DESIGN FIX HERE) --}}
            <div class="space-y-4">
                <label class="block text-xs font-black text-blue-600 uppercase tracking-[0.2em]">Select Visual Icon</label>
                
                {{-- Horizontal Icon Chips --}}
                <div class="flex flex-wrap gap-3">
                    @php
                        $presets = [
                            'heavy rain' => '🌧️', 'tsunami' => '🌊', 'road closure' => '🚧', 'fire' => '🔥',
                            'lightning' => '⚡', 'water outage' => '🚰', 'power outage' => '💡', 'unstable internet' => '📶'
                        ];
                    @endphp
                    @foreach($presets as $key => $emoji)
                        <button type="button" onclick="setIcon('{{ $key }}', '{{ $emoji }}')" 
                                class="flex items-center gap-2 px-4 py-3 bg-gray-50 hover:bg-blue-50 border-2 border-transparent hover:border-blue-400 rounded-2xl transition-all group active:scale-95">
                            <span class="text-2xl">{{ $emoji }}</span>
                            <span class="text-[10px] font-black text-gray-400 group-hover:text-blue-500 uppercase tracking-tight">{{ $key }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Preview Input --}}
                <div class="relative mt-6 group">
                    <div class="absolute left-5 top-1/2 -translate-y-1/2 text-2xl filter drop-shadow-sm" id="icon-preview">⚠️</div>
                    <input type="text" name="icon" id="icon" value="{{ old('icon') }}"
                           class="w-full px-6 py-5 bg-gray-100 border-none rounded-[1.5rem] font-bold text-gray-800 pl-16 focus:ring-2 focus:ring-blue-500 transition-all"
                           placeholder="Selected icon identifier..." readonly>
                </div>
            </div>

            {{-- Row 3: Description --}}
            <div class="space-y-3">
                <label for="description" class="block text-xs font-black text-blue-600 uppercase tracking-[0.2em]">Description (Optional)</label>
                <textarea id="description" name="description" rows="3" 
                          class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-500 focus:bg-white rounded-2xl transition-all font-medium text-gray-600 outline-none"
                          placeholder="Provide details about this alert type...">{{ old('description') }}</textarea>
            </div>

            {{-- Footer Action --}}
            <div class="pt-6 border-t border-gray-50 flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-5 rounded-[1.5rem] font-black text-lg shadow-xl shadow-blue-200 hover:bg-blue-700 hover:-translate-y-1 transition-all duration-300 uppercase tracking-widest">
                    Create New Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Link Color Picker
    const colorPicker = document.getElementById('color_code');
    const colorText = document.getElementById('color_code_text');
    colorPicker.addEventListener('input', (e) => colorText.value = e.target.value.toUpperCase());

    // Icon Picker Function
    const iconInput = document.getElementById('icon');
    const iconPreview = document.getElementById('icon-preview');

    function setIcon(name, emoji) {
        iconInput.value = name;
        iconPreview.innerText = emoji;
    }
</script>
@endsection