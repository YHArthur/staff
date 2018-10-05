<?php
require_once "../inc/common.php";
require_once '../db/staff_weixin.php';
require_once '../db/staff_main.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 员工注册审核通过 ==========================
GET参数
  staff_id        员工ID

返回
  errcode = 0 请求成功

说明
  风赢科技员工注册审核通过
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('staff_id');
chk_empty_args('GET', $args);

// 提交参数整理
$staff_id = get_arg_str('GET', 'staff_id');               // 员工ID
$staff_name = get_arg_str('GET', 'staff_name');           // 员工姓名
$nick_name = get_arg_str('GET', 'nick_name');             // 英文昵称
$staff_phone = get_arg_str('GET', 'staff_phone');         // 员工电话
$join_date = get_arg_str('GET', 'join_date');             // 加入时间
$staff_cd = get_arg_str('GET', 'staff_cd');               // 员工工号
$staff_sex = get_arg_str('GET', 'staff_sex');             // 员工性别
$staff_position = get_arg_str('GET', 'staff_position');   // 员工职位
$staff_mbti = get_arg_str('GET', 'staff_mbti');           // 员工性格
$identity = get_arg_str('GET', 'identity');               // 身份证件
$birth_year = get_arg_str('GET', 'birth_year');           // 出生年份
$birth_day = get_arg_str('GET', 'birth_day');             // 员工生日
$staff_memo = get_arg_str('GET', 'staff_memo', 1024);     // 个人简介

// 取得注册后待申请的员工微信账号记录
$row = get_staff_weixin_sign($staff_id);
if (!$row)
  exit_error('120', '无效的员工ID');

$data = array();
$data['staff_id'] = $staff_id;                            // 员工ID
$data['staff_cd'] = $staff_cd;                            // 员工工号
$data['staff_name'] = $staff_name;                        // 员工姓名
$data['nick_name'] = $nick_name;                          // 英文昵称
$data['staff_avata'] = $row['staff_avata'];               // 员工头像
$data['staff_position'] = $staff_position;                // 员工职位
$data['staff_sex'] = $staff_sex;                          // 员工性别
$data['staff_mbti'] = $staff_mbti;                        // 员工性格
$data['staff_memo'] = $staff_memo;                        // 个人简介
$data['staff_phone'] = $staff_phone;                      // 员工电话
$data['identity'] = $identity;                            // 身份证件
$data['birth_year'] = $birth_year;                        // 出生年份
$data['birth_day'] = $birth_day;                          // 员工生日
$data['join_date'] = $join_date;                          // 加入时间

// 创建员工信息
$ret = ins_staff($data);

// 创建失败
if ($ret == '')
  exit_error('110', '员工情报添加失败');

// 审核通过待申请的员工微信账号记录
$ret = confim_staff_weixin($staff_id);

// 审核失败
if ($ret == '')
  exit_error('110', '员工微信账号审核失败');

// 输出结果
$msg = '【' . $staff_name . '】已正式添加';
exit_ok($msg);
?>
