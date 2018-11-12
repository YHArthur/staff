<?php
require_once "../inc/common.php";
require_once '../db/staff_main.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 员工情报修改 ==========================
POST参数
  staff_id        员工ID

返回
  errcode = 0 请求成功

说明
  风赢科技员工情报修改
*/

// 禁止游客访问
api_exit_guest();

// 参数检查
$args = array('staff_id');
chk_empty_args('POST', $args);

// 提交参数整理
$staff_id = get_arg_str('POST', 'staff_id');                // 员工ID
$staff_cd = get_arg_str('POST', 'staff_cd');                // 员工工号
$staff_name = get_arg_str('POST', 'staff_name');            // 员工姓名
$nick_name = get_arg_str('POST', 'nick_name');              // 英文昵称
$staff_position = get_arg_str('POST', 'staff_position');    // 员工职位
$staff_sex = get_arg_str('POST', 'staff_sex');              // 员工性别
$staff_mbti = get_arg_str('POST', 'staff_mbti');            // 员工性格
$staff_memo = get_arg_str('POST', 'staff_memo', 512);       // 个人简介
$staff_phone = get_arg_str('POST', 'staff_phone');          // 员工电话
$identity = get_arg_str('POST', 'identity');                // 身份证件
$birth_year = get_arg_str('POST', 'birth_year');            // 出生年份
$birth_day = get_arg_str('POST', 'birth_day');              // 员工生日
$join_date = get_arg_str('POST', 'join_date');              // 加入时间
$work_period = get_arg_str('POST', 'work_period');          // 出勤时间段
$is_void = get_arg_str('POST', 'is_void');                  // 是否无效

$data = array();
$data['staff_cd'] = $staff_cd;                              // 员工工号
$data['staff_name'] = $staff_name;                          // 员工姓名
$data['nick_name'] = $nick_name;                            // 英文昵称
$data['staff_position'] = $staff_position;                  // 员工职位
$data['staff_sex'] = $staff_sex;                            // 员工性别
$data['staff_mbti'] = $staff_mbti;                          // 员工性格
$data['staff_memo'] = $staff_memo;                          // 个人简介
$data['staff_phone'] = $staff_phone;                        // 员工电话
$data['identity'] = $identity;                              // 身份证件
$data['birth_year'] = $birth_year;                          // 出生年份
$data['birth_day'] = $birth_day;                            // 员工生日
$data['join_date'] = $join_date;                            // 加入时间
$data['work_period'] = $work_period;                        // 出勤时间段
$data['is_void'] = $is_void;                                // 是否无效

// 创始人信息禁止他人修改
if ($staff_id == '640C3986-5EC2-EABA-59C1-B9C6EC4FF610' && $_SESSION['staff_id'] != $staff_id)
  exit_error('110', '亲爱的，我还没有赋予你修改我情报的权力！');

// 更新员工信息
$ret = upd_staff($data, $staff_id);

// 更新失败
if ($ret == '')
  exit_error('110', '员工情报修改失败');

// 输出结果
$msg = '【' . $staff_name . '】情报已修改成功';
exit_ok($msg);
?>
