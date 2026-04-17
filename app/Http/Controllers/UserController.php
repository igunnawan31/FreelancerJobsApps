<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('skill')) {
            $skill = $request->skill;

            $query->whereHas('skills', function ($q) use ($skill) {
                $q->where('skill_name', 'like', "%$skill%");
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $query->orderBy($sortBy, $sortOrder);
        $users = $query->paginate(10)->appends($request->query());

        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load(['skills', 'projects', 'ratings']);

        return view('users.show', compact('user'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $skillIds = $validated['skill_ids'] ?? [];
        unset($validated['skill_ids']);

        $user = User::create($validated);
        $user->skills()->sync($skillIds);

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');

            $path = $file->store('profile_pictures', 'public');

            $user->update([
                'profile_picture' => $path,
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {

    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $validated   = $request->validated();
        $skillIds    = $validated['skill_ids'] ?? [];
        unset($validated['skill_ids']);

        $oldPicture = $user->profile_picture;

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        unset($validated['profile_picture']);

        $user->update($validated);
        $user->skills()->sync($skillIds);

        if ($request->hasFile('profile_picture')) {
            if ($oldPicture) {
                Storage::disk('public')->delete($oldPicture);
            }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->update(['profile_picture' => $path]);
        }

        return redirect()->route('users.show', $user->user_id);
    }

    public function updatePassword(UpdatePasswordRequest $request, User $user)
    {
        $this->authorize('changePassword', $user);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}
