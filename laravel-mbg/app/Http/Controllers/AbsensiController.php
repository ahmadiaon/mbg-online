<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Absensi;
use App\Models\DatabaseData;
use App\Models\User;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use function Symfony\Component\String\s;

class AbsensiController extends Controller
{
    //
    public function meAbsensi()
    {
        return view('app.personal.kehadiran.meAbsensi');
    }

    public function manageabsensi()
    {
        return view('app.manage.manageAbsensi');
    }

    function getDataAbsensi(Request $request)
    {


        $filter_absen = [];
        $filter_absen = json_decode($request->on_filter, true);
        $filter_absen['ON_FILTER'] = json_decode($request->on_filter, true);

        $filter_absen['is_self'] = $request->is_self ?? false;
        $user = User::where('auth_login', session('auth_token'))->first();


        // return ResponseFormatter::ResponseJson($filter_absen, 'All Absensi Data', 200);

        if ($request->is_self == 'self') {
            // return ResponseFormatter::ResponseJson($filter_absen,'$request->is_self', 200);
            $Q_data_absensi = Absensi::where('absensis.date', '>=',  $filter_absen['date_start'])
                ->where('absensis.date', '<=',  $filter_absen['date_end'])
                ->where('absensis.nrp',  ResponseFormatter::toUUID($user->nrp));
        } else {
            $Q_data_absensi = Absensi::where('absensis.date', '>=',  $filter_absen['date_start'])
                ->where('absensis.date', '<=',  $filter_absen['date_end']);
        }


        $Q_data_absensi = $Q_data_absensi->get(['nrp', 'absen_description', 'cek_log', 'date', 'entry', 'mid', 'exit', 'late_minutes', 'late_points', 'shift', 'status_absen_uuid', 'working_hours', 'uuid']);


        foreach ($Q_data_absensi as $absensi) {
            $arr_data_absen[$absensi->nrp][$absensi->date] = $absensi;
        }
        // $data_to_return['data_absensi'][$filter_absen['nik_employee']] = [];
        // if (!empty($arr_data_absen[$filter_absen['nik_employee']])) {
        //     $data_to_return['data_absensi'][$filter_absen['nik_employee']] = $arr_data_absen[$filter_absen['nik_employee']];
        // }
        if (!empty($filter_absen['NRP'])) {
            foreach ($filter_absen['NRP'] as $I_karyawan) {
                $I_karyawan = ResponseFormatter::toUUID($I_karyawan);
                if (!empty($arr_data_absen[$I_karyawan])) {
                    $data_to_return['data_absensi'][$I_karyawan]['nrp'] = $I_karyawan;
                    $data_to_return['data_absensi'][$I_karyawan]['absensies'] = $arr_data_absen[$I_karyawan];
                } else {
                    $data_to_return['data_absensi'][$I_karyawan]['nrp'] = $I_karyawan;
                    $data_to_return['data_absensi'][$I_karyawan]['absensies'] = [];
                }
            }
        }

        $data_to_return['request'] = $filter_absen;
        return ResponseFormatter::ResponseJson($data_to_return, 'All Absensi Data', 200);
        return ResponseFormatter::ResponseJson($filter_absen, 'All Absensi Data', 200);








        $filter_absen = $request->default_filter_absensi;
        $auth_login = $request->header('x-auth-login');
        $filter_absen['auth_login'] = $auth_login;
        $user = User::where('auth_login', $auth_login)->first();
        $filter_absen['nik_employee'] = $user->nik_employee;

        $data_to_return['data_ketidakhadiran'] = [];
        $NRP = null;
        $data_absen_return = [];
        // 

        $Q_data_absensi = Absensi::where('absensis.date', '>=',  $filter_absen['date_start'])
            ->where('absensis.date', '<=',  $filter_absen['date_end']);


        if (!empty($filter_absen['from'])) {
            $filter_absen['KARYAWAN'] = [$NRP];
            $Q_data_absensi = $Q_data_absensi->where('absensis.employee_uuid', $filter_absen['nik_employee']);
        }

        $Q_data_absensi = $Q_data_absensi->get(['nrp', 'absen_description', 'cek_log', 'date', 'entry', 'mid', 'exit', 'late_minutes', 'late_points', 'shift', 'status_absen_uuid', 'working_hours', 'uuid']);

        foreach ($Q_data_absensi as $absensi) {
            $arr_data_absen[$absensi->employee_uuid][$absensi->date] = $absensi;
        }
        $data_to_return['data_absensi'][$filter_absen['nik_employee']] = [];
        if (!empty($arr_data_absen[$filter_absen['nik_employee']])) {
            $data_to_return['data_absensi'][$filter_absen['nik_employee']] = $arr_data_absen[$filter_absen['nik_employee']];
        }
        if (!empty($filter_absen['KARYAWAN'])) {
            foreach ($filter_absen['KARYAWAN'] as $I_karyawan) {
                if (!empty($arr_data_absen[$I_karyawan])) {
                    $data_to_return['data_absensi'][$I_karyawan] = $arr_data_absen[$I_karyawan];
                }
            }
        }

        // $data_data_ketidakhadiran = [];
        // $data_to_return['data_ketidakhadiran'] = [];
        // $data_data_ketidakhadiran =  DatabaseDataKehadiran::getData($filter_absen); //[nrp][code]


        // // return ResponseFormatter::ResponseJson($data_data_ketidakhadiran, 'All Absensi Data', 200);
        // if (!empty($data_data_ketidakhadiran[$filter_absen['nik_employee']])) {
        //     $data_to_return['data_ketidakhadiran'] =  array_merge($data_to_return['data_ketidakhadiran'], $data_data_ketidakhadiran[$filter_absen['nik_employee']]);
        // }

        // if ($data_data_ketidakhadiran) {
        //     foreach ($filter_absen['KARYAWAN'] as $I_karyawan) {
        //         if (!empty($data_data_ketidakhadiran[$I_karyawan])) {
        //             $data_to_return['data_ketidakhadiran'] =  array_merge($data_to_return['data_ketidakhadiran'], $data_data_ketidakhadiran[$I_karyawan]);
        //         }
        //     }
        // }

        // $data_to_return['data_persetujuan'] = DatabaseDataPersetujuan::getDataPersetujuan('KEHADIRAN', null);

        // return ResponseFormatter::ResponseJson(count($data_to_return['data_absensi']), 'All Absensi Data', 200);
        return ResponseFormatter::ResponseJson($data_to_return, 'All Absensi Dataaaaaaaaaaa', 200);

        return ResponseFormatter::ResponseJson(json_encode($data_to_return), 'All Absensi Data', 200);
    }

