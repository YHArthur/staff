<?php
require_once '../inc/common.php';
require_once '../db/staff_main.php';
require_once '../db/staff_office_sign.php';
require_once 'subsidy.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");
/*
========================== 员工情报一览 ==========================
GET参数
  staff_id            员工id
  
返回
  total     总记录件数
  rows      记录数组
   Traffic  交通补助
    lunch   午餐补助
    dinner   晚餐补助

说明
*/

php_begin();
$staff_id = get_arg_str('GET', 'staff_id');
// 初始化员工情报返回数组
$staff_rows = array();
// 初始化补助计算日期数组
$subsidy_day = array();

// 本周一开始时间计算
$current_monday_begin = strtotime('Sunday -6 day', strtotime(date('Y-m-d')));
$current_day = $current_monday_begin;
while ($current_day < time()) {
  $subsidy_day[] = date('Y-m-d', $current_day);
  $current_day += 60*60*24;
}
  // 取得员工自周一开始的考勤记录
  $sign_rows = get_staff_office_sign_from_time_list($staff_id, $current_monday_begin);
  //员工考勤补贴数组初始值
  $subsidy = array();
  // 员工每日考勤数组初始值
  $staff_daily_signs = array();
  // 员工本周补助初始值
  $staff_subsidy = 0;
  // 循环取得的员工考勤记录
  foreach($sign_rows as $sign_row) {
    $sign_type = $sign_row['sign_type'];
    $ctime = $sign_row['ctime'];
    $sign_date = substr($ctime, 0, 10);
    // 签到日期符合统计条件
    if (in_array($sign_date, $subsidy_day)) {
      // 员工每日考勤数据未设定，设定初期值为空字符串
      if (!isset($staff_daily_signs[$sign_date]))
        $staff_daily_signs[$sign_date] = '';
      // 签入数据
      if ((substr($sign_type, -6, 6) == '签入') && $staff_daily_signs[$sign_date] == '')
        $staff_daily_signs[$sign_date] = $ctime;
      // 签出数据
      if ((substr($sign_type, -6, 6) == '签出') && $staff_daily_signs[$sign_date] != '')
        $staff_daily_signs[$sign_date] = substr($staff_daily_signs[$sign_date], 0, 19) . ',' . $ctime;
    }
  }
  
  // 循环员工每日考勤记录
  foreach($staff_daily_signs as $time_from_to) {
    // 有正确的签入签出数据
    if (strlen($time_from_to) == 39) {
      // 获得出勤开始时间和出勤结束时间
      list($time_begin, $time_end) = explode(",", $time_from_to);
      // 取得交通补助金额
      $Traffic = get_commute_subsidy($time_begin, $time_end);
      // 取得午餐补助金额
      $lunch = get_lunch_subsidy($time_begin, $time_end);
      // 取得晚餐补助金额
      $dinner = get_dinner_subsidy($time_begin, $time_end);
      $subsidy['time'] = $time_begin;
      $subsidy['Traffic'] = $Traffic;
      $subsidy['lunch'] = $lunch;
      $subsidy['dinner'] = $dinner;
      $staff_rows[] = $subsidy;
      $subsidy = array();
      $staff_subsidy +=($Traffic + $lunch + $dinner);
    }
  }


//返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['rows'] = $staff_rows;
$rtn_str = json_encode($rtn_ary);
php_end($rtn_str);
?>
