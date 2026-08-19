<?php

namespace App\Http\Controllers;

use App\Models\WasteCategory;
use Illuminate\Http\Request;

class WasteCategoryController extends Controller
{
    public function index()
    {
        $categories = WasteCategory::withCount('wasteTypes')
            ->orderBy('category_name')
            ->paginate(10);

        return view('waste-categories.index', compact('categories'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'category_name' =>
                'required|string|max:255|unique:waste_categories,category_name',

            'description' =>
                'nullable|string|max:1000',
        ]);


        WasteCategory::create([
            'category_name' => $request->category_name,
            'description' => $request->description,
        ]);


        return redirect()
            ->route('waste-categories.index')
            ->with(
                'success',
                'Waste category added successfully.'
            );
    }


    public function update(
        Request $request,
        WasteCategory $wasteCategory
    )
    {
        $request->validate([
            'category_name' =>
                'required|string|max:255|unique:waste_categories,category_name,' .
                $wasteCategory->id,

            'description' =>
                'nullable|string|max:1000',
        ]);


        $wasteCategory->update([
            'category_name' => $request->category_name,
            'description' => $request->description,
        ]);


        return redirect()
            ->route('waste-categories.index')
            ->with(
                'success',
                'Waste category updated successfully.'
            );
    }


    public function destroy(
        WasteCategory $wasteCategory
    )
    {
        if ($wasteCategory->wasteTypes()->exists()) {

            return redirect()
                ->route('waste-categories.index')
                ->with(
                    'error',
                    'This category cannot be deleted because it is already used by one or more waste types.'
                );
        }


        $wasteCategory->delete();


        return redirect()
            ->route('waste-categories.index')
            ->with(
                'success',
                'Waste category deleted successfully.'
            );
    }
}