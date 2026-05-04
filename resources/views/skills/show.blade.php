@extends('layouts.app')

@section('content')
    <div class="p-6 space-y-6">
        {{-- Header --}}
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-white/20">
            <div class="flex items-center gap-4">
                <a href="{{ route('skills.index') }}" class="p-2 hover:bg-gray-100 rounded-full transition">
                    <img src="{{ asset('assets/back.svg') }}" alt="Back" class="w-6 h-6">
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Edit skill</h1>
                    <p class="text-sm text-gray-500">Updating skill for: {{ $skill->skill_name }}</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form id="skillForm" action="{{ route('skills.update', $skill->skill_id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">Skill Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" disabled value="{{ old('name', $skill->skill_name) }}"
                            class="w-full px-4 py-2 rounded-lg border @error('name') border-red-500 @else border-gray-200 @enderror focus:ring-2 focus:ring-blue-500 outline-none transition">
                        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">Skill Description <span class="text-red-500">*</span></label>
                        <input type="text" name="description" disabled value="{{ old('skill_description', $skill->skill_description) }}"
                            class="w-full px-4 py-2 rounded-lg border @error('skill_description') border-red-500 @else border-gray-200 @enderror focus:ring-2 focus:ring-blue-500 outline-none transition">
                        @error('skill_description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">Created At <span class="text-red-500">*</span></label>
                        <input type="text" name="description" disabled value="{{ old('created_at', $skill->created_at) }}"
                            class="w-full px-4 py-2 rounded-lg border @error('created_at') border-red-500 @else border-gray-200 @enderror focus:ring-2 focus:ring-blue-500 outline-none transition">
                        @error('created_at') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">Updated At <span class="text-red-500">*</span></label>
                        <input type="text" name="description" disabled value="{{ old('updated_at', $skill->updated_at) }}"
                            class="w-full px-4 py-2 rounded-lg border @error('updated_at') border-red-500 @else border-gray-200 @enderror focus:ring-2 focus:ring-blue-500 outline-none transition">
                        @error('updated_at') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </form>
        </div>
    </div
@endsection