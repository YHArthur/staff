<?php
require_once '../inc/common.php';
require_once '../db/staff_weixin.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 员工注册 ==========================
GET参数
  staff_name      员工姓名
  staff_phone     员工电话

返回
  errcode = 0 请求成功

说明
  风赢科技员工注册
*/

php_begin();

// 参数检查
$args = array('staff_name', 'staff_phone');
chk_empty_args('GET', $args);

// 提交参数整理
$staff_name = get_arg_str('GET', 'staff_name');
$staff_phone = get_arg_str('GET', 'staff_phone');
$last_ip = ip2long(get_ip());

if (!session_id())
  session_start();

if (!isset($_SESSION['unionid']))
  exit_error('119', '网页已失效，请刷新页面再试');

$unionid = $_SESSION['unionid'];
// 微信统一标识存在检查
if (exist_staff_weixin($unionid))
  exit_ok('该微信账号已经存在');

// 字段设定
$data = array();
$data['unionid'] = $unionid;
$data['staff_id'] = get_guid();
$data['staff_name'] = $staff_name;
$data['staff_phone'] = $staff_phone;
$data['is_void'] = 1;
$data['last_ip'] = $last_ip;
// 创建员工微信账号
$ret = ins_staff_weixin($data);

if (!$ret)
  exit_error('110', '员工微信账号创建失败');

// 正常返回
exit_ok('微信账号注册成功');
?>
