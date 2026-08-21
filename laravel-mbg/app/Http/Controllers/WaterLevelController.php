<?php

namespace App\Http\Controllers;

use App\Helpers\AssetHelper;
use App\Models\WaterLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WaterLevelController extends Controller
{
    /**
     * Halaman Water Level
     */
    public function index()
    {
        return view('app.feature.water_level.water_level');
    }


    /**
     * Data Water Level
     *
     * Digunakan oleh Chart.js dan tabel.
     */
    public function data(Request $request)
    {
        $query = WaterLevel::query();

        if (
            $request->filled('lokasi') &&
            $request->lokasi !== 'ALL'
        ) {
            $query->where(
                'lokasi',
                $request->lokasi
            );
        }

        $data = $query
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }


    /**
     * Simpan Water Level
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'tanggal' => [
                'required',
                'date',
            ],

            'jam' => [
                'required',
                'date_format:H:i',
            ],

            'lokasi' => [
                'required',
                'string',
                'in:PT. SRI,PT. MB',
            ],

            'tinggi' => [
                'required',
                'numeric',
                'min:0',
            ],

            'foto_panorama' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],

            'foto_draft_meter' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Nama file
        |--------------------------------------------------------------------------
        */

        $fotoPanorama = null;

        $fotoDraftMeter = null;


        /*
        |--------------------------------------------------------------------------
        | Upload Panorama
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile('foto_panorama')
        ) {

            $file =
                $request->file(
                    'foto_panorama'
                );


            /*
            | Nama file:
            |
            | panorama_YYYYMMDD_HHMMSS.ext
            */

            $extension =
                strtolower(
                    $file->getClientOriginalExtension()
                );


            $filename =
                'panorama_' .
                date('Ymd_His') .
                '_' .
                uniqid() .
                '.' .
                $extension;


            $fotoPanorama =
                AssetHelper::upload(
                    $file,
                    'water_level/panorama',
                    $filename
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Upload Draft Meter
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile('foto_draft_meter')
        ) {

            $file =
                $request->file(
                    'foto_draft_meter'
                );


            /*
            | Nama file:
            |
            | draft_meter_YYYYMMDD_HHMMSS.ext
            */

            $extension =
                strtolower(
                    $file->getClientOriginalExtension()
                );


            $filename =
                'draft_meter_' .
                date('Ymd_His') .
                '_' .
                uniqid() .
                '.' .
                $extension;


            $fotoDraftMeter =
                AssetHelper::upload(
                    $file,
                    'water_level/draft_meter',
                    $filename
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Simpan database
        |--------------------------------------------------------------------------
        */

        $waterLevel =
            WaterLevel::create([

                'tanggal' =>
                $validated['tanggal'],

                'jam' =>
                $validated['jam'],

                'lokasi' =>
                $validated['lokasi'],

                'tinggi' =>
                $validated['tinggi'],

                'foto_panorama' =>
                $fotoPanorama,

                'foto_draft_meter' =>
                $fotoDraftMeter,

            ]);


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'message' =>
            'Data water level berhasil disimpan.',

            'data' =>
            $waterLevel,

        ], 201);
    }
}
