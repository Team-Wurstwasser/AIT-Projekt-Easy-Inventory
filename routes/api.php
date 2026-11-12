<?php

use App\Http\Controllers\Api\AbteilungController;
use App\Http\Controllers\Api\AusleiheController;
use App\Http\Controllers\Api\BarcodeController;
use App\Http\Controllers\Api\MitarbeiterController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\WerkzeugController;
use App\Http\Controllers\Api\WerkzeugTypController;
use Illuminate\Support\Facades\Route;

Route::get('barcode', BarcodeController::class);

Route::controller(WerkzeugController::class)->prefix('werkzeuge')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::put('/', 'update');
    Route::delete('/', 'destroy');
});

Route::controller(WerkzeugTypController::class)->prefix('werkzeug_typen')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::put('/', 'update');
    Route::delete('/', 'destroy');
});

Route::controller(StatusController::class)->prefix('status')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::put('/', 'update');
    Route::delete('/', 'destroy');
});

foreach (['abteilung', 'abteilungen'] as $prefix) {
    Route::controller(AbteilungController::class)->prefix($prefix)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('/', 'update');
        Route::delete('/', 'destroy');
    });
}

Route::controller(MitarbeiterController::class)->prefix('mitarbeiter')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::put('/', 'update');
    Route::delete('/', 'destroy');
});

Route::controller(AusleiheController::class)->group(function () {
    Route::post('ausleihen', 'ausleihen');
    Route::post('abgeben', 'abgeben');
    Route::get('ausgeliehen', 'ausgeliehen');
    Route::get('ausgeliehen_historie', 'historie');
    Route::match(['put', 'post'], 'verlängern', 'verlaengern');
});