    static public function findClosestShift($dateToFind, $shiftData)
    {
        // Ubah tanggal yang akan dicari ke format DateTime agar mudah untuk dibandingkan
        $dateToFind = new DateTime($dateToFind);
        $closestDate = null;
        $closestShift = null;

        foreach ($shiftData as $date => $shiftInfo) {
            $currentDate = new DateTime($date);

            // Pastikan hanya tanggal yang lebih kecil atau sama dengan tanggal yang dicari
            if ($currentDate <= $dateToFind) {
                // Jika belum ada closestDate, atau jika currentDate lebih mendekati ke dateToFind, update
                if (!$closestDate || $currentDate > $closestDate) {
                    $closestDate = $currentDate;
                    $closestShift = $shiftInfo;
                }
            }
        }

        return $closestShift; // Nilai shift terdekat yang ditemukan
    }

    static function processFingerTimesOld($finggerTimes, $timeConfig, $isFriday = false)
    {
        $timezone = 'Asia/Jakarta';
        $F = [];
        // Mengonversi string waktu menjadi objek Carbon
        $times = array_map(function ($time) use ($timezone) {
            $F[] = $time;
            return Carbon::createFromFormat('H:i', $time, $timezone);
        }, $finggerTimes);

        // Mengurutkan waktu dari yang paling awal
        usort($times, function ($a, $b) {
            return $a->lt($b) ? -1 : 1;
        });

        // Mengatur waktu konfigurasi
        $entryStart = Carbon::createFromFormat('H:i', $timeConfig['entryStart'], $timezone);
        $lateToleranceMinutes = $timeConfig['lateToleranceMinutes'];
        $exitLimit = Carbon::createFromFormat('H:i', $timeConfig['exitLimit'], $timezone);
        $restStart = Carbon::createFromFormat('H:i', $timeConfig['restStart'], $timezone)->setTimezone($timezone);
        $restEnd = $isFriday ? Carbon::createFromFormat('H:i', $timeConfig['restEndFriday'], $timezone) : Carbon::createFromFormat('H:i', $timeConfig['restEnd'], $timezone);

        $restEnd = Carbon::parse($restEnd)->setTimezone($timezone);
        $entry = null;
        $mid = null;
        $exit = null;
        $latePoints = 0;

        // 1. Tentukan entry (ambil waktu terkecil sebelum jam istirahat dimulai)
        foreach ($times as $key => $time) {
            if ($time->lt($restStart)) {
                $entry = $time;  // Simpan waktu terkecil sebelum batas waktu istirahat
                unset($times[$key]); // Hapus dari array setelah digunakan
                // Hitung poin keterlambatan jika entry melewati batas toleransi
                if ($time->gt($entryStart->copy()->addMinutes($lateToleranceMinutes))) {
                    $minutesLate = $time->diffInMinutes($entryStart);
                    // Hitung poin keterlambatan
                    $latePoints += max(0, floor(($minutesLate - $lateToleranceMinutes) / 60) + 1); // Hitung poin keterlambatan
                }
                break;
            }
        }

        // 2. Tentukan mid (antara restStart dan restEnd)
        foreach ($times as $key => $time) {
            if ($time->between($restStart, $restEnd)) {
                $mid = $time;
                unset($times[$key]); // Hapus dari array setelah digunakan
                break;
            }
        }



        // 3. Tentukan exit (ambil waktu terbesar setelah restEnd)
        foreach (array_reverse($times) as $key => $time) {
            if ($time->gt($restEnd)) {
                // Bandingkan jarak exit ke restEnd dan exitLimit
                $diffToRestEnd = $time->diffInMinutes($restEnd);
                $diffToExitLimit = $time->diffInMinutes($exitLimit);

                if ($diffToRestEnd < $diffToExitLimit) {
                    // Jika lebih dekat ke restEnd dan mid kosong, anggap sebagai mid
                    if (!$mid) {
                        $mid = $time;
                    }
                } else {
                    // Jika lebih dekat ke exitLimit, gunakan sebagai exit
                    $exit = $time;
                }

                $times = array_filter($times, function ($item) use ($time) {
                    return $item !== $time;
                });
                break;
            }
        }
        // return $mid;

        // return $times;

        // 4. Jika tidak ada entry, hitung jam yang hilang
        if (!$entry) {
            $hoursMissed = $entryStart->diffInHours($restStart);
            $latePoints += min($hoursMissed, 10);
        }




        // 5. Jika mid kosong, ambil nilai yang lebih besar dari entry setelah restEnd
        if (!$mid) {
            $closestMid = null;
            $minDifference = null;

            foreach ($times as $key => $time) {
                // Cek apakah waktu tersebut lebih besar dari restEnd, dan bukan entry atau exit
                if ($time->gt($restEnd) && (!$entry || $time->format('H:i') !== $entry) && (!$exit || $time->format('H:i') !== $exit)) {
                    $diffToRestEnd = $time->diffInMinutes($restEnd);
                    $diffToExitLimit = $time->diffInMinutes($exitLimit);

                    if ($diffToRestEnd < $diffToExitLimit) {
                        // Jika lebih dekat ke restEnd dan mid kosong, anggap sebagai mid
                        $mid = $time;
                        // Hitung selisih waktu dengan restEnd
                        $difference = $time->diffInMinutes($restEnd);

                        // Tentukan waktu yang paling mendekati restEnd
                        if ($minDifference === null || $difference < $minDifference) {
                            $closestMid = $time;
                            $minDifference = $difference;
                        }
                    }
                }
            }
            $mid = $closestMid;
        }

        // 5. Cek keterlambatan mid
        // if ($mid) {
        //     $midLateLimit = $restEnd->copy()->addMinutes($lateToleranceMinutes); // batas akhir mid
        //     if ($mid->gt($midLateLimit)) {
        //         $minutesLate = $midLateLimit->diffInMinutes($mid);
        //         if ($minutesLate > 0) {
        //             $latePoints += 1; // Tambah 1 poin jika terlambat lebih dari toleransi
        //             $latePoints += floor($minutesLate / 60); // Tambah poin tambahan per 60 menit
        //         }
        //     }
        // }


        // 6. Cek jika exit sebelum jam 17:00
        if ($exit && $exit->lt($exitLimit)) {
            $earlyLeaveMinutes = $exitLimit->diffInMinutes($exit);
            $latePoints += min(floor($earlyLeaveMinutes / 60), 10);
        }

        // 7. Jika exit kosong dan mid ada, tambahkan 2 poin keterlambatan
        if (!$exit && $mid) {
            $latePoints += 2; // Tambah poin karena tidak ada exit
        }

        // 8. Jika mid dan exit kosong, tambahkan 5 poin keterlambatan
        if (!$mid && !$exit) {
            $latePoints += 5; // Tambah poin karena tidak ada mid dan exit
        }

        // 9. Hitung waktu bekerja
        $workingHours = 0;

        if ($entry) {
            if ($entry->lt($entryStart)) {
                $w_entry = $entryStart;
            } else {
                $w_entry = $entry;
            }
            $workingHours = $restStart->diffInMinutes($w_entry);
        }

        if ($mid && $exit) {
            $w_mid = null;
            $w_exit = null;
            if ($mid->lt($restEnd)) {
                $w_mid = $restEnd;
            } else {
                $w_mid = $mid;
            }

            if ($exit->gt($exitLimit)) {
                $w_exit = $exitLimit;
            } else {
                $w_exit = $exit;
            }

            $workingHours += $w_mid->diffInMinutes($w_exit);
        } else if ($mid) {
            if ($mid->lt($restEnd)) {
                $workingHours += $restEnd->diffInMinutes($exitLimit);
            } else {
                $workingHours += $mid->diffInMinutes($exitLimit);
            }
        } else if ($exit) {
            if ($exit->gt($exitLimit)) {
                $workingHours += $restEnd->diffInMinutes($exitLimit);
            } else {
                $workingHours += $restEnd->diffInMinutes($exit);
            }
        }

        return [
            'status_absen' => $latePoints ? "TA" : 'DS',
            'entry' => $entry ? $entry->format('H:i') : null,
            'mid' => $mid ? $mid->format('H:i') : null,
            'cek_log' => $F,
            'exit' => $exit ? $exit->format('H:i') : null,
            'latePoints' => $latePoints,
            'workingHours' => $workingHours / 60, // Waktu bekerja dalam jam
            'lateMinutes' => (600 - $workingHours) > 0 ? (600 - $workingHours) : 0,
        ];
    }




