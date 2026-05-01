@extends('layouts.app')

@section('content')

<div class="p-8 space-y-6 ">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-white">Projects</h1>

        <a href="{{ route('projects.create') }}"
           class="bg-blue-900 text-white px-4 py-2 rounded-full text-sm hover:opacity-90">
            + New Project
        </a>
    </div>

    {{-- SEARCH --}}
    <div>
        <input type="text" placeholder="Search project"
            class="w-full md:w-1/3 px-4 py-2 rounded-lg border focus:outline-none">
    </div>

    {{-- ================= ADMIN ================= --}}
    @if(auth()->user()->role === \App\Enums\UserEnums\UserRole::ADMIN)

        {{-- ON PROGRESS --}}
        <div class="bg-[#EAF4FB] p-6 rounded-2xl">
            <div class="flex justify-between items-center mb-4">
                <span class="bg-blue-900 text-white px-3 py-1 rounded-lg text-sm">
                    On Progress
                </span>
                <a href="#" class="text-sm text-blue-700">See all →</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($projects->where('project_status', \App\Enums\ProjectEnums\ProjectStatus::STATUS_RUNNING) or where('project_status', \App\Enums\ProjectEnums\ProjectStatus::STATUS_REVISION) as $project)
                    <div class="bg-white rounded-xl p-4 shadow space-y-3">

                        {{-- IMAGE PLACEHOLDER --}}
                        <div class="h-32 bg-gray-200 rounded-lg"></div>

                        {{-- CONTENT --}}
                        <div>
                            <h3 class="font-semibold">{{ $project->project_name }}</h3>
                            <p class="text-sm text-gray-500">Project details</p>
                        </div>

                        {{-- DEADLINE --}}
                        <div class="text-xs bg-blue-100 text-blue-800 inline-block px-2 py-1 rounded">
                            📅 {{ $project->project_deadline }}
                        </div>

                        {{-- FOOTER --}}
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>{{ $project->user->name ?? 'Unassigned' }}</span>
                            <span class="w-3 h-3 bg-gray-300 rounded-full"></span>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>


        {{-- UPCOMING --}}
        <div class="bg-[#EAF4FB] p-6 rounded-2xl">
            <div class="flex justify-between items-center mb-4">
                <span class="bg-blue-500 text-white px-3 py-1 rounded-lg text-sm">
                    Upcoming
                </span>
                <a href="#" class="text-sm text-blue-700">See all →</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($projects->where('project_status', \App\Enums\ProjectEnums\ProjectStatus::STATUS_OPEN) as $project)
                    <div class="bg-white rounded-xl p-4 shadow space-y-3">

                        <div class="h-32 bg-gray-200 rounded-lg"></div>

                        <div>
                            <h3 class="font-semibold">{{ $project->project_name }}</h3>
                            <p class="text-sm text-gray-500">Project details</p>
                        </div>

                        <div class="text-xs bg-blue-100 text-blue-800 inline-block px-2 py-1 rounded">
                            📅 {{ $project->project_deadline }}
                        </div>

                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>{{ $project->client->name ?? '-' }}</span>
                            <span class="w-3 h-3 bg-gray-300 rounded-full"></span>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>

        {{-- FINISHED --}}
        <div class="bg-[#EAF4FB] p-6 rounded-2xl">
            <div class="flex justify-between items-center mb-4">
                <span class="bg-blue-500 text-white px-3 py-1 rounded-lg text-sm">
                    Finished
                </span>
                <a href="#" class="text-sm text-blue-700">See all →</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($projects->where('project_status', \App\Enums\ProjectEnums\ProjectStatus::STATUS_COMPLETED) or where('project_status', \App\Enums\ProjectEnums\ProjectStatus::STATUS_DONE) as $project)
                    <div class="bg-white rounded-xl p-4 shadow space-y-3">

                        <div class="h-32 bg-gray-200 rounded-lg"></div>

                        <div>
                            <h3 class="font-semibold">{{ $project->project_name }}</h3>
                            <p class="text-sm text-gray-500">Project details</p>
                        </div>

                        <div class="text-xs bg-blue-100 text-blue-800 inline-block px-2 py-1 rounded">
                            📅 {{ $project->project_deadline }}
                        </div>

                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>{{ $project->client->name ?? '-' }}</span>
                            <span class="w-3 h-3 bg-gray-300 rounded-full"></span>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>

        {{-- STATS --}}
        <div class="bg-white rounded-2xl shadow p-4 flex justify-around text-center">
            <div>
                <h2 class="text-xl font-bold">
                    {{ $projects->where('project_status', \App\Enums\ProjectEnums\ProjectStatus::STATUS_RUNNING)->count() }}
                </h2>
                <p class="text-sm text-gray-500">On Progress</p>
            </div>

            <div>
                <h2 class="text-xl font-bold">
                    {{ $projects->where('project_status', \App\Enums\ProjectEnums\ProjectStatus::STATUS_OPEN)->count() }}
                </h2>
                <p class="text-sm text-gray-500">Upcoming</p>
            </div>

            <div>
                <h2 class="text-xl font-bold">
                    {{ $projects->where('project_status', \App\Enums\ProjectEnums\ProjectStatus::STATUS_DONE)->count() }}
                </h2>
                <p class="text-sm text-gray-500">Finished</p>
            </div>

            <div>
                <h2 class="text-xl font-bold">
                    {{ $projects->count() }}
                </h2>
                <p class="text-sm text-gray-500">Total Project</p>
            </div>
        </div>

    @endif

</div>

@endsection