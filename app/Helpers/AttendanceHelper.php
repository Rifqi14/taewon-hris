<?php

use App\Models\Employee;
use App\Models\Spl;
use App\Models\Workingtime;
use App\Models\OvertimeSchemeList;
use App\Models\CalendarException;
use App\Models\WorkingtimeDetail;
use App\Models\CalendarShiftSwitch;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeDetailAllowance;
use App\Models\EmployeeSalary;
use App\Models\Overtime;
use App\Models\Config;
use App\Models\OvertimeScheme;
use App\Models\OvertimeAllowance;
use App\Models\LogHistory;
use App\Models\SalaryIncreases;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

if (!function_exists('calculateAttendance')) {
  function calculateAttendance($attendance){
    $employee = Employee::find($attendance->employee_id);
    $breaktimes = listBreaktime($employee->department_id);
    if (!$breaktimes) {
        return response()->json([
            'status'     => false,
            'message'     => 'Break time for this employee workgroup ' . $employee->workgroup->name . ' not found. Please check master break.'
        ], 400);
    }
    /* Check Working Time*/
    $switchworkintime = CalendarException::where('calendar_id', $employee->calendar_id)->first();
    if ($switchworkintime->is_switch_day == 'YES') {
        $workingtime = checkWorkingtimeSwitch($attendance->workingtime_id, $attendance->day);
    } else {
        $workingtime = checkWorkingtime($attendance->workingtime_id, $attendance->day);
    }
    if (!$workingtime) {
        return response()->json([
            'status'     => false,
            'message'     => 'Working shift for employee name ' . $employee->name . ' and attendance date ' . $attendance->attendance_date . ' and this day ' . $attendance->day . ' not found. Please check master shift.'
        ], 400);
    }
    $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $attendance->attendance_out);
    /*Get Min Workhour*/
    $start_shift = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $attendance->attendance_in) . ' ' . $workingtime->start);
    $cek_minworkhour = roundedTime(countWorkingTime($start_shift, $attendance->attendance_out));
    $min_workhour = $workingtime->min_workhour;

    
    
    /*End Get Workhour*/
    $breakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $workingtime); //Count Breaktime Worktime
    $breakovertime = getBreaktimeOvertime($breaktimes, $attendance_hour, $workingtime); //Count Breaktime Overtime
    $breakall = getAllBreaktime($breaktimes, $attendance_hour); //Count Breaktime All
    if($cek_minworkhour >= $min_workhour){
        $min_workhour = $workingtime->min_workhour;
    }else{
        $min_workhour = $cek_minworkhour - $breakworkingtime;
    }
    if ($attendance->attendance_in) {
      $totalattendance = Carbon::parse($attendance->attendance_out)->diffInHours($attendance->attendance_in);
      //$totalbreaktime = $breakworkingtime + $breakovertime;
      $totalbreaktime = $breakall;
      $totalworkingtime = $totalattendance - $totalbreaktime;
      
      /* 
        Day = Off
        Timeout = Yes
        Attendance In = True
        Attendance Out = True
      */
      if($attendance->day == 'Off' && $employee->timeout == 'yes'  && $attendance->attendance_in && $attendance->attendance_out){
        if ($employee->overtime == 'yes') {
            $totalovertime = $totalworkingtime;
            if ($employee->spl == 'yes') {
                $existspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id',$employee->id)->first();
                // ketika ada spl
                if ($existspl) {

                    if($existspl->duration < $ot ){
                        $attendance->adj_over_time = $existspl->duration;
                    }else{
                        $attendance->adj_over_time = $totalovertime;
                    }
                    $attendance->adj_working_time = 0;
                    $attendance->code_case  = "A01/BW$breakworkingtime/BO$breakovertime";
                    $attendance->breaktime = $totalbreaktime;
                } else {
                    $attendance->adj_over_time = 0;
                    $attendance->adj_working_time = 0;
                    $attendance->code_case  = "A02/BW$breakworkingtime/BO$breakovertime";
                    $attendance->breaktime = $totalbreaktime;
                }
            } else {
                //  spl not
                $attendance->adj_over_time = $totalovertime;
                $attendance->adj_working_time = 0;
                $attendance->code_case  = "A03/BW$breakworkingtime/BO$breakovertime";
                $attendance->breaktime = $totalbreaktime;
            }
        } else {
            // overtime no
            $attendance->adj_over_time = 0;
            $attendance->adj_working_time = 0;
            $attendance->code_case  = "A04/BW$breakworkingtime/BO$breakovertime";
            $attendance->breaktime = $totalbreaktime;  
        }
      }
      /* End If*/

      /* 
        Day = Off
        Timeout = Yes
        Attendance In = True
        Attendance Out = False
      */
      if($attendance->day == 'Off' && $employee->timeout == 'yes'  && $attendance->attendance_in && !$attendance->attendance_out){
          $timeout = Carbon::parse($attendance->attendance_in)->addHours($workingtime->min_workhour)->toDateTimeString();
          $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $timeout);
          $breakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $workingtime);
          $attendance_out = Carbon::parse($timeout)->addHours($breakworkingtime)->toDateTimeString();

          $totalattendance = Carbon::parse($attendance_out)->diffInHours($attendance->attendance_in);
          $totalbreaktime = $breakworkingtime;
          $totalworkingtime = $totalattendance - $totalbreaktime;
          if ($employee->overtime == 'yes') {
              $totalovertime = $totalworkingtime - $min_workhour;
              if ($employee->spl == 'yes') {
                  $existspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();
                  if ($existspl) {
                      if ($existspl->duration < $totalovertime) {
                          $attendance->adj_over_time = $existspl->duration;
                      } else {
                          $attendance->adj_over_time = $totalovertime;
                      }
                      $attendance->adj_working_time = 0;
                      $attendance->code_case  = "A05/BW$breakworkingtime/BO$breakovertime";
                      $attendance->breaktime = $totalbreaktime;
                  } else {
                      $attendance->adj_over_time = 0;
                      $attendance->adj_working_time = 0;
                      $attendance->code_case  = "A06/BW$breakworkingtime/BO$breakovertime";
                      $attendance->breaktime = $totalbreaktime;
                  }
              } else {
                  $attendance->adj_over_time = 0;
                  $attendance->adj_working_time = 0;
                  $attendance->code_case  = "A07/BW$breakworkingtime/BO$breakovertime";
                  $attendance->breaktime = $totalbreaktime;
              }
          } else {
              $attendance->adj_over_time = 0;
              $attendance->adj_working_time = 0;
              $attendance->code_case  = "A08/BW$breakworkingtime/BO$breakovertime";
              $attendance->breaktime = $totalbreaktime;
          }
      }
      /* End If*/

      /* 
        Day = Off
        Timeout = No
        Attendance In = True
        Attendance Out = True
      */
      if($attendance->day == 'Off' && $employee->timeout != 'yes'  && $attendance->attendance_in && $attendance->attendance_out){
          if ($employee->overtime == 'yes') {
            $totalovertime = $totalworkingtime - $min_workhour;
            if ($employee->spl == 'yes') {
                $existspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();
                if ($existspl) {
                    if ($existspl->duration < $totalovertime) {
                        $attendance->adj_over_time = $existspl->duration;
                    } else {
                        $attendance->adj_over_time = $totalovertime;
                    }

                    $attendance->adj_working_time = 0;
                    $attendance->code_case  = "A09/BW$breakworkingtime/BO$breakovertime";
                    $attendance->breaktime = $totalbreaktime;
                } else {
                    $attendance->adj_over_time = 0;
                    $attendance->adj_working_time = 0;
                    $attendance->code_case  = "A10/BW$breakworkingtime/BO$breakovertime";
                    $attendance->breaktime = $totalbreaktime;
                }
            } else {
                $attendance->adj_over_time = $totalovertime;
                $attendance->adj_working_time = 0;
                $attendance->code_case  = "A11/BW$breakworkingtime/BO$breakovertime";
                $attendance->breaktime = $totalbreaktime;
            }
        } else {
            $attendance->adj_over_time = 0;
            $attendance->adj_working_time = 0;
            $attendance->code_case  = "A12/BW$breakworkingtime/BO$breakovertime";
            $attendance->breaktime = $totalbreaktime;
        }
      }
      /* End If*/

      /* 
        Day = Off
        Timeout = No
        Attendance In = True
        Attendance Out = False
      */
      if($attendance->day == 'Off' && $employee->timeout != 'yes'  && $attendance->attendance_in && !$attendance->attendance_out){
          $timeout = Carbon::parse($attendance->attendance_in)->addHours($workingtime->min_workhour)->toDateTimeString();
          $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $timeout);
          $breakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $workingtime);
          $attendance->attendance_out = Carbon::parse($timeout)->addHours($breakworkingtime)->toDateTimeString();

          $totalattendance = Carbon::parse($attendance->attendance_out)->diffInHours($attendance->attendance_in);
          $totalbreaktime = $breakworkingtime;
          $totalworkingtime = $totalattendance - $breakworkingtime;

          if ($employee->overtime == 'yes') {
              $totalovertime = $totalworkingtime - $min_workhour;

              if ($employee->spl == 'yes') {
                  $existspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();
                  // ketika ada spl dan adjustment
                  if ($existspl) {

                      if ($existspl->duration < $totalovertime) {
                          $attendance->adj_over_time = $existspl->duration;
                      } else {
                          $attendance->adj_over_time = $totalovertime;
                      }
                      $attendance->adj_working_time = 0;
                      $attendance->code_case  = "A13/BW$breakworkingtime/BO$breakovertime";
                      $attendance->breaktime = $totalbreaktime;
                  } else {
                      $attendance->adj_over_time = 0;
                      $attendance->adj_working_time = 0;
                      $attendance->code_case  = "A14/BW$breakworkingtime/BO$breakovertime";
                      $attendance->breaktime = $totalbreaktime;
                  }
              } else {
                  //  spl not
                  $attendance->adj_over_time = $totalovertime;
                  $attendance->adj_working_time = 0;
                  $attendance->code_case  = "A15/BW$breakworkingtime/BO$breakovertime";
                  $attendance->breaktime = $totalbreaktime;
              }
          } else {
              $attendance->adj_over_time = 0;
              $attendance->adj_working_time = 0;
              $attendance->code_case  = "A16/BW$breakworkingtime/BO$breakovertime";
              $attendance->breaktime = $totalbreaktime;
          }
      }
      /* End If*/

      /* 
        Day = Weekday
        Timeout = Yes
        Attendance In = True
        Attendance Out = True
      */
      if($attendance->day != 'Off' && $employee->timeout == 'yes'  && $attendance->attendance_in && $attendance->attendance_out){
        if ($employee->overtime == 'yes') {
            if($totalworkingtime < $min_workhour){
                $totalovertime = 0;
            }
            else{
                $totalovertime = $totalworkingtime - $min_workhour;
            }
            if ($employee->spl == 'yes') {
                $existspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();
                if ($existspl) {
                    if ($existspl->duration < $totalovertime) {
                        $attendance->adj_over_time = $existspl->duration;
                    } else {
                        $attendance->adj_over_time = $totalovertime;
                    }

                    $attendance->adj_working_time = $min_workhour;
                    $attendance->code_case  = "A17/BW$breakworkingtime/BO$breakovertime";
                    $attendance->breaktime = $totalbreaktime;
                    
                } else {
                    $attendance->adj_over_time = 0;
                    $attendance->adj_working_time = $totalworkingtime;
                    $attendance->code_case  = "A18/BW$breakworkingtime/BO$breakovertime";
                    $attendance->breaktime = $totalbreaktime;
                }
            } else {
                $attendance->adj_over_time = $totalovertime;
                $attendance->adj_working_time = $totalworkingtime;
                $attendance->note = json_encode($breaktimes);
                $attendance->code_case  = "A19/BW$breakworkingtime/BO$breakovertime";
                $attendance->breaktime = $totalbreaktime;
            }
        } else {
            $attendance->adj_over_time = 0;
            $attendance->adj_working_time = $totalattendance;
            $attendance->code_case  = "A20/BW$breakworkingtime/BO$breakovertime";
            $attendance->breaktime = $totalbreaktime;
        }
      }
      /* End If*/

      /* 
        Day = Weekday
        Timeout = Yes
        Attendance In = True
        Attendance Out = False
      */
      if($attendance->day != 'Off' && $employee->timeout == 'yes'  && $attendance->attendance_in && !$attendance->attendance_out){
          $timeout = Carbon::parse($attendance->attendance_in)->addHours($workingtime->min_workhour)->toDateTimeString();
          $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $timeout);
          $breakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $workingtime);
          $attendance_out  = Carbon::parse($timeout)->addHours($breakworkingtime)->toDateTimeString();
          $totalattendance = Carbon::parse($attendance_out)->diffInHours($attendance->attendance_in);
          $totalbreaktime = $breakworkingtime;
          $totalworkingtime = $totalattendance - $totalbreaktime;

          if ($employee->overtime == 'yes') {
              $totalovertime = $totalworkingtime - $min_workhour;
              if ($employee->spl == 'yes') {
                  $existspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();
                  if ($existspl) {

                      if ($existspl->duration < $totalovertime) {
                          $attendance->adj_over_time = $existspl->duration;
                      } else {
                          $attendance->adj_over_time = $totalovertime;
                      }
                      $attendance->adj_working_time = $min_workhour;
                      $attendance->code_case  = "A21/BW$breakworkingtime/BO$breakovertime";
                      $attendance->breaktime = $totalbreaktime;
                  } else {
                      $attendance->adj_over_time = 0;
                      $attendance->adj_working_time = $min_workhour;
                      $attendance->code_case  = "A22/BW$breakworkingtime/BO$breakovertime";
                      $attendance->breaktime = $totalbreaktime;
                  }
              } else {
                  //  spl not
                  $attendance->adj_over_time = 0;
                  $attendance->adj_working_time = 0;
                  $attendance->code_case  = "A23/BW$breakworkingtime/BO$breakovertime";
                  $attendance->breaktime = $totalbreaktime;
              }
          } else {
              $attendance->adj_over_time = 0;
              $attendance->adj_working_time = 0;
              $attendance->code_case  = "A24/BW$breakworkingtime/BO$breakovertime";
              $attendance->breaktime = $totalbreaktime;
          }
      }
      /* End If*/

      /* 
        Day = Weekday
        Timeout = No
        Attendance In = True
        Attendance Out = True
      */
      if($attendance->day != 'Off' && $employee->timeout != 'yes'  && $attendance->attendance_in && $attendance->attendance_out){
        if ($employee->overtime == 'yes') {
            $totalovertime = $totalworkingtime - $min_workhour;
            if ($employee->spl == 'yes') {
                $existspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();
                if ($existspl) {

                    if ($existspl->duration < $totalovertime) {
                        $attendance->adj_over_time = $existspl->duration;
                    } else {
                        $attendance->adj_over_time = $totalovertime;
                    }

                    $attendance->adj_working_time = $min_workhour;
                    $attendance->code_case  = "A25/BW$breakworkingtime/BO$breakovertime";
                    $attendance->breaktime = $totalbreaktime;
                } else {
                    $attendance->adj_over_time = 0;
                    $attendance->adj_working_time = $min_workhour;
                    $attendance->code_case  = "A26/BW$breakworkingtime/BO$breakovertime";
                    $attendance->breaktime = $totalbreaktime;
                }
            } else {
                $attendance->adj_over_time = $totalovertime;
                $attendance->adj_working_time = $min_workhour;
                $attendance->code_case  = "A27/BW$breakworkingtime/BO$breakovertime";
                $attendance->breaktime = $totalbreaktime;
            }
        } else {
            $adjustment->adj_over_time = 0;
            $adjustment->adj_working_time = $totalattendance;
            $adjustment->code_case  = "A28/BW$breakworkingtime/BO$breakovertime";
            $adjustment->breaktime = $totalbreaktime;
        }
      }
      /* End If*/

      /* 
        Day = Weekday
        Timeout = No
        Attendance In = True
        Attendance Out = False
      */
      if($attendance->day != 'Off' && $employee->timeout != 'yes'  && $attendance->attendance_in && !$attendance->attendance_out){
          $timeout = Carbon::parse($attendance->attendance_in)->addHours($workingtime->min_workhour)->toDateTimeString();
          $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $timeout);
          $breakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $workingtime);
          $attendance->attendance_out = Carbon::parse($timeout)->addHours($breakworkingtime)->toDateTimeString();

          $totalattendance = Carbon::parse($attendance->attendance_out)->diffInHours($attendance->attendance_in);

          $totalbreaktime = $breakworkingtime;
          $totalworkingtime = $totalattendance - $breakworkingtime;

          if ($employee->overtime == 'yes') {
              $totalovertime = $totalworkingtime - $min_workhour;
              if ($employee->spl == 'yes') {
                  $existspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();
                  if ($existspl) {
                      if ($existspl->duration < $totalovertime) {
                          $attendance->adj_over_time = $existspl->duration;
                      } else {
                          $attendance->adj_over_time = $totalovertime;
                      }
                      $attendance->adj_working_time = $min_workhour;
                      $attendance->code_case  = "A29/BW$breakworkingtime/BO$breakovertime";
                      $attendance->breaktime = $totalbreaktime;
                  } else {
                      $attendance->adj_over_time = 0;
                      $attendance->adj_working_time = $min_workhour;
                      $attendance->code_case  = "A30/BW$breakworkingtime/BO$breakovertime";
                      $attendance->breaktime = $totalbreaktime;
                  }
              } else {   
                  $attendance->adj_over_time = $totalovertime;
                  $attendance->adj_working_time = $min_workhour;
                  $attendance->code_case  = "A31/BW$breakworkingtime/BO$breakovertime";
                  $attendance->breaktime = $totalbreaktime;
              }
          } else {
              $attendance->adj_over_time = 0;
              $attendance->adj_working_time = $totalworkingtime;
              $attendance->code_case  = "A32/BW$breakworkingtime/BO$breakovertime";
              $attendance->breaktime = $totalbreaktime;
          }
      }
      /* End If*/
      $attendance->save();
      if (!$attendance) {
          DB::rollBack();
          return response()->json([
              'status'     => false,
              'message'     => $attendance
          ], 400);
      }
    }
    /*End Check Working Time*/
  }
}

