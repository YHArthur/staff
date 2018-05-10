<?php
require_once "../inc/common.php";
require_once '../db/obj.php';

header("Access-Control-Allow-Origin: *");
header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 目标设定 ==========================
POST参数
  obj_id          目标ID
  obj_name        目标
  obj_intro       目标内容
  owner_id        创建人ID
  owner_name      创建人
  check_id        监管人ID
  check_name      监管人
  is_public       是否公开
  obj_level       目标等级
  obj_value       目标价值
  obj_perc        目标进度
  obj_status      目标状态
  limit_time      目标期限

返回
  设定结果

说明
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('obj_name', 'limit_time');
chk_empty_args('GET', $args);

// 提交参数整理
$obj_id = get_arg_str('GET', 'obj_id');                   // 目标ID
$obj_name = get_arg_str('GET', 'obj_name', 50);           // 目标
$obj_intro = get_arg_str('GET', 'obj_intro', 8192);       // 目标内容
$check_id = get_arg_str('GET', 'check_id');               // 监管人ID
$check_name = get_arg_str('GET', 'check_name');           // 监管人
$is_public = get_arg_str('GET', 'is_public');             // 是否公开
$obj_level = get_arg_str('GET', 'obj_level');             // 目标等级
$obj_value = get_arg_str('GET', 'obj_value');             // 目标价值
$obj_perc = get_arg_str('GET', 'obj_perc');               // 目标进度
$obj_status = get_arg_str('GET', 'obj_status');           // 目标状态
$limit_time = get_arg_str('GET', 'limit_time');           // 目标期限

// 提交信息整理
$obj_level = intval($obj_level);
$obj_value = intval($obj_value);
$obj_perc = intval($obj_perc);
$obj_status = intval($obj_status);

$staff_id = $_SESSION['staff_id'];
$staff_name = $_SESSION['staff_name'];

// 监管人处理
if ($check_name == '请选择') {
  $check_name = '';
} else if ($check_id == $staff_id) {
  $check_name = $staff_name;
} else {
  list($check_cd, $check_name) = explode(" ", $check_name);
}

$data = array();
$data['obj_id'] = $obj_id;                                // 目标ID
$data['obj_name'] = $obj_name;                            // 目标
$data['obj_intro'] = $obj_intro;                          // 目标内容
$data['check_id'] = $check_id;                            // 监管人ID
$data['check_name'] = $check_name;                        // 监管人
$data['is_public'] = $is_public;                          // 是否公开
$data['obj_level'] = $obj_level;                          // 目标等级
$data['obj_value'] = $obj_value;                          // 目标价值
$data['obj_perc'] = $obj_perc;                            // 目标进度
$data['obj_status'] = $obj_status;                        // 目标状态
$data['limit_time'] = $limit_time;                        // 目标期限

// 目标ID为空，表示创建目标
if ($obj_id == '') {
  // 取得唯一标示符GUID
  $data['obj_id'] = get_guid();                           // 目标ID
  $data['owner_id'] = $staff_id;                          // 创建人ID
  $data['owner_name'] = $staff_name;                      // 创建人

  // 目标创建
  $ret = ins_obj($data);
  $msg = '【' . $obj_name . '】目标已成功添加';
  // 目标信息创建失败
  if ($ret == '')
    exit_error('110', '目标信息创建失败');
} else {
  // 目标更新
  $ret = upd_obj($data, $obj_id);
  $msg = '【' . $obj_name . '】目标已成功更新';
  // 目标信息更新失败
  if (!$ret)
    exit_error('110', '目标信息更新失败');
}

// 输出结果
exit_ok($msg);
?>
