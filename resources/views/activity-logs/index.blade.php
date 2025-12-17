@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Activity Logs</h1>

    <div class="mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="user_id" placeholder="User ID" value="{{ request('user_id') }}" class="px-2 py-1 border rounded">
            <input type="text" name="action" placeholder="Action" value="{{ request('action') }}" class="px-2 py-1 border rounded">
            <button class="px-3 py-1 bg-blue-600 text-white rounded">Filter</button>
        </form>
    </div>

    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Model</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Changes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">IP</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($logs as $log)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $log->created_at }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $log->user?->name ?? $log->user_id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $log->action }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $log->model_type ? class_basename($log->model_type) . ' #' . $log->model_id : '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700"><pre class="whitespace-pre-wrap">{{ json_encode($log->changes) }}</pre></td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $log->ip_address }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
