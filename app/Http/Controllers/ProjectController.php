<?php

namespace App\Http\Controllers;

use App\Enums\ProjectEnums\ProjectStatus;
use App\Enums\RatingEnums\RatingType;
use App\Enums\UserEnums\UserRole;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\UploadAttachmentRequest;
use App\Models\Project;
use App\Models\Rating;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

        $skills = Skill::orderBy('skill_name')->get(['skill_id', 'skill_name']);
        $clients = User::where('role', UserRole::CLIENT)
            ->get(['user_id', 'name', 'email']);

        return view('projects.create', compact('skills', 'clients'));
    }

    public function availableFreelancers(Request $request)
    {
        $this->authorize('create', Project::class);

        $skillIds = $request->input('skill_ids', []);

        $query = User::where('role', UserRole::FREELANCER)
            ->withCount([
                'projects as active_projects_count' => function ($q) {
                    $q->whereIn('project_status', [
                        ProjectStatus::STATUS_REQUESTED_BY_FREELANCER,
                        ProjectStatus::STATUS_REQUESTED_BY_ADMIN,
                        ProjectStatus::STATUS_RUNNING,
                        ProjectStatus::STATUS_REVISION,
                        ProjectStatus::STATUS_COMPLETED,
                    ]);
                }
            ])
            ->having('active_projects_count', '<', 3);

        if (!empty($skillIds)) {
            $query->whereHas('skills', function ($q) use ($skillIds) {
                $q->whereIn('skill_id', $skillIds);
            });
        }

        $freelancers = $query->get(['user_id', 'name', 'email']);

        return response()->json($freelancers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $this->authorize('create', Project::class);
        $validated = $request->validated();

        $attachments = $request->file('attachments', []);
        $skillIds = $validated['skill_ids'] ?? [];
        unset($validated['attachments'], $validated['skill_ids']);

        if (!empty($validated['user_id'])) {
            $validated['project_status'] = ProjectStatus::STATUS_REQUESTED_BY_ADMIN;
            $action = 'created_and_requested_by_admin';
        } else {
            $validated['project_status'] = ProjectStatus::STATUS_OPEN;
            $action = 'created_by_admin';
        }

        $project = Project::create($validated);
        $project->skills()->sync($skillIds);

        $log = $project->projectlogs()->create([
            'actor_id' => auth()->id(),
            'action'   => $action,
        ]);

        foreach ($attachments as $file) {
            $path = $file->store("projects/{$project->project_id}/references", 'local');

            $project->attachments()->create([
                'project_log_id'   => $log->id,
                'file_name'        => $file->getClientOriginalName(),
                'file_path'        => $path,
                'file_type'        => $file->getMimeType(),
                'file_size'        => $file->getSize(),
                'uploaded_by'      => auth()->id(),
            ]);
        }

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
        $this->authorize('update', Project::class);

        $skills = Skill::orderBy('skill_name')->get(['skill_id', 'skill_name']);
        $clients = User::where('role', UserRole::CLIENT)
            ->get(['user_id', 'name', 'email']);

        return view('projects.update', compact('skills', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated   = $request->validated();
        $attachments = $request->file('attachments', []);
        $skillIds    = $validated['skill_ids'] ?? [];
        unset($validated['attachments'], $validated['skill_ids']);

        $project->update($validated);
        $project->skills()->sync($skillIds);

        $log = $project->projectlogs()->create([
            'actor_id' => auth()->id(),
            'action'   => 'updated',
        ]);

        foreach ($attachments as $file) {
            $path = $file->store("projects/{$project->project_id}/updates", 'local');

            $project->attachments()->create([
                'project_log_id'   => $log->id,
                'file_name'        => $file->getClientOriginalName(),
                'file_path'        => $path,
                'file_type'        => $file->getMimeType(),
                'file_size'        => $file->getSize(),
                'uploaded_by'      => auth()->id(),
                'uploaded_by_role' => UserRole::ADMIN,
            ]);
        }

        return redirect()->route('projects.show', $project->project_id)
            ->with('success', 'Project updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

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

        $project->update([
            'project_status' => ProjectStatus::STATUS_REQUESTED_BY_FREELANCER,
            'user_id' => auth()->id(),
        ]);

        $project->projectlogs()->create([
            'actor_id' => auth()->id(),
            'action' => 'requested_by_freelancer',
        ]);

        return back()->with('success', 'Project requested successfully');
    }

    public function assign(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
        ]);

        $freelancer = User::findOrFail($request->user_id);

        if ($freelancer->hasActiveProject()) {
            return back()->withErrors([
                'user_id' => 'This freelancer already has 3 active projects.'
            ]);
        }

        $project->update([
            'project_status' => ProjectStatus::STATUS_REQUESTED_BY_ADMIN,
            'user_id' => $request->user_id,
        ]);

        $project->projectlogs()->create([
            'actor_id' => auth()->id(),
            'action' => 'requested_by_admin',
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

    public function approve(StorePaymentRequest $request, Project $project)
    {
        $this->authorize('approve', $project);

        $validated = $request->validated();

        $payment_attachments = $request->file('payment_attachments', []);

        $log = $project->projectlogs()->create([
            'actor_id' => auth()->id(),
            'action'   => 'payment and project done',
        ]);

        foreach ($payment_attachments as $file) {
            $path = $file->store("projects/{$project->project_id}/payments", 'local');

            $project->payments()->create([
                'project_id'         => $project->project_id,
                'project_log_id'     => $log->id,
                'payment_method'     => $request->payment_method,
                'payment_attachments' => $path,
                'file_name'          => $file->getClientOriginalName(),
                'file_path'          => $path,
                'file_type'          => $file->getMimeType(),
                'file_size'          => $file->getSize(),
                'uploaded_by'        => auth()->id(),
                'note'               => $request->note,
            ]);
        }

        $project->update([
            'project_status' => ProjectStatus::STATUS_DONE,
        ]);

        return back()->with('success', 'Project approved and payment recorded');
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

    public function ratings(Request $request, Project $project)
    {
        $this->authorize('ratings', $project);

        $types = collect(RatingType::cases())->pluck('value');

        foreach ($types as $type) {
            if (!$request->has("ratings.$type")) {
                throw ValidationException::withMessages([
                    "ratings.$type" => "Rating for {$type} is required"
                ]);
            }
        }

        $request->validate([
            'ratings' => 'required|array',
            'ratings.*' => 'required|integer|min:1|max:5',
        ]);

        foreach ($request->ratings as $type => $value) {
            Rating::updateOrCreate(
                [
                    'project_id'  => $project->project_id,
                    'user_id'     => $project->user_id,
                    'rating_type' => $type,
                ],
                [
                    'rating_value' => $value,
                    'penilai_id'   => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Ratings submitted successfully');
    }

    public function logs(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['projectlogs.actor']);

        return view('projects.logs', compact('project'));
    }

    public function attachments(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['attachments.uploader']);

        return view('projects.attachments', compact('project'));
    }
}
