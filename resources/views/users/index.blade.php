@extends('layouts.app')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')
    <div class="p-8 space-y-6">
        {{-- Header Bar --}}
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-black">Management Users</h1>

            <a href="{{ route('users.create') }}"
            class="bg-blue-900 text-white px-4 py-2 rounded-full text-sm hover:opacity-90">
                + New Users
            </a>
        </div>
        
        {{-- Search Bar --}}
        <form action="{{ route('users.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Name</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by name..."
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">
            </div>

            {{-- Role Filter --}}
            <div class="w-full md:w-1/4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" id="role" 
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                    <option value="">All Roles</option>
                    @foreach(\App\Enums\UserEnums\UserRole::cases() as $role)
                        <option value="{{ $role->value }}" {{ request('role') == $role->value ? 'selected' : '' }}>
                            {{ ucfirst($role->value) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-900 text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition">
                    Filter
                </button>
                
                @if(request()->has('search') || request()->has('role'))
                    <a href="{{ route('users.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto rounded-lg">
            <table class="table-auto p-5 w-full text-center rounded-lg border-collapse">
                <thead>
                    <tr class="bg-white border-b-gray-100 border-b text-sm">
                        <th class="px-4 py-4 w-1/32 text-black">No</th>
                        <th class="px-4 py-4 text-black text-left">Nama User</th>
                        <th class="px-4 py-4 text-black">Position</th>
                        <th class="px-4 py-4 text-black">Skill User</th>
                        <th class="px-4 py-4 text-black">Status</th>
                        <th class="px-4 py-4 text-black">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-b border-gray-100 bg-white hover:bg-gray-50 transition">
                            <td class="px-4 py-4 text-black">{{ $loop->iteration }}</td>
                            <td class="px-4 py-4 text-black text-left">
                                <div class="flex items-center">
                                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" 
                                        class="w-10 h-10 rounded-full mr-3 object-cover border">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold">{{ $user->name }}</span>
                                        <span class="text-xs text-gray-400">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium 
                                    {{ $user->role->value === 'admin' ? 'bg-red-100 text-red-700' : 
                                    ($user->role->value === 'freelancer' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                    {{ ucfirst($user->role->value) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-center gap-1">
                                    @forelse($user->skills as $skill)
                                        <span class="bg-purple-100 text-purple-700 text-sm px-2 py-0.5 rounded border border-purple-200">
                                            {{ $skill->skill_name }}
                                        </span>
                                    @empty
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-4 flex flex-col gap-2">
                                @if ($user->role->value === 'client')
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700">
                                        {{ $user->countActiveClientProject() }} Running Project
                                    </span>
                                @elseif ($user->role->value === 'freelancer')
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700">
                                        {{ $user->countActiveProject() }} Running Project
                                    </span>
                                    @if($user->hasActiveProject())
                                        <span class="flex items-center justify-center text-orange-600 text-xs font-bold">
                                            <span class="w-2 h-2 bg-orange-500 rounded-full mr-2 animate-pulse"></span>
                                            BUSY
                                        </span>
                                    @else
                                        <span class="flex items-center justify-center text-green-600 text-xs font-bold">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                            AVAILABLE
                                        </span>
                                    @endif
                                @else
                                    
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('users.show', $user->user_id) }}" class="text-blue-600 hover:text-blue-900 px-2 py-1 bg-blue-100 rounded-lg">View</a>
                                    <a href="{{ route('users.edit', $user->user_id) }}" class="text-yellow-600 hover:text-yellow-900 px-2 py-1 bg-yellow-100 rounded-lg">Edit</a>
                                    <form action="{{ route('users.destroy', $user->user_id) }}" method="POST" onsubmit="return confirm('Delete user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 cursor-pointer px-2 py-1 bg-red-100 rounded-lg">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Done!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    position: 'top-end'
                });
            @endif
        });
    </script>
@endsection