@extends('layouts.app')

@php
    $displayProjects = $user->role === \App\Enums\UserEnums\UserRole::CLIENT 
        ? $user->clients 
        : $user->projects;
@endphp

@section('content')
    <div class="p-8 space-y-6">
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-white/20">
            <div class="flex items-center gap-4">
                <a href="{{ route('users.index') }}" class="p-2 hover:bg-gray-100 rounded-full transition">
                    <img 
                        src="{{ asset('assets/back.svg') }}" 
                        alt="Back" 
                        class="w-6 h-6"
                    >
                </a>
                <h1 class="text-2xl font-bold text-gray-800">User Profile</h1>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('users.edit', $user->user_id) }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl transition font-medium">Edit Profile</a>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="space-y-6">
                <div class="bg-white p-8 rounded-2xl shadow-sm text-center border border-white">
                    <img 
                        src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?size=128&name='.urlencode($user->name) }}" 
                        class="w-32 h-32 rounded-3xl mx-auto object-cover border-4 border-purple-50 shadow-lg mb-4"
                    >
                    <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 mt-1">
                        {{ ucfirst($user->role->value) }}
                    </span>

                    <div class="mt-6 pt-6 border-t border-gray-100 space-y-3 text-left">
                        <div class="flex items-center text-gray-600">
                            <img 
                                src="{{ asset('assets/email.svg') }}" 
                                alt="Email"
                                class="w-4 h-4 mr-4"
                            > 
                            <span class="text-sm">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <img 
                                src="{{ asset('assets/phone.svg') }}" 
                                alt="Phone number"
                                class="w-4 h-4 mr-4"
                            > 
                            <span class="text-sm">{{ $user->phone_number ?? 'Not provided' }}</span>
                        </div>
                        @if($user->portfolio)
                        <div class="flex items-center text-gray-600">
                            <img 
                                src="{{ asset('assets/url.svg') }}" 
                                alt="Portfolio"
                                class="w-4 h-4 mr-4"
                            > 
                            <a href="{{ $user->portfolio }}" target="_blank" class="text-sm text-blue-600 hover:underline">View Portfolio</a>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-white">
                    <div class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <img 
                            src="{{ asset('assets/skill.svg') }}" 
                            alt="Skill"
                            class="w-4 h-4"
                        > 
                        Skills & Expertise
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @forelse($user->skills as $skill)
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 border border-gray-200 rounded-lg text-sm">
                                {{ $skill->skill_name }}
                            </span>
                        @empty
                            <p class="text-gray-400 text-sm italic">No skills listed.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-500 p-6 rounded-2xl text-white flex flex-col gap-2">
                        <p class="text-blue-100 text-sm">Total Projects</p>
                        <p class="text-2xl font-bold">
                            @if ($user->role->value === 'client')
                                {{ $user->totalProjectsClient() }}
                            @elseif ($user->role->value === 'freelancer')
                                {{ $user->totalProjects() }}
                            @else
                                NaN
                            @endif
                        </p>
                    </div>
                    <div class="bg-purple-500 p-6 rounded-2xl text-white flex flex-col gap-2">
                        <p class="text-purple-100 text-sm">
                            Ratings
                        </p>
                        <p class="text-2xl font-bold">
                            @if ($user->role->value === 'freelancer')
                                {{ number_format($user->averageRating(), 1) }} / 5.0
                            @else
                                NaN
                            @endif
                        </p>
                    </div>
                    <div class="bg-emerald-500 p-6 rounded-2xl text-white flex flex-col gap-2">
                        <p class="text-emerald-100 text-sm">
                            {{ $user->role === \App\Enums\UserEnums\UserRole::CLIENT ? 'Running Project' : 'Availability' }}
                        </p>
                        <p class="text-2xl font-bold">
                            @if ($user->role->value === 'client')
                                {{ $user->countActiveClientProject() }}
                            @elseif ($user->role->value === 'freelancer')
                                {{ $user->countActiveProject() }}/3 {{ $user->hasActiveProject() ? 'Busy (3+ Projects)' : 'Available' }}
                            @else
                                NaN
                            @endif
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-white overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">
                            {{ $user->role->value === \App\Enums\UserEnums\UserRole::CLIENT ? 'Commission History' : 'Project History' }}
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-4">Project Name</th>
                                    <th class="px-6 py-4">{{ $user->role->value === 'client' ? 'Freelancer' : 'Client' }}</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($displayProjects as $project)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $project->project_name }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            @if($user->role->value === 'client')
                                                {{ $project->user->name ?? 'Unassigned' }}
                                            @else
                                                {{ $project->client->name ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase border border-current">
                                                {{ $project->project_status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $project->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">No history found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection