@props(['label', 'value', 'color' => 'blue'])

@php
$colorClasses = match($color) {
    'green' => 'bg-green-50 text-green-600',
    'yellow' => 'bg-yellow-50 text-yellow-600',
    'red' => 'bg-red-50 text-red-600',
    'purple' => 'bg-purple-50 text-purple-600',
    default => 'bg-blue-50 text-blue-600',
};
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">{{ $label }}</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $value }}</p>
        </div>
        <div class="w-12 h-12 rounded-lg {{ $colorClasses }} flex items-center justify-center">
            {{ $icon ?? '' }}
        </div>
    </div>
</div>
