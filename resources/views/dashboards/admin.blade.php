@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6 text-white">Admin Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 bg-white rounded shadow">
            <h2 class="font-semibold">Manage Users</h2>
            <p class="text-sm text-gray-500 mt-2">Create, edit, delete users</p>
        </div>
        <div class="p-6 bg-white rounded shadow">
            <h2 class="font-semibold">Projects</h2>
            <p class="text-sm text-gray-500 mt-2">Manage all projects</p>
        </div>
        <div class="p-6 bg-white rounded shadow">
            <h2 class="font-semibold">Reports</h2>
            <p class="text-sm text-gray-500 mt-2">View analytics & reports</p>
        </div>
    </div>
@endsection