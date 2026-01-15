@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold mb-8 text-gray-800 border-b-2 border-blue-500 pb-2 inline-block">
        Admin Dashboard
    </h1>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        {{-- Users --}}
        <div class="bg-white rounded-xl shadow-md border-l-4 border-blue-500 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Users</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $userCount ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-blue-500 mt-4 block hover:underline italic">View all users →</a>
        </div>

        {{-- Posts --}}
        <div class="bg-white rounded-xl shadow-md border-l-4 border-green-500 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 4v4h4"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Posts</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $postCount ?? 0 }}</p>
                </div>
            </div>
            <a href="#" class="text-xs text-green-500 mt-4 block hover:underline italic">View all posts (Coming Soon) →</a>
        </div>

        {{-- Categories (ここを修正しました) --}}
        <div class="bg-white rounded-xl shadow-md border-l-4 border-yellow-500 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Categories</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $categoryCount ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="text-xs text-yellow-500 mt-4 block hover:underline italic">View all categories →</a>
        </div>
    </div>

    {{-- Data Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Latest Users Table --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-700">Latest Registered Users</h3>
            </div>
            <div class="p-6">
                <ul class="divide-y divide-gray-100">
                    @forelse($latestUsers as $user)
                        <li class="py-4 flex justify-between items-center transition hover:bg-gray-50 px-2 rounded-lg">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold mr-3">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-gray-700">{{ $user->name }}</span>
                            </div>
                            <span class="text-xs font-mono text-gray-400 bg-gray-100 px-2 py-1 rounded">{{ $user->created_at->format('Y/m/d') }}</span>
                        </li>
                    @empty
                        <li class="py-4 text-center text-gray-400 italic">No users yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Latest Posts Table --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-700">Recent Activity (Posts)</h3>
            </div>
            <div class="p-6">
                <ul class="divide-y divide-gray-100">
                    @forelse($latestPosts as $post)
                        <li class="py-4 transition hover:bg-gray-50 px-2 rounded-lg">
                            <p class="font-semibold text-gray-800 truncate">{{ $post->title }}</p>
                            <div class="flex justify-between mt-1">
                                <span class="text-xs text-gray-500">By {{ $post->user->name ?? 'Guest' }}</span>
                                <span class="text-xs text-blue-400 font-medium">{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="py-4 text-center text-gray-400 italic">No posts found.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection