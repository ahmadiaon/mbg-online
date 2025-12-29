<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\DatabaseData;
use App\Models\DatabaseDataSource;
use App\Models\DatabaseField;
use App\Models\DatabaseFieldShow;
use App\Models\DatabaseTable;
use App\Models\GroupForm;
use App\Models\Slip;
use App\Models\User;
use App\Models\UserTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

class DatabaseController extends Controller
{
    public function downloadExcel(Request $request)
    {
        $file = $request->file;

        if (!file_exists($file)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download(
            $file,
            basename($file),
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]
        );
    }

    public function createJsonFileDB(Request $request)
    {
        $database = [];
        $auth_token = $request->header('x-auth-Login');
        $database['AUTH'] = $auth_token;
        $database['REQUEST'] = $request->all();
        $filePath = public_path('generate.json');



        /*
            1. tentukan dimana diambilnya ['database','local'],   :  true
            2. tentukan apa yang diambil
            3. update untuk yang local
            4. return kan

        */

        // 1. DATABASE 
        if ($database['REQUEST']['isRefresh'] == 2) { /* DARI DATABASE */

            //* if ($database['REQUEST']['arrKey'][0] == 'menu' || $database['REQUEST']['arrKey'][0] == 'ALL') {
            // 1. menu - group form
            $Q_MENU = GroupForm::get(['uuid', 'description']);
            $data_menu = [];
            foreach ($Q_MENU as $menu) {
                $data_menu[$menu->uuid] = $menu;
            }
            $database['db']['menu'] = $data_menu;
            // 1. menu - group form
            //* } 


            // OTHER
            $Q_data_DatabaseFieldShow = DatabaseFieldShow::get();
            $dataDatabaseFieldShow = [];
            foreach ($Q_data_DatabaseFieldShow as $data_user_template) {
                $dataDatabaseFieldShow[$data_user_template->table_code][$data_user_template->field_code][$data_user_template->sort_field] = $data_user_template;
            }

            $database['db']['database_field_show'] = $dataDatabaseFieldShow;

            $Q_data_source = DatabaseDataSource::get();
            $data_data_source = [];
            foreach ($Q_data_source as $data_source) {
                $data_data_source[$data_source->code_data_source] = $data_source;
            }

            $database['db']['database_data_source'] = $data_data_source;

            $Q_data_source = UserTemplate::get();
            $dataUserTemplate = [];
            foreach ($Q_data_source as $data_user_template) {
                $dataUserTemplate[$data_user_template->employee_uuid][$data_user_template->code_table_get][$data_user_template->code_field] = $data_user_template;
            }

            $database['db']['table_show_template'] = $dataUserTemplate;
            // OTHER

            //* if ($database['REQUEST']['arrKey'][0] == 'database_table' || $database['REQUEST']['arrKey'][0] == 'ALL') {
            // 2. TABLE
            $data_table = [];
            $data_table_child = [];
            $Q_TABLE = DatabaseTable::get(['code_table', 'description_table', 'menu_table', 'parent_table', 'primary_table']);
            if (!empty($Q_TABLE)) {
                $data_table_menu = [];
                foreach ($Q_TABLE as $table) {
                    $data_table[$table->code_table] = $table;
                    $data_table_menu[$table->menu_table][] = $table->code_table;
                    if ($table->parent_table) {
                        $data_table_child[$table->parent_table][] = $table->code_table;
                    }
                }
            }

            $database['db']['database_table'] = $data_table;
            $database['db']['data_table_child'] = $data_table_child;
            // 2. TABLE
            //* }

            //* if ($database['REQUEST']['arrKey'][0] == 'database_field' || $database['REQUEST']['arrKey'][0] == 'ALL') {
            // 3. FIELD
            $Q_field = DatabaseField::get(['code_field', 'code_table_field', 'description_field', 'full_code_field', 'level_data_field', 'sort_field', 'type_data_field', 'visibility_data_field']);
            $data_field = [];
            $data_field_join = [];
            foreach ($Q_field as $field) {
                $data_field[$field->code_table_field][$field->code_field] = $field;
                if ($data_table[$field->code_table_field]['parent_table']) {
                    $data_field_join[$data_table[$field->code_table_field]['parent_table']][$field->code_field] = $field;
                } else {
                    $data_field_join[$field->code_table_field][$field->code_field] = $field;
                }
            }
            $database['db']['database_field'] = $data_field;
            $database['db']['database_field_join'] = $data_field_join;
            // 3. FIELD
            //* }

            if ($database['REQUEST']['arrKey'][0] == 'database_data' || $database['REQUEST']['arrKey'][0] == 'ALL' || $database['REQUEST']['arrKey'][0] == 'public') {
                // 4. DATABASE DATA
                $data_data = [];
                $uuid_all = [];

                // $Q_data_self =  DatabaseData::whereNull('date_end')->get();

                $Q_data_self = DB::table('database_fields')
                    ->join('database_data', function ($join) {
                        $join->on('database_fields.code_field', '=', 'database_data.code_field_data')
                            ->on('database_fields.code_table_field', '=', 'database_data.code_table_data');
                    })
                    // ->where('database_fields.level_data_field', '<=', $user_level_field)
                    ->whereNull('database_data.date_end')
                    ->select(['database_data.code_data', 'database_data.code_field_data', 'database_data.code_table_data', 'database_data.value_data', 'database_data.uuid_data'])
                    ->get();
                foreach ($Q_data_self as $data) {
                    $data_data[$data->code_table_data][ResponseFormatter::toUUID($data->code_data)][$data->code_field_data]['value_data'] = $data->value_data;
                    $data_data[$data->code_table_data][ResponseFormatter::toUUID($data->code_data)][$data->code_field_data]['uuid_data'] = $data->uuid_data;
                }
                $database['db']['database_data'] = $data_data;
                // 4. DATABASE DATA
                // 5. PUBLIC DATA
                $data_public = [];
                foreach ($data_table as $code_table_parent => $table_parent) {
                    if (!empty($data_data[$code_table_parent])) {
                        foreach ($data_data[$code_table_parent] as $code_data => $item_data) {
                            foreach ($item_data as $code_field => $item_field) {
                                if (!empty($data_field[$code_table_parent][$code_field])) { // filter untuk field yang sudah di hapus
                                    $value = $item_field['value_data'];
                                    switch ($data_field[$code_table_parent][$code_field]['type_data_field']) {
                                        case 'DARI-TABEL':
                                            $code_table_data_source = $data_data_source[$code_table_parent . '-' . $code_field]['table_data_source'];
                                            $field_data_source = $data_data_source[$code_table_parent . '-' . $code_field]['field_get_data_source'];
                                            if (!empty($data_data[$code_table_data_source][$value])) {
                                                $value = (!empty($data_data[$code_table_data_source][$value][$field_data_source])) ? $data_data[$code_table_data_source][$value][$field_data_source]['value_data'] : null;
                                            } else {
                                                $value = null;
                                            }
                                            break;
                                        case 'INPUT-AUTOCOMPLITE':
                                            $code_table_data_source = $data_data_source[$code_table_parent . '-' . $code_field]['table_data_source'];
                                            $field_data_source = $data_data_source[$code_table_parent . '-' . $code_field]['field_get_data_source'];
                                            $value = (!empty($data_data[$code_table_data_source][$value])) ? $data_data[$code_table_data_source][$value][$field_data_source]['value_data'] : null;
                                            break;
                                        case 'GABUNGAN':
                                            $value = $item_field['value_data'];
                                            break;
                                        default:
                                            $value = $item_field['value_data'];
                                            break;
                                    }
                                    $data_public['public_value'][$code_table_parent][$code_data][$code_field] = $value;
                                }
                            }
                        }
                    }
                }
                $database['db']['public'] = $data_public;
                // 5. PUBLIC DATA
            }

            // 3. Update ke Local
            file_put_contents($filePath, json_encode($database['db']));
            // 3. Update ke local
        }
        // 1. DATABASE

        // 1. LOCAL
        if ($database['REQUEST']['isRefresh'] == 1) {

            if (file_exists($filePath)) {
                $json = file_get_contents($filePath);
                $database['db'] = json_decode($json, true); // true => jadi array

            } else {
                return ResponseFormatter::ResponseJson('di lokal kosong file', 'success from createJsonFileDB', 200);
            }
        }
        // 1. LOCAL

        // 2. MENENTUKAN YANG DIAMBIL
        if ($database['REQUEST']['arrKey'][0] == 'ALL') {
            $value = $database['db'];
            return ResponseFormatter::ResponseJson($value, 'success from createJsonFileDB ALL', 200);
        } else {
            $keyPath = implode('.', $database['REQUEST']['arrKey']); // hasil: "menu.PROFILE"
            $value = data_get($database['db'], $keyPath);
        }

        // return ResponseFormatter::ResponseJson($data_rr, 'success from createJsonFileDB', 200);
        // 2. MENENTUKAN YANG DIAMBIL

        return ResponseFormatter::ResponseJson($value, 'success from createJsonFileDB', 200);
    }


