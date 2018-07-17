<?php
require_once '../inc/common.php';
require_once '../db/staff_main.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 获取员工信息及相邻员工信息 ==========================
GET参数
  staff_id      当前员工ID

返回
  cur_cd        当前员工工号
  cur_name      当前员工姓名
  aft_id        下一员工ID
  aft_cd        下一员工工号
  aft_name      下一员工姓名
  bef_id        上一员工ID
  bef_cd        上一员工工号
  bef_name      上一员工姓名

说明
*/


// 参数检查
$args = array('staff_id');
chk_empty_args('GET', $args);

// 取得员工ID
$cur_id =  get_arg_str('GET', 'staff_id');

// 取得员工列表
$staff_rows = get_staff_list();
// 默认员工位置
$pos = -1;
$array_pos = 0;
// 寻找员工当前位置
foreach ($staff_rows as $staff) {
  $staff_id = $staff['staff_id'];
  if ($cur_id == $staff_id)
    $pos = $array_pos;
  $array_pos++;
}
$array_pos--;

// 未找到员工
if ($pos == -1) {
  $cur_cd = '';             // 当前员工工号
  $cur_name = '';           // 当前员工姓名
  $aft_id = '';             // 下一员工ID
  $aft_cd = '';             // 下一员工工号
  $aft_name = '';           // 下一员工姓名
  $bef_id = '';             // 上一员工ID
  $bef_cd = '';             // 上一员工工号
  $bef_name = '';           // 上一员工姓名
} else {
  // 找到员工
  $cur_cd = $staff_rows[$pos]['staff_cd'];          // 当前员工工号
  $cur_name = $staff_rows[$pos]['staff_name'];      // 当前员工姓名
  
  // 下一个员工位置
  $aft_pos = $pos + 1;
  // 最后一位员工
  if ($pos == $array_pos)
    $aft_pos = 0;
  $aft_id = $staff_rows[$aft_pos]['staff_id'];      // 下一员工ID
  $aft_cd = $staff_rows[$aft_pos]['staff_cd'];      // 下一员工工号
  $aft_name = $staff_rows[$aft_pos]['staff_name'];  // 下一员工姓名
  
  // 上一个员工位置
  $bef_pos = $pos - 1;
  // 第一位员工
  if ($pos == 0)
    $bef_pos = $array_pos;
  $bef_id = $staff_rows[$bef_pos]['staff_id'];        // 上一员工ID
  $bef_cd = $staff_rows[$bef_pos]['staff_cd'];        // 上一员工工号
  $bef_name = $staff_rows[$bef_pos]['staff_name'];    // 上一员工姓名
}

// 返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['cur_cd'] = $cur_cd;
$rtn_ary['cur_name'] = $cur_name;
$rtn_ary['aft_id'] = $aft_id;
$rtn_ary['aft_cd'] = $aft_cd;
$rtn_ary['aft_name'] = $aft_name;
$rtn_ary['bef_id'] = $bef_id;
$rtn_ary['bef_cd'] = $bef_cd;
$rtn_ary['bef_name'] = $bef_name;

// 正常返回
$rtn_str = json_encode($rtn_ary);

php_end($rtn_str);
?>
