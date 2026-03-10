<x-customer::layouts.master>
    <x-slot name="title">My Account</x-slot>
    <x-slot name="header">My Account</x-slot>

    <p class="text-gray-500">Welcome back, {{ auth()->user()->name }}.</p>

    {{-- Booking summary will go here --}}
</x-customer::layouts.master>
