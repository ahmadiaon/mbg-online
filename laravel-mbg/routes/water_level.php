<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaterLevelController;

Route::prefix('feature/water-level')->group(function () {

    Route::get('/', [
        WaterLevelController::class,
        'index'
    ])->name('water-level.index');


    Route::get('/data', [
        WaterLevelController::class,
        'data'
    ])->name('water-level.data');


    Route::post('/', [
        WaterLevelController::class,
        'store'
    ])->name('water-level.store');

});