@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-6">Dashboard</h1>

<div class="grid grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded shadow">
        <p>Users</p>
        <p class="text-3xl font-bold">{{ $userCount }}</p>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <p>Posts</p>
        <p class="text-3xl font-bold">{{ $postCount }}</p>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <p>Categories</p>
        <p class="text-3xl font-bold">{{ $categoryCount }}</p>
    </div>
</div>
@endsection
