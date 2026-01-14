<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
</head>
<body>
    <h1>Edit User: {{ $user->name }}</h1>

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.users.index') }}"><< Back to Users List</a>
    </div>

    @if ($errors->any())
        <div style="color: red; margin-bottom: 20px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
        @csrf
        @method('PUT') <div>
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div style="margin-top: 15px;">
            <label for="email">Email Address:</label><br>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit">Update User</button>
        </div>
    </form>
</body>
</html>