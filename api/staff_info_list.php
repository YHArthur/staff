<?php
require_once '../inc/common.php';
require_once '../db/staff_main.php';
require_once '../db/staff_office_sign.php';
require_once '../db/staff_expense.php';
require_once '../db/staff_expense_log.php';
require_once '../db/task.php';
require_once 'subsidy.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");
/*
========================== 员工情报一览 ==========================
参数
  无
  
返回
  total     总记录件数
  rows      记录数组
    staff_id          员工ID
    staff_cd          员工工号
    staff_name        员工姓名
    nick_name         昵称（英文名）
    staff_avata       员工头像
    staff_sex         员工性别（0不明 1男 2女）
    staff_position    员工职位
    staff_mbti        员工性格（MBTI）
    staff_memo        员工个人简介
    staff_age         年龄（女性显示保密）
    staff_star_sign   星座
    online_status     在线状态（0签出 1签入）
    staff_subsidy     本周补助
    exp_balance       办公经费余额
    staff_task        当前任务
    join_date         加入时间
    work_period       出勤时间段

说明
*/

php_begin();

// 取得员工记录总数
$total = get_staff_total();
// 取得员工记录列表
$rows = get_staff_list();

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

// 循环取得员工记录
foreach($rows as $row) {
  $staff_row['staff_id'] = $row['staff_id'];                                  // 员工ID
  $staff_row['staff_cd'] = $row['staff_cd'];                                  // 员工工号
  $staff_row['staff_name'] = $row['staff_name'];                              // 员工姓名
  $staff_row['nick_name'] = $row['nick_name'];                                // 昵称（英文名）
  $staff_row['staff_avata'] = $row['staff_avata'];                            // 员工头像
  $staff_row['staff_sex'] = $row['staff_sex'];                                // 员工性别（0不明 1男 2女）
  $staff_row['staff_position'] = $row['staff_position'];                      // 员工职位
  $staff_row['staff_mbti'] = $row['staff_mbti'];                              // 员工性格（MBTI）
  $staff_row['staff_memo'] = $row['staff_memo'];                              // 员工个人简介
  $staff_row['join_date'] = substr($row['join_date'], 0, 10);                 // 加入时间
  $staff_row['work_period'] = $row['work_period'];                            // 出勤时间段
  
  $staff_row['staff_age'] = '保密';                                           // 员工年龄（默认保密）
  $staff_row['staff_star_sign'] = '金星';                                     // 星座（默认金星）
  // 男人
  if ($row['staff_sex'] == 1) {
    $staff_row['staff_age'] = date("Y") - $row['birth_year'];
    $staff_row['staff_star_sign'] = '火星';
  }
  // 生日数据合法
  if (strlen($row['birth_day']) == 5) {
    list($m, $d) = explode(".", $row['birth_day']);
    $staff_row['staff_star_sign'] = get_star_sign_12($m, $d);                 // 星座
  }
  
  // 取得员工自周一开始的考勤记录
  $sign_rows = get_staff_office_sign_from_time_list($row['staff_id'], $current_monday_begin);
  // 员工每日考勤数组初始值
  $staff_daily_signs = array();
  // 员工本周补助初始值
  $staff_subsidy = 0;
  // 员工签到类型初期值
  $sign_type = '000000';

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
  
  $staff_row['online_status'] = '0';                                          // 在线状态（默认签出）
  // 最后签到状态为签入
  if (substr($sign_type, -6, 6) == '签入')
    $staff_row['online_status'] = '1';
    
  // 循环员工每日考勤记录
  foreach($staff_daily_signs as $time_from_to) {
    // 有正确的签入签出数据
    if (strlen($time_from_to) == 39) {
      // 获得出勤开始时间和出勤结束时间
      list($time_begin, $time_end) = explode(",", $time_from_to);
      // 取得交通补助金额
      $staff_subsidy += get_commute_subsidy($time_begin, $time_end);
      // 取得午餐补助金额
      $staff_subsidy += get_lunch_subsidy($time_begin, $time_end);
      // 取得晚餐补助金额
      $staff_subsidy += get_dinner_subsidy($time_begin, $time_end);
    }
  }
  
  $staff_row['staff_subsidy'] =  $staff_subsidy;                              // 本周补助
  
  // 办公经费余额初期值
  $exp_balance = 0;
  $staff_row['exp_balance'] = '00';
  // 取得指定员工最后一次经费变动记录
  $last_log = get_staff_last_expense_log($row['staff_id']);
  if ($last_log) {
    $exp_balance = $last_log['exp_balance'];
    $staff_row['exp_balance'] = str_pad($exp_balance, 2, '0', STR_PAD_LEFT);
  }
  
  // 取得指定员工待扣除办公经费总额
  $pendin_sum = get_pending_staff_expense_amount_sum($row['staff_id']);
  $staff_row['pendin_sum'] = $pendin_sum;
  
  // 可报销办公经费
  $free_exp = $exp_balance + $pendin_sum;
  $staff_row['free_exp'] = str_pad($free_exp, 2, '0', STR_PAD_LEFT);

  // 锁定办公经费
  if ($free_exp < 0) {
    $lock_exp = $exp_balance;
  } else {
    $lock_exp = $exp_balance - $free_exp;
  }
  $staff_row['lock_exp'] = str_pad($lock_exp, 2, '0', STR_PAD_LEFT);
  
  // 取得员工当前任务总数
  $staff_row['staff_task'] = get_staff_task_total($row['staff_id']);
  $staff_rows[] = $staff_row;
}

//返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['total']  = $total;
$rtn_ary['rows'] = $staff_rows;
$rtn_str = json_encode($rtn_ary);

php_end($rtn_str);
?>