if (!function_exists('listBreaktime')) {
  function listBreaktime($workgroup)
  {
    $query = DB::table('break_times');
    $query->select('break_times.*');
    $query->leftJoin('breaktime_departments', 'breaktime_departments.breaktime_id', '=', 'break_times.id');
    $query->where('breaktime_departments.department_id', '=', $workgroup);
    return $query->get();
  }
}

if (!function_exists('checkWorkingtimeSwitch')) {
  function checkWorkingtimeSwitch($id, $day)
  {
    $query = CalendarShiftSwitch::whereNotNull('min_workhour')->where('workingtime_id', $id)->where('day', $day);
    return $query->first();
  }
}

if (!function_exists('checkWorkingtime')) {
  function checkWorkingtime($id, $day)
  {
    $query = WorkingtimeDetail::whereNotNull('min_workhour')->where('workingtime_id', $id)->where('day', $day);
    return $query->first();
  }
}


if (!function_exists('findShift')) {
  function findShift($array, $hour)
  {
    $attendance_in = $hour['attendance_in'] != null ? changeDateFormat('H:i:s', $hour['attendance_in']) : null;
    $attendance_out = $hour['attendance_out'] != null ? changeDateFormat('H:i:s', $hour['attendance_out']) : null;
    $interval = array();
    foreach ($array as $hours) {
      if ($attendance_in != null) {
        $interval[] = abs(strtotime($attendance_in) - strtotime($hours->start)) < abs(strtotime($attendance_in) - strtotime($hours->min_in)) ? abs(strtotime($attendance_in) - strtotime($hours->start)) : abs(strtotime($attendance_in) - strtotime($hours->min_in));
      } elseif ($attendance_out != null) {
        $interval[] = abs(strtotime($attendance_out) - strtotime($hours->finish)) < abs(strtotime($attendance_out) - strtotime($hours->max_out)) ? abs(strtotime($attendance_out) - strtotime($hours->finish)) : abs(strtotime($attendance_out) - strtotime($hours->max_out));
      }
    }

    asort($interval);
    $closest = key($interval);
    if(isset($closest))
    {
      return $array[$closest];
    }else{
      return false;
    }
  }
}

