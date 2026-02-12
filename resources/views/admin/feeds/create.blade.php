@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-black mb-6">Add Feed</h1>

<form method="POST" action="{{ route('admin.feeds.store') }}" class="space-y-4 max-w-2xl">
@csrf

<input name="original_url" placeholder="Facebook URL" class="w-full rounded" required>
<input name="external_author" placeholder="Author (PHIVOLCS etc)" class="w-full rounded">
<textarea name="content" placeholder="Post content" class="w-full rounded"></textarea>
<textarea name="embed_html" placeholder="Embed HTML (optional)" class="w-full rounded"></textarea>
<input name="tags" placeholder="earthquake, flood" class="w-full rounded">
<input type="datetime-local" name="published_at" class="w-full rounded">

<label class="flex items-center gap-2">
  <input type="checkbox" name="is_visible" value="1" checked> Visible
</label>

<button class="px-4 py-2 bg-blue-600 text-white rounded font-bold">
  Save
</button>
</form>
@endsection
