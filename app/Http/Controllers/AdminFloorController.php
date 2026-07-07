<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;

class AdminFloorController extends Controller
{
    public function index()
    {
        $floors = Floor::ordered()->get();
        return view('admin.floors.index', compact('floors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        Floor::create($validated);

        return redirect()->route('admin.floors.index')
            ->with('success', 'Floor created successfully.');
    }

    public function update(Request $request, Floor $floor)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $floor->update($validated);

        return redirect()->route('admin.floors.index')
            ->with('success', 'Floor updated successfully.');
    }

    public function destroy(Floor $floor)
    {
        // Set rooms under this floor to null
        $floor->rooms()->update(['floor_id' => null]);
        $floor->delete();

        return redirect()->route('admin.floors.index')
            ->with('success', 'Floor deleted successfully.');
    }
}
