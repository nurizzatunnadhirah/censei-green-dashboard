<?php

namespace App\Http\Controllers;

use App\Models\WasteCategory;
use App\Models\WasteType;
use Illuminate\Http\Request;

class WasteTypeController extends Controller
{
    public function index()
    {
        $wasteTypes = WasteType::with('category')
            ->orderBy('type_name')
            ->paginate(10);

        $categories = WasteCategory::orderBy('category_name')
            ->get();

        return view('waste-types.index', compact(
            'wasteTypes',
            'categories'
        ));
    }


    public function store(Request $request)
    {
        $request->validate([
            'type_name' => 'required|string|max:255|unique:waste_types,type_name',
            'category_id' => 'required|exists:waste_categories,id',
            'description' => 'nullable|string|max:1000',
        ]);


        WasteType::create([
            'type_name' => $request->type_name,
            'category_id' => $request->category_id,
            'description' => $request->description,
        ]);


        return redirect()
            ->route('waste-types.index')
            ->with(
                'success',
                'Waste type added successfully.'
            );
    }


    public function update(
        Request $request,
        WasteType $wasteType
    )
    {
        $request->validate([
            'type_name' =>
                'required|string|max:255|unique:waste_types,type_name,' .
                $wasteType->id,

            'category_id' =>
                'required|exists:waste_categories,id',

            'description' =>
                'nullable|string|max:1000',
        ]);


        $wasteType->update([
            'type_name' => $request->type_name,
            'category_id' => $request->category_id,
            'description' => $request->description,
        ]);


        return redirect()
            ->route('waste-types.index')
            ->with(
                'success',
                'Waste type updated successfully.'
            );
    }


    public function destroy(WasteType $wasteType)
    {
        if ($wasteType->wasteRecords()->exists()) {

            return redirect()
                ->route('waste-types.index')
                ->with(
                    'error',
                    'This waste type cannot be deleted because it is already used by waste records.'
                );
        }


        $wasteType->delete();


        return redirect()
            ->route('waste-types.index')
            ->with(
                'success',
                'Waste type deleted successfully.'
            );
    }
}