<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>Admin | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-800 text-white p-6 space-y-4">
        <h2 class="text-xl font-bold mb-6">Admin Panel</h2>

        <a href="{{ route('admin.dashboard') }}" class="block hover:underline">Dashboard</a>
        <a href="#" class="block hover:underline">Users</a>
        <a href="#" class="block hover:underline">Posts</a>
        <a href="#" class="block hover:underline">Categories</a>
    </aside>

    {{-- Main --}}
    <main class="flex-1 p-8">
        @yield('content')
    </main>

</div>

</body>
</html>
