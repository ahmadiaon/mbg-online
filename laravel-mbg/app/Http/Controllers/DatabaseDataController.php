<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\DatabaseData;
use App\Models\DatabaseDataSource;
use App\Models\DatabaseField;
use App\Models\DatabaseFieldShow;
use App\Models\DatabaseTable;
use App\Models\GroupForm;
use App\Models\User;
use App\Models\UserTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class DatabaseDataController extends Controller
{

    public static function getrefreshSession()
    {
        return DatabaseDataController::refreshSessionProses('ABC');
    }

    public static function getrefreshSessionRole()
    {
        for ($i = 2; $i <= 5; $i++) {
            $default_database = DatabaseDataController::refreshSessionProses($i);
            Cache::forever('db_role_' . $i, $default_database);
        }
    }
    public static function refreshSessionProses($auth_login)
    {

        $users = User::where('auth_login', $auth_login)->first();

        if ($auth_login == 'ABC') {
            $users = User::where('nrp', 'MBLE-0422003')->first();
        }

        $Q_user_feture = DatabaseData::where('code_table_data', 'KARYAWAN-ACCESS-FEATURE')
            ->where('code_data', 'like', ResponseFormatter::toUUID($users->nrp) . '%')
            ->where('code_field_data', 'FEATURE')
            ->get();
        $arr_data_feature = [];
        foreach ($Q_user_feture as $data_user_feture) {
            if (!in_array($data_user_feture->value_data, $arr_data_feature)) {
                $arr_data_feature[] =  $data_user_feture->value_data;
            }
        }


        $FILTER_APP = [
            'DEFAULT_FILTER' => [
                'day' => date('d'),
                'month' => date('m'),
                'year' => date('Y'),
                "date_start" => "2025-09-01",
                "date_end" => "",
                "statusKaryawan" => [
                    "AKTIVE",
                    "PHK-BULAN-INI"
                ],
                "FEATURE" => $arr_data_feature,
                "PERUSAHAAN" => [],
                "PROJECT" => [],
                "DEPARTEMEN" => [],
                "DIVISI" => [],
                "KARYAWAN" => [],
                "JABATAN" => [],
                "from" => null,
            ],
            'ON_FILTER' => [
                'day' => date('d'),
                'month' => date('m'),
                'year' => date('Y'),
                "date_start" => "2025-09-01",
                "date_end" => "2025-09-30",
                "statusKaryawan" => [
                    "AKTIVE",
                    "PHK-BULAN-INI"
                ],
                "FEATURE" =>  $arr_data_feature,
                "PERUSAHAAN" => [],
                "PROJECT" => [],
                "DEPARTEMEN" => [],
                "DIVISI" => [],
                "KARYAWAN" => [],
                "JABATAN" => [],
                "from" => null,
            ],
            'USER' => $users
        ];

        // ====== GET DATA USER ROLE ===================
        // 5. Ambil db Jabatan User untuk menentukan role user
        $Q_data_jabatan = DatabaseData::where(
            'code_data',
            ResponseFormatter::toUUID($users->nrp)
        )->where('code_field_data', 'JABATAN')->first();

        // 6. Ambil db Grade Jabatan untuk menentukan role user
        $Q_data_grade_jabatan = DatabaseData::where(
            'code_data',
            $Q_data_jabatan->value_data
        )->where('code_field_data', 'GRADE')->first();

        $users->role =  $Q_data_grade_jabatan->value_data;

        if ($auth_login == 'ABC') {
            $users->role =  2;
        }
        if ($auth_login == '3') {
            $users->role = 3 ;
        }
        if ($auth_login == '4') {
            $users->role = 4 ;
        }


        // ====== GET DATA USER ROLE ===================
        $Q_data_single = DB::table('database_fields')
            ->join('database_data', function ($join) {
                $join->on('database_fields.code_field', '=', 'database_data.code_field_data')
                    ->on('database_fields.code_table_field', '=', 'database_data.code_table_data');
            })
            ->join('database_tables', function ($join) {
                $join->on('database_data.code_table_data', '=', 'database_tables.code_table')
                    ->on('database_data.code_table_data', '=', 'database_tables.code_table');
            })
            ->where('database_data.code_data', '=', ResponseFormatter::toUUID($users['nrp']))
            ->select(['database_data.*', 'database_fields.level_data_field', 'database_tables.menu_table'])
            ->get([
                'database_data.code_table_data',
                'database_data.code_field_data',
                'database_data.value_data',
                'database_data.code_data',
                'database_data.uuid_data',
                'database_fields.level_data_field',
                'database_tables.menu_table',
                'database_fields.level_data_field',
                'database_tables.menu_table'
            ]);

        // ====== GET DATABASE mengikuti ROLE USER ===================
        $Q_data_database = DB::table('database_fields')
            ->join('database_data', function ($join) {
                $join->on('database_fields.code_field', '=', 'database_data.code_field_data')
                    ->on('database_fields.code_table_field', '=', 'database_data.code_table_data');
            })
            ->join('database_tables', function ($join) {
                $join->on('database_data.code_table_data', '=', 'database_tables.code_table')
                    ->on('database_data.code_table_data', '=', 'database_tables.code_table');
            })
            ->where('database_tables.menu_table', '=', 'DATABASE')
            ->where('database_fields.level_data_field', '<=', $users['role'])
            ->select(['database_data.*', 'database_fields.level_data_field', 'database_tables.menu_table'])
            ->get([
                'database_data.code_table_data',
                'database_data.code_field_data',
                'database_data.value_data',
                'database_data.code_data',
                'database_data.uuid_data',
                'database_fields.level_data_field',
                'database_tables.menu_table',
                'database_fields.level_data_field',
                'database_tables.menu_table'
            ]);
        $Q_data_single = $Q_data_single->merge($Q_data_database);


        if ((int)$users->role > 1) {
            $Q_data = DB::table('database_fields')
                ->join('database_data', function ($join) {
                    $join->on('database_fields.code_field', '=', 'database_data.code_field_data')
                        ->on('database_fields.code_table_field', '=', 'database_data.code_table_data');
                })
                ->join('database_tables', function ($join) {
                    $join->on('database_data.code_table_data', '=', 'database_tables.code_table')
                        ->on('database_data.code_table_data', '=', 'database_tables.code_table');
                })
                // ->where('database_tables.menu_table', '=', 'DATABASE')
                ->where('database_fields.level_data_field', '<=', $users['role'])
                ->select(['database_data.*', 'database_fields.level_data_field', 'database_tables.menu_table'])
                ->get([
                    'database_data.code_table_data',
                    'database_data.code_field_data',
                    'database_data.value_data',
                    'database_data.code_data',
                    'database_data.uuid_data',
                    'database_fields.level_data_field',
                    'database_tables.menu_table',
                    'database_fields.level_data_field',
                    'database_tables.menu_table'
                ]);
            $Q_data_single = $Q_data_single->merge($Q_data);
        }

        $database_data = [];
        foreach ($Q_data_single as $data) {
            $value_data = [
                'value_data' => $data->value_data,
                'uuid_data' => $data->uuid_data,
                'text_data' => $data->value_data,
                'code_data' => $data->code_data,
                'id'    => $data->id
            ];
            $database_data[$data->code_table_data][$data->code_data][$data->code_field_data] = $value_data;
            // $database_data[$data->level_data_field][$data->menu_table][$data->code_table_data][$data->code_data][$data->code_field_data] = $value_data;
        }

        $Q_data_DatabaseFieldShow = DatabaseFieldShow::get();
        $dataDatabaseFieldShow = [];
        foreach ($Q_data_DatabaseFieldShow as $data_user_template) {
            $dataDatabaseFieldShow[$data_user_template->table_code][$data_user_template->field_code][$data_user_template->sort_field] = $data_user_template;
        }



        // Get Database Group Forms
        $Q_group_forms = GroupForm::get();
        $data_group_forms = [];
        foreach ($Q_group_forms as $group_forms) {
            $data_group_forms[$group_forms->uuid] = $group_forms;
        }

        $Q_data_DatabaseFieldShow = DatabaseFieldShow::get();
        $dataDatabaseFieldShow = [];
        foreach ($Q_data_DatabaseFieldShow as $data_user_template) {
            $dataDatabaseFieldShow[$data_user_template->table_code][$data_user_template->field_code][$data_user_template->sort_field] = $data_user_template;
        }

        $Q_data_source = DatabaseDataSource::get(['field_get_data_source', 'code_data_source', 'table_data_source']);
        $data_data_source = [];
        foreach ($Q_data_source as $data_source) {
            $data_data_source[$data_source->code_data_source] = $data_source;
        }

        // Get Database Tables
        $Q_table = DatabaseTable::get([
            'code_table',
            'parent_table',
            'primary_table',
            'menu_table',
            'description_table'
        ]);
        $data_table = [];
        $data_table_child = [];
        $data_table_menu = [];

        // Get Database Fields
        $Q_field = DatabaseField::get([
            'code_table_field',
            'description_field',
            'type_data_field',
            'level_data_field',
            'code_field',
            'visibility_data_field',
            'full_code_field',
            'sort_field'
        ]);
        $data_field = [];
        $data_field_join = [];
        foreach ($Q_field as $field) {
            if (isset($data_data_source[$field->full_code_field])) {
                $field->data_source = $data_data_source[$field->full_code_field];
            }
            $data_field[$field->code_table_field][$field->code_field] = $field;
        }

        foreach ($Q_table as $table) {
            $data_table[$table->code_table] = $table;

            if (isset($data_field[$table->code_table])) {
                $data_table[$table->code_table]['fields'] = $data_field[$table->code_table];
            }
            if (isset($database_data[$table->code_table])) {
                $data_table[$table->code_table]['data'] = $database_data[$table->code_table];
            }

            $data_table[$table->code_table] = $table;
            $data_table_menu[$table->menu_table][] = $table->code_table;

            if ($table->parent_table) {
                $data_table_child[$table->parent_table][] = $table->code_table;
            }
        }

        if (!empty($data_field_join)) {
            foreach ($data_field_join as $code_table_parent => $field_join) {
                if (isset($data_field[$code_table_parent])) {
                    $data_field[$code_table_parent] = array_merge($data_field[$code_table_parent], $field_join['join_fields']);
                } else {
                    $data_field[$code_table_parent] = $field_join['join_fields'];
                }
            }
        }

        foreach ($data_table_child as $parent_table => $child_tables) {

            if (isset($data_field[$parent_table])) {

                $data_table[$parent_table]['join_fields'] = [];

                foreach ($child_tables as $child_table) {

                    if (isset($data_field[$child_table])) {

                        // copy langsung tanpa spread agar associative key tetap utuh
                        $field_childs = $data_field[$child_table];

                        // merge associative array tanpa menghilangkan key
                        $data_table[$parent_table]['join_fields'] += $field_childs;
                    }
                }
            }
        }

        // ====== MANIPULATION DATA ===================

        $data_table_new = json_decode(json_encode($data_table), true);

        foreach ($data_table as $table_code => $tables) {

            foreach ($tables['fields'] as $field_code => $field) {

                if ($field['type_data_field'] == 'DARI-TABEL') {
                    // echo $field_code;
                    // echo $table_code.'~'.$field_code. PHP_EOL;
                    // dd($tables);
                    $data_source = $field['data_source'];
                    $table_src = $data_source['table_data_source'];      // PERUSAHAAN
                    $field_src = $data_source['field_get_data_source'];  // NAMA-PERUSAHAAN-PENDEK

                    // pastikan tabel sumber ada
                    if (!isset($data_table[$table_src]['data'])) continue;
                    if (!isset($tables['data'])) continue;
                    // looping data
                    foreach ($tables['data'] as $row_code => $row) {

                        // ambil value_data untuk mencari di table source

                        if (!isset($row[$field_code]['value_data'])) {
                            continue;
                        }
                        $key_lookup = $row[$field_code]['value_data']; // contoh: "CV--MB"
                        // cek apakah key lookup ada di table source
                        if (!isset($data_table[$table_src]['data'][$key_lookup])) {
                            continue;
                        }

                        // ambil text_data dari tabel sumber
                        $new_text = $data_table[$table_src]['data'][$key_lookup][$field_src]['text_data'] ?? null;

                        if ($new_text !== null) {
                            $data_table_new[$table_code]['data'][$row_code][$field_code]['text_data'] = $new_text;
                        }
                    }
                }
            }
        }

        $data_table = $data_table_new;
        foreach ($data_table_child as $parent_table => $child_tables) {

            // Jika parent tidak punya data → skip agar tidak error
            if (empty($data_table_new[$parent_table]['data']) || !is_array($data_table_new[$parent_table]['data'])) {
                $data_table_new[$parent_table]['join_data'] = [];
                continue;
            }

            $joined_data = [];

            // Loop data parent
            foreach ($data_table_new[$parent_table]['data']  as $key_parent_data => $value_parent_data) {

                // Inisiasi data parent
                $dataParent = is_array($value_parent_data) ? $value_parent_data : [];

                // Loop child table
                foreach ($child_tables as $child_table) {

                    // Jika child tidak ada → lewati
                    if (empty($data_table_new[$child_table]['data']) || !is_array($data_table_new[$child_table]['data'])) {
                        continue;
                    }

                    // Ambil data child berdasarkan key parent
                    $data_table_child_value = $data_table_new[$child_table]['data'][$key_parent_data] ?? null;

                    // Jika tidak ada data child → skip
                    if (!empty($data_table_child_value) && is_array($data_table_child_value)) {
                        $dataParent = array_merge($dataParent, $data_table_child_value);
                    }
                }

                // Simpan hasil join per parent key
                $joined_data[$key_parent_data] = $dataParent;
            }

            // Simpan hasil join ke data_table
            $data_table_new[$parent_table]['join_data'] = $joined_data;
        }
        // dd($data_table_new['KARYAWAN']);

        // ====== APPLY USER TEMPLATE ===================
        $Q_table_field_show = UserTemplate::where('employee_uuid', ResponseFormatter::toUUID($users->nrp))->get();

        $data_table_field_show = [];

        // Loop hasil query
        foreach ($Q_table_field_show as $table) {

            // Cek table code ada di $data_table
            if (!empty($table->code_table) && isset($data_table_new[$table->code_table])) {

                // Pastikan key array terbentuk
                if (!isset($data_table_field_show[$table->code_table]['field_show'])) {
                    $data_table_field_show[$table->code_table]['field_show'] = [];
                }

                // Tambahkan field_show hanya jika ada datanya
                if (!empty($table->code_field)) {
                    $data_table_field_show[$table->code_table]['field_show'][] = $table->code_field;
                }
            }
        }

        // Proses apply ke $data_table
        foreach ($data_table_field_show as $code_table => $table) {

            // Pastikan $data_table ada key tsb dan field_show tidak kosong
            if (isset($data_table[$code_table]) && !empty($table['field_show'])) {
                $data_table_new[$code_table]['field_show'] = $table['field_show'];
            }
        }

        // ========================================================



        $Q_data_source = UserTemplate::get();
        $dataUserTemplate = [];
        foreach ($Q_data_source as $data_user_template) {
            $dataUserTemplate[$data_user_template->employee_uuid][$data_user_template->code_table_get][$data_user_template->code_field] = $data_user_template;
        }

        // ====== MANIPULATION DATA ===================

        foreach ($dataDatabaseFieldShow as $key_table => $gabungan_fields) {
            # code...
            if (isset($data_table_new[$key_table]['data'])) {
                $table_data = isset($data_table_new[$key_table]['join_data']) ? $data_table_new[$key_table]['join_data'] : $data_table_new[$key_table]['data'];
                // return $gabungan_fields;
                $isJoin = isset($data_table_new[$key_table]['join_data']) ? true : false;
                foreach ($table_data as $code_data => $data_value) { // looping data

                    foreach ($gabungan_fields as $field_fields) { // looping field gabungan
                        $val_gabungan = '';
                        foreach ($field_fields as $field_value) {
                            $val_gabungan = $val_gabungan . $data_value[$field_value['field_show_code']]['text_data'] . $field_value['split_by'];
                        }
                        $arr_data = [
                            'value_data' => rtrim($val_gabungan, $field_value['split_by']),
                            'uuid_data' => $data_value[$field_value['field_show_code']]['uuid_data'],
                            'text_data' => rtrim($val_gabungan, $field_value['split_by']),
                            'code_data' => $code_data,
                            'id'    => null
                        ];

                        if ($isJoin) {
                            $data_table_new[$key_table]['join_data'][$code_data][$field_value['field_code']] = $arr_data;
                        }
                        $data_table_new[$key_table]['data'][$code_data][$field_value['field_code']] = $arr_data;
                    }
                    // return $val_gabungan;
                }
            }
        }

        $FILTER_APP['PROFILE'] = $data_table_new['KARYAWAN']['join_data'][ResponseFormatter::toUUID($users->nrp)] ?? null;
        $FILTER_APP['USER'] = $users;

        $default_database = [
            'database_user_template' => $dataUserTemplate,
            'database_tables' => $data_table_new,
            'data_group_forms' => $data_group_forms,
            'database_tables_child' => $data_table_child,
            'database_data_source' => $data_data_source,
            'database_tables_menu' => $data_table_menu,
            'database_field_show' => $dataDatabaseFieldShow,
        ];



        if ($users->role >= 5) {
            // $default_database['database_data'] = $database_data;
        } else {
            // $default_database['database_data'] = $database_data;
        }

        session()->put('FILTER_APP', $FILTER_APP);
        $default_database['FILTER_APP'] = $FILTER_APP;
        return $default_database;
    }

    public function refreshSession(Request $request)
    {

        /*
            1. validasi apakah default filter ada di session


        */
        $request->validate([
            'auth_token' => 'required|string',
        ]);
        $auth_login = $request->header('token');
        $default_database = $this->refreshSessionProses($auth_login);
        session()->put('DATABASE', $default_database);
        $default_database['SESSION_'] =  session()->all();
        return response()->json([
            'data' => $default_database,
            'status' => 'success',
            'message' => 'Session refreshed successfully code 1',
        ]);
    }

    public function deleteDatadataTable(Request $request)
    {
        $request->validate([
            'group_data' => 'required|string',
        ]);

        $data = $request->all();
        $deleted = [];

        // return ResponseFormatter::ResponseJson($data, 'Data table deleted successfully', 200);
        // Process the data as needed, e.g., delete from database
        // For demonstration, we'll just return the received data
        // delete data table
        if ($data['group_data'] == 'database_tables') {
            // database_tables
            $Q_delete = DatabaseTable::where('code_table', $data['code_data'])->delete();
            $deleted['table'] = $Q_delete;

            // database_fields
            $Q_delete = DatabaseField::where('code_table_field', $data['code_data'])->delete();
            $deleted['field'] = $Q_delete;
            // database_data
            $Q_delete = DatabaseData::where('code_table_data',  $data['code_data'])->delete();
            $deleted['data'] = $Q_delete;
            return ResponseFormatter::ResponseJson($deleted, 'Data table deleted successfully', 200);
        } elseif ($data['group_data'] == 'database_datas') {
            // database_fields
            $Q_delete = DatabaseField::where('code_table_field', $data['code_table'])
                ->where('code_field', $data['code_field'])->delete();

            // database_data
            $Q_delete = DatabaseData::where('code_table_data',  $data['code_table'])
                ->where('code_field_data', $data['code_field'])->delete();
        } else {
            return ResponseFormatter::ResponseJson(null, 'group_data is required', 400);
        }


        return ResponseFormatter::ResponseJson($data, 'Data table deleted successfully', 200);
    }

    public function getTableData($code_table)
    {
        //1.0 GET table properties
        $Q_table = DatabaseTable::where('code_table', $code_table)->get();
        $data_return = [];
        $data_table = [];
        $data_table_child = [];
        foreach ($Q_table as $table) {
            // $data_table['table'] = $table;
            $data_table['all_table'][$table->code_table] = $table;
            $data_table['the_table'] = $table;
            $data_return['table'] = $table;
        }




        // 1.1 GET Fields
        $Q_field = DatabaseField::where('code_table_field', $code_table)->get();
        foreach ($Q_field as $field) {
            // $data_table['fields'][$field->code_field] = $field;
            $data_table['all_fields'][$field->full_code_field] = $field;
            $data_return['fields'][$field->code_table_field] = $field;
        }


        // 2.0 GET data
        $Q_data_table = DatabaseData::where('code_table_data', $code_table)->whereNull('date_end')->get([
            'code_table_data',
            'code_field_data',
            'value_data',
            'code_data',
            'uuid_data'
        ]);

        $data_datatables = [];
        foreach ($Q_data_table  as $data_datatable) {
            $data_datatables[$data_datatable->code_data][$data_datatable->code_field_data] =   $data_datatable;
            $data_table['the_data'][$data_datatable->uuid_data][$data_datatable->code_field_data] =  $data_datatable;
        }
        $data_return['data_tables'] = $data_datatables;


        return ResponseFormatter::ResponseJson($data_return, 'Success Get ' . $code_table, 200);

        $Q_table = DatabaseTable::where('parent_table', $code_table)->get();
        foreach ($Q_table as $table) {
            $Q_field = DatabaseField::where('code_table_field', $table->code_table)->get();
            foreach ($Q_field as $field) {
                // $data_table['child']['table'][$table->code_table]['fields'][$field->code_field] = $field;                
                $data_table['all_fields'][$field->full_code_field] = $field;
            }
            $data_table['child']['table'][$table->code_table]['table'] = $table;
            $data_table['all_table'][$table->code_table] = $table;
        }

        foreach ($data_table['all_fields']  as $arr_field) {
            $data_table['arr_fields'][] =  $arr_field;
            if ($arr_field->code_table_field ==  $data_table['the_table']['code_table']) {
                $data_table['the_fields'][$arr_field->code_field] = $arr_field;
            }
        }

        $data_table['the_table']['fields'] = $data_table['the_fields'];



        // DATA
        $Q_data_table = DatabaseData::where('code_table_data', $code_table)->whereNull('date_end')->get();
        // $data_table['the_data'] = $Q_data_table;
        foreach ($Q_data_table  as $data_datatable) {
            $data_table['the_data'][$data_datatable->uuid_data][$data_datatable->code_field_data] =  $data_datatable;
        }

        $data_table['the_template'] = null;
        dd($data_table);
        return ResponseFormatter::ResponseJson($data_table, 'Success Get ' . $code_table, 200);
    }


    // ======================== CACHE MASTER DATA =====================================
    // 
    // =============================================================================


    function masterCacheSet()
    {
        $data = $this->getrefreshSession();
        Cache::forever('db', $data);
        $this->getrefreshSessionRole();
        $users = User::where(function ($query) {
            $query->whereNull('pin')->orWhere('pin', '');
        })->whereNotNull('nrp')->where('nrp', '!=', '')->get();

        if ($users->isEmpty()) {
        } else {
            $count = 0;
            foreach ($users as $user) {
                // Generate token 8 karakter dari NRP (sama seperti di JavaScript)
                $hex = hash('sha256', $user->nrp);
                $token = strtoupper(substr($hex, 0, 8));

                $user->auth_login = $token;
                $user->save();
                $count++;
            }
        }

        return view('app.database.master_cache');
    }

    /**
     * Ambil data dari cache.
     * Jika $codeData diberikan, hanya kembalikan satu record (tanpa key 'data' sebagai array).
     * Jika tidak ada, kembalikan null.
     */
    function masterCache(string $codeTable, ?string $codeData = null): ?array
    {
        $cached = Cache::get("master_{$codeTable}");
        if (!$cached) return null;

        if ($codeData) {
            return $cached['data'][$codeData] ?? null;  // langsung itemnya, atau null
        }

        return $cached;
    }

    function masterCacheGet(Request $request)
    {
        // return ResponseFormatter::ResponseJson($request->all(), "Cache for {$request->key} retrieved successfully", 200);
        $keyCache = $request->key;
        $cached = Cache::get("{$keyCache}");
        if ($cached) {
            $cached = json_decode(json_encode($cached), true);
        }

        if ($request->code_table) {
            $cached = $cached['database_tables'][$request->code_table] ?? null;
            if ($request->code_data) {
                if ($cached['join_data']) {
                    $cached = $cached['join_data'][$request->code_data] ?? null;
                } else {
                    $cached = $cached['data'][$request->code_data] ?? null;
                }
            }
        }
        $data_return = [
            'key_cache' => $keyCache,
            'cached' => $cached,
            'code_table' => $request->code_table ?? null,
            'code_data' => $request->code_data ?? null,
        ];
        if (!$cached) {
            return ResponseFormatter::ResponseJson(null, "Cache for {$keyCache} not found", 404);
        }
        return ResponseFormatter::ResponseJson($data_return, "Cache for {$keyCache} retrieved successfully", 200);
    }

    // delete cache
    function masterCacheForget(Request $request)
    {
        $keyCache = $request->key_cache;
        Cache::forget("{$keyCache}");
        return ResponseFormatter::ResponseJson(null, "Cache for {$keyCache} forgotten successfully", 200);
    }


    // ============================================
    // DATABASE
    // ============================================
    public function getDatadataTable(Request $request)
    {
        $request->validate([
            'table_name' => 'required|string',
            'field' => 'nullable|string',
            'value_field' => 'nullable|string',
        ]);


        $table = $request->table_name;
        if (!Schema::hasTable($table)) {
            return response()->json(['message' => 'Table not found'], 404);
        }

        $query = DB::table($table);
        if ($request->filled('field') && $request->filled('value_field')) {
            $query->where($request->field, $request->value_field);
        }

        $data = $query->get();
        return ResponseFormatter::ResponseJson($data, 'success', 200);
    }
}
