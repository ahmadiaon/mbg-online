<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\DatabaseDataController;
use App\Http\Controllers\GeneralRouteController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\ShiftKaryawanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaterLevelController;
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

Route::prefix('web')->group(function () {
    Route::fallback(function () {
        return redirect('/app');
    });
});

Route::prefix('WEB')->group(function () {
    Route::fallback(function () {
        return redirect('/app');
    });
});



Route::get('/file/{folder}/{filename}', [DatabaseController::class, 'showPdf']);

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
    Route::get('/', function () {
        return redirect('/app', 301);
    });
    Route::get('/user', [PersonalController::class, 'User']);

    Route::prefix('/payroll')->group(function () {
        Route::get('/slip', [GeneralRouteController::class, 'managePayrollSlip']);
        Route::post('/slip', [DatabaseController::class, 'slipStoreV2']);
    });

    Route::prefix('/manage')->group(function () {
        Route::get('/recruitment', [GeneralRouteController::class, 'manageRecruitment']);
        Route::prefix('/absensi')->group(function () {
            Route::get('/', [AbsensiController::class, 'manageabsensi']);
            Route::post('/getDataAbsensi', [AbsensiController::class, 'getDataAbsensi']);
            Route::post('/import-absensi', [AbsensiController::class, 'importAbsensi']);
        });
        Route::prefix('/shift')->group(function () {
            Route::get('/', [ShiftKaryawanController::class, 'manageShift']);
            Route::post('/getDataShift', [ShiftKaryawanController::class, 'getDataShift']);
            Route::post('/import-shift', [ShiftKaryawanController::class, 'importShift']);
        });

        Route::get('/file-manager', [GeneralRouteController::class, 'manageFile']);




        Route::post('/slip', [DatabaseController::class, 'slipStore']);
    });


    Route::prefix('/feature')->group(function () {
        Route::get('/water-level', [WaterLevelController::class, 'index']);
        Route::post('/water-level', [WaterLevelController::class, 'store']);
        Route::get('/water-level/data', [WaterLevelController::class, 'data']);
        Route::put('/water-level/{id}', [WaterLevelController::class, 'update']);
        Route::delete('/water-level/{id}', [WaterLevelController::class, 'destroy']);
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

    Route::prefix('/logistik')->group(function () {
        Route::prefix('/permintaan')->group(function () {
            // Halaman utama permintaan
            Route::get('/permintaan', [PermintaanController::class, 'indexPermintaan']);
            Route::get('/pengadaan', [PermintaanController::class, 'indexPengadaan']);

            // Import/Export datatable
            Route::post('/import-datatable', [PermintaanController::class, 'importDatatable']);
            Route::post('/export-datatable', [PermintaanController::class, 'exportDatatable']);
        });

        Route::prefix('/stok')->group(function () {
            // Halaman utama permintaan
            Route::get('/stok', [PermintaanController::class, 'indexPermintaan']);

            // Import/Export datatable
            Route::post('/import-datatable', [PermintaanController::class, 'importDatatable']);
            Route::post('/export-datatable', [PermintaanController::class, 'exportDatatable']);
        });
    });


    Route::prefix('/me')->group(function () {
        Route::prefix('/kehadiran')->group(function () {
            Route::get('/absensi', [AbsensiController::class, 'meAbsensi']);
        });
    });

    Route::prefix('/source')->group(function () {
        Route::prefix('/database')->group(function () {
            Route::prefix('/menu')->group(function () {
                Route::post('/getdatadatatable',  [DatabaseDataController::class, 'getDatadataTable']);
            });
            Route::prefix('/cache')->group(function () {
                Route::post('/get-cache', [DatabaseDataController::class, 'masterCacheGet']);
            });
        });
    });
});


Route::prefix('/database')->group(function () {
    Route::post('/refresh-session', [DatabaseDataController::class, 'masterCacheGet']);
    Route::GET('/get-refresh-session', [DatabaseDataController::class, 'getrefreshSession']);
});
Route::prefix('/superadmin')->group(function () {
    Route::post('/cache/forget', [DatabaseDataController::class, 'masterCacheForget']);
    Route::GET('/get-refresh-cache', [DatabaseDataController::class, 'masterCacheSet']);
});

Route::prefix('/hauling')->group(function () {
    Route::get('/time-cek', [GeneralRouteController::class, 'haulingTimerCek']);
});

Route::get('/struktur-organisasi', function () {
    return view('app.database.struktur-organisasi.struktur_organisasi_index');
});







// ============= AUTHENTICATION ROUTES ==============
Route::get('/authentication/{auth_token}', [GeneralRouteController::class, 'authentication']);
// Route::get('/authentication', [GeneralRouteController::class, 'authentication']);
Route::post('/authentication/{auth_token}', [GeneralRouteController::class, 'processToken']);
// ============= AUTHENTICATION ROUTES ==============

Route::prefix('/auth')->group(function () {
    Route::get('/login', function () {
        Session::flush();
        Session::invalidate();
        Session::regenerateToken();
        return view('login');
    })->name('login');

    Route::get('/logout', function () {
        Session::flush();
        Session::invalidate();
        Session::regenerateToken();

        return redirect('/auth/login');
    });

    Route::post('/login', [UserController::class, 'login'])->name('login.ajax');
});
