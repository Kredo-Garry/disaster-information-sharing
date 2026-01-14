<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
</head>
<body>
    <h1>Edit Category: {{ $category->name }}</h1>

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

    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
        @csrf
        @method('PUT') {{-- 更新時はPUTメソッドを使います --}}

        <div>
            <label for="name">Category Name:</label><br>
            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required>
        </div>

        <div style="margin-top: 15px;">
            <label for="icon">Icon Name:</label><br>
            <input type="text" id="icon" name="icon" value="{{ old('icon', $category->icon) }}" placeholder="wifi, flame, alert-triangle">
            <p style="font-size: 0.8rem; color: #666;">※WiFiマークにするには「<strong>wifi</strong>」と入力してください</p>
        </div>

        <div style="margin-top: 15px;">
            <label for="description">Description (Optional):</label><br>
            <textarea id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit">Update Category</button>
        </div>
    </form>
</body>
</html>