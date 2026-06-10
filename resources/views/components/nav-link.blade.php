@props(['active' => false, 'href' => '#'])

<a href="{{ $href }}" {{ $attributes->merge([
    'class' => 'flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors ' .
        ($active
            ? 'bg-blue-50 text-blue-700'
            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900')
]) }}>
    <span class="mr-3 {{ $active ? 'text-blue-600' : 'text-gray-400' }}">{{ $icon }}</span>
    {{ $slot }}
</a>
