<?php
require_once '../inc/common.php';
require_once '../db/staff_office_sign.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 办公室记录 ==========================
Get参数
  limit     （记录条数，可选）默认10 最大100
  offset    （记录偏移量，可选）默认0 与limit参数一起分页使用。如设置 offset=20&limit=10 取第21-30条记录

返回
  total     总记录件数
  rows      记录数组
    log_id      签到日志ID
    staff_name  员工姓名
    sign_type   签到类型
    ctime       签到时间

说明
*/

php_begin();

// 取得分页参数
list($limit, $offset) = get_paging_arg('GET');

// 取得员工办公室签到记录总数
$total = get_staff_office_sign_total();
// 取得员工办公室签到记录列表
$rows = get_staff_office_sign_list($limit, $offset);

// 返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['total'] = $total;
$rtn_ary['rows'] = $rows;

// 正常返回
$rtn_str = json_encode($rtn_ary);

// 输出内容
php_end($rtn_str);
?>
