<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('distance');
});

Route::post('/distance/import', [\App\Http\Controllers\DistanceController::class, 'importCSV'])->name('distance.import');

Route::resources([
    'distance' => \App\Http\Controllers\DistanceController::class,
]);