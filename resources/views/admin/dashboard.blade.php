@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold mb-8 text-gray-800 border-b-2 border-blue-500 pb-2 inline-block">
        Admin Dashboard
    </h1>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        {{-- Users --}}
        <div class="bg-white rounded-xl shadow-md border-l-4 border-violet-500 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-violet-100 text-violet-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Users</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $userCount ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-violet-500 mt-4 block hover:underline italic">
                View all users →
            </a>
        </div>

        {{-- Posts --}}
        <div class="bg-white rounded-xl shadow-md border-l-4 border-green-500 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M14 4v4h4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Posts</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $postCount ?? 0 }}</p>
                </div>
            </div>
            <a href="#" class="text-xs text-green-500 mt-4 block hover:underline italic">View all posts (Coming Soon) →</a>
        </div>

        {{-- Categories (unused) --}}
        {{--
        <div class="bg-white rounded-xl shadow-md border-l-4 border-yellow-500 p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Categories</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $categoryCount ?? 0 }}</p>
                </div>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="text-xs text-yellow-500 mt-4 block hover:underline italic">View all categories →</a>
        </div>
        --}}
    </div>

    {{-- PHIVOLCS Fetch Status --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-800">PHIVOLCS Update Status</h2>
        <a href="{{ route('admin.phivolcs.index') }}" class="text-sm text-indigo-600 hover:underline">
            Open manual fetch page →
        </a>
    </div>

    @php
        // ==============================
        // Status color helpers
        // ==============================

        $statusToBorder = function($s) {
            return match($s) {
                'ok' => 'border-emerald-500',
                'warn' => 'border-amber-500',
                default => 'border-red-500',
            };
        };

        $statusToBadge = function($s) {
            return match($s) {
                'ok' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                'warn' => 'bg-amber-50 text-amber-700 border-amber-100',
                default => 'bg-red-50 text-red-700 border-red-100',
            };
        };

        $statusToText = function($s) {
            return match($s) {
                'ok' => 'text-emerald-700',
                'warn' => 'text-amber-700',
                default => 'text-red-700',
            };
        };

        // ==============================
        // 時間表示 helper
        // ==============================

    $agoLabel = function ($dt) {
        if (!$dt) return 'Never fetched';

        // タイムゾーン統一（PH時間）
        $dt = \Carbon\Carbon::parse($dt)->timezone('Asia/Manila');
        $now = now()->timezone('Asia/Manila');

        // ★ 第2引数 true で絶対値になる（マイナス消える）
        $minutes = $now->diffInMinutes($dt, true);

        if ($minutes < 60) {
            return intval($minutes) . 'm ago';
        }

        $hours = $now->diffInHours($dt, true);

        if ($hours < 24) {
            return intval($hours) . 'h ago';
        }

        $days = $now->diffInDays($dt, true);

        return intval($days) . 'd ago';
    };

    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        {{-- Earthquakes --}}
        <div class="bg-white rounded-xl shadow-md border-l-4 border-yellow-500 p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0 pr-16">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Earthquakes</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold border {{ $statusToBadge($eqFresh['status'] ?? 'danger') }}">
                            {{ $agoLabel($eqFetchedAt) }}
                        </span>
                    </div>

                    <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($eqCount ?? 0) }}</p>

                    <p class="text-xs text-gray-500 mt-2">
                        Last fetched:
                        <span class="font-mono">
                            {{ $eqFetchedAt ? $eqFetchedAt->format('Y/m/d H:i') : '—' }}
                        </span>
                    </p>

                    @if(($eqFresh['status'] ?? 'danger') !== 'ok')
                        <p class="text-xs mt-2 {{ $statusToText($eqFresh['status'] ?? 'danger') }} font-semibold">
                            Action: run manual fetch or check scheduler/cron.
                        </p>
                    @endif
                </div>

                {{-- Icon: Earthquake (crack + vibration) --}}
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 4l-2.5 5 3 2-2 7 7-8-3-2 2.5-4z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 8v4" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 8v4" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Volcano --}}
        <div class="bg-white rounded-xl shadow-md border-l-4 border-red-500 p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0 pr-16">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Volcano</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold border {{ $statusToBadge($volFresh['status'] ?? 'danger') }}">
                            {{ $agoLabel($volFetchedAt) }}
                        </span>

                    </div>

                    <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($volCount ?? 0) }}</p>

                    <p class="text-xs text-gray-500 mt-2">
                        Last fetched:
                        <span class="font-mono">
                            {{ $volFetchedAt ? $volFetchedAt->format('Y/m/d H:i') : '—' }}
                        </span>
                    </p>

                    @if(($volFresh['status'] ?? 'danger') !== 'ok')
                        <p class="text-xs mt-2 {{ $statusToText($volFresh['status'] ?? 'danger') }} font-semibold">
                            Action: run manual fetch or check scheduler/cron.
                        </p>
                    @endif
                </div>

                {{-- Icon: Volcano (cone + crater + smoke + lava) --}}
                <div class="p-3 rounded-full bg-red-100 text-red-600 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10 5c0-1 1-2 2-2s2 1 2 2-1 2-2 2-2 1-2 2 1 2 2 2" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 10h6" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 20l4-10h4l4 10H6z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v5" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Tsunami --}}
        <div class="bg-white rounded-xl shadow-md border-l-4 border-blue-500 p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0 pr-16">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Tsunami</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold border {{ $statusToBadge($tsuFresh['status'] ?? 'danger') }}">
                            {{ $agoLabel($tsuFetchedAt) }}
                        </span>
                    </div>

                    <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($tsuCount ?? 0) }}</p>

                    <p class="text-xs text-gray-500 mt-2">
                        Last fetched:
                        <span class="font-mono">
                            {{ $tsuFetchedAt ? $tsuFetchedAt->format('Y/m/d H:i') : '—' }}
                        </span>
                    </p>

                    @if(($tsuFresh['status'] ?? 'danger') !== 'ok')
                        <p class="text-xs mt-2 {{ $statusToText($tsuFresh['status'] ?? 'danger') }} font-semibold">
                            Action: run manual fetch or check scheduler/cron.
                        </p>
                    @endif
                </div>

                {{-- Icon: Tsunami (waves) --}}
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 17c2-2 4-2 6 0s4 2 6 0 4-2 6 0" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12c2-2 4-2 6 0s4 2 6 0 4-2 6 0" />
                    </svg>
                </div>
            </div>
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
