<?php

use Carbon\Carbon;

if (!function_exists('getDatesFromRange')) {
  function getDatesFromRange($start, $end, $format = 'Ym', $routine)
  {
    $array = array();
    $interval = DateInterval::createFromDateString("$routine");

    $realEnd = new DateTime($end);
    $realEnd->add($interval);

    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

    foreach ($period as $date) {
      if ($date <= new DateTime($end)) {
        $array[] = $date->format($format);
      }
    }
    return $array;
  }
}

if (!function_exists('changeDateFormat')) {
  function changeDateFormat($format, $date)
  {
    return date($format, strtotime($date));
  }
}

if (!function_exists('changeSlash')) {
  function changeSlash($date)
  {
    return str_replace('/', '-', $date);
  }
}

if (!function_exists('standardDate')) {
  function standardDate($date)
  {
    return date('d/m/Y', strtotime($date));
  }
}

if (!function_exists('dbDate')) {
  function dbDate($date)
  {
    $date = str_replace('/', '-', $date);
    return date('Y-m-d', strtotime($date));
  }
}

if (!function_exists('countWorkingTime')) {
  function countWorkingTime($start = null, $finish = null)
  {
    $in = $start ? Carbon::parse($start) : null;
    $out = $finish ? Carbon::parse($finish) : null;
    $mins = $in && $out ? $in->diffInMinutes($out) : 0;
    return $mins / 60;
  }
}

if (!function_exists('countOverTime')) {
  function countOverTime($start = null, $finish = null)
  {
    $overtime = $start && $finish ? (new Carbon($finish))->diffInMinutes(new Carbon($start)) : 0;
    return $overtime / 60;
  }
}

if (!function_exists('dateInAMonth')) {
  function dateInAMonth($month, $year)
  {
    $dates = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $amonth = [];
    for ($i = 1; $i <= $dates; $i++) {
      $amonth[] = changeDateFormat('Y-m-d', $year . '-' . $month . '-' . $i);
    }

    return $amonth;
  }
}

if (!function_exists('countDateDiff')) {
  function countDateDiff($start, $end)
  {
    $date1 = date_create($start);
    $date2 = date_create($end);
    return date_diff($date1, $date2, true);
  }
}