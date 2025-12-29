<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\GroupForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupFormController extends Controller
{
    // public function getDatadataTable(Request $request)
    // {


    //     $request->validate([
    //         'table_name' => 'required|string',
    //     ]);



    //     $tableName = $request->table_name;


    //     $data = GroupForm::all();
    //     // if (! preg_match('/^[A-Za-z0-9_]+$/', $tableName)) {
    //     //     abort(400, 'Invalid table name');
    //     // }

    //     // return ResponseFormatter::ResponseJson($tableName, 'message', 200);
    //     $data = DB::table($tableName)->get();
    //     // return $data;
    //     return ResponseFormatter::ResponseJson($data, 'message', 200);
    // }

    public function getDatadataTable(Request $request)
    {
        // 1. Validasi dasar
        $request->validate([
            'table_name' => 'required|string',
        ]);

        $tableName = $request->table_name;

        // 2. Whitelist tabel (WAJIB demi keamanan)

        // 3. Ambil semua request kecuali table_name
        $filters = collect($request->all())->except('table_name');

        // 4. Query dinamis
        $query = DB::table($tableName);

        foreach ($filters as $field => $value) {
            if (!is_null($value) && $value !== '') {
                $query->where($field, $value);
            }
        }

        $data = $query->get();

        return ResponseFormatter::ResponseJson($data, 'success', 200);
    }


    public function storeDatadataTable(Request $request)
    {
        $header = $request->headers->all();
        $content = $request->all();
        $file = [];

        $data_complite = [
            'header' => $header,
            'content'   => $content,
            'file'  => $file
        ];

        // field yang menjadi primary key
        $field_uuid = 'description';

        if (empty($content['uuid'])) {
            $content['uuid'] = ResponseFormatter::toUUID($content[$field_uuid]);
        }

        $store = GroupForm::updateOrCreate(['uuid' => $content['uuid']], $content);
        $status = $store->wasRecentlyCreated ? 'created' : 'updated';
        $data_return = [
            'status_data' => $status,
            'data_complite' => $store
        ];
        return ResponseFormatter::ResponseJson($data_return, 'Store storeDatadataTable', 200);
    }

    public function deleteDatadataTable(Request $request)
    {
        $request->validate([
            'id' => 'required' // Sesuaikan dengan nama tabel
        ]);

        // Cari data berdasarkan ID
        $menu = GroupForm::find($request->id);

        if (!$menu) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found'
            ], 404);
        }

        // Hapus data
        $menu->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data deleted successfully'
        ], 200);
    }
}
