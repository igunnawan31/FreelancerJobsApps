@extends('layouts.app')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')
    <div class="p-8 space-y-6">
        {{-- Header Bar --}}
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-black">Management Skills</h1>

            <a href="{{ route('skills.create') }}"
            class="bg-blue-900 text-white px-4 py-2 rounded-full text-sm hover:opacity-90">
                + New Skills
            </a>
        </div>
        
        {{-- Search Bar --}}
        <form action="{{ route('skills.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Skills Name</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by skill name..."
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-white focus:ring-2 focus:ring-blue-500 outline-none transition">
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-900 text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition">
                    Filter
                </button>
                
                @if(request()->has('search') || request()->has('role'))
                    <a href="{{ route('skills.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
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
                        <th class="px-4 py-4 text-black text-left">Nama Skill</th>
                        <th class="px-4 py-4 text-black text-left">Description</th>
                        <th class="px-4 py-4 text-black text-left">Created At</th>
                        <th class="px-4 py-4 text-black text-left">Updated At</th>
                        <th class="px-4 py-4 text-black">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($skills as $skill)
                        <tr class="border-b border-gray-100 bg-white hover:bg-gray-50 transition">
                            <td class="px-4 py-4 text-black">{{ $loop->iteration }}</td>
                            <td class="px-4 py-4 text-black text-left">
                                <div class="flex items-center">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold">{{ $skill->skill_name }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-black text-left">
                                <div class="flex items-center">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold">{{ $skill->skill_description }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-black text-left">
                                <div class="flex items-center">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold">{{ $skill->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-black text-left">
                                <div class="flex items-center">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold">{{ $skill->updated_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('skills.show', $skill->skill_id) }}" class="text-blue-600 hover:text-blue-900 px-2 py-1 bg-blue-100 rounded-lg">View</a>
                                    <a href="{{ route('skills.edit', $skill->skill_id) }}" class="text-yellow-600 hover:text-yellow-900 px-2 py-1 bg-yellow-100 rounded-lg">Edit</a>
                                    <form action="{{ route('skills.destroy', $skill->skill_id) }}" method="POST" onsubmit="return confirm('Delete user?')">
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
            {{ $skills->links() }}
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