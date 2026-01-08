@extends('layouts.admin')

@section('content')
<h1 class="text-3xl font-bold mb-8">Admin Dashboard</h1>

<div class="grid grid-cols-3 gap-6">
    <div class="bg-blue-100 p-6 rounded shadow">
        <p class="text-sm text-gray-600">Users</p>
        <p class="text-3xl font-bold">{{ $userCount }}</p>
    </div>

    <div class="bg-green-100 p-6 rounded shadow">
        <p class="text-sm text-gray-600">Posts</p>
        <p class="text-3xl font-bold">{{ $postCount }}</p>
    </div>

    <div class="bg-yellow-100 p-6 rounded shadow">
        <p class="text-sm text-gray-600">Categories</p>
        <p class="text-3xl font-bold">{{ $categoryCount }}</p>
    </div>
</div>
@endsection