    public function store(Request $request)
    {
        $request_data = $request['form_detail'];
        return ResponseFormatter::ResponseJson($request_data, "store database", 200);
        $code_table = $request_data['code_table'];
        if (empty($code_table)) {
            return ResponseFormatter::ResponseJson('code_table is required', 'error from store database', 400);
        }

        $store_database_table = DatabaseTable::updateOrCreate([
            'code_table' => $code_table,
        ], [
            'parent_table' => (!empty($request_data['parent_table'])) ? $request_data['parent_table'] : null,
            'primary_table' => ResponseFormatter::toUUID($request_data['primary_table']),
            'menu_table' => $request_data['menu_table'],
            'description_table' => $request_data['description_table'],
        ]);

        foreach ($request_data['fields'] as $field) {
            $store_database_fields = DatabaseField::updateOrCreate([
                'full_code_field' => $store_database_table->code_table . '-' . ResponseFormatter::toUUID($field['description_field'])
            ], [
                'code_table_field' => $store_database_table->code_table,
                'description_field' => $field['description_field'],
                'visibility_data_field' => $field['visibility_data_field'],
                'type_data_field' => $field['type_data_field'],
                'level_data_field' => $field['level_data_field'],
                'code_field' => ResponseFormatter::toUUID($field['description_field']),
                'full_code_field' => $store_database_table->code_table . '-' . ResponseFormatter::toUUID($field['description_field']),
                'sort_field' => $field['sort_field'],
            ]);



            if (!empty($field['data_source'])) {
                $store_database_data_source = DatabaseDataSource::updateOrCreate([
                    'code_data_source' => $store_database_fields->full_code_field
                ], [
                    'table_data_source' =>  $field['data_source']['table_data_source'],
                    'field_get_data_source' =>  $field['data_source']['field_get_data_source'],
                ]);
            }

            if (!empty($field['gabungan'])) {
                $table_code = $store_database_table->code_table;
                $field_code = ResponseFormatter::toUUID($field['description_field']);
                foreach ($field['gabungan'] as $key => $value) {
                    $store_database_field_shows = DatabaseFieldShow::updateOrCreate([
                        'table_code' => $table_code,
                        'field_code' => $field_code,
                        'field_show_code' => $value['field_show_code']
                    ], [
                        'split_by' =>  $value['split_by'],
                        'sort_field' =>  $value['sort_field'],
                        'table_show_code' => ($value['table_show_code']) ? ($value['table_show_code']) : $table_code
                    ]);
                }
            }
        }
        return ResponseFormatter::ResponseJson($store_database_table, "store database", 200);

        if (!empty($request_data['parent_table'])) {
            $table_parent = DatabaseTable::where('code_table', $request_data['parent_table'])->first();
            $store_database_fields = DatabaseField::updateOrCreate([
                'full_code_field' => $store_database_table->code_table . '-' . $table_parent->primary_table, //CODE-TABLE-FIELD-PRIMARY-CODE-TABLE
            ], [
                'code_table_field' => $store_database_table->code_table,
                'description_field' => ResponseFormatter::toUUID($request_data['primary_table']),
                'type_data_field' => 'hidden',
                'level_data_field' => 1,
                'code_field' => $table_parent->primary_table,
                'full_code_field' => $store_database_table->code_table . '-' . ResponseFormatter::toUUID($table_parent->primary_table),
                'sort_field' => null,
            ]);
        }

        // if (!empty($request_data['persetujuan'])) {
        //     $Q_delete = DatabasePersetujuan::where('form_code', $code_table)->delete();
        //     foreach ($request_data['persetujuan'] as $persetujuan) {
        //         DatabasePersetujuan::updateOrCreate(
        //             [
        //                 'form_code' => $code_table,
        //                 'level' =>  $persetujuan['level'],
        //                 'grade' =>  $persetujuan['grade'],
        //             ],
        //             [
        //                 'form_code' => $code_table,
        //                 'level' =>  $persetujuan['level'],
        //                 'grade' =>  $persetujuan['grade'],
        //                 'description' =>  $persetujuan['description'],
        //                 'reference' =>  $persetujuan['reference'],
        //             ]
        //         );
        //     }
        // }

        return ResponseFormatter::ResponseJson($request_data, "store database", 200);
    }

