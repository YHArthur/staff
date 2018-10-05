<?php
require_once '../inc/common.php';
require_once '../db/staff_weixin.php';
require_once '../db/staff_main.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");
/*
========================== 员工个人情报一览 ==========================
GET参数
  staff_id            员工id
  
返回
  rows      记录数组
    staff_id          员工ID
    staff_cd          员工工号
    staff_name        员工姓名
    nick_name         昵称（英文名）
    staff_avata       员工头像
    staff_sex         员工性别（0不明 1男 2女）
    staff_position    员工职位
    staff_mbti        员工性格（MBTI）
    staff_memo        员工个人简介
    staff_age         年龄（女性显示保密）
    staff_star_sign   星座
    online_status     在线状态（0签出 1签入）
    staff_subsidy     本周补助
    exp_balance       办公经费余额
    join_date         加入时间
    staff_phone       员工电话号码
    identity          员工身份证号
    

说明

说明
*/

php_begin();
// 获取当前员工id
$staff_id = $_SESSION['staff_id'];
$staff_rows = get_staff($staff_id);

//返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['rows'] = $staff_rows;
$rtn_str = json_encode($rtn_ary);
php_end($rtn_str);
?>