if (!function_exists('shiftBetween')) {
  function shiftBetween($array, $hour)
  {
    $attendance_in = $hour['attendance_in'] != null ? changeDateFormat('H:i:s', $hour['attendance_in']) : null;
    $attendance_out = $hour['attendance_out'] != null ? changeDateFormat('H:i:s', $hour['attendance_out']) : null;
    $between = array();
    if ($attendance_in != null) {
      foreach ($array as $hours) {
        if ($hours->min_in <= $attendance_in || $attendance_in <= $hours->start) {
          $between[] = $hours;
        }
      }
    } elseif ($attendance_out != null) {
      foreach ($array as $hours) {
        if ($hours->finish <= $attendance_out || $attendance_out <= $hours->max_out) {
          $between[] = $hours;
        }
      }
    } elseif (empty($between)) {
      foreach ($array as $hours) {
        if (($hours->start <= $attendance_in || $attendance_in <= $hours->finish) || ($hours->start <= $attendance_out || $attendance_out >= $hours->finish)) {
          $between[] = $hours;
        }
      }
    }
    return $between;
  }
}

if (!function_exists('roundedTime')) {
  function roundedTime($time)
  {
    return floor($time * 2) / 2;
  }
}

if (!function_exists('breaktime')) {
  function breaktime($array, $hour)
  {
    $attendance_in = $hour['attendance_in'] ? changeDateFormat('H:i:s', $hour['attendance_in']) : null;
    $attendance_out = $hour['attendance_out'] ? changeDateFormat('H:i:s', $hour['attendance_out']) : null;
    $day = changeDateFormat('Y-m-d', $hour['attendance_out']);
    $between = array();
    $breaktime = 0;
    if ($attendance_in && $attendance_out) {
      foreach ($array as $hours) {
        if (changeDateFormat('H:i', $hour['attendance_in']) > changeDateFormat('H:i', '18:00')) {
          $start_time = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $hours->start_time);
          $finish_time = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $hours->finish_time);
          if ($hour['attendance_in'] < $start_time && $finish_time < $hour['attendance_out']) {
            $between[] = $hours;
          }
        } else {
          if ($attendance_in < $hours->start_time && $hours->finish_time < $attendance_out) {
            $between[] = $hours;
          }
        }
      }
    }
    foreach ($between as $value) {
      $breaktime += $value->breaktime;
    }
    return $breaktime;
  }
}

if (!function_exists('getBreaktime')) {
  function getBreaktime($array, $hour)
  {
    $attendance_in = $hour['attendance_in'] ? changeDateFormat('H:i:s', $hour['attendance_in']) : null;
    $attendance_out = $hour['attendance_out'] ? changeDateFormat('H:i:s', $hour['attendance_out']) : null;
    $day = changeDateFormat('Y-m-d', $hour['attendance_out']);
    $between = array();
    if ($attendance_in && $attendance_out) {
      foreach ($array as $hours) {
        if (changeDateFormat('H:i', $hour['attendance_in']) > changeDateFormat('H:i', '22:00')) {
          $start_time = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $hours->start_time);
          $finish_time = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $hours->finish_time);
          if ($hour['attendance_in'] < $start_time && $finish_time < $hour['attendance_out']) {
            $between[] = abs(strtotime($hour['attendance_out']) - strtotime($finish_time));
          }
        } else {
          if ($attendance_in < $hours->start_time && $hours->finish_time < $attendance_out) {
            $between[] = abs(strtotime($attendance_out) - strtotime($hours->finish_time));
          }
        }
      }
    }

    asort($between);
    $closest = key($between);

    return $array[$closest];
  }
}

if (!function_exists('getBreaktimeOvertime')) {
  function getBreaktimeOvertime($array, $hour, $workingtime)
  {
    $datetime_in = changeDateFormat('Y-m-d H:i:s', $hour['attendance_in']);
    $datetime_out = changeDateFormat('Y-m-d H:i:s', $hour['attendance_out']);
    $time_in = changeDateFormat('H:i:s', $hour['attendance_in']);
    $time_out = changeDateFormat('H:i:s', $hour['attendance_out']);

    $time_start_shift = changeDateFormat('H:i:s', $workingtime->start);
    $time_finish_shift = changeDateFormat('H:i:s', $workingtime->finish);
    $time_min_in_shift = changeDateFormat('H:i:s', $workingtime->min_in);
    $time_max_out_shift = changeDateFormat('H:i:s', $workingtime->max_out);
    $time_finish = $time_max_out_shift;
    $between = array();
    $breaktime = 0;
    // $cutOffCross = changeDateFormat('H:i:s', '15:00:00');
    // $cutOffDay  = changeDateFormat('H:i:s', '09:00:00');
    $nextDay = Carbon::parse($datetime_in)->addDays(1);
    $finishNow = changeDateFormat('Y-m-d H:i:s', Carbon::parse($datetime_in)->toDateString() . ' ' . $time_max_out_shift);
    $finishTomorrow = changeDateFormat('Y-m-d H:i:s', $nextDay->toDateString() . ' ' . $time_max_out_shift);
    $finishShift = $time_max_out_shift < $time_start_shift ? Carbon::parse($finishTomorrow)->subHours(1)->toDateTimeString() : Carbon::parse($finishNow)->subHours(1)->toDateTimeString();
    // if ($time_max_out_shift < $time_start_shift) {
    //   $finishShift = Carbon::parse($finishTomorrow)->subHours(1)->toDateTimeString();
    // } else {
    //   if ($time_out < $time_start_shift) {
    //     $finishShift = Carbon::parse($finishTomorrow)->subHours(1)->toDateTimeString();
    //   } else {
    //     $finishShift = Carbon::parse($finishNow)->subHours(1)->toDateTimeString();
    //   }
    // }
    $finishShift = $time_max_out_shift == '00:30:00' ? Carbon::parse($finishShift)->addMinutes(30)->toDateTimeString() : $finishShift;
    foreach ($array as $break) {
      // if ($time_finish_shift > changeDateFormat('H:i:s', '22:00:00') && $time_finish_shift < changeDateFormat('H:i:s', '09:00:00')) {
      //   $start_break = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $nextDay->toDateString()) . ' ' . $break->start_time);
      //   $finish_break = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $nextDay->toDateString()) . ' ' . $break->finish_time);
      // } else {
      // }
      $dateIn = $time_finish_shift > $break->start_time ? $nextDay->toDateString() : $datetime_in;
      $start_break = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $dateIn) . ' ' . $break->start_time);
      $finish_break = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $dateIn) . ' ' . $break->finish_time);

     
      // if ($time_max_out_shift < $time_start_shift) {
      //   $day_start_break = 
      // } else {
      // }
      // if ($time_finish_shift < $time_start_shift) {
      //   $day = changeDateFormat('Y-m-d', $datetime_out);
      //   $day_start_break = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $start_break);
      //   $day_finish_break = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $finish_break);
      //   $day_finish_shift = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $time_finish_shift);
      //   $day_max_out_shift = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $time_max_out_shift);
      //   if (($finishShift <= $start_break) && ($finish_break <= $datetime_out)) {
      //     $between[] = $break;
      //   } else {
      //     continue;
      //   }
      // } else {
      // }
      $diff = Carbon::parse($finishShift)->diffInHours(Carbon::parse($datetime_out));
      $diffBreak = Carbon::parse($datetime_out)->diffInMinutes(Carbon::parse($finish_break));
      // echo 'Date in :'.$dateIn . '<br/>' . 'start break :' . $start_break . '<br/>' . 'finish break :' . $finish_break . '<br/>' . 'diff :' . $diff . '<br/>' . 'diff break :' . $diffBreak. '<br/>' . 'finish shift :' . $finishShift. '<br/>' . 'Date time out:' . $datetime_out . '<br/>';
      if ((($finishShift <= $start_break) && ($finish_break < $datetime_out)) && $diff >= 2 && $diffBreak > 30) {
        $between[] = $break;
      } else {
        continue;
      }
    }
    // dd($between);
    foreach ($between as $value) {
      $breaktime += $value->breaktime;
    }
    return $breaktime;
  }
}

