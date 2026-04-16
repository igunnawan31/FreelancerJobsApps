@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6 text-white">My Tasks Overview</h1>
    <div class="bg-white p-6 rounded shadow">
        <h2 class="font-semibold mb-4">Assigned Tasks</h2>
        <ul class="space-y-3">
            <li class="p-3 bg-gray-100 rounded">Task 1 - Build API</li>
            <li class="p-3 bg-gray-100 rounded">Task 2 - Fix Bug</li>
            <li class="p-3 bg-gray-100 rounded">Task 3 - UI Design</li>
        </ul>
    </div>
@endsection