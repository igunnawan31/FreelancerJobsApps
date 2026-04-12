<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Skill::class, 'skill');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $skills = Skill::latest()->paginate(10);
        return response()->json($skills);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSkillRequest $request)
    {
        $skill = Skill::create($request->validated());

        return response()->json([
            'message' => 'Skill created successfully',
            'skill' => $skill
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Skill $skill)
    {
        return response()->json($skill);   
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Skill $skill)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSkillRequest $request, Skill $skill)
    {
        $skill->update($request->validated());

        return response()->json([
            'message' => 'Skill updated successfully',
            'skill' => $skill
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill)
    {
        $skill = Skill::find($skill->skill_id);

        if (!$skill) {
            return response()->json([
                'message' => 'Skill not found'
            ], 404);
        }

        $skill->delete();

        return response()->json([
            'message' => 'Skill deleted successfully'  
        ], 200);
    }
}
