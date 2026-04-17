@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-white">Projects</h1>
            <p class="text-sm text-gray-300">Manage all projects</p>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded-xl shadow">
            + New Project
        </button>
    </div>

    {{-- SEARCH --}}
    <div class="flex justify-between items-center">
        <input type="text" placeholder="Search project..."
            class="px-4 py-2 rounded-xl bg-white w-1/3 shadow">

        <button class="bg-white px-4 py-2 rounded-xl shadow">
            Filter
        </button>
    </div>

    {{-- SECTIONS --}}
    @php
        $sections = [
            ['title' => 'On Progress', 'color' => 'bg-yellow-400'],
            ['title' => 'Upcoming', 'color' => 'bg-blue-400'],
            ['title' => 'Finished', 'color' => 'bg-green-400'],
        ];
    @endphp

    @foreach ($sections as $section)
    <div class="bg-white rounded-xl shadow overflow-hidden">

        {{-- HEADER --}}
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <div class="flex items-center gap-2">
                <span class="{{ $section['color'] }} w-2 h-6 rounded"></span>
                <h2 class="font-semibold">{{ $section['title'] }}</h2>
            </div>

            <button class="text-gray-500">+</button>
        </div>

        {{-- TABLE --}}
        <table class="w-full text-sm">
            <thead class="text-gray-500 border-b">
                <tr>
                    <th class="px-6 py-3 text-left">Project</th>
                    <th class="px-6 py-3 text-left">Client</th>
                    <th class="px-6 py-3 text-left">Deadline</th>
                    <th class="px-6 py-3 text-left">Freelancer</th>
                    <th class="px-6 py-3 text-left">Status</th>
                </tr>
            </thead>

            <tbody>
                @for ($i = 0; $i < 3; $i++)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">Project Name</td>
                    <td class="px-6 py-4">Client Name</td>
                    <td class="px-6 py-4">Jun 2025</td>
                    <td class="px-6 py-4">
                        <span class="bg-gray-200 px-2 py-1 rounded text-xs">
                            Assigned / Unassigned
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs">
                            {{ $section['title'] }}
                        </span>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>

    </div>
    @endforeach

</div>
@endsection