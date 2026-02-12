@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-800">Feed Management</h1>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl font-bold text-sm shadow-sm">
            {{ session('success') }}
        </div>
    @endif

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
                @foreach($feeds as $feed)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-bold text-gray-800">{{ $feed->external_author }}</span>
                            <span class="text-xs text-blue-500 bg-blue-50 px-2 py-0.5 rounded-full ml-2">
                                {{ $feed->source_platform }}
                            </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600 line-clamp-2">{{ $feed->content }}</p>
                        <span class="text-[10px] text-gray-400">{{ $feed->created_at->format('Y/m/d H:i') }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('admin.feeds.destroy', $feed) }}" method="POST" class="inline" onsubmit="return confirm('この投稿を削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 font-bold text-sm transition">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $feeds->links() }}
        </div>
    </div>
</div>
@endsection
