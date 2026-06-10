@props(['message' => 'No data found.', 'action' => null, 'actionUrl' => null])

<div class="text-center py-12">
    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
    </svg>
    <h3 class="mt-2 text-sm font-medium text-gray-500">{{ $message }}</h3>
    @if($action && $actionUrl)
    <div class="mt-4">
        <a href="{{ $actionUrl }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
            {{ $action }}
        </a>
    </div>
    @endif
</div>
