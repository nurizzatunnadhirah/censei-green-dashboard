<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\WasteRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $locations = Location::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('location_name', 'like', '%' . $search . '%');
            })
            ->orderBy('location_name')
            ->paginate(10)
            ->withQueryString();

        return view('locations.index', compact('locations', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_name' => ['required', 'string', 'max:255', Rule::unique('locations', 'location_name')],
        ]);

        Location::create(['location_name' => trim($validated['location_name'])]);

        return redirect()->route('locations.index')
            ->with('success', 'Location / Program added successfully.');
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'location_name' => ['required', 'string', 'max:255', Rule::unique('locations', 'location_name')->ignore($location->id)],
        ]);

        $location->update(['location_name' => trim($validated['location_name'])]);

        return redirect()->route('locations.index', $request->only('search', 'page'))
            ->with('success', 'Location / Program updated successfully.');
    }

    public function destroy(Request $request, Location $location)
    {
        // Protect against deleting a location referenced by existing waste records.
        // A database foreign key should also restrict deletion for additional safety.
        if (WasteRecord::where('location_id', $location->id)->exists()) {
            return redirect()->route('locations.index', $request->only('search', 'page'))
                ->with('error', 'This location cannot be deleted because it is used by waste records.');
        }

        try {
            $location->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('locations.index', $request->only('search', 'page'))
                ->with('error', 'This location cannot be deleted because it is referenced by existing data.');
        }

        return redirect()->route('locations.index', $request->only('search', 'page'))
            ->with('success', 'Location / Program deleted successfully.');
    }
}
