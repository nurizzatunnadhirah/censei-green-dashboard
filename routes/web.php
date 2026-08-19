<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\WasteCategoryController;
use App\Http\Controllers\WasteTypeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WasteRecordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LegacyImportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LandfillImportController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Authenticated + Active Users
|--------------------------------------------------------------------------
|
| Every route inside this group requires:
|
| 1. User must be logged in
| 2. User account must have status = Active
|
*/

Route::middleware(['auth', 'active'])->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Waste Records
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/waste-records',
        [WasteRecordController::class, 'index']
    )->name('waste-records.index');


    Route::get(
        '/waste-records/create',
        [WasteRecordController::class, 'create']
    )->name('waste-records.create');


    Route::post(
        '/waste-records',
        [WasteRecordController::class, 'store']
    )->name('waste-records.store');


    Route::get(
        '/waste-records/{wasteRecord}/edit',
        [WasteRecordController::class, 'edit']
    )->name('waste-records.edit');


    Route::put(
        '/waste-records/{wasteRecord}',
        [WasteRecordController::class, 'update']
    )->name('waste-records.update');


    Route::delete(
        '/waste-records/{wasteRecord}',
        [WasteRecordController::class, 'destroy']
    )->name('waste-records.destroy');


    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/reports',
        [ReportController::class, 'index']
    )->name('reports.index');


    Route::get(
        '/reports/export/excel',
        [ReportController::class, 'exportExcel']
    )->name('reports.export.excel');


    Route::get(
        '/reports/print',
        [ReportController::class, 'print']
    )->name('reports.print');


    /*
    |--------------------------------------------------------------------------
    | Locations / Programs
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/locations',
        [LocationController::class, 'index']
    )->name('locations.index');


    Route::post(
        '/locations',
        [LocationController::class, 'store']
    )->name('locations.store');


    Route::put(
        '/locations/{location}',
        [LocationController::class, 'update']
    )->name('locations.update');


    Route::delete(
        '/locations/{location}',
        [LocationController::class, 'destroy']
    )->name('locations.destroy');


    /*
    |--------------------------------------------------------------------------
    | Waste Types
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/waste-types',
        [WasteTypeController::class, 'index']
    )->name('waste-types.index');


    Route::post(
        '/waste-types',
        [WasteTypeController::class, 'store']
    )->name('waste-types.store');


    Route::put(
        '/waste-types/{wasteType}',
        [WasteTypeController::class, 'update']
    )->name('waste-types.update');


    Route::delete(
        '/waste-types/{wasteType}',
        [WasteTypeController::class, 'destroy']
    )->name('waste-types.destroy');


    /*
    |--------------------------------------------------------------------------
    | Waste Categories
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/waste-categories',
        [WasteCategoryController::class, 'index']
    )->name('waste-categories.index');


    Route::post(
        '/waste-categories',
        [WasteCategoryController::class, 'store']
    )->name('waste-categories.store');


    Route::put(
        '/waste-categories/{wasteCategory}',
        [WasteCategoryController::class, 'update']
    )->name('waste-categories.update');


    Route::delete(
        '/waste-categories/{wasteCategory}',
        [WasteCategoryController::class, 'destroy']
    )->name('waste-categories.destroy');


    /*
    |--------------------------------------------------------------------------
    | Landfill Import
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/landfill-import',
        [LandfillImportController::class, 'index']
    )->name('landfill.index');


    Route::post(
        '/landfill-import/upload',
        [LandfillImportController::class, 'upload']
    )->name('landfill.upload');


    Route::post(
        '/landfill-import/import',
        [LandfillImportController::class, 'import']
    )->name('landfill.import');


    /*
    |--------------------------------------------------------------------------
    | Legacy Recycling Import
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/legacy-import',
        [LegacyImportController::class, 'index']
    )->name('legacy.index');


    Route::post(
        '/legacy-import/upload',
        [LegacyImportController::class, 'upload']
    )->name('legacy.upload');


    Route::post(
        '/legacy-import/preview',
        [LegacyImportController::class, 'preview']
    )->name('legacy.preview');


    Route::post(
        '/legacy-import/import',
        [LegacyImportController::class, 'import']
    )->name('legacy.import');


    /*
    |--------------------------------------------------------------------------
    | User Management - ADMIN ONLY
    |--------------------------------------------------------------------------
    |
    | Staff cannot access these routes even by manually typing the URL.
    |
    */

    Route::middleware('admin')
        ->prefix('users')
        ->name('users.')
        ->group(function () {

            Route::get(
                '/',
                [UserController::class, 'index']
            )->name('index');


            Route::post(
                '/',
                [UserController::class, 'store']
            )->name('store');


            Route::put(
                '/{user}',
                [UserController::class, 'update']
            )->name('update');

        });


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');


    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile/photo',
        [ProfileController::class, 'destroyPhoto']
    )->name('profile.photo.destroy');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');

});


/*
|--------------------------------------------------------------------------
| Laravel Breeze Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';