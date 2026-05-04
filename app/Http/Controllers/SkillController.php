<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Skill::class);

        $query = Skill::query();

        if ($request->filled('search')) {
            $query->where('skill_name', 'like', '%' . $request->search . '%');
        }

        $sortBy = $request->get('sort_by', 'skill_name');
        $sortOrder = $request->get('sort_order', 'asc');

        $query->orderBy($sortBy, $sortOrder);
        $skills = $query->paginate(10)->appends($request->query());
        
        return view('skills.index', compact('skills'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Skill::class);

        return view('skills.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSkillRequest $request)
    {
        $this->authorize('create', Skill::class);

        $skill = Skill::create($request->validated());

        return redirect()->route('skills.index')
            ->with('success', 'Skill created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Skill $skill)
    {
        $this->authorize('view', $skill);

        $skill->load(['users', 'projects']);

        return view('skills.show', compact('skill'));   
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Skill $skill)
    {
        $this->authorize('update', $skill);

        return view('skills.edit', compact('skill'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSkillRequest $request, Skill $skill)
    {
        $this->authorize('update', $skill);

        $skill->update($request->validated());

        return redirect()->route('skills.index')
            ->with('success', 'Skill updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill)
    {
        $this->authorize('delete', $skill);

        $skill->delete();

        return redirect()->route('skills.index')
            ->with('success', 'Skill deleted successfully');
    }
}