    public function destroy(Request $request)
    {
        // return ResponseFormatter::ResponseJson($request->all(), "destroy database", 200);
        $request_data = $request->all();

        $arrKey = $request_data['arrKey'];
        if ($arrKey[0] == 'database_table') {
            $code_table = $arrKey[1];
            // Hapus data tabel
            DatabaseTable::where('code_table', $code_table)->delete();
            // Hapus data field
            DatabaseField::where('code_table_field', $code_table)->delete();
            // Hapus data source
            DatabaseDataSource::where('code_data_source', 'like', $code_table . '-%')->delete();
            // Hapus data field show
            DatabaseFieldShow::where('table_code', $code_table)->delete();
        }



        return ResponseFormatter::ResponseJson($request_data, "destroy database", 200);
    }

    public function storeData(Request $request)
    {

        $request_data = $request->all();
        foreach ($request_data['data_text'] as $item) {
            $result[$item['name']] = $item['value'];
        }
        $request_data['data_text'] = $result;
        $uuid_data = Str::uuid();
        $code_data = ResponseFormatter::toUUID($request_data['data_text'][$request_data['data_table']['primary_table']]);
        $date_now = Carbon::now()->format('Y-m-d');
        $code_table_data = $request_data['data_table']['code_table'];
        if (isset($request_data['data_text']['uuid_data'])) { // ini update
            $uuid_data = $request_data['data_text']['uuid_data'];
        } else {
        }
        unset($request_data['data_text']['uuid_data']);

        $report_store = [];
        foreach ($request_data['data_text'] as $code_field => $value_data) {
            $store_data = DatabaseData::updateOrCreate(
                [
                    'uuid_data' => $uuid_data,
                    'code_table_data' => $code_table_data,
                    'code_field_data' => $code_field,
                ],
                [
                    'value_data' => $value_data,
                    'code_data' => $code_data,
                    'date_start' => $date_now,
                    'date_end' => null,
                ]
            );

            $data_to_database_datatable = [
                'uuid_data' => $uuid_data,
                'value_data' => $value_data,
                'text_data' => $value_data,
                'code_data' => $code_data,
            ];


            $report_store[$code_field] = $data_to_database_datatable;
            $return_data['add_data'][$code_field] = $value_data;
        }
        $data_session = session('DATABASE');
        $data_session['database_tables'][$code_table_data]['data'][$code_data] = $report_store;

        session()->put('DATABASE', $data_session);

        $return_data['data'] = $report_store;
        $return_data['uuid_data'] = $uuid_data;
        return ResponseFormatter::ResponseJson($return_data, "destroy database", 200);
    }

