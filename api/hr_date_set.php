<?php
require_once "../inc/common.php";
require_once '../db/hr_date_tag.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 节假日标志设定 ==========================
POST参数
  date_ymd        年月日
  date_type       日期类型
  date_tag        日期标注

返回
  设定结果

说明
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('date_ymd');
chk_empty_args('POST', $args);

// 提交参数整理
$date_ymd = get_arg_str('POST', 'date_ymd');                // 年月日
$date_type = get_arg_str('POST', 'date_type');              // 日期类型
$date_tag = get_arg_str('POST', 'date_tag', 255);           // 日期标注

// 节假日标志更新
$ret = upd_hr_date_tag($date_ymd, $date_type, $date_tag);

if (!$ret)
  exit_error('110', '节假日标志修改失败');
// 正常返回
exit_ok('节假日标志设定成功');

?>
