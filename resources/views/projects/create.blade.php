@extends('layouts.app')

@section('content')

<div class="flex gap-6 p-6">

    <div class="flex-1 space-y-6">

        <h1 class="text-2xl font-bold text-white">Create Project</h1>

        <div class="bg-[#CFE5F2] p-6 rounded-2xl space-y-6">

            <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- PROJECT TITLE --}}
                <div>
                    <label class="text-sm font-medium">Project Title</label>
                    <input type="text" name="project_name"
                        value="{{ old('project_name') }}"
                        class="w-full mt-1 px-4 py-2 rounded-xl border outline-none">
                    @error('project_name')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DESCRIPTION --}}
                <div>
                    <label class="text-sm font-medium">Project Description</label>
                    <textarea name="project_description" rows="4"
                        class="w-full mt-1 px-4 py-2 rounded-xl border outline-none">{{ old('project_description') }}</textarea>
                    @error('project_description')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DEADLINE --}}
                <div>
                    <label class="text-sm font-medium">Deadline</label>
                    <input type="date" name="project_deadline"
                        value="{{ old('project_deadline') }}"
                        class="w-full mt-1 px-4 py-2 rounded-xl border outline-none">
                    @error('project_deadline')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- TYPE --}}
                <div>
                    <label class="text-sm font-medium">Required Skills</label>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-2">

                        @foreach($skills as $skill)
                            <label class="flex items-center gap-2 bg-white p-2 rounded-lg shadow-sm cursor-pointer">

                                <input type="checkbox"
                                    name="skills[]"
                                    value="{{ $skill->skill_id }}"
                                    class="accent-blue-500"
                                    {{ in_array($skill->skill_id, old('skills', [])) ? 'checked' : '' }}>

                                <span class="text-sm">{{ $skill->skill_name }}</span>

                            </label>
                        @endforeach

                    </div>

                    @error('skills')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ATTACHMENT --}}
                <div>
                    <label class="text-sm font-medium">Attachment</label>
                    <input type="file" name="attachments[]"
                        multiple
                        class="w-full mt-1 px-4 py-2 rounded-xl border bg-white">
                    @error('attachments')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ================= FREELANCER ================= --}}
                <div class="mt-6 space-y-4">
                    <h2 class="font-semibold">Assign Freelancer</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        @foreach($freelancers as $freelancer)
                        <label class="bg-white p-4 rounded-xl shadow cursor-pointer">

                            <input type="radio" name="user_id"
                                value="{{ $freelancer->user_id }}"
                                class="hidden peer">

                            <div class="space-y-3 peer-checked:border-2 peer-checked:border-blue-500 rounded-xl">

                                <div class="h-32 bg-gray-300 rounded-lg"></div>

                                <div>
                                    <h3 class="font-semibold">{{ $freelancer->name }}</h3>
                                    <p class="text-sm text-gray-500">
                                        {{ $freelancer->skills->pluck('skill_name')->join(', ') }}
                                    </p>
                                </div>

                                <div class="flex justify-between text-sm">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">
                                        Availability
                                    </span>
                                    <span>{{ $freelancer->projects_count ?? 0 }}/5</span>
                                </div>

                            </div>

                        </label>
                        @endforeach

                    </div>
                </div>

                {{-- SAVE --}}
                <div class="flex justify-end mt-6">
                    <button class="bg-[#1E3A8A] text-white px-6 py-2 rounded-full">
                        Save
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

@endsection