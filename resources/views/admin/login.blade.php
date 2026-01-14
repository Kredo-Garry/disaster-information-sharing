@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-20 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Admin Login</h2>
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-2 mb-4 rounded">
            {{ $errors->first() }}
        </div>
    @endif
    <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <input type="email" name="email" placeholder="Email" class="border p-2 w-full mb-2 rounded" required>
        <input type="password" name="password" placeholder="Password" class="border p-2 w-full mb-4 rounded" required>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 w-full rounded hover:bg-blue-600 transition">Login</button>
    </form>
</div>
@endsection
