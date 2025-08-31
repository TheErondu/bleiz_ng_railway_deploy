@extends('layouts.app')

@section('content')
<div class="flex">
    {{-- Sidebar --}}
    @include('components.admin.sidebar')

    {{-- Main Content --}}
    <div class="flex-1 p-6">
        <h1 class="text-2xl font-semibold text-gray-800 mb-4">Admin Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white shadow rounded p-6">
                <h2 class="text-lg font-bold text-gray-700">Total Users</h2>
                <p class="mt-2 text-3xl font-semibold text-indigo-600">112</p>
            </div>
            <div class="bg-white shadow rounded p-6">
                <h2 class="text-lg font-bold text-gray-700">Active Loans</h2>
                <p class="mt-2 text-3xl font-semibold text-indigo-600">37</p>
            </div>
            <div class="bg-white shadow rounded p-6">
                <h2 class="text-lg font-bold text-gray-700">Transactions</h2>
                <p class="mt-2 text-3xl font-semibold text-indigo-600">₦4.5M</p>
            </div>
        </div>
    </div>
</div>
@endsection
