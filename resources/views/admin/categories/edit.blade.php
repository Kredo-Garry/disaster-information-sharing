<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category | AdminPanel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f8fafc; font-family: sans-serif; }
    </style>
</head>
<body class="p-4 sm:p-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Category: {{ $category->name }}</h1>
                <p class="text-sm text-gray-500 mt-1">Change disaster type and map icons.</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
                &larr; Back to List
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-r-xl shadow-sm">
                <ul class="list-disc ml-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 sm:p-10">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="flex flex-col space-y-2">
                    <label for="name" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Category Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required
                           class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all text-gray-700">
                </div>

                <div class="flex flex-col space-y-2">
                    <label for="icon" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Select Icon</label>
                    <div class="relative">
                        <select id="icon" name="icon" required
                                class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all text-gray-700 appearance-none cursor-pointer">
                            @foreach($icons as $value => $label)
                                <option value="{{ $value }}" {{ old('icon', $category->icon) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            ▼
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 ml-1">※ Choosing an icon here updates the React Map markers.</p>
                </div>

                <div class="flex flex-col space-y-2">
                    <label for="description" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Description (Optional)</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all text-gray-700">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-lg shadow-lg shadow-blue-100 transition-all transform active:scale-95">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>