    public function importDatatable(Request $request)
    {
        $session_data   = session('DATABASE');
        $database_tables = $session_data['database_tables'];
        $the_file       = $request->file('uploaded_file');

        $ALFABET = ResponseFormatter::abjads();
        $column_fields = [];
        $arr_value = [];
        $insert = [];
        $upsert = [];

        $date_now = Carbon::now()->format('Y-m-d');

        $properties_data_table = [];

        // try {

        $sheet       = IOFactory::load($the_file->getRealPath())->getActiveSheet();
        $row_limit   = $sheet->getHighestDataRow();

        // ============================================
        // 1. MAP KOLOM → TABLE + FIELD
        // ============================================
        $colIndex = 4;

        while ($sheet->getCell($ALFABET[$colIndex] . '1')->getValue()) {

            $table_code = ResponseFormatter::toUUID($sheet->getCell($ALFABET[$colIndex] . '2')->getValue());
            $field_code = ResponseFormatter::toUUID($sheet->getCell($ALFABET[$colIndex] . '1')->getValue());

            // jika field terdaftar dalam konfigurasi
            if (isset($database_tables[$table_code]['fields'][$field_code])) {

                $fieldInfo = $database_tables[$table_code]['fields'][$field_code];
                $properties_data_table[$table_code][] = $field_code;

                $column_fields[$ALFABET[$colIndex]] = [
                    'table' => $table_code,
                    'field' => $field_code,
                    'type'  => $fieldInfo['type_data_field'],
                    'is_uuid' => isset($fieldInfo['data_source']),
                ];
            }

            $colIndex++;
        }

        // ============================================
        // 2. AMBIL SEMUA ROW EXCEL
        // ============================================

        $uuid_row = [];
        for ($r = 5; $r <= $row_limit; $r++) {
            $uuid_row[$r] = Str::uuid();
            foreach ($column_fields as $col => $meta) {

                $raw = $sheet->getCell($col . $r)->getValue();
                if ($raw === null) continue;

                $value = $meta['is_uuid'] ? ResponseFormatter::toUUID($raw) : $raw;
                if ($meta['type'] == 'DATE') {
                    $value = ResponseFormatter::convertToDate($value);
                }

                $arr_value[$meta['table']][$r][$meta['field']] = $value;
            }
        }
        $status_kerja = [];
        if (in_array('STATUS-KERJA-KARYAWAN', array_keys($properties_data_table))) {
            $status_kerja = $arr_value['STATUS-KERJA-KARYAWAN'] + [];
        }


        if (in_array('KONTRAK-KARYAWAN', array_keys($properties_data_table))) {

            // Ambil kontrak
            $arr_kontrak = $arr_value['KONTRAK-KARYAWAN'] + [];

            foreach ($arr_kontrak as $r_3 => $rows_3) {

                // Hapus field STATUS
                unset($rows_3['STATUS']);
                // menambah ke fingger tambahan anak bawang hihi
                $NRP = $arr_value['KARYAWAN'][$r_3]['NRP'] ?? null;
                $ID_FINGGER = ResponseFormatter::toNumber($NRP);
                $arr_value['DATABASE-KODE-TABEL-ID-FINGGER'][$r_3]['ID-FINGGER'] = $ID_FINGGER;
                $arr_value['DATABASE-KODE-TABEL-ID-FINGGER'][$r_3]['NRP'] = $NRP;
                $arr_value['DATABASE-KODE-TABEL-ID-FINGGER'][$r_3]['KODE-TABEL-ID-FINGGER'] = $NRP . '-' . $ID_FINGGER;

                // Copy semua field lain ke STATUS-KERJA-KARYAWAN
                foreach ($rows_3 as $field_row_3 => $value_row_3) {
                    if (!empty($status_kerja[$r_3][$field_row_3])) continue;
                    $arr_value['STATUS-KERJA-KARYAWAN'][$r_3][$field_row_3] = $value_row_3;
                }
            }
        }

        if (
            in_array('PHK-KARYAWAN', array_keys($properties_data_table))
            && isset($arr_value['PHK-KARYAWAN'])
        ) {
            // Ambil kontrak
            $arr_PHK = $arr_value['PHK-KARYAWAN'] + [];
            foreach ($arr_PHK as $r_6 => $rows_3) {
                $arr_value['STATUS-KERJA-KARYAWAN'][$r_6]['STATUS'] = 'PHK';
            }
        }

        // ============================================
        // 3. PROSES PER TABLE — LEBIH CEPAT
        // ============================================

        foreach ($arr_value as $table_code => $rows) {

            $tbl = $database_tables[$table_code];
            $table_first = $tbl['parent_table'] ?? $tbl['code_table'];
            $tbl_parent = $database_tables[$table_first]['data'] ?? [];
            $dataExisting = $database_tables['data'] ?? [];
            $primary = $tbl['primary_table'];          // PK untuk lookup

            $field_primary = $tbl['primary_table'];


            // buat lookup PK → UUID (supaya O(1))
            $pkLookup = [];
            if (count($tbl_parent) != 0) {
                foreach ($tbl_parent as $pk => $d) {
                    $pkLookup[$pk] = $d[$primary]['uuid_data'];
                }
            }




            if (isset($tbl['parent_table'])) {
                foreach ($rows as $r => $rowData) {
                    $rows[$r][$tbl['primary_table']] = $arr_value[$tbl['parent_table']][$r][$tbl['primary_table']];
                }
            }


            foreach ($rows as $r => $rowData) {

                $pkExcel = ResponseFormatter::toUUID($rowData[$primary] ?? null);



                // ========================
                // INSERT DATA BARU
                // ========================


                $data_old_table = [];
                // jika ada data di session berrti upsert & menggunakan uuid lama
                if (isset($database_tables[$table_first]['data'][$pkExcel][$field_primary])) {
                    $uuid_row[$r] = $database_tables[$table_first]['data'][$pkExcel][$field_primary]['uuid_data'];
                    $data_old_table = $tbl['data'][$pkExcel] ?? [];
                }


                foreach ($rowData as $field => $value) {
                    $data_query = [
                        'code_table_data' => $table_code,
                        'code_field_data' => $field,
                        'value_data'      => $value,
                        'code_data'       => $pkExcel,
                        'uuid_data'       => $uuid_row[$r],
                        'date_start'      => $date_now
                    ];
                    $isOld = false;
                    if (isset($pkLookup[$pkExcel])) {
                        if ($data_old_table) {
                            $id = $data_old_table[$field]['id'] ?? null;
                            if ($id) {
                                if ($data_old_table[$field]['value_data'] != $value) {
                                    $data_query['id'] = $id;
                                    $upsert[] = $data_query;
                                }
                                $isOld = true;
                            };
                        }
                    }

                    if (!$isOld) {
                        $insert[] = $data_query;
                    }
                }
            }
        }

        $users_to_upsert = [];
        $users_to_insert = [];

        if (
            in_array('IDENTITAS-KARYAWAN', array_keys($properties_data_table)) &&
            in_array('NIK-KTP', $properties_data_table['IDENTITAS-KARYAWAN'])
        ) {
            $arr_identitas      = $arr_value['IDENTITAS-KARYAWAN'] + [];
            $table_jabatan_data = $database_tables['JABATAN']['DATA'] ?? [];

            $Q_users = User::get();
            $data_users = [];
            if (!empty($Q_users)) {
                foreach ($Q_users as $user) {
                    $data_users[$user->nrp] = $user->id;
                }
            }


            foreach ($arr_identitas as $r => $row) {

                // Ambil NRP dari table KARYAWAN
                $nrp = $arr_value['KARYAWAN'][$r]['NRP'] ?? null;
                if (!$nrp) continue;

                // Ambil NIK untuk password
                $nik = $row['NIK-KTP'] ?? null;

                // Ambil jabatan dari STATUS-KERJA-KARYAWAN
                $jabatan = $arr_value['STATUS-KERJA-KARYAWAN'][$r]['JABATAN'] ?? null;
                $role    = $table_jabatan_data[$jabatan] ?? '1';
                $users_upsert = [
                    'nrp'      => $nrp,
                    'password' => $nik ? Hash::make($nik) : null,
                    'role'     => $role,
                ];

                if (isset($data_users[$nrp])) {
                    $users_upsert['id'] = $data_users[$nrp];
                    $users_to_upsert[] = $users_upsert;
                } else {
                    $users_to_insert[] = $users_upsert;
                }
            }
        }
        $now = Carbon::now();
        if ($insert) {
            $insert = array_map(function ($row) use ($now) {
                $row['created_at'] = $now;
                $row['updated_at'] = $now;
                return $row;
            }, $insert);

            $Q_store_insert = DatabaseData::insert($insert);
        }
        return ResponseFormatter::ResponseJson([
            "users_to_upsert" => $users_to_upsert,
            "UUID_ROW" => $uuid_row,
            'detaexiting' => $uuid_row,
            "insert" => $insert,
            "upsert" => $upsert,
            "Q_store_insert" => $Q_store_insert
        ], "Optimized import success", 200);





        // ============================================
        // 4. SIMPAN SEKALI SAJA
        // ============================================

        DB::transaction(function () use ($insert, $upsert) {
            $now = Carbon::now();
            if ($insert) {
                $insert = array_map(function ($row) use ($now) {
                    $row['created_at'] = $now;
                    $row['updated_at'] = $now;
                    return $row;
                }, $insert);

                DatabaseData::insert($insert);
            }

            if ($upsert) {
                DatabaseData::upsert(
                    $upsert,
                    ['id'],
                    ['value_data']
                );
            }
        });

        DB::transaction(function () use ($users_to_insert, $users_to_upsert) {
            $now = Carbon::now();
            if ($users_to_insert) {
                $users_to_insert = array_map(function ($row) use ($now) {
                    $row['created_at'] = $now;
                    $row['updated_at'] = $now;
                    return $row;
                }, $users_to_insert);

                User::insert($users_to_insert);
            }

            if ($users_to_upsert) {
                User::upsert(
                    $users_to_upsert,
                    ['id'],
                    ['password', 'role']
                );
            }
        });
        // DatabaseDataController::refreshSessionProses('1234');

        return ResponseFormatter::ResponseJson([
            "users_to_upsert" => $users_to_upsert,
            "UUID_ROW" => $uuid_row,
            'detaexiting' => $uuid_row,
            "insert" => $insert,
            "upsert" => $upsert
        ], "Optimized import success", 200);

        return ResponseFormatter::ResponseJson([
            "users_to_upsert" => $users_to_upsert,

            "insert" => $insert,
            "upsert" => $upsert
        ], "Optimized import success", 200);
        // } catch (\Throwable $e) {
        //     return ResponseFormatter::ResponseJson($e->getMessage(), 'ERR', 500);
        // }
    }


