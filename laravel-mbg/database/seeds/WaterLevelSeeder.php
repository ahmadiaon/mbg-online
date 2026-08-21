<?php

use Illuminate\Database\Seeder;
use App\Models\WaterLevel;
use Carbon\Carbon;

class WaterLevelSeeder extends Seeder
{
    public function run()
    {
        $data = [

            // =====================================================
            // PT. SRI
            // =====================================================

            [
                'tanggal' => '2026-08-18',
                'jam' => '08:00',
                'lokasi' => 'PT. SRI',
                'tinggi' => 105,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-18',
                'jam' => '12:00',
                'lokasi' => 'PT. SRI',
                'tinggi' => 110,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-18',
                'jam' => '16:00',
                'lokasi' => 'PT. SRI',
                'tinggi' => 114,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-19',
                'jam' => '08:00',
                'lokasi' => 'PT. SRI',
                'tinggi' => 108,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-19',
                'jam' => '12:00',
                'lokasi' => 'PT. SRI',
                'tinggi' => 116,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-19',
                'jam' => '16:00',
                'lokasi' => 'PT. SRI',
                'tinggi' => 121,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-20',
                'jam' => '08:00',
                'lokasi' => 'PT. SRI',
                'tinggi' => 115,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-20',
                'jam' => '12:00',
                'lokasi' => 'PT. SRI',
                'tinggi' => 122,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-20',
                'jam' => '16:00',
                'lokasi' => 'PT. SRI',
                'tinggi' => 126,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-21',
                'jam' => '08:00',
                'lokasi' => 'PT. SRI',
                'tinggi' => 118,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-21',
                'jam' => '12:00',
                'lokasi' => 'PT. SRI',
                'tinggi' => 128,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-21',
                'jam' => '16:00',
                'lokasi' => 'PT. SRI',
                'tinggi' => 125,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],


            // =====================================================
            // PT. MB
            // =====================================================

            [
                'tanggal' => '2026-08-18',
                'jam' => '08:00',
                'lokasi' => 'PT. MB',
                'tinggi' => 92,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-18',
                'jam' => '12:00',
                'lokasi' => 'PT. MB',
                'tinggi' => 96,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-18',
                'jam' => '16:00',
                'lokasi' => 'PT. MB',
                'tinggi' => 99,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-19',
                'jam' => '08:00',
                'lokasi' => 'PT. MB',
                'tinggi' => 95,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-19',
                'jam' => '12:00',
                'lokasi' => 'PT. MB',
                'tinggi' => 101,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-19',
                'jam' => '16:00',
                'lokasi' => 'PT. MB',
                'tinggi' => 104,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-20',
                'jam' => '08:00',
                'lokasi' => 'PT. MB',
                'tinggi' => 98,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-20',
                'jam' => '12:00',
                'lokasi' => 'PT. MB',
                'tinggi' => 106,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-20',
                'jam' => '16:00',
                'lokasi' => 'PT. MB',
                'tinggi' => 109,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-21',
                'jam' => '08:00',
                'lokasi' => 'PT. MB',
                'tinggi' => 103,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-21',
                'jam' => '12:00',
                'lokasi' => 'PT. MB',
                'tinggi' => 110,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

            [
                'tanggal' => '2026-08-21',
                'jam' => '16:00',
                'lokasi' => 'PT. MB',
                'tinggi' => 108,
                'foto_panorama' => null,
                'foto_draft_meter' => null,
            ],

        ];


        foreach ($data as $item) {

            WaterLevel::create($item);

        }
    }
}