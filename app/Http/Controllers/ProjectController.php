<?php

namespace App\Http\Controllers;

use App\Enums\ProjectEnums\ProjectStatus;
use App\Enums\UserEnums\UserRole;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\UploadAttachmentRequest;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Project::class);
        $user = auth()->user();
        $query = Project::with(['user', 'client', 'skills', 'projectlogs.actor']);

        // Role-based data yang ditampilkan
        if ($user->role === UserRole::ADMIN) {} 
        elseif ($user->role === UserRole::FREELANCER) {
            $query->where(function ($q) use ($user) {
                $q->where('project_status', ProjectStatus::STATUS_OPEN)

                ->orWhere(function ($q2) use ($user) {
                    $q2->where('project_status', ProjectStatus::STATUS_REQUESTED_BY_ADMIN)
                        ->where('user_id', $user->user_id);
                })

                ->orWhere(function ($q3) use ($user) {
                    $q3->where('project_status', ProjectStatus::STATUS_REQUESTED_BY_FREELANCER)
                        ->where('user_id', $user->user_id);
                })

                ->orWhere(function ($q4) use ($user) {
                    $q4->whereIn('project_status', [
                        ProjectStatus::STATUS_RUNNING,
                        ProjectStatus::STATUS_REVISION,
                        ProjectStatus::STATUS_COMPLETED,
                        ProjectStatus::STATUS_DONE,
                    ])
                    ->where('user_id', $user->user_id);
                });
            });
        } elseif ($user->role === UserRole::CLIENT) {
            $query->where('client_id', $user->user_id);
        }

        // Role-based data log yang ditampilkan
        if ($user->role)

        // Filtering berdasarkan query parameter
        // Filter Search
        if ($request->filled('search')) {
            $query->where('project_name', 'like', '%' . $request->search . '%');
        }
        // Filter Status
        if ($request->filled('status')) {
            $query->where('project_status', $request->status);
        }
        // Filter Deadline
        if ($request->filled('deadline')) {
            $query->whereDate('project_deadline', $request->deadline);
        }
        // Filter Skill
        if ($request->filled('skill')) {
            $skill = $request->skill;

            $query->whereHas('skills', function ($q) use ($skill) {
                $q->where('skill_name', 'like', "%$skill%");
            });
        }
        // Sorting berdasarkan created at
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $query->orderBy($sortBy, $sortOrder);
        $projects = $query->paginate(10)->appends($request->query());

        $this->authorize('viewAny', Project::class);
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Project::class);

        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $this->authorize('create', Project::class);
        $validated = $request->validated();

        if (!empty($validated['user_id'])) {
            $validated['project_status'] = ProjectStatus::STATUS_REQUESTED_BY_ADMIN;
            $action = 'created and requested by admin';
        } else {
            $validated['project_status'] = ProjectStatus::STATUS_OPEN;
            $action = 'created new project by admin';
        }

        $project = Project::create($validated);

        $project->projectlogs()->create([
            'actor_id' => auth()->id(),
            'action' => $action,
        ]);

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['user', 'client', 'skills', 'projectlogs.actor']);

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $project->update($request->validated());

        $project->projectlogs()->create([
            'actor_id' => auth()->id(),
            'action' => 'updated',
        ]);

        return redirect()->route('projects.show', $project->project_id)
            ->with('success', 'Project updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        // 📝 LOG before delete (optional)
        $project->projectlogs()->create([
            'actor_id' => auth()->id(),
            'action' => 'deleted',
        ]);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully');
    }

    public function request(Project $project)
    {
        $this->authorize('request', $project);
        $user = auth()->user();

        if ($user->role === UserRole::FREELANCER) {
            $project->update([
                'project_status' => ProjectStatus::STATUS_REQUESTED_BY_FREELANCER,
                'user_id' => $user->user_id
            ]);
            $action = 'requested_by_freelancer';
        } 
        $project->projectlogs()->create([
            'actor_id' => $user->user_id,
            'action' => $action,
        ]);

        return back()->with('success', 'Project requested successfully');
    }

    public function assign(Request $request, Project $project)
    {
        $this->authorize('assign', $project);
        $user = auth()->user();

        $request->validate([
            'user_id' => 'required|exists:users,user_id',
        ]);

        if ($user->role === UserRole::ADMIN) {
            $project->update([
                'project_status' => ProjectStatus::STATUS_REQUESTED_BY_ADMIN,
                'user_id' => $request->user_id,
            ]);
            $action = 'requested_by_admin';
        }
        $project->projectlogs()->create([
            'actor_id' => $user->user_id,
            'action' => $action,
        ]);

        return back()->with('success', 'Project assigned successfully');
    }

    public function accept(Project $project)
    {
        $this->authorize('accept', $project);

        $project->update([
            'project_status' => ProjectStatus::STATUS_RUNNING
        ]);
        $project->projectlogs()->create([
            'actor_id' => auth()->id(),
            'action' => 'accepted',
        ]);

        return back()->with('success', 'Project accepted successfully');
    }

    public function reject(Project $project, Request $request)
    {
        $this->authorize('reject', $project);
        $user = auth()->user();

        $request->validate([
            'comment' => 'required|string|max:255',
        ]);
        $project->update([
            'project_status' => ProjectStatus::STATUS_OPEN,
            'user_id' => null
        ]);
        $project->projectlogs()->create([
            'actor_id' => $user->user_id,
            'action' => $user->role === UserRole::ADMIN
                ? 'rejected_by_admin'
                : 'rejected_by_freelancer',
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Project rejected');
    }

    public function submit(UploadAttachmentRequest $request, Project $project)
    {
        $this->authorize('submit', $project);

        $request->validate([
            'attachments'   => 'required|array|min:1',
            'attachments.*' => 'file|max:10240',
        ]);

        $project->update([
            'project_status' => ProjectStatus::STATUS_COMPLETED,
        ]);

        $log = $project->projectlogs()->create([
            'actor_id' => auth()->id(),
            'action'   => 'submitted project',
        ]);

        foreach ($request->file('attachments') as $file) {
            $path = $file->store("projects/{$project->project_id}/submissions", 'local');

            $project->attachments()->create([
                'project_log_id' => $log->id,
                'file_name'      => $file->getClientOriginalName(),
                'file_path'      => $path,
                'file_type'      => $file->getMimeType(),
                'file_size'      => $file->getSize(),
                'uploaded_by'    => auth()->id(),
            ]);
        }

        return back()->with('success', 'Work submitted successfully');
    }

    public function approve(Project $project)
    {
        $this->authorize('approve', $project);

        $project->update([
            'project_status' => ProjectStatus::STATUS_DONE,
        ]);

        $project->projectlogs()->create([
            'actor_id' => auth()->id(),
            'action' => 'approved',
        ]);

        return back()->with('success', 'Project approved');
    }

    public function revise(Request $request, Project $project)
    {
        $this->authorize('revise', $project);

        $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        $revisionNumber = $project->projectlogs()
            ->where('action', 'revision_requested')
            ->count() + 1;

        $project->update([
            'project_status' => ProjectStatus::STATUS_REVISION,
        ]);

        $project->projectlogs()->create([
            'actor_id'        => auth()->id(),
            'action'          => 'revision_requested',
            'comment'         => $request->comment,
            'revision_number' => $revisionNumber,
        ]);

        return back()->with('success', 'Revision requested');
    }

    public function resubmit(UploadAttachmentRequest $request, Project $project)
    {
        $this->authorize('resubmit', $project);

        $request->validate([
            'attachments'   => 'required|array|min:1',
            'attachments.*' => 'file|max:10240',
            'comment'       => 'nullable|string|max:255',
        ]);

        $revisionNumber = $project->projectlogs()
            ->where('action', 'revision_requested')
            ->count();

        $project->update([
            'project_status' => ProjectStatus::STATUS_COMPLETED,
        ]);

        $log = $project->projectlogs()->create([
            'actor_id'        => auth()->id(),
            'action'          => 'revision_submitted',
            'comment'         => $request->comment,
            'revision_number' => $revisionNumber,
        ]);

        foreach ($request->file('attachments') as $file) {
            $path = $file->store("projects/{$project->project_id}/revisions/{$revisionNumber}", 'local');

            $project->attachments()->create([
                'project_log_id' => $log->id,
                'file_name'      => $file->getClientOriginalName(),
                'file_path'      => $path,
                'file_type'      => $file->getMimeType(),
                'file_size'      => $file->getSize(),
                'uploaded_by'    => auth()->id(),
            ]);
        }

        return back()->with('success', 'Revision submitted successfully');
    }
}