    public function exportDatatable(Request $request)
    {
        $database_datatable = $request->code_table;

        $session_data = session('DATABASE');
        $data_table = $session_data['database_tables'][$database_datatable];
        $field_exports = isset($data_table['join_fields']) ? $data_table['join_fields'] : $data_table['fields'];


        $abjads = ResponseFormatter::abjads();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // =============================
        // HEADER UTAMA
        // =============================
        $sheet->setCellValue('A1', 'KETERANGAN DATA');
        $sheet->setCellValue('A2', 'PENGELOMPOKAN DATA');
        $sheet->setCellValue('A3', 'URUTAN');
        $sheet->setCellValue('A5', 'NAMA TABEL');
        $sheet->setCellValue('A6', $data_table['description_table']);

        $sheet->setCellValue('C1', 'TANGGAL UPDATE');
        $sheet->setCellValue('D1', 'No.');

        // =============================
        // HEADER FIELD EXPORT
        // =============================
        $colIndex = 4; // E
        foreach ($field_exports as $field) {
            $sheet->setCellValue($abjads[$colIndex] . '1', $field['description_field']);
            $sheet->setCellValue($abjads[$colIndex] . '2', $field['code_table_field']);
            $colIndex++;
        }

        // =============================
        // ISI DATA
        // =============================
        $rowIndex = 5;
        $num = 1;
        $data_export = isset($data_table['join_data']) ? $data_table['join_data'] : $data_table['data'];
        foreach ($data_export ?? [] as $code_data => $item_export) {

            $sheet->setCellValue('D' . $rowIndex, $num);

            $fieldCol = 4;

            foreach ($field_exports as $field) {
                $code = $field['code_field'];

                if (!empty($item_export[$code])) {
                    $value = $item_export[$code]['text_data'];
                    $sheet->setCellValue($abjads[$fieldCol] . $rowIndex, $value);
                }

                $fieldCol++;
            }

            $num++;
            $rowIndex++;
        }

        // =============================
        // STYLE HEADER
        // =============================
        $lastCol = $abjads[$colIndex - 1];

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

        $sheet->getStyle("A1:{$lastCol}3")->applyFromArray($headerStyle);

        // =============================
        // BORDER UNTUK SEMUA DATA
        // =============================
        $sheet->getStyle("A1:{$lastCol}" . ($rowIndex - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        // =============================
        // AUTO SIZE
        // =============================
        for ($i = 1; $i <= $colIndex; $i++) {
            $sheet->getColumnDimension($abjads[$i])->setAutoSize(true);
        }

        // =============================
        // FREEZE HEADER
        // =============================
        $sheet->freezePane('A4');

        // =============================
        // SAVE FILE KE FOLDER
        // =============================
        $folder = 'file/export/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $name = $folder . $database_datatable . '-' . rand(100, 9999) . '-file.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($name);

        // =============================
        // RETURN JSON (seperti permintaan)
        // =============================
        return ResponseFormatter::ResponseJson($name, 'export database', 200);
    }







    // ============================================
    // STORE SLIP GAJI
    // ============================================
    public function slipStore(Request $request)
    {
        $files_file = $request->file('file');

        $split_year_month = explode(" ", $request['month-year']);
        $month = ResponseFormatter::monthSort($split_year_month[0]);
        $year  = $split_year_month[1];

        $parent_path = public_path('file/slips/');
        if (!file_exists($parent_path)) {
            mkdir($parent_path, 0755, true);
        }

        $files = [];

        foreach ($files_file as $item_file) {

            $original_name = $item_file->getClientOriginalName();
            $extension = $item_file->getClientOriginalExtension();
            $filenameWithoutExtension = pathinfo($original_name, PATHINFO_FILENAME);

            $employee_uuid = ResponseFormatter::toUUID($filenameWithoutExtension);
            $new_filename = Str::uuid() . '.' . $extension;

            // pindahkan file
            $item_file->move($parent_path, $new_filename);

            $code_file = $employee_uuid . '-' . $year . '-' . $month;

            $data = [
                'nrp' => $employee_uuid,
                'code_file'     => $code_file,
                'year'          => $year,
                'month'         => $month,
                'original_file' => $new_filename,
            ];

            Slip::updateOrCreate(
                ['code_file' => $code_file],
                $data
            );

            $files[] = $data;
        }

        return ResponseFormatter::ResponseJson($files, 'success', 200);
    }

     public function showSlip($filename)
    {
        // Lokasi file sebenarnya (pilih salah satu)
        // $path = storage_path('app/public/file/slips/' . $filename);
        // atau kalau memang di public
        $path = public_path('file/slips/' . $filename);

        if (!File::exists($path)) {
            return response()->json([
                'message' => 'File PDF tidak ditemukan atau tidak bisa diakses GET'
            ], 404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ]);
    }

    
}