if (!function_exists('getBreaktimeWorkingtime')) {
  function getBreaktimeWorkingtime($array, $hour, $workingtime)
  {
    // $datetime_in = changeDateFormat('Y-m-d H:i:s', $hour['attendance_in']);
    // $datetime_out = changeDateFormat('Y-m-d H:i:s', $hour['attendance_out']);
    // $time_in = changeDateFormat('H:i:s', $hour['attendance_in']);
    // $time_out = changeDateFormat('H:i:s', $hour['attendance_out']);

    // $time_start_shift = changeDateFormat('H:i:s', $workingtime->start);
    // $time_finish_shift = changeDateFormat('H:i:s', $workingtime->finish);
    // $time_min_in_shift = changeDateFormat('H:i:s', $workingtime->min_in);
    // $time_max_out_shift = changeDateFormat('H:i:s', $workingtime->max_out);
    // $between = array();
    // $breaktime = 0;
    // foreach ($array as $break) {
    //   $start_break = changeDateFormat('H:i:s', $break->start_time);
    //   $finish_break = changeDateFormat('H:i:s', $break->finish_time);
    //   // $diff = Carbon::parse($time_in)->diffInMinutes(Carbon::parse($start_break)) / 60;
    //   if ($time_in > $time_out) {
    //     $day = changeDateFormat('Y-m-d', $datetime_out);
    //     $day_start_break = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $start_break);
    //     $day_finish_break = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $finish_break);
    //     $day_finish_shift = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $time_finish_shift);
    //     $day_max_out_shift = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $time_max_out_shift);
    //     $diff = Carbon::parse($datetime_in)->diffInMinutes(Carbon::parse($day_start_break)) / 60;
    //     if ($diff >= 2) {
    //       if ($time_out < $time_finish_shift) {
    //         if (($datetime_in < $day_start_break) && ($day_finish_break < $datetime_out)) {
    //           $between[] = $break;
    //         } else {
    //           continue;
    //         }
    //       } else {
    //         if (($datetime_in < $day_start_break) && ($day_finish_break < $day_finish_shift)) {
    //           $between[] = $break;
    //         } else {
    //           continue;
    //         }
    //       }
    //     } else {
    //       continue;
    //     }
    //   } else {
    //     $diff = Carbon::parse($time_in)->diffInMinutes(Carbon::parse($start_break)) / 60;
    //     if ($diff >= 2) {
    //       if ($time_out < $time_finish_shift) {
    //         if (($time_in < $start_break) && ($finish_break < $time_out)) {
    //           $between[] = $break;
    //         } else {
    //           continue;
    //         }
    //       } else {
    //         if (($time_in < $start_break) && ($finish_break < $time_finish_shift)) {
    //           $between[] = $break;
    //         } else {
    //           continue;
    //         }
    //       }
    //     } else {
    //       continue;
    //     }
    //   }
    // }

    $datetime_in = changeDateFormat('Y-m-d H:i:s', $hour['attendance_in']);
    $datetime_out = changeDateFormat('Y-m-d H:i:s', $hour['attendance_out']);
    $time_in = changeDateFormat('H:i:s', $hour['attendance_in']);
    $time_out = changeDateFormat('H:i:s', $hour['attendance_out']);

    $time_start_shift = changeDateFormat('H:i:s', $workingtime->start);
    $time_finish_shift = changeDateFormat('H:i:s', $workingtime->finish);
    $time_min_in_shift = changeDateFormat('H:i:s', $workingtime->min_in);
    $time_max_out_shift = changeDateFormat('H:i:s', $workingtime->max_out);
    $between = array();
    $breaktime = 0;
    $nextDay = Carbon::parse($datetime_in)->addDays(1);
    $finishNow = changeDateFormat('Y-m-d H:i:s', Carbon::parse($datetime_in)->toDateString() . ' ' . $time_finish_shift);
    $finishTomorrow = changeDateFormat('Y-m-d H:i:s', $nextDay->toDateString() . ' ' . $time_finish_shift);
    $finishShift = $time_finish_shift < $time_start_shift ? $finishTomorrow : $finishNow;
    $finishShift = $finishShift > $datetime_out ? $datetime_out : $finishShift;
    foreach ($array as $break) {
      // if ($time_finish_shift > changeDateFormat('H:i:s', '22:00:00') && $time_finish_shift < changeDateFormat('H:i:s', '09:00:00')) {
      //   $start_break = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $nextDay->toDateString()) . ' ' . $break->start_time);
      //   $finish_break = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $nextDay->toDateString()) . ' ' . $break->finish_time);
      // } else {
      // }
      $dateIn = $time_start_shift > $break->start_time ? $nextDay->toDateString() : $datetime_in;
      $start_break = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $dateIn) . ' ' . $break->start_time);
      $finish_break = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $dateIn) . ' ' . $break->finish_time);
      $start_shift = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $dateIn) . ' ' . $time_start_shift);
      // if ($time_max_out_shift < $time_start_shift) {
      //   $day_start_break = 
      // } else {
      // }
      // if ($time_finish_shift < $time_start_shift) {
      //   $day = changeDateFormat('Y-m-d', $datetime_out);
      //   $day_start_break = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $start_break);
      //   $day_finish_break = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $finish_break);
      //   $day_finish_shift = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $time_finish_shift);
      //   $day_max_out_shift = changeDateFormat('Y-m-d H:i:s', $day . ' ' . $time_max_out_shift);
      //   if (($finishShift <= $start_break) && ($finish_break <= $datetime_out)) {
      //     $between[] = $break;
      //   } else {
      //     continue;
      //   }
      // } else {
      // }
      $diff = Carbon::parse($start_shift)->diffInHours(Carbon::parse($start_break));
      if ($diff >= 2) {
        $diffIn = Carbon::parse($datetime_in)->diffInHours(Carbon::parse($start_break));
        if ($diffIn >= 2) {
          if (((($datetime_in <= $start_break) && ($finish_break <= $finishShift)))) {
            $between[] = $break;
          } else {
            continue;
          }
        }
      }
    }

    foreach ($between as $value) {
      $breaktime += $value->breaktime;
    }
    return $breaktime;
  }
}

if (!function_exists('getAllBreaktime')) {
  function getAllBreaktime($array, $hour)
  {
    $datetime_in = changeDateFormat('Y-m-d H:i:s', $hour['attendance_in']);
    $datetime_out = changeDateFormat('Y-m-d H:i:s', $hour['attendance_out']);
    $time_in = changeDateFormat('H:i:s', $hour['attendance_in']);
    $time_out = changeDateFormat('H:i:s', $hour['attendance_out']);

    $between = array();
    $breaktime = 0;
    $nextDay = Carbon::parse($datetime_in)->addDays(1);
    $finishNow = changeDateFormat('Y-m-d H:i:s', Carbon::parse($datetime_in)->toDateString() . ' ' . $time_out);
    $finishTomorrow = changeDateFormat('Y-m-d H:i:s', $nextDay->toDateString() . ' ' . $time_out);
    $finishAttendance = $time_out < $time_in ? $finishTomorrow : $finishNow;
    $finishAttendance = $finishAttendance > $datetime_out ? $datetime_out : $finishAttendance;
    foreach ($array as $break) {
      $dateIn = $time_in > $break->start_time ? $nextDay->toDateString() : ($break->cross_date? $nextDay->toDateString():$datetime_in);
      $start_break = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $dateIn) . ' ' . $break->start_time);
      $finish_break = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $dateIn) . ' ' . $break->finish_time);
      $start_shift = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $dateIn) . ' ' . $time_in);
      $diff = Carbon::parse($start_shift)->diffInHours(Carbon::parse($start_break));
      $diffBreak = Carbon::parse($datetime_out)->diffInMinutes(Carbon::parse($finish_break));
      if ($diff >= 2) {
        $diffIn = Carbon::parse($datetime_in)->diffInHours(Carbon::parse($start_break));
        if ($diffIn >= 2) {
          if (($datetime_in <= $start_break) && ($finish_break <= $finishAttendance) && $diffBreak > 30) {
            $between[] = $break;
          } else {
            continue;
          }
        }
      }
    }

    foreach ($between as $value) {
      $breaktime += $value->breaktime;
    }
    return $breaktime;
  }
}

