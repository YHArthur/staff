<?php
require_once '../inc/common.php';
require_once '../db/staff_office_sign.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 办公室签到 ==========================
GET参数
  sign_type           签到类型
  latitude            纬度
  longitude           经度

返回
  errcode = 0 请求成功

说明
  风赢科技员工签到
*/

php_begin();

// 参数检查
$args = array('sign_type', 'latitude', 'longitude');
chk_empty_args('GET', $args);

// 提交参数整理
$sign_type = get_arg_str('GET', 'sign_type');
$latitude = get_arg_str('GET', 'latitude');
$longitude = get_arg_str('GET', 'longitude');
$user_ip = get_int_ip();

if (!session_id())
  session_start();

if (!isset($_SESSION['staff_id']))
  exit_error('119', '网页已失效，请刷新页面再试');

$staff_id = $_SESSION['staff_id'];
$staff_name = $_SESSION['staff_name'];
$sign_location = round($latitude, 4) . ',' . round($longitude, 4);

// 字段设定
$data = array();
$data['staff_id'] = $staff_id;
$data['staff_name'] = $staff_name;
$data['sign_type'] = $sign_type;
$data['sign_location'] = $sign_location;
$data['user_ip'] = $user_ip;
// 创建员工签到
$ret = ins_staff_office_sign($data);

if (!$ret)
  exit_error('110', '员工签到信息创建失败');

// 正常返回
exit_ok('签到成功');
?>
