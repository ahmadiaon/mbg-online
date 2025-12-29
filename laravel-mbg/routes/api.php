<?php

use App\Http\Controllers\API\DatabaseTableController;
use App\Http\Controllers\API\GroupFormController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\DatabaseDataController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('/database')->group(function () {

    Route::post('/create-json-file-db', [DatabaseController::class, 'createJsonFileDB']);
    Route::post('/store-data-form', [DatabaseController::class, 'store']);
    Route::post('/delete-data-form', [DatabaseController::class, 'destroy']);

    Route::prefix('/menu')->group(function () {
        Route::post('/getdatadatatable',  [GroupFormController::class, 'getDatadataTable']);
        Route::post('/storedatadatatable',  [GroupFormController::class, 'storeDatadataTable']);
        Route::post('/deletedatadatatable',  [GroupFormController::class, 'deleteDatadataTable']);
    });

    Route::prefix('/form')->group(function () {
        Route::post('/getdatadatatable',  [DatabaseTableController::class, 'getDatadataTable']);
        Route::post('/getdatadatatable',  [DatabaseTableController::class, 'getDatadataTable']);
        Route::post('/storedatadatatable',  [DatabaseTableController::class, 'storeDatadataTable']);
        Route::post('/deletedatadatatable',  [DatabaseDataController::class, 'deleteDatadataTable']);
    });
    Route::prefix('/data')->group(function () {
        Route::post('/storeData',  [DatabaseController::class, 'storeData']);
    });

    Route::prefix('/group-form')->group(function () {
        Route::post('/store',  [DatabaseTableController::class, 'storeGroupForm']);
    });
});
Route::prefix('/user')->group(function () {
    Route::post('/update',  [UserController::class, 'Update']);
    Route::post('/update-user',  [UserController::class, 'UpdateUser']);
});
