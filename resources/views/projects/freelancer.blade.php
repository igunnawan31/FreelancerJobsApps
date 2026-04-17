@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-white">My Projects</h1>
        <p class="text-sm text-gray-300">Manage your work</p>
    </div>

    @php
        $sections = [
            ['title' => 'On Going', 'color' => 'bg-yellow-400', 'action' => ''],
            ['title' => 'Available Projects', 'color' => 'bg-blue-400', 'action' => 'Request'],
            ['title' => 'Finished', 'color' => 'bg-green-400', 'action' => ''],
        ];
    @endphp

    @foreach ($sections as $section)
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <div class="px-6 py-4 border-b flex items-center gap-2">
            <span class="{{ $section['color'] }} w-2 h-6 rounded"></span>
            <h2 class="font-semibold">{{ $section['title'] }}</h2>
        </div>

        <table class="w-full text-sm">
            <thead class="text-gray-500 border-b">
                <tr>
                    <th class="px-6 py-3 text-left">Project</th>
                    <th class="px-6 py-3 text-left">Client</th>
                    <th class="px-6 py-3 text-left">Deadline</th>
                    @if($section['action'])
                        <th class="px-6 py-3 text-left">Action</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @for ($i = 0; $i < 3; $i++)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">Project Name</td>
                    <td class="px-6 py-4">Client Name</td>
                    <td class="px-6 py-4">Jun 2025</td>

                    @if($section['action'])
                    <td class="px-6 py-4">
                        <button class="bg-blue-600 text-white px-3 py-1 rounded text-xs">
                            Request
                        </button>
                    </td>
                    @endif
                </tr>
                @endfor
            </tbody>
        </table>

    </div>
    @endforeach

</div>
@endsection