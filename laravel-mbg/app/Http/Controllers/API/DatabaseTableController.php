<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\DatabaseData;
use App\Models\DatabaseDataSource;
use App\Models\DatabaseField;
use App\Models\DatabaseFieldShow;
use App\Models\DatabaseTable;
use Illuminate\Http\Request;

class DatabaseTableController extends Controller
{
    public function getDatadataTable()
    {
        $data = DatabaseTable::all();
        return ResponseFormatter::ResponseJson($data, 'message', 200);
    }

    public function storeDatadataTable(Request $request)
    {
        $data = $request->all();
        // Process the data as needed, e.g., save to database
        // For demonstration, we'll just return the received data
        $request_data = $request->data;
        // return ResponseFormatter::ResponseJson($request_data, "store database", 200);
        $code_table = ResponseFormatter::toUUID($request_data['description_table']);
        $store_database_table = DatabaseTable::updateOrCreate([
            'code_table' => ResponseFormatter::toUUID($request_data['description_table'])
        ], [
            'parent_table' => (!empty($request_data['parent_table'])) ? $request_data['parent_table'] : null,
            'primary_table' => ResponseFormatter::toUUID($request_data['primary_table']),
            'menu_table' => $request_data['menu_table'],
            'description_table' => $request_data['description_table'],
        ]);
        $countSortField = 0;
        if (!empty($request_data['parent_table'])) {
            $table_parent = DatabaseTable::where('code_table', $request_data['parent_table'])->first();
            $store_database_fields = DatabaseField::updateOrCreate([
                'full_code_field' => $store_database_table->code_table . '-' . $table_parent->primary_table, //CODE-TABLE-FIELD-PRIMARY-CODE-TABLE
            ], [
                'code_table_field' => $store_database_table->code_table,
                'description_field' => ResponseFormatter::toUUID($request_data['primary_table']),
                'type_data_field' => 'HIDDEN',
                'visibility_data_field' => 'hide',
                'level_data_field' => 1,
                'code_field' => $table_parent->primary_table,
                'full_code_field' => $store_database_table->code_table . '-' . ResponseFormatter::toUUID($table_parent->primary_table),
                'sort_field' => $countSortField,
            ]);
            $countSortField++;
        }

        foreach ($request_data['fields'] as $field) {
            $store_database_fields = DatabaseField::updateOrCreate([
                'full_code_field' => $store_database_table->code_table . '-' . ResponseFormatter::toUUID($field['description_field'])
            ], [
                'code_table_field' => $store_database_table->code_table,
                'description_field' => $field['description_field'],
                'type_data_field' => $field['type_data_field'],
                'visibility_data_field' => $field['visibility_data_field'],
                'level_data_field' => $field['level_data_field'],
                'code_field' => ResponseFormatter::toUUID($field['description_field']),
                'full_code_field' => $store_database_table->code_table . '-' . ResponseFormatter::toUUID($field['description_field']),
                'sort_field' => $countSortField,
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

        return ResponseFormatter::ResponseJson('stored', "store database", 200);
        return ResponseFormatter::ResponseJson($data, 'Data table stored successfully', 200);
    }
}
