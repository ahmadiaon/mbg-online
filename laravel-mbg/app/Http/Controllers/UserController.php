<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\DatabaseData;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public static function validatedAuth($token)
    {
        $database_data = [
            'database_table' => 'IDENTITAS-KARYAWAN',
        ];

        $user = User::where('auth_login', $token)->first();
        if (!$user) {
            return null;
        }
        $NRP = $user->nrp;



        $data_identitias_karyawan = DatabaseData::where('code_table_data', $database_data['database_table'])
            ->whereNull('date_end')
            ->where('code_data', $NRP)
            ->get();
        $result = [];
        if (count($data_identitias_karyawan) === 0) {
            return $result;
        }
        foreach ($data_identitias_karyawan as $data) {
            $result[$data->code_field_data] = $data->value_data;
        }
        return $result;
    }
    public static function login(Request $request)
    {
        $request->validate([
            'nrp' => 'required|string',
            'pin' => 'nullable|string',
            'nik_ktp' => 'nullable|string',
        ]);

        $user = User::where('nrp', $request->nrp)->first();

        $isValid = false;
        $message = '';
        $data = [];
        $token = null;

        if (!$user) {
            $message = 'User tidak ditemukan';
            $data = ['notFound' => true];
        } elseif ($user->pin === null) {
            if (!$request->nik_ktp) {
                $message = 'PIN belum di-set, masukkan NIK';
                $data = ['isPin' => false];
            } elseif (!Hash::check($request->nik_ktp, $user->password)) {

                $message = Hash::make($request->nik_ktp); //'NIK salah';
                $data = ['errorType' => 'nik'];
            } else {
                $isValid = true;
                $message = 'Login berhasil dengan NIK';
            }
        } else {
            if (!$request->pin) {
                $message = 'Masukkan PIN';
                $data = ['isPin' => true];
            } elseif (!Hash::check($request->pin, $user->pin)) {
                $message = 'PIN salah';
                $data = ['errorType' => 'pin'];
            } else {
                $isValid = true;
                $message = 'Login berhasil';
            }
        }

        if ($isValid) {
            $token = Str::random(60);
            $user->auth_login = $token;
            $user->save();


            $data_identitias_karyawan = self::validatedAuth($token);
            $user->data_identitias_karyawan = $data_identitias_karyawan;


            $data = $user;
            $request->session()->put('auth_token', $token);
        }

        // dd($_SESSION);

        return response()->json([
            'status' => $isValid ? 'success' : 'error',
            'message' => $message,
            'data' => $data,
            'request' => $request->all(),
            'session' => $request->session()->all(),
            'auth_token' => $token
        ], 200); // <- selalu 200
    }

    public static function manageUser(Request $request)
    {
        return view('app.database.manageUser');
    }

    public static function Update(Request $request)
    {
        $dataValidated = $request->validate([
            'email' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'pin' => 'nullable|string',
            'uuid_data' => 'nullable|string',
        ]);

        $auth_token = json_decode($request->header('token'));
        $user = User::where('auth_login', $auth_token)->first();
        if (!$user) {
            return ResponseFormatter::ResponseJson(null, 'Unauthorized', 401);
        }



        if (isset($request->pin)) {
            $Q_store_data = User::updateOrCreate(
                [
                    'nrp' => $user->nrp, //value primary key
                ],
                [
                    'pin' => Hash::make($request->pin),
                ]
            );
        }

        if (isset($request->email)) {
            if ($request->id_email) {
                $Q_store_data = DatabaseData::updateOrCreate(
                    [
                        'id' => $request->id_email
                    ],
                    [
                        'code_table_data' => 'IDENTITAS-KARYAWAN', //table data source
                        'code_field_data' => 'EMAIL',
                        'code_data' => $user->nrp, //value primary key
                        'uuid_data' => $request->uuid_data,
                        'value_data' => $request->email,
                        'date_start' => Carbon::now()->format('Y-m-d'),
                        'date_end' => null,
                    ]
                );
            } else {
                $Q_store_data = DatabaseData::updateOrCreate(
                    [
                        'code_table_data' => 'IDENTITAS-KARYAWAN', //table data source
                        'code_field_data' => 'EMAIL',
                        'code_data' => $user->nrp, //value primary key
                        'uuid_data' => $request->uuid_data,
                    ],
                    [
                        'value_data' => $request->email,
                        'date_start' => Carbon::now()->format('Y-m-d'),
                        'date_end' => null,
                    ]
                );
            }
        }
        if (isset($request->no_hp)) {
            if ($request->id_no_hp) {
                $Q_store_data = DatabaseData::updateOrCreate(
                    [
                        'id' => $request->id_no_hp
                    ],
                    [
                        'code_table_data' => 'IDENTITAS-KARYAWAN', //table data source
                        'code_field_data' => 'NO-HP',
                        'code_data' => $user->nrp, //value primary key
                        'uuid_data' => $request->uuid_data,
                        'value_data' => $request->no_hp,
                        'date_start' => Carbon::now()->format('Y-m-d'),
                        'date_end' => null,
                    ]
                );
            } else {
                $Q_store_data = DatabaseData::updateOrCreate(
                    [
                        'code_table_data' => 'IDENTITAS-KARYAWAN', //table data source
                        'code_field_data' => 'NO-HP',
                        'code_data' => $user->nrp, //value primary key
                        'uuid_data' => $request->uuid_data,
                    ],
                    [
                        'value_data' => $request->no_hp,
                        'date_start' => Carbon::now()->format('Y-m-d'),
                        'date_end' => null,
                    ]
                );
            }
        }


        return ResponseFormatter::ResponseJson(true, 'User updated successfully', 200);
    }

    public static function UpdateUser(Request $request)
    {
        $dataValidated = $request->validate([
            'nrp' => 'required|string',
            'nik_ktp' => 'nullable|string',
        ]);

        $user = User::where('nrp', $request->nrp)->first();
        if (!$user) {
            return ResponseFormatter::ResponseJson(false, 'User not found', 404);
        }

        if (isset($request->pin)) {
            $user->pin = NULL;
        }
        if (isset($request->nik_ktp)) {
            $password = Hash::make($request->nik_ktp);

            $userStore = User::updateOrCreate(['nrp' => $request->nrp], [
                'pin'   => NULL,
                'password' => $password,
            ]);

            $storeKTP = DatabaseData::updateOrCreate(
                [
                    'code_table_data' => 'IDENTITAS-KARYAWAN', //table data source
                    'code_field_data' => 'NIK-KTP',
                    'code_data' => ResponseFormatter::toUUID($user->nrp), //value primary key
                ],
                [
                    'value_data' => $request->nik_ktp,
                    'date_end' => null,
                ]
            );
        }

        return ResponseFormatter::ResponseJson(true, 'User updated successfully', 200);
    }
}
