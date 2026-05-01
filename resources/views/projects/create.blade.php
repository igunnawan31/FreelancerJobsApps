@extends('layouts.app')

@section('content')
<div class="p-8 space-y-6">

    <h1 class="text-2xl font-bold text-white">New Project</h1>

    <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Project Name --}}
        <div>
            <label class="block text-sm font-medium text-white">Project Name</label>
            <input type="text" name="project_name"
                class="w-full mt-1 p-2 rounded border"
                required>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-white">Description</label>
            <textarea name="project_description"
                class="w-full mt-1 p-2 rounded border"
                rows="4"></textarea>
        </div>

        {{-- Client --}}
        <div>
            <label class="block text-sm font-medium text-white">Client</label>
            <select name="client_id" class="w-full mt-1 p-2 rounded border">
                <option value="">-- Select Client --</option>
                @foreach($clients as $client)
                    <option value="{{ $client->user_id }}">
                        {{ $client->name }} ({{ $client->email }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Deadline --}}
        <div>
            <label class="block text-sm font-medium text-white">Deadline</label>
            <input type="date" name="project_deadline"
                class="w-full mt-1 p-2 rounded border">
        </div>

        {{-- Skills --}}
        <div>
            <label class="block text-sm font-medium text-white mb-2">Skills (Type)</label>
            <div class="grid grid-cols-2 gap-2">
                @foreach($skills as $skill)
                    <label class="flex items-center gap-2 bg-white p-2 rounded shadow">?
                        <input type="checkbox" name="skill_ids[]" value="{{ $skill->skill_id }}">
                        <span>{{ $skill->skill_name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Attachment --}}
        <div>
            <label class="block text-sm font-medium text-white">Attachments</label>
            <input type="file" name="attachments[]" multiple
                class="w-full mt-1 p-2 bg-white rounded">
        </div>

        {{-- Hidden freelancer --}}
        <input type="hidden" name="user_id" id="user_id">

        {{-- Selected freelancer --}}
        <div id="selected-freelancer" class="text-white text-sm"></div>

        {{-- ================= AVAILABLE FREELANCER ================= --}}
        <div class="space-y-4">
            <h2 class="font-semibold text-white text-lg">Available Freelancer</h2>

            <div id="freelancer-container" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- isi dari JS --}}
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Create Project
        </button>

    </form>

</div>

{{-- ================= JS ================= --}}
<script>
    const skillCheckboxes = document.querySelectorAll('input[name="skill_ids[]"]');
    const container = document.getElementById('freelancer-container');

    function loadFreelancers() {
        let selectedSkills = [];

        skillCheckboxes.forEach(cb => {
            if (cb.checked) {
                selectedSkills.push(cb.value);
            }
        });

        let url = `/freelancers/available`;
        if (selectedSkills.length > 0) {
            url += `?skill_ids[]=${selectedSkills.join('&skill_ids[]=')}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                container.innerHTML = '';

                if (data.length === 0) {
                    container.innerHTML = `<p class="text-gray-400">No freelancers available</p>`;
                    return;
                }

                data.forEach(f => {
                    container.innerHTML += `
                        <div class="bg-white p-4 rounded-xl shadow space-y-3">

                            <div class="h-32 bg-gray-300 rounded-lg flex items-center justify-center text-gray-500">
                                No Image
                            </div>

                            <div>
                                <h3 class="font-semibold">${f.name}</h3>
                                <p class="text-sm text-gray-500">${f.email}</p>
                            </div>

                            <div class="flex justify-between items-center text-sm">
                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">
                                    Available
                                </span>
                                <span class="text-xs text-gray-500">
                                    ${f.active_projects_count}/3
                                </span>
                            </div>

                            <button 
                                type="button"
                                onclick="selectFreelancer(${f.user_id}, '${f.name}')"
                                class="text-blue-600 text-sm">
                                Assign Freelancer →
                            </button>

                        </div>
                    `;
                });
            });
    }

    function selectFreelancer(id, name) {
        document.getElementById('user_id').value = id;

        document.getElementById('selected-freelancer').innerHTML = 
            `Selected: <span class="font-semibold">${name}</span>`;
    }

    skillCheckboxes.forEach(cb => {
        cb.addEventListener('change', loadFreelancers);
    });

    loadFreelancers();
</script>

@endsection