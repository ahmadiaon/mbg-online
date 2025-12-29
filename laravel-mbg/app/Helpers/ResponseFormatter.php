<?php

namespace App\Helpers;

use Carbon\Carbon;

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
}
