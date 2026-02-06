@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-6 py-8">
  <h1 class="text-3xl font-bold mb-6 text-gray-800 border-b-2 border-indigo-500 pb-2 inline-block">
    PHIVOLCS Fetch
  </h1>

  <div class="flex flex-wrap gap-3 mb-6">
    <form method="POST" action="{{ route('admin.phivolcs.fetchAll') }}">
      @csrf
      <button type="submit" class="px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 shadow">
        Fetch All
      </button>
    </form>

    <form method="POST" action="{{ route('admin.phivolcs.fetchEarthquakes') }}">
      @csrf
      <button type="submit" class="px-4 py-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 shadow">
        Fetch Earthquakes
      </button>
    </form>

    <form method="POST" action="{{ route('admin.phivolcs.fetchTsunami') }}">
      @csrf
      <button type="submit" class="px-4 py-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 shadow">
        Fetch Tsunami
      </button>
    </form>

    <form method="POST" action="{{ route('admin.phivolcs.fetchVolcano') }}">
      @csrf
      <button type="submit" class="px-4 py-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 shadow">
        Fetch Volcano Alerts
      </button>
    </form>
  </div>

  <p class="text-sm text-gray-600 mb-6">
    Scheduled fetch runs daily via Laravel Scheduler. Manual buttons call the same artisan commands.
  </p>

  @if(session('phivolcs_results'))
    <div class="grid gap-4">
      @foreach(session('phivolcs_results') as $r)
        <div class="bg-white rounded-xl shadow border border-gray-100 p-4">
          <div class="flex justify-between items-start gap-3">
            <div class="font-extrabold {{ $r['ok'] ? 'text-emerald-600' : 'text-red-600' }}">
              {{ $r['ok'] ? '✅ Success' : '❌ Failed' }}
            </div>
            <div class="text-xs text-gray-500">{{ $r['started_at'] }}</div>
          </div>

          <div class="mt-3 text-xs font-mono bg-gray-50 border border-gray-100 rounded-lg p-3 overflow-auto">
            {{ $r['command'] }}
          </div>

          <div class="mt-2 text-sm text-gray-700">
            exit_code: <span class="font-bold">{{ $r['exit_code'] }}</span>
          </div>

          @if(!empty($r['output']))
            <pre class="mt-3 text-xs bg-gray-50 border border-gray-100 rounded-lg p-3 overflow-auto whitespace-pre-wrap">{{ $r['output'] }}</pre>
          @endif
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
