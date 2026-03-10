<x-vendor::layouts.master>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">Dashboard</x-slot>

    <p class="text-gray-500">Welcome to the Vendor Panel, {{ auth()->user()->name }}.</p>

    {{-- Stats / widgets will go here --}}
</x-vendor::layouts.master>
