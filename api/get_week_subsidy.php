<?php
require_once '../inc/common.php';
require_once '../db/staff_office_sign.php';
require_once 'subsidy.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");
/*
========================== 获得员工周补贴明细 ==========================
参数
  staff_id          员工ID
  week              前几周（默认0）
  
返回
  week_begin        周开始日（月-日）
  week_end          周结束日（月-日）
  sum               合计补助金额
  rows              记录数组
    sign_date         考勤日期
    time_begin        签到时间
    time_end          签出时间
    commute_subsidy   交通补助金额
    lunch_subsidy     午餐补助金额
    dinner_subsidy    晚餐补助金额
    
说明
*/

php_begin();

// 参数检查
$args = array('staff_id');
chk_empty_args('GET', $args);

// 提交参数整理
$staff_id = get_arg_str('GET', 'staff_id');
$week = get_arg_str('GET', 'week');

$week = intval($week);

// 初始化返回数组
$rtn_rows = array();
// 初始化补助计算日期数组
$subsidy_day = array();

// 周一开始时间计算
$week_monday_begin = strtotime('Sunday -6 day', strtotime(date('Y-m-d'))) - $week*7*24*60*60;
$week_sunday_end = $week_monday_begin + 7*24*60*60 - 1;

$current_day = $week_monday_begin;
while ($current_day < $week_sunday_end) {
  $ymd = date('Y-m-d', $current_day);
  $subsidy_day[$ymd] = '';
  $current_day += 60*60*24;
}

// 取得员工自周一开始的考勤记录
$sign_rows = get_staff_office_sign_duration_list($staff_id, $week_monday_begin, $week_sunday_end);
// 循环取得的员工考勤记录
foreach($sign_rows as $sign_row) {
  $sign_type = $sign_row['sign_type'];
  $ctime = $sign_row['ctime'];
  $sign_date = substr($ctime, 0, 10);
  // 签到日期符合统计条件
  if (array_key_exists($sign_date, $subsidy_day)) {
    // 签入数据
    if ((substr($sign_type, -6, 6) == '签入') && $subsidy_day[$sign_date] == '')
      $subsidy_day[$sign_date] = $ctime;
    // 签出数据
    if ((substr($sign_type, -6, 6) == '签出') && $subsidy_day[$sign_date] != '')
      $subsidy_day[$sign_date] = substr($subsidy_day[$sign_date], 0, 19) . ',' . $ctime;
  }
}

// 员工周补助初始值
$subsidy_sum = 0;

// 循环员工每日考勤记录
foreach($subsidy_day as $sign_date => $time_from_to) {
  if ($sign_date > date('Y-m-d'))
    break;

  $rtn_row = array();
  $rtn_row['sign_date'] = $sign_date;
  
  $rtn_row['time_begin'] = '';
  // 有签入时间
  if (strlen($time_from_to) >= 19) {
    $rtn_row['time_begin'] = substr($time_from_to, 11, 8);
  }
  
  // 有正确的签入签出数据
  if (strlen($time_from_to) == 39) {
    $rtn_row['time_end'] = substr($time_from_to, 31);
    // 获得出勤开始时间和出勤结束时间
    list($time_begin, $time_end) = explode(",", $time_from_to);
    // 取得交通补助金额
    $commute_subsidy = get_commute_subsidy($time_begin, $time_end);
    // 取得午餐补助金额
    $lunch_subsidy = get_lunch_subsidy($time_begin, $time_end);
    // 取得晚餐补助金额
    $dinner_subsidy = get_dinner_subsidy($time_begin, $time_end);
    $rtn_row['commute_subsidy'] = $commute_subsidy;
    $rtn_row['lunch_subsidy'] = $lunch_subsidy;
    $rtn_row['dinner_subsidy'] = $dinner_subsidy;
    $subsidy_sum += ($commute_subsidy + $lunch_subsidy + $dinner_subsidy);
  } else {
    $rtn_row['time_end'] = '';
    $rtn_row['commute_subsidy'] = 0;
    $rtn_row['lunch_subsidy'] = 0;
    $rtn_row['dinner_subsidy'] = 0;
  }
  
  $rtn_rows[] = $rtn_row;
}


//返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['week_begin'] = date('n月j日', $week_monday_begin);
$rtn_ary['week_end'] = date('n月j日', $week_sunday_end);
$rtn_ary['sum'] = $subsidy_sum;
$rtn_ary['rows'] = $rtn_rows;
$rtn_str = json_encode($rtn_ary);
php_end($rtn_str);
?>