if (!function_exists('calculateOvertime')) {
  function calculateOvertime($attendance)
  {
    $overtime = Overtime::where('date', $attendance->attendance_date)->where('employee_id', $attendance->employee_id);
    $overtime->delete();
    if ($attendance && $attendance->adj_over_time > 0) {
      $readConfigs = Config::where('option', 'cut_off')->first();
      $cut_off = $readConfigs->value;
      if (date('d', strtotime($attendance->attendance_date)) > $cut_off) {
        $month = date('m', strtotime($attendance->attendance_date));
        $year = date('Y', strtotime($attendance->attendance_date));
        $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
        $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
      } else {
        $month =  date('m', strtotime($attendance->attendance_date));
        $year =  date('Y', strtotime($attendance->attendance_date));
      }
      $rules = OvertimeSchemeList::select('hour', 'amount')->where('overtime_scheme_id', '=', $attendance->overtime_scheme_id)->groupBy('hour','amount')->get();
       if ($rules) {
        $i = 0;
        $overtimes = $attendance->adj_over_time;
        $length = count($rules);
        foreach ($rules as $key => $value) {
          $date = Carbon::parse($attendance->attendance_date);
          $sallary = SalaryIncreases::GetSalaryIncreaseDetail($attendance->employee_id, $date->month, $date->year)->get();
          $overtimescheme = OvertimeScheme::where('id', $attendance->overtime_scheme_id)->first();
          $allowance_id = [];
          $overtimeallowances = OvertimeAllowance::where('overtime_scheme_id', $attendance->overtime_scheme_id)->get();
          foreach($overtimeallowances as $overtimeallowance){
              $allowance_id[] = $overtimeallowance->allowance_id;
          }
          if(count($allowance_id) > 0){
              $employeeAllowance = EmployeeAllowance::select(DB::raw('coalesce(sum(value::integer),0) as total'))->where('employee_id', $attendance->employee_id)
              ->where('month', $month)->where('year', $year)->whereIn('allowance_id', $allowance_id)->first();
          }
          else{
              $employeeAllowance = EmployeeAllowance::select(DB::raw('coalesce(sum(value::integer),0) as total'))->where('employee_id', $attendance->employee_id)
              ->where('month', $month)->where('year', $year)->whereIn('allowance_id', [-1])->first();
          }
          if($overtimescheme->type == 'BASIC'){
            if ($attendance->attendance_date >= $sallary->max('date')) {
                // $upcomingSalary = SalaryIncreases::whereHas('salaryIncreaseDetail', function($q) use ($emp_id){
                //     $q->where('employee_id', $emp_id);
                // })->where('date','=', $sallary->max('date'))->first();
                $getSallary = EmployeeSalary::where('employee_id', '=', $attendance->employee_id)->orderBy('created_at', 'desc')->first();
                if ($overtimes >= 0) {
                    $overtime = Overtime::create([
                        'employee_id'   => $attendance->employee_id,
                        'day'           => $attendance->day,
                        'scheme_rule'   => $value->hour,
                        'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                        'amount'        => $value->amount,
                        'basic_salary'  => $getSallary ? $getSallary->amount / 173 : 0,
                        'date'          => changeDateFormat('Y-m-d', $attendance->attendance_date),
                        'year'          => $year,
                        'month'         => $month,
                    ]);
                } else {
                    continue;
                }
                $overtime->final_salary = $overtime->hour * $overtime->amount * $overtime->basic_salary;
                $overtime->save();
                $i++;
                $overtimes = $overtimes - 1;
                if (!$overtime) {
                    DB::rollBack();
                    return response()->json([
                        'status'     => false,
                        'message'     => $overtime
                    ], 400);
                }
            } else {
                // $query = SalaryIncreases::with(['salaryIncreaseDetail' => function ($q) use ($emp_id)
                // {
                //     $q->where('employee_id', $emp_id);
                // }])->whereMonth('date', $date->month)->whereYear('date', $date->year)->where('date', '<', $approve->attendance_date)->orderBy('date', 'desc');
                // $salary = $query->first();
                $getSallary = EmployeeSalary::where('employee_id', '=', $attendance->employee_id)->orderBy('created_at', 'desc')->first();
                if ($overtimes >= 0) {
                    $overtime = Overtime::create([
                        'employee_id'   => $attendance->employee_id,
                        'day'           => $attendance->day,
                        'scheme_rule'   => $value->hour,
                        'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                        'amount'        => $value->amount,
                        'basic_salary'  => $getSallary ? $getSallary->amount / 173 : 0,
                        'date'          => changeDateFormat('Y-m-d', $attendance->attendance_date),
                        'year'          => $year,
                        'month'         => $month,
                    ]);
                } else {
                    continue;
                }
                $overtime->final_salary = $overtime->hour * $overtime->amount * $overtime->basic_salary;
                $overtime->save();
                $i++;
                $overtimes = $overtimes - 1;
                if (!$overtime) {
                    DB::rollBack();
                    return response()->json([
                        'status'     => false,
                        'message'     => $overtime
                    ], 400);
                }
            }
          }
          if($overtimescheme->type == 'BASIC & ALLOWANCE'){
              if ($attendance->attendance_date >= $sallary->max('date')) {
                  // $upcomingSalary = SalaryIncreases::whereHas('salaryIncreaseDetail', function($q) use ($emp_id){
                  //     $q->where('employee_id', $emp_id);
                  // })->where('date','=', $sallary->max('date'))->first();
                  $getSallary = EmployeeSalary::where('employee_id', '=', $attendance->employee_id)->orderBy('created_at', 'desc')->first();
                  if ($overtimes >= 0) {
                      $overtime = Overtime::create([
                          'employee_id'   => $attendance->employee_id,
                          'day'           => $attendance->day,
                          'scheme_rule'   => $value->hour,
                          'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                          'amount'        => $value->amount,
                          'basic_salary'  => $getSallary ? ($getSallary->amount + $employeeAllowance->total) / 173 : 0,
                          'date'          => changeDateFormat('Y-m-d', $attendance->attendance_date),
                          'year'          => $year,
                          'month'         => $month,
                      ]);
                  } else {
                      continue;
                  }
                  $overtime->final_salary = $overtime->hour * $overtime->amount * $overtime->basic_salary;
                  $overtime->save();
                  $i++;
                  $overtimes = $overtimes - 1;
                  if (!$overtime) {
                      DB::rollBack();
                      return response()->json([
                          'status'     => false,
                          'message'     => $overtime
                      ], 400);
                  }
              } else {
                  // $query = SalaryIncreases::with(['salaryIncreaseDetail' => function ($q) use ($emp_id)
                  // {
                  //     $q->where('employee_id', $emp_id);
                  // }])->whereMonth('date', $date->month)->whereYear('date', $date->year)->where('date', '<', $approve->attendance_date)->orderBy('date', 'desc');
                  // $salary = $query->first();
                  $getSallary = EmployeeSalary::where('employee_id', '=', $attendance->employee_id)->orderBy('created_at', 'desc')->first();
                  if ($overtimes >= 0) {
                      $overtime = Overtime::create([
                          'employee_id'   => $attendance->employee_id,
                          'day'           => $attendance->day,
                          'scheme_rule'   => $value->hour,
                          'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                          'amount'        => $value->amount,
                          'basic_salary'  => $getSallary ? ($getSallary->amount + $employeeAllowance->value) / 173 : 0,
                          'date'          => changeDateFormat('Y-m-d', $attendance->attendance_date),
                          'year'          => $year,
                          'month'         => $month,
                      ]);
                  } else {
                      continue;
                  }
                  $overtime->final_salary = $overtime->hour * $overtime->amount * $overtime->basic_salary;
                  $overtime->save();
                  $i++;
                  $overtimes = $overtimes - 1;
                  if (!$overtime) {
                      DB::rollBack();
                      return response()->json([
                          'status'     => false,
                          'message'     => $overtime
                      ], 400);
                  }
              }
          }
          if ($overtimescheme->type == 'ALLOWANCE') {
            if ($overtimes >= 0) {
                    $overtime = Overtime::create([
                        'employee_id'   => $attendance->employee_id,
                        'day'           => $attendance->day,
                        'scheme_rule'   => $value->hour,
                        'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                        'amount'        => $value->amount,
                        'basic_salary'  => $employeeAllowance->total ? $employeeAllowance->total / 173 : 0,
                        'date'          => changeDateFormat('Y-m-d', $attendance->attendance_date),
                        'year'          => $year,
                        'month'         => $month,
                    ]);
                } else {
                    continue;
                }
                $overtime->final_salary = $overtime->hour * $overtime->amount * $overtime->basic_salary;
                $overtime->save();
                $i++;
                $overtimes = $overtimes - 1;
                if (!$overtime) {
                    DB::rollBack();
                    return response()->json([
                        'status'     => false,
                        'message'     => $overtime
                    ], 400);
                }
        } 
        }
      }
    }
  }
}

