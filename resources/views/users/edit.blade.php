@extends('layouts.app')

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

@section('content')
    <div class="p-6 space-y-6">
        {{-- Header --}}
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-white/20">
            <div class="flex items-center gap-4">
                <a href="{{ route('users.index') }}" class="p-2 hover:bg-gray-100 rounded-full transition">
                    <img src="{{ asset('assets/back.svg') }}" alt="Back" class="w-6 h-6">
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
                    <p class="text-sm text-gray-500">Updating profile for: **{{ $user->name }}**</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form id="userForm" action="{{ route('users.update', $user->user_id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name', $user->name) }}"
                            class="w-full px-4 py-2 rounded-lg border @error('name') border-red-500 @else border-gray-200 @enderror focus:ring-2 focus:ring-blue-500 outline-none transition">
                        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required value="{{ old('email', $user->email) }}"
                            class="w-full px-4 py-2 rounded-lg border @error('email') border-red-500 @else border-gray-200 @enderror focus:ring-2 focus:ring-blue-500 outline-none transition">
                        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">User Role <span class="text-red-500">*</span></label>
                        <select name="role" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                            @foreach(\App\Enums\UserEnums\UserRole::cases() as $role)
                                <option value="{{ $role->value }}" {{ old('role', $user->role->value) == $role->value ? 'selected' : '' }}>
                                    {{ ucfirst($role->value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">Phone Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" placeholder="0812..."
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition">
                        @error('phone_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Skills & Expertise</label>
                    <select id="skills-select" name="skill_ids[]" multiple class="w-full">
                        @foreach($skills as $skill)
                            <option value="{{ $skill->skill_id }}" 
                                {{ in_array($skill->skill_id, old('skill_ids', $userSkillIds)) ? 'selected' : '' }}>
                                {{ $skill->skill_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Portfolio Link</label>
                    <input type="url" name="portfolio" value="{{ old('portfolio', $user->portfolio) }}" placeholder="https://..."
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                <div class="space-y-3">
                    <label class="text-sm font-semibold text-gray-700">Profile Picture</label>
                    <div class="flex items-center gap-4">
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Current" class="w-16 h-16 rounded-full object-cover border-2 border-gray-100">
                        @else
                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-xs">No Image</div>
                        @endif
                        <div class="flex-1">
                            <input type="file" name="profile_picture" accept="image/*"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                            <p class="text-xs text-gray-400 mt-1">Leave empty to keep current picture.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-50 flex gap-3">
                    <button type="submit" class="px-8 py-3 bg-blue-900 text-white font-bold rounded-xl hover:bg-blue-800 transition shadow-lg cursor-pointer">
                        Update Changes
                    </button>
                    <a href="{{ route('users.index') }}" class="px-8 py-3 bg-gray-100 text-gray-600 font-semibold rounded-xl hover:bg-gray-200 transition text-center">
                        Cancel
                    </a>
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
            // TomSelect Init
            new TomSelect("#skills-select", {
                plugins: ['remove_button', 'checkbox_options', 'dropdown_input'],
                persist: false,
                create: false,
                render: {
                    item: (data, escape) => `<div class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-md text-sm border border-blue-200">${escape(data.text)}</div>`
                }
            });

            // Handle Errors
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: 'Please correct the highlighted errors.',
                    confirmButtonColor: '#1e3a8a'
                });
            @endif

            // Confirmation on Submit
            const form = document.getElementById('userForm');
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                Swal.fire({
                    title: 'Save Changes?',
                    text: "Are you sure you want to update this user's information?",
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