    public static function processFingerTimes($fingerTimes, $timeConfig, $isFriday = false)
    {
        $timezone = 'Asia/Jakarta';

        $times = array_map(fn($t) => Carbon::createFromFormat('H:i', $t, $timezone), $fingerTimes);
        usort($times, fn($a, $b) => $a->lt($b) ? -1 : 1);

        $entryStart   = Carbon::createFromFormat('H:i', $timeConfig['entryStart'], $timezone);
        $lateTolerance = $timeConfig['lateToleranceMinutes'];
        $exitLimit    = Carbon::createFromFormat('H:i', $timeConfig['exitLimit'], $timezone);
        $restStart    = Carbon::createFromFormat('H:i', $timeConfig['restStart'], $timezone);
        $restEnd      = $isFriday
            ? Carbon::createFromFormat('H:i', $timeConfig['restEndFriday'], $timezone)
            : Carbon::createFromFormat('H:i', $timeConfig['restEnd'], $timezone);

        $entry = null;
        $mid   = null;
        $exit  = null;
        $latePoints = 0;
        $remaining = $times;

        // 1. Cari ENTRY
        foreach ($remaining as $index => $time) {
            if ($time->lt($restStart)) {
                $entry = $time;
                unset($remaining[$index]);
                $toleratedStart = $entryStart->copy()->addMinutes($lateTolerance);
                if ($entry->gt($toleratedStart)) {
                    $minutesLate = $entry->diffInMinutes($entryStart);
                    $latePoints += max(0, floor(($minutesLate - $lateTolerance) / 60) + 1);
                }
                break;
            }
        }

        if (!$entry) {
            $hoursMissed = $entryStart->diffInHours($restStart);
            $latePoints += min($hoursMissed, 10);
        }

        // 2. Kelompokkan sisa waktu
        $during = [];
        $after  = [];
        foreach ($remaining as $time) {
            if ($time->between($restStart, $restEnd)) {
                $during[] = $time;
            } elseif ($time->gt($restEnd)) {
                $after[] = $time;
            }
        }
        usort($during, fn($a, $b) => $a->lt($b) ? -1 : 1);
        usort($after,  fn($a, $b) => $a->lt($b) ? -1 : 1);

        // 3. MID dari during
        if (!empty($during)) {
            $mid = $during[0];
        }

        // 4. Tentukan EXIT dan MID dari after
        if (!empty($after)) {
            // Hitung titik tengah antara restEnd dan exitLimit
            $totalMinutes = $restEnd->diffInMinutes($exitLimit);
            $threshold = $restEnd->copy()->addMinutes($totalMinutes / 2);

            if (count($after) == 1) {
                $time = $after[0];
                if ($time->lte($threshold)) {
                    $mid = $time; // lebih dekat ke restEnd
                } else {
                    $exit = $time; // lebih dekat ke exitLimit
                }
            } else {
                // Lebih dari satu: yang terkecil bisa jadi mid, yang terbesar jadi exit
                $first = $after[0];
                $last = end($after);

                // Cek apakah first di bawah threshold? Jika ya, jadikan mid
                if ($first->lte($threshold) && !$mid) {
                    $mid = $first;
                    $exit = $last;
                } else {
                    $exit = $last; // tidak ada mid
                }
            }
        }

        // 5. Poin tambahan
        if ($exit && $exit->lt($exitLimit)) {
            $earlyMinutes = $exitLimit->diffInMinutes($exit);
            $latePoints += min(floor($earlyMinutes / 60), 10);
        }

        if (!$exit) {
            $latePoints += $mid ? 2 : 5;
        }

        // 6. Hitung jam kerja
        $workMinutes = 0;

        if ($entry) {
            $workStart = $entry->lt($entryStart) ? $entryStart : $entry;
            $workMinutes += $workStart->diffInMinutes($restStart);
        }

        $afterStart = $mid ?? $restEnd;
        $afterEnd   = $exit ?? $exitLimit;

        if ($afterStart->lt($afterEnd)) {
            $workMinutes += $afterStart->diffInMinutes($afterEnd);
        }

        $status = $latePoints > 0 ? 'TA' : 'DS';
        $targetMinutes = 600; // 10 jam

        return [
            'status_absen'  => $status,
            'entry'         => $entry ? $entry->format('H:i') : null,
            'mid'           => $mid ? $mid->format('H:i') : null,
            'exit'          => $exit ? $exit->format('H:i') : null,
            'latePoints'    => $latePoints,
            'workingHours'  => round($workMinutes / 60, 2),
            'lateMinutes'   => max(0, $targetMinutes - $workMinutes),
        ];
    }

