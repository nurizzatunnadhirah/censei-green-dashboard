<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('location_name')
            ->paginate(10);

        return view('locations.index', compact('locations'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'location_name' => 'required|string|max:255|unique:locations,location_name',
        ]);

        Location::create([
            'location_name' => $request->location_name,
        ]);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Location / Program added successfully.');
    }


    public function update(Request $request, Location $location)
    {
        $request->validate([
            'location_name' =>
                'required|string|max:255|unique:locations,location_name,' . $location->id,
        ]);

        $location->update([
            'location_name' => $request->location_name,
        ]);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Location / Program updated successfully.');
    }


    public function destroy(Location $location)
    {
        if ($location->wasteRecords()->exists()) {

            return redirect()
                ->route('locations.index')
                ->with(
                    'error',
                    'This location cannot be deleted because it is already used by waste records.'
                );
        }

        $location->delete();

        return redirect()
            ->route('locations.index')
            ->with('success', 'Location / Program deleted successfully.');
    }
}