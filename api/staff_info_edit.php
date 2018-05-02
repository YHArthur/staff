<?php
require_once '../inc/common.php';
require_once('../db/staff_weixin.php');
require_once '../db/staff_main.php';

header("cache-control:no-cache,must-revalidate");
header("Content-type:text/html;charset=utf-8");

// 需要员工登录
need_staff_login();

$staff_id = $_SESSION['staff_id'];

$data = array();
if (isset($_GET['staff_avata']))
  $data['staff_avata'] = get_arg_str('GET', 'staff_avata', 255);
if (isset($_GET['staff_memo']))
  $data['staff_memo'] = get_arg_str('GET', 'staff_memo', 512);
if (isset($_GET['staff_mbti']))
  $data['staff_mbti'] = get_arg_str('GET', 'staff_mbti');

if (empty($data)) 
  exit_error('120', '没有设定修改的参数');

// 更新员工信息
$ret = upd_staff($data, $staff_id);
if (!$ret)
  exit_error('110', '员工信息修改失败');

// 正常返回
exit_ok('修改成功');
?>
