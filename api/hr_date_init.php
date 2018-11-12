<?php
require_once '../inc/common.php';
require_once '../db/hr_date_tag.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 初始化节假日 ==========================
Get参数
  year      初始化年份

返回

说明
*/

// 禁止游客访问
api_exit_guest();

// 参数检查
$args = array('year');
chk_empty_args('GET', $args);

// 提交参数整理
$year = get_arg_str('GET', 'year');                  // 初始化年份

// 提交信息整理
$year = intval($year);

// 初始化年份存在检查
$ret = chk_hr_date_tag_year_exist($year);
if ($ret)
  exit_error('130', $year . '年份的数据已经存在');

// 国定假期
$holiday = array();
$holiday['0101'] = '元旦';
$holiday['0501'] = '劳动节';
$holiday['1001'] = '国庆节';

// 循环创建一年的数据
$time_now = strtotime("{$year}-01-01");
while (date('Y', $time_now) == $year) {
  // 默认日期类型
  $date_type = 0;
  $week = date("w", $time_now);
  if ($week == 0 || $week == 6)
    $date_type = 1;
  
  // 国定假日判断
  $md = date('md', $time_now);
  $date_tag = '';
  if (array_key_exists($md, $holiday)) {
    $date_type = 2;
    $date_tag = $holiday[$md];
  }
  
  // 节假日标志创建
  $ret = ins_hr_date_tag(date('Y-m-d', $time_now), $date_type, $date_tag);
  if (!$ret)
    exit_error('110', '节假日标志信息创建失败');
  $time_now += 24 * 60 * 60;
}


// 正常返回
$msg = $year . "年的节假日已经成功初始化";

// 输出结果
exit_ok($msg);
?>
