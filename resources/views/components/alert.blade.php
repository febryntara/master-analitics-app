{{-- resources/views/components/alert.blade.php --}}
@props(['type' => 'success', 'message'])

@php
    $colors = [
        'success' => 'bg-green-100 text-green-800',
        'error' => 'bg-red-100 text-red-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'info' => 'bg-blue-100 text-blue-800',
    ];

    $color = $colors[$type] ?? $colors['info'];
@endphp

<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
    class="fixed top-5 right-5 z-50 p-4 rounded shadow-lg {{ $color }} flex items-center space-x-2" role="alert">
    <span>{{ $message }}</span>
    <button @click="show = false" class="ml-2 font-bold">&times;</button>
</div>
