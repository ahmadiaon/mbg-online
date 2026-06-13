<?php

namespace App\Helpers;

use Carbon\Carbon;

use App\Models\Aktivity\Aktivity;
use App\Models\CoalFrom;
use App\Models\Company;
use App\Models\Department;
use App\Models\Dictionary;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeOut;
use App\Models\Payment\PaymentGroup;
use App\Models\Poh;
use App\Models\Position;
use App\Models\Premi;
use App\Models\Religion;
use App\Models\Safety\AtributSize;
use App\Models\StatusAbsen;
use App\Models\Support\DataSource;
use App\Models\UserDetail\UserDetail;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResponseFormatter
{
  /*
    1.0 Processing text
  */
  public static function ResponseJson($data, $message, $code)
  {
    if (empty($data)) {
      return response()->json(['code' => 204, 'message' => "data not found", 'data' => null], $code);
    }
    return response()->json(['code' => $code, 'message' => $message, 'data' => $data], $code);
  }

  public static function abjads()
  {
    $abjads = [];

    // Single letters A to Z
    foreach (range('A', 'Z') as $char) {
      $abjads[] = $char;
    }

    // Double letters AA to ZZ
    foreach (range('A', 'Z') as $first) {
      foreach (range('A', 'Z') as $second) {
        $abjads[] = $first . $second;
      }
    }

    return $abjads;
  }












  // 1.0 Processing text
  public static function isString($string)
  {
    $string = preg_replace('/[^A-Za-z0-9\-_&]/', ' ', $string);
    return $string;
  }

  public static function toUUID($uuid)
  {
    $uuid = ResponseFormatter::isString($uuid);
    return strtoupper(str_replace(' ', '-', str_replace('.', '-', str_replace('/', '-',  str_replace('_', '-',  $uuid)))));
  }

  public static function convertToDate($value)
  {
    // Check if the value is a string and in the format yyyy-mm-dd
    if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
      return $value; // It's already in yyyy-mm-dd format
    }

    // If the value is a number, convert it to yyyy-mm-dd
    if (is_numeric($value)) {
      // Convert the number to a Carbon instance, assuming the number is an Excel serial date
      // Adjust the base date if needed; this example assumes it's an Excel date.
      $date = Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($value);
      return $date->format('Y-m-d');
    }

    // If the value is not valid, return null or throw an exception
    return null;
  }

  public static function toNumber($data)
  {

    $data = preg_replace("/[^0-9]/", "", $data);
    $data = (int)$data;
    return $data;
  }

  public static function monthSort($stringMonth)
  {
    $months = [
      "Januari" => 1,
      "Februari" => 2,
      "Maret" => 3,
      "April" => 4,
      "Mei" => 5,
      "Juni" => 6,
      "Juli" => 7,
      "Agustus" => 8,
      "September" => 9,
      "Oktober" =>  10,
      "November" => 11,
      "Desember" => 12,
      "January" => 1,
      "February" => 2,
      "March" => 3,
      "April" => 4,
      "May" => 5,
      "June" => 6,
      "July" => 7,
      "August" => 8,
      "September" => 9,
      "October" =>  10,
      "November" => 11,
      "December" => 12,
    ];

    return $months[$stringMonth];
  }
  // Processing text

  protected static $response = [
    'meta' => [
      'code' => 200,
      'status' => 'success',
      'message' => null
    ],
    'data' => null
  ];



  public static function dateToArray($dateString)
  {
    $date = Carbon::parse($dateString);

    return $dateArray = [
      'year' => $date->year,
      'month' => $date->month,
      'day' => $date->day
    ];
  }

  public static function convertToDateOld($value)
  {
    // Check if the value is a string and in the format yyyy-mm-dd
    if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
      return $value; // It's already in yyyy-mm-dd format
    }

    // If the value is a number, convert it to yyyy-mm-dd
    if (is_numeric($value)) {
      // Convert the number to a Carbon instance, assuming the number is an Excel serial date
      // Adjust the base date if needed; this example assumes it's an Excel date.
      $date = Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($value);
      return $date->format('Y-m-d');
    }

    // If the value is not valid, return null or throw an exception
    return null;
  }

  public static function monthSortOld($stringMonth)
  {
    $months = [
      "Januari" => 1,
      "Februari" => 2,
      "Maret" => 3,
      "April" => 4,
      "Mei" => 5,
      "Juni" => 6,
      "Juli" => 7,
      "Agustus" => 8,
      "September" => 9,
      "Oktober" =>  10,
      "November" => 11,
      "Desember" => 12,
    ];

    return $months[$stringMonth];
  }

  public static function abjadsold2()
  {
    $abjads = [];

    // Single letters A to Z
    foreach (range('A', 'Z') as $char) {
      $abjads[] = $char;
    }

    // Double letters AA to ZZ
    foreach (range('A', 'Z') as $first) {
      foreach (range('A', 'Z') as $second) {
        $abjads[] = $first . $second;
      }
    }

    return $abjads;
  }

  public static function getDateToday()
  {
    return Carbon::today()->isoFormat('Y-MM-DD');
  }

  public static function isFormulaExcell($getOldCalculatedValue, $getValue)
  {
    if (gettype($getValue) == 'string') {
      return $getOldCalculatedValue;
    } else {
      return $getValue;
    }
  }
  public static function createIndexArray($array, $key)
  {
    $data = [];
    foreach ($array as $item) {
      $data[$item->$key] = $item;
    }
    return $data;
  }

  public static function success($data = null, $message = null)
  {
    self::$response['meta']['message'] = $message;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['meta']['code']);
  }

  public static function error($data = null, $message = null, $code = 400)
  {
    self::$response['meta']['status'] = 'error';
    self::$response['meta']['code'] = $code;
    self::$response['meta']['message'] = $message;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['meta']['code']);
  }

  public static function toDate($data = null)
  {
    $date_end = explode(" ", $data);
    $months = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    $result = $date_end[2] . "-" . array_search($date_end[1], $months) . "-" . $date_end[0];
    $result = Carbon::createFromFormat('Y-m-d',  $result);
    return $result;
  }

  public static function getTIme($data = null)
  {
    if ($data) {
      $cls_date =  Carbon::parse($data);
      $timeOnly = $cls_date->format('H:i');
      return $timeOnly;
    }
    return $time = null;
  }

  public static function excelToDateArray($date = null)
  {


    if ($date != null) {
      if ($date == '') {
        return null;
      }
      if (gettype($date) == 'string') {
        $cls_date = new DateTime($date);
        $return = $cls_date->format('Y-m-d');
      } else {
        $miliseconds = ($date - (25567 + 2)) * 86400 * 1000;
        $seconds = $miliseconds / 1000;
        $return = date("Y-m-d", $seconds);
      }
      $result = Carbon::createFromFormat('Y-m-d',  $return);
      $date = [
        'year' => $result->isoFormat('Y'),
        'month' => $result->isoFormat('MM'),
        'day' => $result->isoFormat('DD'),
      ];
      return $date;
    } else {
      return null;
    }
  }

  public static function getMonthName($data = null)
  {
    $data = (int)$data;
    $months = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    $result = $months[$data];

    return $result;
  }
  public static function getNumArray($index, $array)
  {
    return array_search($index, $array);
  }

  public static function getDatesBetween($startDate, $endDate)
  {
    // Pastikan tanggal dalam format Carbon
    $start = Carbon::parse($startDate);
    $end = Carbon::parse($endDate);

    // Tambahkan satu hari pada tanggal akhir untuk memastikan tanggal akhir juga disertakan
    $end = $end->addDay();

    // Buat DatePeriod
    $interval = new DateInterval('P1D'); // Interval 1 hari
    $datePeriod = new DatePeriod($start, $interval, $end);

    // Array untuk menyimpan hasil tanggal
    $dates = [];

    // Loop melalui DatePeriod dan ambil tanggal (day)
    foreach ($datePeriod as $date) {
      $dates[] = $date->format('Y-m-d'); // Atau gunakan 'd' untuk hanya mendapatkan hari
    }

    return $dates;
  }

  public static function toFloat($data)
  {

    $data = str_replace(',', '.', $data);
    $data = (float)$data;
    return $data;
  }

  public static function toNumberOld($data)
  {

    $data = preg_replace("/[^0-9]/", "", $data);
    $data = (int)$data;
    return $data;
  }

  public static function ResponseJsonOld($data, $message, $code)
  {
    if (empty($data)) {
      return response()->json(['code' => 204, 'message' => "data not found", 'data' => null], $code);
    }
    return response()->json(['code' => $code, 'message' => $message, 'data' => $data], $code);
  }



  public static function toJson($data, $message = "success")
  {
    return response()->json(['code' => 200, 'message' => $message, 'data' => $data], 200);
  }

  public static function getEndDay($year_month)
  {
    $datetime = Carbon::createFromFormat('Y-m-d', $year_month . '-01');
    $day_month = Carbon::parse($datetime)->endOfMonth()->isoFormat('D');
    return $day_month;
  }
  public static function getEndDayFromDate($year_month)
  {
    $datetime = Carbon::parse('2024-07-15');
    $day_month = Carbon::parse($year_month)->endOfMonth()->isoFormat('YYYY-MM-DD');
    return $day_month;
  }
  public static function getStartDayFromDate($year_month)
  {
    // Parse the provided year and month and get the first day of the month
    $first_day_month = Carbon::parse($year_month)->startOfMonth()->isoFormat('YYYY-MM-DD');
    return $first_day_month;
  }

  public static function toUUIDOld($uuid)
  {
    $uuid = ResponseFormatter::isString($uuid);
    return strtoupper(str_replace(' ', '-', str_replace('.', '-', str_replace('/', '-',  str_replace('_', '-',  $uuid)))));
  }

  public static function toUuidLower($uuid)
  {
    $uuid = ResponseFormatter::isString($uuid);
    return strtolower(str_replace(' ', '_', str_replace('.', '_', str_replace('/', '_', $uuid))));
  }



  public static function numberToAlfhabet($letters)
  {
    $alphabet = range('A', 'Z');

    return $alphabet[$letters];
  }


  public static function toValueRupiah($value_rupiah)
  {
    $integer_value = ResponseFormatter::toNumber($value_rupiah);
    $hasil_rupiah = "Rp " . number_format($integer_value, 0, ',', '.');
    return $hasil_rupiah;
  }




  public static function foreachData($obj_data)
  {
    $data = [];
    if (!empty($obj_data)) {
      foreach ($obj_data as $item_data) {
        $data[$item_data->uuid] = $item_data->toArray();
      }
    }

    return $data;
  }

  public static function tableList()
  {
    $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();

    $table_field = [];
    foreach ($tables as $name_table) {
      if ($name_table != 'migrations') {
        $columns = DB::getSchemaBuilder()->getColumnListing($name_table);
        $table_field[$name_table] = $columns;
      }
    }

    session()->put('table_field', $table_field);
    return $table_field;
    dd(session('table_field'));
    dd($table_field);
  }

  public static function countMonthLongWork($date1, $date2)
  {
    $ts1 = strtotime($date1);
    $ts2 = strtotime($date2);

    $year1 = date('Y', $ts1);
    $year2 = date('Y', $ts2);

    $month1 = date('m', $ts1);
    $month2 = date('m', $ts2);

    return $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
  }

  public static function countDayLongWork($date1, $date2)
  {
    $startTimeStamp = strtotime($date1);
    $endTimeStamp = strtotime($date2);
    $timeDiff = abs($endTimeStamp - $startTimeStamp);

    $numberDays = $timeDiff / 86400;  // 86400 seconds in one day

    // and you might want to convert to integer
    return $numberDays = intval($numberDays) + 1;
  }

  public static function excelToDate($date)
  {
    if ($date != null) {
      if ($date == '') {
        return null;
      }
      if (gettype($date) == 'string') {
        $cls_date = new DateTime($date);
        return $cls_date->format('Y-m-d');
      } else {
        $miliseconds = ($date - (25567 + 2)) * 86400 * 1000;
        $seconds = $miliseconds / 1000;
        return  date("Y-m-d", $seconds);
      }
    } else {
      return null;
    }
  }

  public static function to2Digit($num)
  {
    $loading_tongkang_first = [
      'main-table' => [
        'DIBUAT-OLEH' => [
          'SOURCE_TYPE' => 'tables',
          'SOURCE' => 'employees',
          'VALUE' =>  'MBLE-005',
        ],
        'UNIT' => [
          [
            'UNIT' => [
              'SOURCE_TYPE' => 'tables',
              'SOURCE' => 'vehicles',
              'VALUE' =>  'DZ-005',
            ],
            'CONTROLLER' => [
              'SOURCE_TYPE' => 'tables',
              'SOURCE' => 'employees',
              'VALUE' =>  'MBLE-006',
            ]
          ],
          [
            'UNIT' => [
              'SOURCE_TYPE' => 'tables',
              'SOURCE' => 'vehicles',
              'VALUE' =>  'EX-001',
            ],
            'CONTROLLER' => [
              'SOURCE_TYPE' => 'tables',
              'SOURCE' => 'employees',
              'VALUE' =>  'MBLE-007',
            ]
          ],
        ]
      ]
    ];
    return str_pad($num, 2, "0", STR_PAD_LEFT);
  }

  public static function addDate($currentDate, $countAddDay)
  {
    $date = Carbon::createFromFormat('Y-m-d', $currentDate);
    $newDate = $date->addDays($countAddDay - 1);
    return $newDate->toDateString();
  }
}
