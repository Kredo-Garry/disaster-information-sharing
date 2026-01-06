@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto mt-10 space-y-6">

    <h1 class="text-3xl font-bold mb-4">Admin Dashboard</h1>
    <p class="text-gray-600 mb-6">Welcome, {{ auth()->user()->name }}!</p>

    {{-- 3つのカード --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="p-4 bg-blue-100 rounded shadow">
            <h2 class="font-bold text-lg">Total Users</h2>
            <p class="text-2xl">{{ \App\Models\User::count() }}</p>
        </div>
        <div class="p-4 bg-green-100 rounded shadow">
            <h2 class="font-bold text-lg">Total Disasters</h2>
            <p class="text-2xl">{{ \App\Models\Disaster::count() }}</p>
        </div>
        <div class="p-4 bg-yellow-100 rounded shadow">
            <h2 class="font-bold text-lg">Other Info</h2>
            <p>---</p>
        </div>
    </div>

    {{-- サンプルテーブル --}}
    <div class="overflow-x-auto bg-white rounded shadow mt-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach (\App\Models\User::all() as $user)
                    <tr>
                        <td class="px-6 py-4">{{ $user->id }}</td>
                        <td class="px-6 py-4">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">{{ $user->is_admin ? 'Yes' : 'No' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- サンプル簡易チャート風 --}}
    <div class="mt-6 p-4 bg-white rounded shadow">
        <h2 class="font-bold mb-2">Monthly Users (Sample Chart)</h2>
        <div class="h-32 bg-gray-100 flex items-end space-x-2">
            <div class="w-4 bg-blue-500" style="height:50%"></div>
            <div class="w-4 bg-blue-500" style="height:70%"></div>
            <div class="w-4 bg-blue-500" style="height:40%"></div>
            <div class="w-4 bg-blue-500" style="height:90%"></div>
            <div class="w-4 bg-blue-500" style="height:60%"></div>
        </div>
    </div>

</div>
@endsection