if (!function_exists('calculateAllowance')) {
  function calculateAllowance($attendance)
  {
    // $month =  date('m', strtotime($attendance->attendance_date));
    // $year =  date('Y', strtotime($attendance->attendance_date));
    $readConfigs = Config::where('option', 'cut_off')->first();
    $cut_off = $readConfigs->value;
    if (date('d', strtotime($attendance->attendance_date)) > $cut_off) {
      $month = date('m', strtotime($attendance->attendance_date));
      $year = date('Y', strtotime($attendance->attendance_date));
      $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
      $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
    } else {
      $month =  date('m', strtotime($attendance->attendance_date));
      $year =  date('Y', strtotime($attendance->attendance_date));
    }
    $query = DB::table('attendances');
    $query->select(
      'attendances.employee_id as employee_id',
      'attendances.workingtime_id as workingtime_id',
      'attendances.attendance_date as date',
      'allowances.reccurance as reccuran',
      'employee_allowances.allowance_id as allowance_id',
      'employee_allowances.value as value',
      'employee_allowances.type as type',
      'workingtime_allowances.workingtime_id as workingtime'
    );
    $query->leftJoin('employee_allowances', 'attendances.employee_id', '=', 'employee_allowances.employee_id');
    $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
    $query->leftJoin('workingtime_allowances', 'workingtime_allowances.allowance_id', '=', 'allowances.id');
    $query->where('attendances.id', '=', $attendance->id);
    $query->where('employee_allowances.status', '=', 1);
    $query->where('allowances.reccurance', '=', 'daily');
    $query->where('employee_allowances.month', $month);
    $query->where('employee_allowances.year', $year);
    $histories = $query->get();
    $deletedetail = EmployeeDetailAllowance::where('employee_id', $attendance->employee_id)->where('tanggal_masuk', $attendance->attendance_date);
    $deletedetail->delete();
    foreach ($histories as $history) {
      if ($history) {
        if ($history->workingtime) {
          if ($history->workingtime == $history->workingtime_id) {
            $employeedetailallowance = EmployeeDetailAllowance::create([
              'employee_id' => $history->employee_id,
              'allowance_id' => $history->allowance_id,
              'workingtime_id' => $history->workingtime_id,
              'tanggal_masuk' => $history->date,
              'value' => $history->value,
              'month' => $month,
              'year' => $year
            ]);

            if ($employeedetailallowance) {
              $query = EmployeeAllowance::select('employee_allowances.*');
              $query->where('employee_id', '=', $history->employee_id);
              $query->where('allowance_id', '=', $history->allowance_id);
              $query->where('month', $month);
              $query->where('year', $year);
              $updatefactor = $query->first();
              $updatequery = DB::table('employee_detailallowances');
              $updatequery->select('employee_detailallowances.*', DB::raw('count(tanggal_masuk) as date'));
              $updatequery->where('employee_detailallowances.employee_id', '=', $history->employee_id);
              $updatequery->where('employee_detailallowances.allowance_id', '=', $history->allowance_id);
              $updatequery->where('employee_detailallowances.month', '=',$month);
              $updatequery->where('employee_detailallowances.year', '=', $year);
              $updatequery->groupBy('employee_detailallowances.id');
              $updatecount = $updatequery->get()->count();
              if ($updatefactor) {
                $updatefactor->factor = $updatecount;
                $updatefactor->save();
              }
            }
          }
        } else {
          $employeedetailallowance = EmployeeDetailAllowance::create([
            'employee_id' => $history->employee_id,
            'allowance_id' => $history->allowance_id,
            'workingtime_id' => $history->workingtime_id,
            'tanggal_masuk' => $history->date,
            'value' => $history->value,
            'month' => $month,
            'year' => $year
          ]);

          if ($employeedetailallowance) {
            $month =  date('m', strtotime($attendance->attendance_date));
            $year =  date('Y', strtotime($attendance->attendance_date));
            $query = EmployeeAllowance::select('employee_allowances.*');
            $query->where('employee_id', '=', $history->employee_id);
            $query->where('allowance_id', '=', $history->allowance_id);
            $query->where('month', $month);
            $query->where('year', $year);
            $updatefactor = $query->first();
            $updatequery = DB::table('employee_detailallowances');
            $updatequery->select('employee_detailallowances.*', DB::raw('count(tanggal_masuk) as date'));
            $updatequery->where('employee_detailallowances.employee_id', '=', $history->employee_id);
            $updatequery->where('employee_detailallowances.allowance_id', '=', $history->allowance_id);
            $updatequery->where('employee_detailallowances.month', '=', $month);
            $updatequery->where('employee_detailallowances.year', '=', $year);
            $updatequery->groupBy('employee_detailallowances.id');
            $updatecount = $updatequery->get()->count();
            if ($updatefactor) {
              $updatefactor->factor = $updatecount;
              $updatefactor->save();
            }
          }
        }
      } else {
        DB::rollBack();
        return response()->json([
          'status'      => false,
          'message'     => $history
        ], 400);
      }
    }
    if ($attendance->workingtime_id) {
      // hourly
      $query = DB::table('attendances');
      $query->select(
        'employee_allowances.*',
        'attendances.employee_id as employee_id',
        'attendances.workingtime_id as workingtime_id',
        'attendances.attendance_date as date',
        'allowances.reccurance as reccuran',
        'employee_allowances.allowance_id as allowance_id',
        'attendances.adj_working_time as value',
        'employee_allowances.type as type',
        'workingtime_allowances.workingtime_id as workingtime'
      );
      $query->leftJoin('employee_allowances', 'attendances.employee_id', '=', 'employee_allowances.employee_id');
      $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
      $query->leftJoin('workingtime_allowances', 'workingtime_allowances.allowance_id', '=', 'allowances.id');
      $query->where('attendances.id', '=', $attendance->id);
      $query->where('employee_allowances.status', '=', 1);
      $query->where('allowances.reccurance', '=', 'hourly');
      $query->where('employee_allowances.month', $month);
      $query->where('employee_allowances.year', $year);
      $query->where('employee_allowances.employee_id', '=', $attendance->employee_id);
      $hourly = $query->get();
      foreach ($hourly as $hour) {
        if ($hour) {
          if ($hour->workingtime) {
            if ($hour->workingtime == $hour->workingtime_id) {
              $employeedetailallowance = EmployeeDetailAllowance::create([
                'employee_id' => $hour->employee_id,
                'allowance_id' => $hour->allowance_id,
                'workingtime_id' => $hour->workingtime_id,
                'tanggal_masuk' => $hour->date,
                'value' => $hour->value,
                'month' => $month,
                'year' => $year
              ]);
  
              if ($employeedetailallowance) {
                $query = EmployeeAllowance::select('employee_allowances.*');
                $query->where('employee_id', '=', $hour->employee_id);
                $query->where('allowance_id', '=', $hour->allowance_id);
                $query->where('month', $month);
                $query->where('year', $year);
                $updatefactor = $query->first();
                $updatequery = DB::table('employee_detailallowances');
                $updatequery->where('employee_detailallowances.employee_id', '=', $hour->employee_id);
                $updatequery->where('employee_detailallowances.allowance_id', '=', $hour->allowance_id);
                $updatequery->where('employee_detailallowances.month', '=', $month);
                $updatequery->where('employee_detailallowances.year', '=', $year);
                $updatequery->groupBy('employee_detailallowances.id');
                $updatecount = $updatequery->get()->sum('value');
                if ($updatefactor) {
                  $updatefactor->factor = $updatecount;
                  $updatefactor->save();
                }
              }
            }
          } else {
            $employeedetailallowance = EmployeeDetailAllowance::create([
              'employee_id' => $hour->employee_id,
              'allowance_id' => $hour->allowance_id,
              'workingtime_id' => $hour->workingtime_id,
              'tanggal_masuk' => $hour->date,
              'value' => $hour->value,
              'month' => $month,
              'year' => $year
            ]);
  
            if ($employeedetailallowance) {
              $readConfigs = Config::where('option', 'cut_off')->first();
              $cut_off = $readConfigs->value;
              if (date('d', strtotime($attendance->attendance_date)) > $cut_off) {
                $month = date('m', strtotime($attendance->attendance_date));
                $year = date('Y', strtotime($attendance->attendance_date));
                $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
              } else {
                $month =  date('m', strtotime($attendance->attendance_date));
                $year =  date('Y', strtotime($attendance->attendance_date));
              }
              $query = EmployeeAllowance::select('employee_allowances.*');
              $query->where('employee_id', '=', $hour->employee_id);
              $query->where('allowance_id', '=', $hour->allowance_id);
              $query->where('month', $month);
              $query->where('year', $year);
              $updatefactor = $query->first();
              $updatequery = DB::table('employee_detailallowances');
              // $updatequery->select('employee_detailallowances.*', DB::raw('count(tanggal_masuk) as date'));
              $updatequery->where('employee_detailallowances.employee_id', '=', $hour->employee_id);
              $updatequery->where('employee_detailallowances.allowance_id', '=', $hour->allowance_id);
              $updatequery->where('employee_detailallowances.month', '=', $month);
              $updatequery->where('employee_detailallowances.year', '=', $year);
              $updatequery->groupBy('employee_detailallowances.id');
              $updatecount = $updatequery->get()->sum('value');
              if ($updatefactor) {
                $updatefactor->factor = $updatecount;
                $updatefactor->save();
              }
            }
          }
        } else {
          DB::rollBack();
          return response()->json([
            'status'      => false,
            'message'     => $hour
          ], 400);
        }
      }

      // breaktime
      $query = DB::table('attendances');
      $query->select(
        'employee_allowances.*',
        'attendances.employee_id as employee_id',
        'attendances.workingtime_id as workingtime_id',
        'attendances.attendance_date as date',
        'allowances.reccurance as reccuran',
        'allowances.allowance as allowance_name',
        'employees.name as employee_name',
        'employee_allowances.allowance_id as allowance_id',
        'attendances.breaktime as value',
        'employee_allowances.type as type',
        'workingtime_allowances.workingtime_id as workingtime'
      );
      $query->leftJoin('employee_allowances', 'attendances.employee_id', '=', 'employee_allowances.employee_id');
      $query->leftJoin('employees', 'employees.id', '=', 'employee_allowances.employee_id');
      $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
      $query->leftJoin('workingtime_allowances', 'workingtime_allowances.allowance_id', '=', 'allowances.id');
      $query->where('attendances.id', '=', $attendance->id);
      $query->where('employee_allowances.status', '=', 1);
      $query->where('allowances.reccurance', '=', 'breaktime');
      $query->where('employee_allowances.month', $month);
      $query->where('employee_allowances.year', $year);
      $query->where('employee_allowances.employee_id', '=', $attendance->employee_id);
      $breaktimes = $query->get();
      foreach ($breaktimes as $breaktime) {
        if ($breaktime) {
          if ($breaktime->workingtime) {
            if ($breaktime->workingtime == $breaktime->workingtime_id) {
              try {
                $employeedetailallowance = EmployeeDetailAllowance::create([
                  'employee_id' => $breaktime->employee_id,
                  'allowance_id' => $breaktime->allowance_id,
                  'workingtime_id' => $breaktime->workingtime_id,
                  'tanggal_masuk' => $breaktime->date,
                  'value' => $breaktime->value,
                  'month' => $month,
                  'year' => $year
                ]);
              } catch (\Illuminate\Database\QueryException $e) {
                return response()->json([
                  'status'      => false,
                  'message'     => 'There is error in employee name ' . $breaktime->employee_name . ' when approved attendance in date ' . $breaktime->date . ' and allowance ' . $breaktime->allowance_name
                ], 400);
              }

              if ($employeedetailallowance) {
                $query = EmployeeAllowance::select('employee_allowances.*');
                $query->where('employee_id', '=', $breaktime->employee_id);
                $query->where('allowance_id', '=', $breaktime->allowance_id);
                $query->where('month', $month);
                $query->where('year', $year);
                $updatefactor = $query->first();
                $updatequery = DB::table('employee_detailallowances');
                $updatequery->where('employee_detailallowances.employee_id', '=', $breaktime->employee_id);
                $updatequery->where('employee_detailallowances.allowance_id', '=', $breaktime->allowance_id);
                $updatequery->where('employee_detailallowances.month', '=', $month);
                $updatequery->where('employee_detailallowances.year', '=', $year);
                $updatequery->groupBy('employee_detailallowances.id');
                $updatecount = $updatequery->get()->sum('value');
                if ($updatefactor) {
                  $updatefactor->factor = $updatecount;
                  $updatefactor->save();
                }
              }
            }
          } else {
            try {
              $employeedetailallowance = EmployeeDetailAllowance::create([
                'employee_id' => $breaktime->employee_id,
                'allowance_id' => $breaktime->allowance_id,
                'workingtime_id' => $breaktime->workingtime_id,
                'tanggal_masuk' => $breaktime->date,
                'value' => $breaktime->value,
                'month' => $month,
                'year' => $year
              ]);
            } catch (\Illuminate\Database\QueryException $e) {
              return response()->json([
                'status'      => false,
                'message'     => 'There is error in employee name ' . $breaktime->employee_name . ' when approved attendance in date ' . $breaktime->date . ' and allowance ' . $breaktime->allowance_name
              ], 400);
            }

            if ($employeedetailallowance) {
              if (date('d', strtotime($attendance->attendance_date)) > $cut_off) {
                $month = date('m', strtotime($attendance->attendance_date));
                $year = date('Y', strtotime($attendance->attendance_date));
                $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
              } else {
                $month =  date('m', strtotime($attendance->attendance_date));
                $year =  date('Y', strtotime($attendance->attendance_date));
              }
              $query = EmployeeAllowance::select('employee_allowances.*');
              $query->where('employee_id', '=', $breaktime->employee_id);
              $query->where('allowance_id', '=', $breaktime->allowance_id);
              $query->where('month', $month);
              $query->where('year', $year);
              $updatefactor = $query->first();
              $updatequery = DB::table('employee_detailallowances');
              // $updatequery->select('employee_detailallowances.*', DB::raw('count(tanggal_masuk) as date'));
              $updatequery->where('employee_detailallowances.employee_id', '=', $breaktime->employee_id);
              $updatequery->where('employee_detailallowances.allowance_id', '=', $breaktime->allowance_id);
              $updatequery->where('employee_detailallowances.month', '=', $month);
              $updatequery->where('employee_detailallowances.year', '=', $year);
              $updatequery->groupBy('employee_detailallowances.id');
              $updatecount = $updatequery->get()->sum('value');
              if ($updatefactor) {
                $updatefactor->factor = $updatecount;
                $updatefactor->save();
              }
            }
          }
        } else {
          DB::rollBack();
          return response()->json([
            'status'      => false,
            'message'     => $breaktime
          ], 400);
        }
      }
    }
    // $query = EmployeeAllowance::select('employee_allowances.*');
    // $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
    // $query->where('allowances.reccurance', '=', 'hourly');
    // $query->where('employee_allowances.employee_id', '=', $attendance->employee_id);
    // $hourly = $query->first();
    // if ($hourly) {
    //   $hourly->factor = $hourly->factor + $attendance->adj_working_time;
    //   $hourly->save();
    // }
  }
}

