@extends('layouts.app')

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

@section('content')
    <div class="p-6 space-y-6">
        {{-- Header --}}
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-white/20">
            <div class="flex items-center gap-4">
                <a href="{{ route('skills.index') }}" class="p-2 hover:bg-gray-100 rounded-full transition">
                    <img src="{{ asset('assets/back.svg') }}" alt="Back" class="w-6 h-6">
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Edit Skill</h1>
                    <p class="text-sm text-gray-500">Updating profile for: {{ $skill->skill_name }}</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form id="skillForm" action="{{ route('skills.update', $skill->skill_id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6">
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">Skill Name <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            name="skill_name" 
                            required 
                            value="{{ old('skill_name', $skill->skill_name) }}"
                            class="w-full px-4 py-2 rounded-lg border 
                                @error('skill_name') border-red-500 
                                @else border-gray-200 
                                @enderror focus:ring-2 focus:ring-blue-500 outline-none transition"
                        >
                        @error('skill_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">Skill Description <span class="text-red-500">*</span></label>
                        <textarea 
                            name="skill_description" 
                            required 
                            class="w-full px-4 py-2 rounded-lg border
                                @error('skill_description') border-red-500 
                                @else border-gray-200 
                                @enderror focus:ring-2 focus:ring-blue-500 outline-none transition"
                            rows="4"
                        >{{ old('skill_description', $skill->skill_description) }}</textarea>
                        @error('skill_description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-6 border-t border-gray-50 flex gap-3">
                        <button type="submit" class="px-8 py-3 bg-blue-900 text-white font-bold rounded-xl hover:bg-blue-800 transition shadow-lg cursor-pointer">
                            Update Changes
                        </button>
                        <a href="{{ route('skills.index') }}" class="px-8 py-3 bg-gray-100 text-gray-600 font-semibold rounded-xl hover:bg-gray-200 transition text-center">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .swal2-container { z-index: 9999 !important; }
        .ts-dropdown { z-index: 9000 !important; border-radius: 0.75rem !important; margin-top: 4px !important; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important; }
        .ts-control { border-radius: 0.5rem !important; padding: 0.6rem 0.5rem !important; border-color: #e5e7eb !important; transition: all 0.2s; }
        .ts-wrapper.focus .ts-control { border-color: #3b82f6 !important; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5) !important; }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: 'Please correct the highlighted errors.',
                    confirmButtonColor: '#1e3a8a'
                });
            @endif
            
            const form = document.getElementById('skillForm');
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                Swal.fire({
                    title: 'Save Changes?',
                    text: "Are you sure you want to update this skill's information?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1e3a8a',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Yes, Update!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection