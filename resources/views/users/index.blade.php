@extends('layouts.admin')

@section('content')
<h1 class="text-xl font-bold mb-4">Users</h1>

<table class="w-full bg-white shadow rounded">
    <tr class="border-b">
        <th class="p-2">ID</th>
        <th>Name</th>
        <th>Email</th>
    </tr>

    @foreach ($users as $user)
        <tr class="border-b">
            <td class="p-2">{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
        </tr>
    @endforeach
</table>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
