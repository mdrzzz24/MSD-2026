<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class AdminGroupController extends Controller
{
    public function index()
    {
        $groups = Group::withCount('users')->latest()->get();
        return view('admin.groups.index', compact('groups'));
    }

    public function create()
    {
        $permissionList = User::allPermissions();
        return view('admin.groups.form', compact('permissionList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:groups,name'],
            'permissions' => ['nullable', 'array'],
        ]);

        Group::create([
            'name'        => $request->name,
            'permissions' => collect($request->permissions ?? [])->map(fn() => true)->all(),
        ]);

        return redirect()->route('admin.management.groups.index')
            ->with('success', 'Group "' . $request->name . '" created.');
    }

    public function edit(Group $group)
    {
        $permissionList = User::allPermissions();
        return view('admin.groups.form', compact('group', 'permissionList'));
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:groups,name,' . $group->id],
            'permissions' => ['nullable', 'array'],
        ]);

        $group->update([
            'name'        => $request->name,
            'permissions' => collect($request->permissions ?? [])->map(fn() => true)->all(),
        ]);

        return redirect()->route('admin.management.groups.index')
            ->with('success', 'Group "' . $group->name . '" updated.');
    }

    public function destroy(Group $group)
    {
        // Unassign all users in this group
        User::where('group_id', $group->id)->update(['group_id' => null]);
        $group->delete();

        return redirect()->route('admin.management.groups.index')
            ->with('success', 'Group deleted. Users were unassigned.');
    }
}
