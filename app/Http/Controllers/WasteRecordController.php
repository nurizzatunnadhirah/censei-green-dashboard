<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\WasteType;
use App\Models\WasteCategory;
use App\Models\WasteRecord;
use App\Models\WasteFile;
use Illuminate\Http\Request;

class WasteRecordController extends Controller
{
    public function index(Request $request)
    {
        // Data for filter dropdowns
        $locations = Location::orderBy('location_name')->get();
        $wasteTypes = WasteType::orderBy('type_name')->get();
        $wasteCategories = WasteCategory::orderBy('category_name')->get();

        // Base query
        $query = WasteRecord::with([
            'location',
            'wasteType',
            'recordedBy',
            'files'
        ]);

        // Filter by Waste Stream
        if ($request->filled('waste_stream')) {
            $query->where(
                'waste_stream',
                $request->waste_stream
            );
        }

        // Search by Waste Type or Remarks
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->whereHas('wasteType', function ($q2) use ($search) {

                    $q2->where(
                        'type_name',
                        'like',
                        "%{$search}%"
                    );

                })
                ->orWhere(
                    'remarks',
                    'like',
                    "%{$search}%"
                );

            });
        }

        // Filter by Location
        if ($request->filled('location')) {

            $query->where(
                'location_id',
                $request->location
            );
        }

        // Filter by Waste Category
        if ($request->filled('waste_category')) {

            $query->whereHas(
                'wasteType',
                function ($q) use ($request) {

                    $q->where(
                        'category_id',
                        $request->waste_category
                    );
                }
            );
        }

        // Filter by Waste Type
        if ($request->filled('waste_type')) {

            $query->where(
                'waste_type_id',
                $request->waste_type
            );
        }

        // Filter by Collection Date
        if ($request->filled('collection_date')) {

            $query->whereDate(
                'collection_date',
                $request->collection_date
            );
        }

        // Pagination
        $records = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'waste-records.index',
            compact(
                'records',
                'locations',
                'wasteTypes',
                'wasteCategories'
            )
        );
    }


    public function create()
    {
        $locations = Location::orderBy('location_name')->get();

        $wasteTypes = WasteType::orderBy('type_name')->get();

        return view(
            'waste-records.create',
            compact(
                'locations',
                'wasteTypes'
            )
        );
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' =>
                'required|exists:locations,id',

            'waste_type_id' =>
                'required|exists:waste_types,id',

            'actual_weight' =>
                'required|numeric|min:0',

            'collection_date' =>
                'required|date',

            'collection_method' =>
                'nullable|string|max:100',

            'remarks' =>
                'nullable|string',

            'attachment' =>
                'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        // Manual records are always Recycling records.
        // Landfill records are added through Landfill Import.
        $wasteRecord = WasteRecord::create([
            'location_id' =>
                $validated['location_id'],

            'waste_type_id' =>
                $validated['waste_type_id'],

            'recorded_by' =>
                auth()->id(),

            'actual_weight' =>
                $validated['actual_weight'],

            'collection_date' =>
                $validated['collection_date'],

            'collection_method' =>
                $validated['collection_method'] ?? null,

            'remarks' =>
                $validated['remarks'] ?? null,

            'waste_stream' =>
                'Recycling',

            'recycled' =>
                true,
        ]);

        // Upload file if provided
        if ($request->hasFile('attachment')) {

            $file = $request->file('attachment');

            $path = $file->store(
                'waste-files',
                'public'
            );

            WasteFile::create([
                'waste_record_id' =>
                    $wasteRecord->id,

                'file_name' =>
                    $file->getClientOriginalName(),

                'file_type' =>
                    $file->getClientOriginalExtension(),

                'file_size' =>
                    round(
                        $file->getSize() / 1024,
                        2
                    ),

                'file_path' =>
                    $path,

                'uploaded_at' =>
                    now(),
            ]);
        }

        return redirect()
            ->route('waste-records.index')
            ->with(
                'success',
                'Waste record added successfully.'
            );
    }


    public function edit(WasteRecord $wasteRecord)
    {
        $locations = Location::orderBy('location_name')->get();

        $wasteTypes = WasteType::orderBy('type_name')->get();

        return view(
            'waste-records.edit',
            compact(
                'wasteRecord',
                'locations',
                'wasteTypes'
            )
        );
    }


    public function update(
        Request $request,
        WasteRecord $wasteRecord
    ) {
        $validated = $request->validate([
            'location_id' =>
                'required|exists:locations,id',

            'waste_type_id' =>
                'required|exists:waste_types,id',

            'actual_weight' =>
                'required|numeric|min:0',

            'collection_date' =>
                'required|date',

            'collection_method' =>
                'nullable|string|max:100',

            'remarks' =>
                'nullable|string',
        ]);

        // Update editable fields only.
        // Existing waste_stream is intentionally left unchanged.
        $wasteRecord->update($validated);

        return redirect()
            ->route('waste-records.index')
            ->with(
                'success',
                'Waste record updated successfully.'
            );
    }


    public function destroy(WasteRecord $wasteRecord)
    {
        $wasteRecord->delete();

        return redirect()
            ->route('waste-records.index')
            ->with(
                'success',
                'Waste record deleted successfully.'
            );
    }
}