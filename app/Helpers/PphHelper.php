<?php

const MAX_POSITION_ALLOWANCE = 500000;
const PERCENTAGE_POSITION_ALLOWANCE = 0.05;
const DEFAULT_MULTIPLIER_MONTH = 12;
const PPH_ITERATION = 4;
const PPH_FIRST_ITERATION_VALUE = 50000000;
const PPH_FIRST_ITERATION_PERCENTAGE = 0.05;
const PPH_SECOND_ITERATION_VALUE = 250000000;
const PPH_SECOND_ITERATION_PERCENTAGE = 0.15;
const PPH_THIRD_ITERATION_VALUE = 500000000;
const PPH_THIRD_ITERATION_PERCENTAGE = 0.25;
const PPH_LAST_ITERATION_PERCENTAGE = 0.3;
CONST PPH_MULTIPLE_NOT_HAVE_NPWP = 1.2;
if (!function_exists('getPositionAllowance')) {
  /**
   * Function helper to get position allowance from given parameter
   *
   * @param float $grossSalary gross salary send from salary_reports table (gross_salary) column
   * @return float
   */
  function getPositionAllowance(float $grossSalary)
  {
    $positionAllowance = ($grossSalary * PERCENTAGE_POSITION_ALLOWANCE > MAX_POSITION_ALLOWANCE) ? MAX_POSITION_ALLOWANCE : $grossSalary * PERCENTAGE_POSITION_ALLOWANCE;
    return $positionAllowance;
  }
}
if (!function_exists('getGrossSalaryAfterPositionAllowance')) {
  /**
   * Function helper to get gross salary after deducting by position allowance
   *
   * @param float $grossSalary gross salary send from salary_reports table (gross_salary) column
   * @param float $positionAllowance
   * @return float
   */
  function getGrossSalaryAfterPositionAllowance(float $grossSalary, float $positionAllowance)
  {
    $grossNew = $grossSalary - $positionAllowance;

    return $grossNew;
  }
}

