@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-end justify-between gap-4 flex-wrap">
        <h1 class="text-3xl font-bold text-gray-800">Feed Management</h1>

        @if(session('status'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl font-bold text-sm shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl font-bold text-sm shadow-sm">
                {{ session('success') }}
            </div>
        @endif
    </div>

    {{-- Add Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Add Feed</h2>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl font-bold text-sm shadow-sm">
                {{ implode(' / ', $errors->all()) }}
            </div>
        @endif

        <form action="{{ route('admin.feeds.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Platform</label>
                    <select name="source_platform" class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-blue-200">
                        <option value="facebook" @selected(old('source_platform', 'facebook') === 'facebook')>Facebook</option>
                        <option value="web" @selected(old('source_platform') === 'web')>Web</option>
                        <option value="x" @selected(old('source_platform') === 'x')>X</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Author</label>
                    <input
                        type="text"
                        name="external_author"
                        value="{{ old('external_author') }}"
                        placeholder="e.g. PHIVOLCS / NDRRMC / Admin"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-blue-200"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Visibility</label>
                    <select name="is_visible" class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-blue-200">
                        <option value="1" @selected(old('is_visible', '1') === '1')>Visible</option>
                        <option value="0" @selected(old('is_visible') === '0')>Hidden</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Original URL</label>
                    <input
                        type="url"
                        name="original_url"
                        value="{{ old('original_url') }}"
                        placeholder="https://..."
                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-blue-200"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Published At</label>
                    <input
                        type="text"
                        name="published_at"
                        value="{{ old('published_at') }}"
                        placeholder="2026-02-13 12:34:00"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-blue-200"
                    />
                    <p class="text-[11px] text-gray-400 mt-1">If empty, current time will be used.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Sort Weight</label>
                    <input
                        type="number"
                        name="sort_weight"
                        value="{{ old('sort_weight', 0) }}"
                        class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-blue-200"
                    />
                    <p class="text-[11px] text-gray-400 mt-1">Higher numbers appear first.</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tags (comma separated)</label>
                <input
                    type="text"
                    name="tags"
                    value="{{ old('tags') }}"
                    placeholder="earthquake, tsunami, volcano"
                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-blue-200"
                />
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Content</label>
                <textarea
                    name="content"
                    rows="4"
                    placeholder="Write feed content..."
                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-blue-200"
                >{{ old('content') }}</textarea>

                <p class="text-[11px] text-gray-400 mt-1">
                    If your database requires content (NOT NULL), this field must not be empty.
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Embed HTML (optional)</label>
                <input
                    type="text"
                    name="embed_html"
                    value="{{ old('embed_html') }}"
                    placeholder='<iframe ...></iframe>'
                    class="w-full rounded-xl border-gray-200 focus:border-blue-400 focus:ring-blue-200"
                />
                <p class="text-[11px] text-gray-400 mt-1">
                    Only trusted HTML should be entered. This will be rendered using dangerouslySetInnerHTML.
                </p>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-sm transition"
                >
                    Add Feed
                </button>
            </div>
        </form>
    </div>

    {{-- Feed List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Author</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Content</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($feeds as $feed)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap align-top">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-gray-800">
                                {{ $feed->external_author ?? 'Feed' }}
                            </span>

                            @if($feed->source_platform)
                                <span class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                                    {{ ucfirst($feed->source_platform) }}
                                </span>
                            @endif

                            @if($feed->is_visible)
                                <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">
                                    Visible
                                </span>
                            @else
                                <span class="text-xs text-red-700 bg-red-50 px-2 py-0.5 rounded-full">
                                    Hidden
                                </span>
                            @endif

                            <span class="text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">
                                Weight: {{ $feed->sort_weight }}
                            </span>
                        </div>

                        <div class="mt-2 space-y-1">
                            <div class="text-[11px] text-gray-400">
                                {{ optional($feed->published_at)->format('Y/m/d H:i') ?? $feed->created_at->format('Y/m/d H:i') }}
                            </div>

                            @if(is_array($feed->tags) && count($feed->tags) > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($feed->tags as $t)
                                        <span class="text-[11px] text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">
                                            {{ $t }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </td>

                    <td class="px-6 py-4 align-top">
                        <p class="text-sm text-gray-600 line-clamp-2">
                            {{ $feed->content }}
                        </p>

                        <div class="mt-2 flex items-center gap-3 flex-wrap">
                            @if($feed->original_url)
                                <a href="{{ $feed->original_url }}" target="_blank" rel="noreferrer"
                                   class="text-xs font-bold text-blue-600 hover:text-blue-800 underline">
                                    Open Original
                                </a>
                            @endif

                            @if($feed->embed_html)
                                <span class="text-[11px] text-gray-500 bg-gray-50 px-2 py-0.5 rounded-full border border-gray-200">
                                    Embed Saved
                                </span>
                            @endif
                        </div>
                    </td>

                    <td class="px-6 py-4 text-right align-top">
                        <form action="{{ route('admin.feeds.destroy', $feed) }}" method="POST" class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this feed?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 font-bold text-sm transition">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="px-6 py-6 text-sm text-gray-500" colspan="3">
                        No feeds available.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $feeds->links() }}
        </div>
    </div>
</div>
@endsection
