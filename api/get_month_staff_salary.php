<?php
require_once '../inc/common.php';
require_once '../db/hr_date_tag.php';
require_once '../db/fin_staff_salary.php';
require_once '../db/fin_staff_salary_log.php';
require_once '../db/staff_office_sign.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");
/*
========================== 获得指定年月员工工资列表 ==========================
参数
  ym                指定年月（YYYY-MM）

返回
  salary_date       支付日期
  total             总记录件数             
  rows              记录数组
    staff_id          员工ID
    staff_cd          员工工号
    staff_name        员工姓名
    base_salary       基本工资
    effic_salary      绩效工资
    pension_base      社保基数
    fund_base         公积金基数
    lack_days         欠勤天数
    lack_days_list    欠勤工作日列表

说明
*/

php_begin();

// 参数检查
$args = array('ym');
chk_empty_args('GET', $args);

// 获取提交参数
$ym = get_arg_str('GET', 'ym');
if (strlen($ym) != 7)
  exit_error('120', '指定年月日期格式不正确');

// 本月第一天
$month_first_day = date($ym  . '-01');
// 本月最后一天
$month_last_day = date('Y-m-d', strtotime('+1 month -1 day', strtotime($month_first_day)));
// 支付日
$salary_date = date('Y-m-d', strtotime('+1 month +5 day', strtotime($month_first_day)));

// 取得当月正常出勤年月日列表
$rows = get_hr_date_tag_by_type($month_first_day, $month_last_day);
if (!$rows)
  exit_error('120', '指定年月工作日未设定');
$work_days = array();
foreach($rows as $row) {
  $work_days[] = $row['date_ymd'];
}

// 取得当月签到的员工ID列表
$rows = get_staff_office_sign_id_list($month_first_day . ' 00:00:00', $month_last_day . ' 23:59:59');
if (!$rows)
  exit_error('120', '该月没有员工出勤');
$work_staffs = array();
foreach($rows as $row) {
  $work_staffs[] = $row['staff_id'];
}

// 取得出勤员工的工资基数
$rows = get_fin_staff_salary_list($work_staffs, $ym);
$rtn_rows = array();
foreach($rows as $row) {
  $staff_id = $row['staff_id'];
  // 取得员工当月的办公室签到记录列表
  $sign_list = get_staff_office_sign_duration_list($staff_id, strtotime($month_first_day . ' 00:00:00'), strtotime($month_last_day . ' 23:59:59'));
  $staff_work_days = array();
  foreach($sign_list as $sign) {
    $sign_day = substr($sign['ctime'], 0, 10);
    $staff_work_days[] = $sign_day;
  }
  $lack_days = array_diff($work_days, array_unique($staff_work_days));
  // 计算该员工的欠勤天数
  $row['lack_days'] = count($lack_days);
  $row['lack_days_list'] = join(",", $lack_days);
  // 取得员工当月工资发放记录
  $row['salary_log'] = get_fin_staff_salary_log($ym, $staff_id);
  $rtn_rows[] = $row;
}

//返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['cur_month'] = $ym;
$rtn_ary['salary_date'] = $salary_date;
$rtn_ary['total'] = count($rtn_rows);
$rtn_ary['rows'] = $rtn_rows;
$rtn_str = json_encode($rtn_ary);

php_end($rtn_str);
?>