if(!function_exists('getGrossSalaryJoinMonth')){

  /**
   * Function helper to get gross salary additional join date
   *
   * @param float $grossSalary gross salary send from salary_reports table (gross_salary) column
   * @param integer $multipleMonth
   * @return float
   */
  function getGrossSalaryJoinMonth(float $grossSalary, $multipleMonth){
    $grossJoinMonth = $grossSalary * $multipleMonth;

    return $grossJoinMonth;
  }
}
if(!function_exists('getTotal')){

  /**
   * grt total after additional gross salary join month and thr
   *
   * @param float $grossJoinMonth
   * @param float $thr
   * @return float
   */
  function getTotal(float $grossJoinMonth, float $thr){
    $total = $grossJoinMonth + $thr;
    return $total;
  }
}
if(!function_exists('getTotalPositionAllowance')){

  /**
   * Additional Total and Percentage
   *
   * @param float $total
   * @return float   
   * */
  function getTotalPositionAllowance(float $total){
    $total_position_allowance = $total * PERCENTAGE_POSITION_ALLOWANCE;

    return $total_position_allowance;
  }
}
if(!function_exists('getNetSalaryThr')){

  /**
   * net salary dedecution from total and total position allowance
   *
   * @param float $total
   * @param float $total_position_allowance
   * @return float
   */
  function getNetSalaryThr(float $total, float $total_position_allowance)
  {
    $netSalary = $total - $total_position_allowance;

    return $netSalary;
  }
}
if(!function_exists('getPkpThr')){

  /**
   * get pkp deduction from net salary and ptkp
   *
   * @param float $netSalary
   * @param float $ptkp
   * @return float
   */
  function getPkpThr(float $netSalary, float $ptkp){
    $pkp = $netSalary - $ptkp;

    return $pkp;
  }
}
if(!function_exists('getTarifThr')){

  /**
   * get Tarif additional pkp
   *
   * @param float $pkp
   * @return float
   */
  function getTarifThr(float $pkp){
    $tarif = $pkp * PERCENTAGE_POSITION_ALLOWANCE;
    return $tarif;
  }
}
if (!function_exists('getMultiplierMonth')) {
  /**
   * Function helper to get multiplier month
   *
   * @param date $joinDate join date from employees table
   * @return int
   */
  function getMultiplierMonth($joinDate)
  {
    $yearJoinDate = changeDateFormat('Y', $joinDate);
    $yearNow = changeDateFormat('Y', now());
    $joinMonth = changeDateFormat('n', $joinDate);
    $multiplierMonth = $yearJoinDate == $yearNow ? (DEFAULT_MULTIPLIER_MONTH - $joinMonth) + 1 : DEFAULT_MULTIPLIER_MONTH;

    return $multiplierMonth;
  }
}
if (!function_exists('getGrossSalaryPerYear')) {
  /**
   * Function helper to get gross salary per year
   *
   * @param float $grossSalary gross salary send from salary_reports table (gross_salary) column
   * @param date $joinDate join date from employees table
   * @return float
   */
  function getGrossSalaryPerYear(float $grossSalaryAfterPositionAllowance, $multipleMonth)
  {
    $grossSalaryPerYear = $grossSalaryAfterPositionAllowance * $multipleMonth;

    return $grossSalaryPerYear;
  }
}
if (!function_exists('getPKP')) {
  /**
   * Function helper to get PKP where is gross salary per year - ptkp
   *
   * @param float $grossSalaryPerYear
   * @param float $ptkp
   * @return float
   */
  function getPKP(float $grossSalaryPerYear, float $ptkp)
  {
    $pkp = $grossSalaryPerYear - $ptkp;

    return $pkp;
  }
}
if (!function_exists('getPPH21Yearly')) {
  /**
   * Function helper to get PPH21 yearly
   *
   * @param float $pkp pkp value
   * @param string $npwp npwp number from employees table
   * @return float
   */
  function getPPH21Yearly(float $pkps, string $npwp)
  {
    $pkp = $pkps;
    $pph21 = 0;
    $iteration = PPH_ITERATION;

    if ($npwp) {
      for ($i=1; $i <= $iteration; $i++) { 
        if ($pkp > 0) {
          if ($i == 1) {
            if ($pkp <= PPH_FIRST_ITERATION_VALUE) {
              $pph21 += $pkp * PPH_FIRST_ITERATION_PERCENTAGE;
            } else {
              $pph21 += PPH_FIRST_ITERATION_VALUE * PPH_FIRST_ITERATION_PERCENTAGE;
            }
            $pkp -= PPH_FIRST_ITERATION_VALUE;
          }
          if ($i == 2) {
            if ($pkp >= PPH_SECOND_ITERATION_VALUE) {
              $pph21 += PPH_SECOND_ITERATION_VALUE * PPH_SECOND_ITERATION_PERCENTAGE;
            } else {
              $pph21 += $pkp * PPH_SECOND_ITERATION_PERCENTAGE;
            }
            $pkp -= PPH_SECOND_ITERATION_VALUE; 
          }
          if ($i == 3) {
            if ($pkp >= PPH_THIRD_ITERATION_VALUE) {
              $pph21 += PPH_THIRD_ITERATION_VALUE * PPH_THIRD_ITERATION_PERCENTAGE;
            } else {
              $pph21 += $pkp * PPH_THIRD_ITERATION_PERCENTAGE;
            }
            $pkp -= PPH_THIRD_ITERATION_VALUE; 
          }
          if ($i == 4) {
            $pph21 += $pkp * PPH_LAST_ITERATION_PERCENTAGE;
          }
        } else {
          break;
        }
      }
    } else {
      for ($i=1; $i <= $iteration; $i++) { 
        if ($pkp > 0) {
          if ($i == 1) {
            if ($pkp <= PPH_FIRST_ITERATION_VALUE) {
              $pph21 += $pkp * PPH_FIRST_ITERATION_PERCENTAGE * PPH_MULTIPLE_NOT_HAVE_NPWP;
            } else {
              $pph21 += PPH_FIRST_ITERATION_VALUE * PPH_FIRST_ITERATION_PERCENTAGE * PPH_MULTIPLE_NOT_HAVE_NPWP;
            }
            $pkp -= PPH_FIRST_ITERATION_VALUE;
          }
          if ($i == 2) {
            if ($pkp >= PPH_SECOND_ITERATION_VALUE) {
              $pph21 += PPH_SECOND_ITERATION_VALUE * PPH_SECOND_ITERATION_PERCENTAGE * PPH_MULTIPLE_NOT_HAVE_NPWP;
            } else {
              $pph21 += $pkp * PPH_SECOND_ITERATION_PERCENTAGE * PPH_MULTIPLE_NOT_HAVE_NPWP;
            }
            $pkp -= PPH_SECOND_ITERATION_VALUE; 
          }
          if ($i == 3) {
            if ($pkp >= PPH_THIRD_ITERATION_VALUE) {
              $pph21 += PPH_THIRD_ITERATION_VALUE * PPH_THIRD_ITERATION_PERCENTAGE * PPH_MULTIPLE_NOT_HAVE_NPWP;
            } else {
              $pph21 += $pkp * PPH_THIRD_ITERATION_PERCENTAGE * PPH_MULTIPLE_NOT_HAVE_NPWP;
            }
            $pkp -= PPH_THIRD_ITERATION_VALUE; 
          }
          if ($i == 4) {
            $pph21 += $pkp * PPH_LAST_ITERATION_PERCENTAGE * PPH_MULTIPLE_NOT_HAVE_NPWP;
          }
        } else {
          break;
        }
      }
    }

    return $pph21;
  }
}