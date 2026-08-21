<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Recruitment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecruitmentController extends Controller
{
    //
    public function store(Request $request)
    {
        // Jika ada id_lamaran, berarti update status
        if (!empty($request->id_lamaran)) {
            $Q_store = Recruitment::updateOrCreate(
                ['id' => $request->id_lamaran],
                ['status' => $request->val_lamaran]
            );
            if ($Q_store) {
                return ResponseFormatter::ResponseJson('Success', 'Success store data recruitment', 200);
            }
            return ResponseFormatter::ResponseJson('failed', 'Gagal Store data recruitment', 200);
        }

        // Validasi minimal (sesuaikan dengan kebutuhan)
        $request->validate([
            'nik_ktp' => 'required|string',
            'full_name' => 'required|string',
            'position' => 'required|string',
            'uploaded_file' => 'required', // bisa URL atau file
        ]);

        // Siapkan data untuk disimpan
        $data = [
            'address_description' => $request->address_description ?? null,
            'provinsi'            => $request->text_provinsi ?? null,
            'kabupaten'           => $request->text_kabupaten ?? null,
            'kecamatan'           => $request->text_kecamatan ?? null,
            'position'            => $request->position ?? null,
            'status'              => $request->status ?? 'Diajukan',
            'email'               => $request->email ?? null,
            'phone_number'        => $request->phone_number ?? null,
            'full_name'           => $request->full_name ?? null,
            'nik_ktp'             => $request->nik_ktp,
            'time_propose'        => Carbon::now()->format('Y-m-d'),
        ];

        if ($request->hasFile('uploaded_file')) {
            // Mode 1: File langsung dikirim (fallback)
            $the_file = $request->file('uploaded_file');
            $file_extension = $the_file->getClientOriginalExtension();
            $file_name_change = ResponseFormatter::toUUID($request->nik_ktp) . '.' . $file_extension;
            $the_file->move(public_path('file/recruitments'), $file_name_change);
            $data['file'] = $file_name_change;
        } else {
            // Mode 2: Nama file dari server assets (frontend baru)
            $uploadedFile = $request->input('uploaded_file');

            // Jika ternyata masih berupa URL (misal dari versi sebelumnya), ambil nama file saja
            if (filter_var($uploadedFile, FILTER_VALIDATE_URL)) {
                $uploadedFile = basename($uploadedFile); // menjadi "6303050503870004.pdf"
            }

            // Simpan hanya nama file (bukan URL lengkap)
            $data['file'] = $uploadedFile;
        }

        // Simpan atau update berdasarkan NIK
        $Q_store = Recruitment::updateOrCreate(
            ['nik_ktp' => $request->nik_ktp],
            $data
        );

        if ($Q_store) {
            return ResponseFormatter::ResponseJson($Q_store, 'Success store data recruitment', 200);
        }
        return ResponseFormatter::ResponseJson(null, 'Gagal menyimpan data', 500);
    }


    public function getDataRecruitment(Request $request)
    {
        $Q_get_data = Recruitment::where('nik_ktp', $request->nik_ktp)->get();


        if (!empty($Q_get_data)) {
            return ResponseFormatter::ResponseJson($Q_get_data, 'Get Data ' . $request->nik_ktp, 200);
        }
        return ResponseFormatter::ResponseJson(null, 'Get Data ' . $request->nik_ktp, 200);
    }

    public function getData(Request $request)
    {
        $Q_get_data = Recruitment::orderBy('time_propose', 'DESC')->get();
        $array_data = [];
        foreach ($Q_get_data as $get_data) {
            $array_data[$get_data->id] = $get_data;
        }
        return ResponseFormatter::ResponseJson($array_data, 'Get Data Recruitments', 200);
    }
}
