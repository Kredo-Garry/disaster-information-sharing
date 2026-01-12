<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen">
        {{-- 上部のナビゲーション --}}
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <span class="text-xl font-bold text-blue-600">Admin Panel</span>
                    </div>
                    <div class="flex items-center">
                        <div class="flex items-center space-x-3">
                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="font-medium text-gray-700">{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        {{-- メインコンテンツエリア --}}
        <main class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
            @yield('content')
        </main>
    </div>
</body>
</html>