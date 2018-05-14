<?php
require_once '../inc/common.php';
require_once '../db/staff_main.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 员工情报修改 ==========================
GET参数
  staff_avata       员工头像
  nick_name         昵称（英文名）
  staff_memo        员工个人简介
  staff_mbti        员工性格
  
返回
  errcode = 0     请求成功

说明
  只有本人才能修改
*/

if (!session_id())
  session_start();

if (!isset($_SESSION['staff_id']))
  exit_error('119', '网页已失效，请刷新页面再试');

$staff_id = $_SESSION['staff_id'];

$data = array();
if(!empty($_GET['nick_name']))
  $data['nick_name'] = get_arg_str('GET', 'nick_name');
if(!empty($_GET['staff_avata']))
  $data['staff_avata'] = get_arg_str('GET', 'staff_avata', 255);
if(!empty($_GET['staff_position']))
  $data['staff_position'] = get_arg_str('GET', 'staff_position');
if(!empty($_GET['staff_sex']))
  $data['staff_sex'] = get_arg_str('GET', 'staff_sex');
if(!empty($_GET['staff_mbti']))
  $data['staff_mbti'] = get_arg_str('GET', 'staff_mbti');
if(!empty($_GET['staff_memo']))
  $data['staff_memo'] = get_arg_str('GET', 'staff_memo', 512);
if(!empty($_GET['staff_phone']))
  $data['staff_phone'] = get_arg_str('GET', 'staff_phone');
if(!empty($_GET['identity']))
  $data['identity'] = get_arg_str('GET', 'identity');
if(!empty($_GET['birth_year']))
  $data['birth_year'] = get_arg_str('GET', 'birth_year');
if(!empty($_GET['birth_day']))
  $data['birth_day'] = get_arg_str('GET', 'birth_day');

// 更新员工信息
if (empty($data))
  exit_error('120', '没有信息被修改');

$ret = upd_staff($data, $staff_id);
if (!$ret)
  exit_error('110', '员工情报修改失败');

// 正常返回
exit_ok('修改成功');
?>
