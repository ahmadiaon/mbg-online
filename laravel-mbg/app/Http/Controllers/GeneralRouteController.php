<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class GeneralRouteController extends Controller
{
    public function indexMenu()
    {
        return view('app.general.menuIndex');
    }

    public function manageFile(){
        return view('app.manage.manageFile');
    }
    public function authentication($auth_token)
    {
        // if (session()->missing('auth_token')) {
        //     return redirect()->intended('/auth/login#TOKEN-FAILED');
        // }

        // return view('app.general.authentication');
        // $user = User::where('auth_login', $auth_token)->first();


        // return $user;
        return view('app.general.authentication', [
            // 'token' => $auth_token,
            // 'valid' => !is_null($user), // bisa dipakai di view untuk menampilkan pesan awal
        ]);
    }

    public function processToken(Request $request, $auth_token)
    {
        // Validasi token
        // ====== Algorithm:
        // - cek apakah ada user dengan auth_login = $auth_token
        // - jika tidak ada, return error
        // - jika ada, login user tersebut buat token ulang menggunakan Str::random(60)
        // - simpan di auth_token simpan ke DB
        // - jalankan fungsi untuk mengambil data user dari DB (misal: UserController::validatedAuth($auth_token))
        // - return response JSON dengan status success dan data user yg akan di simpan di localStorage
        // ====== end Algorithm

        $user = User::where('auth_login', $auth_token)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token tidak valid atau sudah kadaluarsa.',
            ], 400);
        }

        // Jika token valid, lakukan proses yang diperlukan (misalnya login user)
        // ...

        $token = Str::random(60);
        $user->auth_login = $token;
        $user->save();

        // 4. Simpan token baru ke session (opsional, karena Auth::login sudah menyimpan user ID)
        session(['auth_token' => $token]);

        // 5. Ambil data user yang valid
        $data_db = DatabaseDataController::refreshSessionProses($token);
        $data_identitias_karyawan = UserController::validatedAuth($token);
        $user->data_identitias_karyawan = $data_identitias_karyawan;


        // Simpan data user ke session
        $data_db['FILTER_APP']['USER'] = $user;

        Session::put('auth_token', $token);
        Session::put('FILTER_APP', $data_db['FILTER_APP'] ?? []);


        return response()->json([
            'status' => 'success',
            'users' => $user,
            'message' => 'Token valid. Proses berhasil.',
            'FILTER_APP' => $data_db['FILTER_APP'] ?? [],
            'auth_token' => $token,
            'session' => session('auth_token'),
        ]);
    }

    public function haulingTimerCek()
    {
        return view('hauling.timerCek');
    }


    public function managePayrollSlip()
    {
        return view('app.payroll.manageSlip');
    }
    public function manageRecruitment()
    {
        // dd(session()->all());
        return view('app.manage.manageRecruitment');
    }

    public function mySlip()
    {
        // $query = DB::table('slips');
        // $data = $query->get();
        // return $data;
        return view('app.personal.mySlip');
    }
}
