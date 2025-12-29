<?php

use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\DatabaseDataController;
use App\Http\Controllers\GeneralRouteController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/slip/{filename}', function ($filename) {

    $path = public_path("file/slips/{$filename}.pdf");

    if (!File::exists($path)) {
        return response()->json([
            'message' => 'File PDF tidak ditemukan'
        ], 404);
    }

    return response()->stream(function () use ($path) {
        readfile($path);
    }, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"',
        'Accept-Ranges' => 'bytes',
    ]);
});

Route::get('/slip-download/{filename}/{downloadName}', function ($filename, $downloadName) {

    $path = public_path("file/slips/{$filename}.pdf");

    if (!File::exists($path)) {
        abort(404, 'File tidak ditemukan');
    }

    return response()->download(
        $path,
        $downloadName . '.pdf',
        ['Content-Type' => 'application/pdf']
    );
});


Route::middleware(['auth.login'])->group(function () {
    Route::get('/download-excel', [DatabaseController::class, 'downloadExcel']);
    Route::get('/menu', [GeneralRouteController::class, 'indexMenu']);
    Route::post('/menu', [GeneralRouteController::class, 'indexMenu']);

    // SINGLE ROUTE
    Route::get('/profile', [PersonalController::class, 'Profile']);

    Route::get('/my-slip', [GeneralRouteController::class, 'mySlip']);
    Route::get('/app', [PersonalController::class, 'Menu']);
    Route::get('/', [PersonalController::class, 'Menu']);
    Route::get('/user', [PersonalController::class, 'User']);


    // Route::get('/slip/{filename}', [DatabaseController::class, 'showSlip'])
    //     ->where('filename', '.*');



    Route::prefix('/payroll')->group(function () {
        Route::get('/slip', [GeneralRouteController::class, 'managePayrollSlip']);
        Route::post('/slip', [DatabaseController::class, 'slipStore']);
    });


    Route::prefix('/database')->group(function () {
        Route::get('/form', function () {
            return view('app.database.form.index');
        });

        Route::prefix('/data')->group(function () {
            Route::post('/import-datatable',  [DatabaseController::class, 'importDatatable']);
            Route::post('/export-datatable',  [DatabaseController::class, 'exportDatatable']);

            Route::get('/', function () {
                return view('app.database.database');
            });
        });


        Route::get('/user', [UserController::class, 'manageUser']);



        Route::get('/menu', function () {
            return view('app.database.menu.index');
        });
    });
});

Route::prefix('/database')->group(function () {
    Route::post('/refresh-session', [DatabaseDataController::class, 'refreshSession']);
    Route::GET('/get-refresh-session', [DatabaseDataController::class, 'getrefreshSession']);
});

Route::prefix('/hauling')->group(function () {
    Route::get('/time-cek', [GeneralRouteController::class, 'haulingTimerCek']);
});


Route::get('/authentication', [GeneralRouteController::class, 'authentication']);


Route::get('/struktur-organisasi', function () {
    return view('app.database.struktur-organisasi.struktur_organisasi_index');
});


Route::prefix('/auth')->group(function () {
    Route::get('/login', function () {

        return view('login');
    })->name('login');

    Route::get('/logout', function () {
        return redirect('/auth/login');
    });

    Route::post('/login', [UserController::class, 'login'])->name('login.ajax');
});
