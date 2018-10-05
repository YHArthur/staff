<?php
require_once '../inc/common.php';
require_once '../db/hr_date_tag.php';
require_once '../db/fin_staff_salary.php';
require_once '../db/staff_office_sign.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");
/*
========================== 获得指定年月员工工资列表 ==========================
参数
  ym                指定年月（YYYYMM）

返回
  work_days         本月工作日
  rows              记录数组
    day               日
    date_type         日期类型 0 工作日 1 休日 2 国定假日
    date_tag          日期标注
    
说明
*/

php_begin();

// 参数检查
$args = array('ym');
chk_empty_args('GET', $args);

// 获取提交参数
$ym = get_arg_str('GET', 'ym');
if (strlen(intval($ym)) != 6)
  exit_error('120', '指定年月日期格式不正确');

$year = substr($ym, 0, 4);
$month = substr($ym, 4, 2);
// 取得人事节假日记录总数
$rows = get_hr_date_type_total($year, $month);

if (!$rows)
  exit_error('120', '指定年月的数据不存在');

// 该月总天数
$sum_days = 0;
// 该月工作日天数
$work_days = 0;
// 取得该月休息日天数
$rest_days = 0;

foreach($rows as $row) {
  // 工作日
  if ($row['date_type'] == 0) {
    $work_days += $row['log_total'];
  } else {
    $rest_days += $row['log_total'];
  }
  $sum_days += $row['log_total'];
}

// 取得上个月
$month_first_day = date($year . '-' . $month . '-01');
$last_month = date('Ym', strtotime('-1 day', strtotime($month_first_day)));
// 取得下个月
$month_last_day = date('Y-m-d', strtotime('+1 month -1 day', strtotime($month_first_day)));
$next_month = date('Ym', strtotime('+1 month', strtotime($month_first_day)));

// 取得该月第一天对应周的周一
$month_first_monday = date('Y-m-d', strtotime('Sunday -6 day', strtotime($month_first_day)));

// 取得该月最后一天对应周的周日
$month_last_sunday = date('Y-m-d', strtotime('Sunday', strtotime($month_last_day)));

// 取得该月人事节假日标志列表
$rtn_rows = get_hr_date_tag_list($month_first_monday, $month_last_sunday);

//返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['sum_days'] = $sum_days;
$rtn_ary['work_days'] = $work_days;
$rtn_ary['rest_days'] = $rest_days;
$rtn_ary['first_day'] = $month_first_day;
$rtn_ary['last_day'] = $month_last_day;
$rtn_ary['first_monday'] = $month_first_monday;
$rtn_ary['last_sunday'] = $month_last_sunday;
$rtn_ary['cur_month'] = $ym;
$rtn_ary['last_month'] = $last_month;
$rtn_ary['next_month'] = $next_month;
$rtn_ary['rows'] = $rtn_rows;
$rtn_str = json_encode($rtn_ary);

php_end($rtn_str);
?>