    public function importAbsensi(Request $request)
    {
        ini_set('max_execution_time', 2200);       // Set waktu eksekusi maksimum menjadi 20 menit
        $the_file = $request->file('uploaded_file');
        $data_database = session('data_database');
        $createSpreadsheet = new spreadsheet();
        $createSheet = $createSpreadsheet->getActiveSheet();
        $year_start = '';
        $month_start = '';
        $dataaa = [];
        $all_datas = [];
        $session_data = session('DATABASE');

        // return ResponseFormatter::toJson($session_data, 'here');



        try {
            $spreadsheet = IOFactory::load($the_file->getRealPath());
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();


            $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ'];

            if ($sheet->getCell('A2')->getValue() == 'Excel') { //File dari Excell berbentuk tabel full bulan
                $month_absensi = $sheet->getCell('H19')->getValue();
                $arr_month_absensi = explode(' ', $month_absensi);
                // return ResponseFormatter::toJson($arr_month_absensi, 'store from excel report');
                $year =  $arr_month_absensi[4];
                $month = str_pad(ResponseFormatter::monthSort($arr_month_absensi[2]), 2, '0', STR_PAD_LEFT);
                $year_month = $year . '-' . $month;

                $last_day = ResponseFormatter::getEndDay($year . '-' . $month);

                $start_date = ResponseFormatter::getStartDayFromDate($year_month . '-01');
                $end_date = ResponseFormatter::getEndDayFromDate($year_month . '-' . $last_day);

                $data_absensi_old_ceklog = Absensi::where('absensis.date', '>=', $start_date)
                    ->where('absensis.date', '<=', $end_date)
                    ->get();


                $array_data_old_absensi = [];
                foreach ($data_absensi_old_ceklog as $item_data_absensi_old_ceklog) {
                    $array_data_old_absensi[$item_data_absensi_old_ceklog->employee_uuid][$item_data_absensi_old_ceklog->date] = $item_data_absensi_old_ceklog;
                }


                $no_employee = 21;
                /*

                    data dimulai dari baris ke 21,
                    data bulan apa ada di cell 


                */


                while ($sheet->getCell('A' . $no_employee)->getValue() != null) {
                    $column_date = 7;
                    $nik_employee = ResponseFormatter::toUUID($sheet->getCell('B' . $no_employee)->getValue());
                    $data_absensi_row = [];

                    for ($day = 1; $day <= $last_day; $day++) {
                        if ($sheet->getCell($rows[$column_date] . $no_employee)->getValue()) {
                            if ($sheet->getCell($rows[$column_date] . $no_employee)->getValue() != "-") {
                                // if (empty($array_data_old_absensi[$nik_employee][ResponseFormatter::excelToDate($year_month . '-' . $day)])) {
                                $store_employee_absen = Absensi::updateOrCreate(
                                    [
                                        'nrp'  => $nik_employee,
                                        'date' => ResponseFormatter::excelToDate($year_month . '-' . $day),
                                    ],
                                    [
                                        'shift' => 'S1',
                                        'uuid' => ResponseFormatter::excelToDate($year_month . '-' . $day) . '-' . $nik_employee,
                                        'status_absen_uuid'     => ResponseFormatter::toUUID($sheet->getCell($rows[$column_date] . $no_employee)->getValue()),
                                    ]
                                );
                                // }
                            }
                        }
                        $data_absensi_row[ResponseFormatter::excelToDate($year_month . '-' . $day)] = $sheet->getCell($rows[$column_date] . $no_employee)->getValue();
                        $column_date++;
                    }
                    // return ResponseFormatter::toJson($data_absensi_row, 'data_absensi_row');


                    $column_date = 4 + $last_day;
                    $no_employee++;
                }
                // return ResponseFormatter::toJson($sheet->getCell('A' . $no_employee)->getValue(), 'a');
                return ResponseFormatter::toJson($no_employee, 'excel');
                return back();
            } elseif ('DARI-LIST' == ResponseFormatter::toUUID($sheet->getCell('C' . '1')->getValue())) {
                // return ResponseFormatter::toJson('list','list');
                $no_employee = 21;

                // $employees = [];
                $arr_data_list = [];
                while ($sheet->getCell('A' . $no_employee)->getValue() != null) {
                    $cel_val = $sheet->getCell('J' .  $no_employee)->getValue();
                    $firstCharacter = substr($cel_val, 0, 1);
                    $value_STATUS_ABSEN = $sheet->getCell('K' .  $no_employee)->getValue();
                    $firstCharacter_STATUS_ABSEN = substr($value_STATUS_ABSEN, 0, 1);
                    if ($firstCharacter == '=') {
                        $value = $sheet->getCell('J' .  $no_employee)->getOldCalculatedValue();
                    } elseif ($firstCharacter == '-') {
                        $value = $sheet->getCell('J' .  $no_employee)->getOldCalculatedValue();
                    } else {
                        $value = $sheet->getCell('J' .  $no_employee)->getValue();
                    }

                    if ($firstCharacter_STATUS_ABSEN == '=') {
                        $value_STATUS_ABSEN = $sheet->getCell('K' .  $no_employee)->getOldCalculatedValue();
                    } elseif ($firstCharacter_STATUS_ABSEN == '-') {
                        $value_STATUS_ABSEN = $sheet->getCell('K' .  $no_employee)->getOldCalculatedValue();
                    } else {
                        $value_STATUS_ABSEN = $sheet->getCell('K' .  $no_employee)->getValue();
                    }

                    $data_one_row = [
                        'nik_employee' => ResponseFormatter::toUUID($sheet->getCell('B' . $no_employee)->getValue()),
                        'nrp' => ResponseFormatter::toUUID($sheet->getCell('B' . $no_employee)->getValue()),
                        'date_start' => ResponseFormatter::excelToDate($sheet->getCell('H' .  $no_employee)->getValue()),
                        'date_end' => ResponseFormatter::excelToDate($value),
                        'status_absen_uuid' => ResponseFormatter::toUUID($value_STATUS_ABSEN),
                        'shift' => 'S1',
                        'absen_description' => $sheet->getCell('L' .  $no_employee)->getValue(),
                    ];

                    $startDate = new \DateTime($data_one_row['date_start']);
                    $endDate = new \DateTime($data_one_row['date_end']);
                    $validatedData['edited'] = 'edited';

                    for ($date = $startDate; $date <= $endDate; $date->modify('+1 day')) {
                        $data_one_row['date'] =  $date->format('Y-m-d');

                        $data_one_row['uuid']  = $data_one_row['date'] . '-' . $data_one_row['nrp'];
                        if ($data_one_row['status_absen_uuid'] != '-') {
                            $store = Absensi::updateOrCreate(
                                [
                                    'nrp'  => $data_one_row['nik_employee'],
                                    'date' => $data_one_row['date'],
                                ],
                                $data_one_row
                            );
                            $validatedData['store'][] = $store;
                        }
                    }

                    $arr_data_list[] = $data_one_row;
                    $no_employee++;
                }
                return ResponseFormatter::tojson($arr_data_list, 'from list employee absen');
            } else { //dari mesin fingger

                $sheet = $spreadsheet->getSheet(2);
                $validatedData = $request->all();
                $array_friday = [];

                $timeConfig = [
                    'entryStart' => '06:00',         // Jam mulai kerja
                    'lateToleranceMinutes' => 15,    // Toleransi keterlambatan dalam menit
                    'restStart' => '11:00',          // Jam mulai istirahat
                    'restEnd' => '12:00',            // Jam akhir istirahat (hari biasa)
                    'restEndFriday' => '13:00',      // Jam akhir istirahat (hari Jumat)
                    'exitLimit' => '17:00',           // Jam pulang kerja
                    'isFriday'  => false,
                    'shift' => null,
                ];

                // ==== GET DATE DATA ====

                $row_limit    = $sheet->getHighestDataRow();
                $tanggal = $sheet->getCell('C' . 3)->getValue();

                $splitTanggal =  str_split($tanggal, 1);

                $date_start =  $splitTanggal[8] . $splitTanggal[9];
                $date_end   =  $splitTanggal[21] . $splitTanggal[22];
                $month_start = $splitTanggal[5] . $splitTanggal[6];
                $month_end   = $splitTanggal[18] . $splitTanggal[19];
                $year_start = $splitTanggal[0] . $splitTanggal[1] . $splitTanggal[2] . $splitTanggal[3];
                $year_end  = $splitTanggal[13] . $splitTanggal[14] . $splitTanggal[15] . $splitTanggal[16];
                $year_end . "-" . $month_end . "-" . $date_end;
                $start_date = date_create($year_start . "-" . $month_start . "-" . $date_start);
                $end_date = date_create($year_end . "-" . $month_end . "-" . $date_end);
                $end_date = date_add($end_date, date_interval_create_from_date_string("1 days"));
                // ==== END GET DATE DATA ====

                // ==== VALIDATION DATE PREPROCESING =====
                $all_datas['configuration'] = [
                    'long_date' => $date_end,
                    'first_date'    => $start_date->format('Y-m-d'),
                    'end_date'    => $end_date->format('Y-m-d'),
                ];

                if (empty($validatedData['date_absen_start'])) {
                    $data_return = [
                        'date_absen_start' => $start_date->format('Y-m-d'),
                        'date_absen_end' => $end_date->format('Y-m-d'),
                    ];
                    return ResponseFormatter::toJson($data_return, 'data return to setup');
                }

                // 4. MENDAPATKAN SHIFT KARYAWAN
                // $Q_DATA_SHIFT = DatabaseData::where('code_table_data', 'DATA-SHIFT-KARYAWAN')
                //     ->whereNull('date_end')
                //     ->where('code_field_data', 'NRP')
                //     ->get();

                // $DATA_SHIFT_KARYAWAN = [];
                // foreach ($Q_DATA_SHIFT  as $I_DATA_SHIFT) {
                //     $DATA_SHIFT_KARYAWAN[$I_DATA_SHIFT->code_data]['NRP'] = $I_DATA_SHIFT->value_data;
                // }

                // $Q_DATA_SHIFT = DatabaseData::where('code_table_data', 'DATA-SHIFT-KARYAWAN')
                //     ->whereNull('date_end')
                //     ->where('code_field_data', 'SHIFT')
                //     ->get();

                // foreach ($Q_DATA_SHIFT  as $I_DATA_SHIFT) {
                //     $DATA_SHIFT_KARYAWAN[$I_DATA_SHIFT->code_data]['SHIFT'] = $I_DATA_SHIFT->value_data;
                //     $DATA_SHIFT_KARYAWAN[$I_DATA_SHIFT->code_data]['DATE'] = $I_DATA_SHIFT->date_start;
                // }

                // $DATA_SHIFT = [];
                // foreach ($DATA_SHIFT_KARYAWAN as $I_DATA_SHIFT_KARYAWAN) {
                //     $DATA_SHIFT[$I_DATA_SHIFT_KARYAWAN['NRP']][$I_DATA_SHIFT_KARYAWAN['DATE']] = $I_DATA_SHIFT_KARYAWAN['SHIFT'];
                // }

                // 5. MENDAPATKAN ID FINGGER KARYAWAN
                $employees_machine_ids = [];

                if ($session_data['database_tables']['DATABASE-KODE-TABEL-ID-FINGGER']) {
                    foreach ($session_data['database_tables']['DATABASE-KODE-TABEL-ID-FINGGER']['data'] as $item_data_fingger) {
                        $employees_machine_ids[$item_data_fingger['ID-FINGGER']['value_data']] = $item_data_fingger['NRP']['value_data'];
                    }
                }

                // return ResponseFormatter::toJson($request->all(),'data fingger id employees');



                // ==== END VALIDATION DATE PREPROCESING =====
                $start_date = date_create($validatedData['date_absen_start']);
                $end_date = date_create($validatedData['date_absen_end']);

                // $data_database = session('data_database');
                // $session_data = session('DATABASE');
                $data_employees = $session_data['database_tables']['KARYAWAN']['data'];


                // dd($end_date);
                $result = $end_date->format('Y-m-d');

                $interval = date_diff($start_date, $end_date);
                $interval_data =  $interval->days + 1;
                $period = new DatePeriod(
                    new DateTime($year_start . "-" . $month_start . "-" . $date_start),
                    new DateInterval('P1D'),
                    new DateTime($result)
                );
                $date_data = array();
                $all_data = [];

                $data_absensi_old_ceklog = Absensi::where('absensis.date', '>=', $start_date->format('Y-m-d'))
                    ->where('absensis.date', '<=', $end_date->format('Y-m-d'))
                    ->get();


                $array_data_old_absensi = [];
                foreach ($data_absensi_old_ceklog as $item_data_absensi_old_ceklog) {
                    $array_data_old_absensi[$item_data_absensi_old_ceklog->nrp][$item_data_absensi_old_ceklog->date] = $item_data_absensi_old_ceklog;
                }




                //6. mencari array tanggal
                foreach ($period as $key => $value) {
                    if ($value->format('N') == 5) {
                        $array_friday[] = $value->format('Y-m-d');
                    }
                    $date_data[] = $value->format('Y-m-d');
                }

                $employees_count = ($row_limit - 4) / 2;
                $i = 5;
                $arr_machine_id = [];

                $un_identification = [];
                $identification = [];

                $data_storesss = [];

                // foreach employees
                for ($j = 0; $j < $employees_count; $j++) {

                    $employeeName = $sheet->getCell('K' . $i)->getValue();
                    $employee_uuid = null;

                    $ID_Fingger = $sheet->getCell('C' . $i)->getValue();

                    $arr_user_finger = [
                        'machine_id'  => $employeeName,
                        'id_fingger'  => $ID_Fingger,
                    ];


                    // 7. MENCOCOKAN ID FINGGER
                    if (!empty($employees_machine_ids[$ID_Fingger])) {
                        $employee_uuid =  $arr_user_finger['nrp'] = $employees_machine_ids[$ID_Fingger];
                    } else if (!empty($employees_machine_ids[$employeeName])) {
                        $employee_uuid =   $arr_user_finger['nrp'] = $employees_machine_ids[$employeeName];
                    } else {
                        $employee_uuid = $sheet->getCell('K' . $i)->getValue();
                        // return ResponseFormatter::toJson($employee_uuid,'data user finger not found');
                    }

                    $employee_uuid = ResponseFormatter::toUUID($employee_uuid);

                    if (!empty($arr_user_finger['nrp'])) {
                        $arr_user_finger['nrp'] = $employee_uuid;
                        $identification[$arr_user_finger['nrp']] = $arr_user_finger;
                    } else {
                        $un_identification[] = $arr_user_finger;
                    }

                    $absensies = array();
                    $count_day = 0;

                    // 7. UNTUK STORE KE DB
                    foreach ($date_data as $date_process) {
                        $cell_d = $i + 1;
                        $date_now = date_create($date_process);

                        //7.0 cek apakah ini bagian dari tanggal yang mau di ambil
                        if (($date_now >= $start_date) && ($date_now <= $end_date)) {
                            $data_fingger = $sheet->getCell($rows[$count_day] . $cell_d)->getValue(); //data_ceklog dari absen

                            $old_cek_log = null;
                            $isEdited = null;
                            $statusAbsen = null;
                            // 7.1 jika data ceklog tidak kosong maka lakukan proses untuk mendapatkan status absen, entry, mid, exit, late points, dan working hours
                            if (!empty($data_fingger)) {
                                $merge_arr_absen = str_split($data_fingger, 5);
                                $absensies = [
                                    'uuid' => $employee_uuid . '-' . ResponseFormatter::excelToDate($date_process),
                                    'nrp'  => $employee_uuid,
                                    'date' => $date_process,
                                    'status_absen_uuid'     => null,
                                    'cek_log'       =>  $merge_arr_absen,

                                    'late_points' => 0,
                                    'late_minutes' => 0,
                                    'working_hours' => 0,
                                    'entry' => null,
                                    'exit' => null,
                                    'mid' => null,
                                    'shift' => null,
                                ];

                                //7.1 ubah shift, isFriday, 
                                if (in_array($date_process, $array_friday)) {
                                    $timeConfig['isFriday'] = true;
                                } else {
                                    $timeConfig['isFriday'] = false;
                                }
                                //7.1 ubah shift
                                $data_shift = [
                                    '2026-01-01' => 'S1',
                                    '2026-01-02' => 'S2',
                                    '2026-01-03' => 'S3',
                                ];
                                if (isset($data_shift[$tanggal])) {
                                    $timeConfig['shift'] = $data_shift[$tanggal];
                                } else {
                                    $timeConfig['shift'] = 'S1';
                                }
                                // 7.2 cek absensi lama
                                $isEqual = false;
                                $isEdited = (!empty($array_data_old_absensi[$employee_uuid][$date_process]['edited'])) ? $array_data_old_absensi[$employee_uuid][$date_process]['edited'] : null;

                                if (isset($array_data_old_absensi[$employee_uuid][$date_process]['cek_log'])) {
                                    $old_cek_log = $array_data_old_absensi[$employee_uuid][$date_process]['cek_log'];

                                    if (gettype($old_cek_log) == 'string') {
                                        $old_cek_log = json_decode($old_cek_log);
                                    }

                                    $merge_arr_absen = array_unique(array_merge($old_cek_log, $merge_arr_absen), SORT_REGULAR);
                                    $merge_arr_absen = array_unique($merge_arr_absen);
                                    $isEqual = false;
                                }

                                if (!$isEqual) {

                                    $data_fingger = [];
                                    foreach ($merge_arr_absen as $is_fingger) {
                                        $data_fingger[] = $is_fingger;
                                    }
                                    $data_fingger = array_unique($data_fingger);
                                    $merge_arr_absen = array_values(array_unique($data_fingger));
                                    // $statusAbsen = AbsensiController::processFingerTimes($data_fingger, $timeConfig, $timeConfig['isFriday']); //perhari dilakukan proses
                                }
                                // return ResponseFormatter::toJson([
                                //     'merge_arr_absen' => $merge_arr_absen,
                                //     'timeConfig' => $timeConfig,
                                //     'isFriday' => $timeConfig['isFriday'],
                                // ], 'data return to setup');

                                $statusAbsen = AbsensiController::processFingerTimes($merge_arr_absen, $timeConfig, $timeConfig['isFriday']);
                                // return ResponseFormatter::toJson($statusAbsen, 'data return to setup');

                                if (!empty($statusAbsen)) {
                                    $absensies['cek_log'] = json_encode($merge_arr_absen);
                                    $absensies['entry'] = $statusAbsen['entry'];
                                    $absensies['mid'] = $statusAbsen['mid'];
                                    $absensies['exit'] = $statusAbsen['exit'];
                                    $absensies['late_points'] = $statusAbsen['latePoints'];
                                    $absensies['late_minutes'] = $statusAbsen['lateMinutes'];
                                    $absensies['working_hours'] = round($statusAbsen['workingHours'], 2);
                                    $absensies['shift'] = $timeConfig['shift'];
                                    $absensies['edited'] = $isEdited;
                                    $absensies['status_absen_uuid'] = $statusAbsen['status_absen'];

                                    if (!empty($isEdited)) {
                                        $absensies['status_absen_uuid'] = null;
                                    }
                                    $absensies = array_filter($absensies);
                                    $data_storesss[$absensies['nrp']][$absensies['date']] = $absensies;
                                    $all_data[$employee_uuid][] = $absensies;
                                }
                                // $all_datas['all_data'][$employee_uuid][$date_process] = $absensies;
                                $arr_employee_uuid[$employee_uuid] = $employeeName;
                                if (!empty($data_employees[$employee_uuid])) {
                                    if (empty($all_datas['have_employees']['data'][$employee_uuid])) {
                                        $all_datas['have_employees']['data'][$employee_uuid] = [
                                            'nrp' => $employee_uuid,
                                            'machine_id'    => $employeeName,
                                            'nik_employee'  => $employee_uuid,
                                        ];
                                    }
                                    $all_datas['have_employees']['data'][$employee_uuid]['data_absensi'][$date_process] = $absensies;
                                } else {
                                    if (empty($all_datas['un_have_employees']['data'][$employee_uuid])) {
                                        $all_datas['un_have_employees']['data'][$employee_uuid] = [
                                            'nrp' => $employee_uuid,
                                            'machine_id'    => $employeeName,
                                            'nik_employee'  => $employee_uuid,
                                        ];
                                    }
                                    $all_datas['un_have_employees']['data'][$employee_uuid]['data_absensi'][$date_process] = $absensies;
                                }
                            }
                            $arr_machine_id[$employee_uuid]['date'][$date_process] = $statusAbsen;
                        }
                        $count_day++;
                    }
                    // $all_datas['all_data_user_finger'][$employee_uuid] = $arr_user_finger;
                    $i = $i + 2;
                }
                return ResponseFormatter::toJson($all_datas, 'data return to all data 1');

                //  DatabaseData::upsert(
                //     $upsert,
                //     ['id'],
                //     ['value_data']
                // );

                // $all_datas['configuration'] = [
                //     'long_date' => $date_end,
                //     'first_date'    => $start_date->format('Y-m-d'),
                //     'end_date'    => $end_date->format('Y-m-d'),
                // ];
                $all_datas['GENERAL'] = Absensi::storeAbsensiesGeneral($data_storesss);
                $all_datas['identification'] = $identification;
                $all_datas['un_identification'] = $un_identification;
                $all_datas['employees_machine_ids'] = $employees_machine_ids;
                session()->put('after-import', $all_datas);

                return ResponseFormatter::toJson($all_datas, 'here');
                $row = 4;
                foreach ($date_data as $date_process) {
                    $createSheet->setCellValue($rows[$row] . '5', $date_process);
                    $row++;
                    $row++;
                }
                $row = 7;
                foreach ($all_data as $index => $item) {
                    foreach ($item as $item_day) {
                        $explode_date = explode('-', $item_day['date']);
                        if ($item_day['status_absen_uuid']) {
                            $the_row = (int)$explode_date[2] * 2 + 2;
                            $createSheet->setCellValue($rows[$the_row] . $row, $item_day['status_absen_uuid']); //status
                            $the_row++;
                            $createSheet->setCellValue($rows[$the_row] . $row, $item_day['cek_log']); //ceklog
                        }
                    }
                    $createSheet->setCellValue('D' . $row, $index);
                    $row++;
                }

                $crateWriter = new Xls($createSpreadsheet);
                $name = 'file/absensi/file/ekstrak-absen-' . rand(99, 9999) . 'file.xls';
                $crateWriter->save($name);
                return ResponseFormatter::toJson($all_datas, 'here');
                dd($dataaa);
            }
        } catch (Exception $e) {
            // $error_code = $e->errorInfo[1];
            return back()->withErrors('There was a problem uploading the data!');
        }
    }
}