if (!function_exists('deleteAllowance')) {
  function deleteAllowance($attendance)
  {
    $readConfigs = Config::where('option', 'cut_off')->first();
    $cut_off = $readConfigs->value;
    if (date('d', strtotime($attendance->attendance_date)) > $cut_off) {
      $month = date('m', strtotime($attendance->attendance_date));
      $year = date('Y', strtotime($attendance->attendance_date));
      $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
      $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
    } else {
      $month =  date('m', strtotime($attendance->attendance_date));
      $year =  date('Y', strtotime($attendance->attendance_date));
    }
    // daily
    $query = DB::table('attendances');
    $query->select(
      'attendances.employee_id as employee_id',
      'attendances.workingtime_id as workingtime_id',
      'attendances.attendance_date as date',
      'allowances.reccurance as reccuran',
      'employee_allowances.allowance_id as allowance_id',
      'employee_allowances.value as value',
      'employee_allowances.type as type',
      'workingtime_allowances.workingtime_id as workingtime'
    );
    $query->leftJoin('employee_allowances', 'attendances.employee_id', '=', 'employee_allowances.employee_id');
    $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
    $query->leftJoin('workingtime_allowances', 'workingtime_allowances.allowance_id', '=', 'allowances.id');
    $query->where('attendances.id', '=', $attendance->id);
    $query->where('employee_allowances.status', '=', 1);
    $query->where('allowances.reccurance', '=', 'daily');
    $query->where('employee_allowances.month', $month);
    $query->where('employee_allowances.year', $year);
    $histories = $query->get();
    $deletedetail = EmployeeDetailAllowance::where('employee_id', $attendance->employee_id)->where('tanggal_masuk', $attendance->attendance_date);
    $deletedetail->delete();
    foreach ($histories as $history) {
      if ($history) {
        if ($history->workingtime) {
          if ($history->workingtime == $history->workingtime_id) {
            $readConfigs = Config::where('option', 'cut_off')->first();
            $cut_off = $readConfigs->value;
            if (date('d', strtotime($attendance->attendance_date)) > $cut_off) {
              $month = date('m', strtotime($attendance->attendance_date));
              $year = date('Y', strtotime($attendance->attendance_date));
              $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
              $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
            } else {
              $month =  date('m', strtotime($attendance->attendance_date));
              $year =  date('Y', strtotime($attendance->attendance_date));
            }
            $query = EmployeeAllowance::select('employee_allowances.*');
            $query->where('employee_id', '=', $history->employee_id);
            $query->where('allowance_id', '=', $history->allowance_id);
            $query->where('month', $month);
            $query->where('year', $year);
            $updatefactor = $query->first();
            $updatequery = DB::table('employee_detailallowances');
            $updatequery->select('employee_detailallowances.*', DB::raw('count(tanggal_masuk) as date'));
            $updatequery->where('employee_detailallowances.employee_id', '=', $history->employee_id);
            $updatequery->where('employee_detailallowances.allowance_id', '=', $history->allowance_id);
            $updatequery->where('employee_detailallowances.month', '=', $month);
            $updatequery->where('employee_detailallowances.year', '=', $year);
            $updatequery->groupBy('employee_detailallowances.id');
            $updatecount = $updatequery->get()->count();
            if ($updatefactor) {
              $updatefactor->factor = $updatecount;
              $updatefactor->save();
            }
          }
        } else {
          $readConfigs = Config::where('option', 'cut_off')->first();
          $cut_off = $readConfigs->value;
          if (date('d', strtotime($attendance->attendance_date)) > $cut_off) {
            $month = date('m', strtotime($attendance->attendance_date));
            $year = date('Y', strtotime($attendance->attendance_date));
            $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
            $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
          } else {
            $month =  date('m', strtotime($attendance->attendance_date));
            $year =  date('Y', strtotime($attendance->attendance_date));
          }
          $query = EmployeeAllowance::select('employee_allowances.*');
          $query->where('employee_id', '=', $history->employee_id);
          $query->where('allowance_id', '=', $history->allowance_id);
          $query->where('month', $month);
          $query->where('year', $year);
          $updatefactor = $query->first();
          $updatequery = DB::table('employee_detailallowances');
          $updatequery->select('employee_detailallowances.*', DB::raw('count(tanggal_masuk) as date'));
          $updatequery->where('employee_detailallowances.employee_id', '=', $history->employee_id);
          $updatequery->where('employee_detailallowances.allowance_id', '=', $history->allowance_id);
          $updatequery->where('employee_detailallowances.month', '=', $month);
          $updatequery->where('employee_detailallowances.year', '=', $year);
          $updatequery->groupBy('employee_detailallowances.id');
          $updatecount = $updatequery->get()->count();
          if ($updatefactor) {
            $updatefactor->factor = $updatecount;
            $updatefactor->save();
          }
        }
      } else {
        DB::rollBack();
        return response()->json([
          'status'      => false,
          'message'     => $history
        ], 400);
      }
    }
    // hourly
    $query = DB::table('attendances');
    $query->select(
      'employee_allowances.*',
      'attendances.employee_id as employee_id',
      'attendances.workingtime_id as workingtime_id',
      'attendances.attendance_date as date',
      'allowances.reccurance as reccuran',
      'employee_allowances.allowance_id as allowance_id',
      'attendances.adj_working_time as value',
      'employee_allowances.type as type',
      'workingtime_allowances.workingtime_id as workingtime'
    );
    $query->leftJoin('employee_allowances', 'attendances.employee_id', '=', 'employee_allowances.employee_id');
    $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
    $query->leftJoin('workingtime_allowances', 'workingtime_allowances.allowance_id', '=', 'allowances.id');
    $query->where('attendances.id', '=', $attendance->id);
    $query->where('employee_allowances.status', '=', 1);
    $query->where('allowances.reccurance', '=', 'hourly');
    $query->where('employee_allowances.month', $month);
    $query->where('employee_allowances.year', $year);
    $query->where('employee_allowances.employee_id', '=', $attendance->employee_id);
    $hourly = $query->get();
    foreach ($hourly as $hour) {
      if ($hour) {
        if ($hour->workingtime) {
          if ($hour->workingtime == $hour->workingtime_id) {
            $employeedetailallowance = EmployeeDetailAllowance::create([
              'employee_id' => $hour->employee_id,
              'allowance_id' => $hour->allowance_id,
              'workingtime_id' => $hour->workingtime_id,
              'tanggal_masuk' => $hour->date,
              'value' => $hour->value,
              'month' => $month,
              'year' => $year
            ]);

            if ($employeedetailallowance) {
              $query = EmployeeAllowance::select('employee_allowances.*');
              $query->where('employee_id', '=', $hour->employee_id);
              $query->where('allowance_id', '=', $hour->allowance_id);
              $query->where('month', $month);
              $query->where('year', $year);
              $updatefactor = $query->first();
              $updatequery = DB::table('employee_detailallowances');
              $updatequery->where('employee_detailallowances.employee_id', '=', $hour->employee_id);
              $updatequery->where('employee_detailallowances.allowance_id', '=', $hour->allowance_id);
              $updatequery->where('employee_detailallowances.month', '=', $month);
              $updatequery->where('employee_detailallowances.year', '=', $year);
              $updatequery->groupBy('employee_detailallowances.id');
              $updatecount = $updatequery->get()->sum('value');
              if ($updatefactor) {
                $updatefactor->factor = $updatecount;
                $updatefactor->save();
              }
            }
          }
        } else {
          $employeedetailallowance = EmployeeDetailAllowance::create([
            'employee_id' => $hour->employee_id,
            'allowance_id' => $hour->allowance_id,
            'workingtime_id' => $hour->workingtime_id,
            'tanggal_masuk' => $hour->date,
            'value' => $hour->value,
            'month' => $month,
            'year' => $year
          ]);

          if ($employeedetailallowance) {
            $readConfigs = Config::where('option', 'cut_off')->first();
            $cut_off = $readConfigs->value;
            if (date('d', strtotime($attendance->attendance_date)) > $cut_off) {
              $month = date('m', strtotime($attendance->attendance_date));
              $year = date('Y', strtotime($attendance->attendance_date));
              $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
              $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
            } else {
              $month =  date('m', strtotime($attendance->attendance_date));
              $year =  date('Y', strtotime($attendance->attendance_date));
            }
            $query = EmployeeAllowance::select('employee_allowances.*');
            $query->where('employee_id', '=', $hour->employee_id);
            $query->where('allowance_id', '=', $hour->allowance_id);
            $query->where('month', $month);
            $query->where('year', $year);
            $updatefactor = $query->first();
            $updatequery = DB::table('employee_detailallowances');
            // $updatequery->select('employee_detailallowances.*', DB::raw('count(tanggal_masuk) as date'));
            $updatequery->where('employee_detailallowances.employee_id', '=', $hour->employee_id);
            $updatequery->where('employee_detailallowances.allowance_id', '=', $hour->allowance_id);
            $updatequery->where('employee_detailallowances.month', '=', $month);
            $updatequery->where('employee_detailallowances.year', '=', $year);
            $updatequery->groupBy('employee_detailallowances.id');
            $updatecount = $updatequery->get()->sum('value');
            if ($updatefactor) {
              $updatefactor->factor = $updatecount;
              $updatefactor->save();
            }
          }
        }
      } else {
        DB::rollBack();
        return response()->json([
          'status'      => false,
          'message'     => $hour
        ], 400);
      }
    }

    // breaktime
    $query = DB::table('attendances');
    $query->select(
      'employee_allowances.*',
      'attendances.employee_id as employee_id',
      'attendances.workingtime_id as workingtime_id',
      'attendances.attendance_date as date',
      'allowances.reccurance as reccuran',
      'allowances.allowance as allowance_name',
      'employees.name as employee_name',
      'employee_allowances.allowance_id as allowance_id',
      'attendances.breaktime as value',
      'employee_allowances.type as type',
      'workingtime_allowances.workingtime_id as workingtime'
    );
    $query->leftJoin('employee_allowances', 'attendances.employee_id', '=', 'employee_allowances.employee_id');
    $query->leftJoin('employees', 'employees.id', '=', 'employee_allowances.employee_id');
    $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
    $query->leftJoin('workingtime_allowances', 'workingtime_allowances.allowance_id', '=', 'allowances.id');
    $query->where('attendances.id', '=', $attendance->id);
    $query->where('employee_allowances.status', '=', 1);
    $query->where('allowances.reccurance', '=', 'breaktime');
    $query->where('employee_allowances.month', $month);
    $query->where('employee_allowances.year', $year);
    $query->where('employee_allowances.employee_id', '=', $attendance->employee_id);
    $breaktimes = $query->get();
    foreach ($breaktimes as $breaktime) {
      if ($breaktime) {
        if ($breaktime->workingtime) {
          if ($breaktime->workingtime == $breaktime->workingtime_id) {
            try {
              $employeedetailallowance = EmployeeDetailAllowance::create([
                'employee_id' => $breaktime->employee_id,
                'allowance_id' => $breaktime->allowance_id,
                'workingtime_id' => $breaktime->workingtime_id,
                'tanggal_masuk' => $breaktime->date,
                'value' => $breaktime->value,
                'month' => $month,
                'year' => $year
              ]);
            } catch (\Illuminate\Database\QueryException $e) {
              return response()->json([
                'status'      => false,
                'message'     => 'There is error in employee name ' . $breaktime->employee_name . ' when approved attendance in date ' . $breaktime->date . ' and allowance ' . $breaktime->allowance_name
              ], 400);
            }

            if ($employeedetailallowance) {
              $query = EmployeeAllowance::select('employee_allowances.*');
              $query->where('employee_id', '=', $breaktime->employee_id);
              $query->where('allowance_id', '=', $breaktime->allowance_id);
              $query->where('month', $month);
              $query->where('year', $year);
              $updatefactor = $query->first();
              $updatequery = DB::table('employee_detailallowances');
              $updatequery->where('employee_detailallowances.employee_id', '=', $breaktime->employee_id);
              $updatequery->where('employee_detailallowances.allowance_id', '=', $breaktime->allowance_id);
              $updatequery->where('employee_detailallowances.month', '=', $month);
              $updatequery->where('employee_detailallowances.year', '=', $year);
              $updatequery->groupBy('employee_detailallowances.id');
              $updatecount = $updatequery->get()->sum('value');
              if ($updatefactor) {
                $updatefactor->factor = $updatecount;
                $updatefactor->save();
              }
            }
          }
        } else {
          try {
            $employeedetailallowance = EmployeeDetailAllowance::create([
              'employee_id' => $breaktime->employee_id,
              'allowance_id' => $breaktime->allowance_id,
              'workingtime_id' => $breaktime->workingtime_id,
              'tanggal_masuk' => $breaktime->date,
              'value' => $breaktime->value,
              'month' => $month,
              'year' => $year
            ]);
          } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
              'status'      => false,
              'message'     => 'There is error in employee name ' . $breaktime->employee_name . ' when approved attendance in date ' . $breaktime->date . ' and allowance ' . $breaktime->allowance_name
            ], 400);
          }

          if ($employeedetailallowance) {
            if (date('d', strtotime($attendance->attendance_date)) > $cut_off) {
              $month = date('m', strtotime($attendance->attendance_date));
              $year = date('Y', strtotime($attendance->attendance_date));
              $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
              $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
            } else {
              $month =  date('m', strtotime($attendance->attendance_date));
              $year =  date('Y', strtotime($attendance->attendance_date));
            }
            $query = EmployeeAllowance::select('employee_allowances.*');
            $query->where('employee_id', '=', $breaktime->employee_id);
            $query->where('allowance_id', '=', $breaktime->allowance_id);
            $query->where('month', $month);
            $query->where('year', $year);
            $updatefactor = $query->first();
            $updatequery = DB::table('employee_detailallowances');
            // $updatequery->select('employee_detailallowances.*', DB::raw('count(tanggal_masuk) as date'));
            $updatequery->where('employee_detailallowances.employee_id', '=', $breaktime->employee_id);
            $updatequery->where('employee_detailallowances.allowance_id', '=', $breaktime->allowance_id);
            $updatequery->where('employee_detailallowances.month', '=', $month);
            $updatequery->where('employee_detailallowances.year', '=', $year);
            $updatequery->groupBy('employee_detailallowances.id');
            $updatecount = $updatequery->get()->sum('value');
            if ($updatefactor) {
              $updatefactor->factor = $updatecount;
              $updatefactor->save();
            }
          }
        }
      } else {
        DB::rollBack();
        return response()->json([
          'status'      => false,
          'message'     => $breaktime
        ], 400);
      }
    }
  }
}

if (!function_exists('setRecordLogHistory')) {
  function setrecordloghistory($user_id,$employee_id,$department_id,$page,$activity,$detail,$result){
        $employee = LogHistory::create([
            'date'                  => date('Y-m-d h:i:s'),
            'user_id'               => $user_id,
            'employee_id'           => $employee_id,
            'department_id'         => $department_id,
            'page'                  => $page,
            'activity'              => $activity,
            'detail'                => $detail,
            'result'                => $result,
        ]);
    }
}