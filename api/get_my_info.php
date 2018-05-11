<?php
require_once '../inc/common.php';
require_once '../db/staff_main.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 获得员工本人情报 ==========================
参数
  无
  
返回
  staff_id          员工ID
  staff_cd          员工工号
  staff_name        员工姓名
  nick_name         昵称（英文名）
  staff_avata       员工头像
  staff_position    员工职位
  staff_sex         员工性别（0不明 1男 2女）
  staff_mbti        员工性格（MBTI）
  staff_memo        员工个人简介
  staff_phone       员工手机
  identity          身份证件号
  birth_year        出生年份
  birth_day         生日
  join_date         加入时间
    
说明
*/

php_begin();

if (!session_id())
  session_start();

if (!isset($_SESSION['staff_id']))
  exit_error('119', '网页已失效，请刷新页面再试');

// 获取当前员工ID
$staff_id = $_SESSION['staff_id'];
// 取得指定员工ID的员工记录
$rtn_ary = get_staff($staff_id);

// 返回数据做成
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_str = json_encode($rtn_ary);

php_end($rtn_str);
?>
