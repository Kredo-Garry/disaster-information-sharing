<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Category</title>
</head>
<body>
    <h1>Create New Category</h1>

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.categories.index') }}"><< Back to Categories List</a>
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

    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf

        <div>
            <label for="name">Category Name:</label><br>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. No Wifi, Earthquakes" required autofocus>
        </div>

        {{-- ここから追加：アイコン入力欄 --}}
        <div style="margin-top: 15px;">
            <label for="icon">Icon Name (Lucide/FontAwesome):</label><br>
            <input type="text" id="icon" name="icon" value="{{ old('icon') }}" placeholder="e.g. wifi, flame, alert-triangle">
            <p style="font-size: 0.8rem; color: #666;">※WiFiマークにするには「<strong>wifi</strong>」と入力してください</p>
        </div>
        {{-- ここまで追加 --}}

        <div style="margin-top: 15px;">
            <label for="description">Description (Optional):</label><br>
            <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit">Create Category</button>
        </div>
    </form>
</body>
</html>