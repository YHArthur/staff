<?php
require_once "../inc/common.php";
require_once '../db/task.php';

header("Access-Control-Allow-Origin: *");
header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 任务设定 ==========================
POST参数
  task_id         任务ID
  task_name       任务
  task_intro      任务内容
  owner_id        创建人ID
  owner_name      创建人
  respo_id        责任人ID
  respo_name      责任人
  check_id        监管人ID
  check_name      监管人
  is_public       是否公开
  task_level      任务等级
  task_value      任务价值
  task_perc       任务进度
  task_status     任务状态
  limit_time      任务期限

返回
  设定结果

说明
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('task_name', 'task_intro', 'limit_time');
chk_empty_args('GET', $args);

// 提交参数整理
$task_id = get_arg_str('GET', 'task_id');                // 任务ID
$task_name = get_arg_str('GET', 'task_name', 100);       // 任务
$task_intro = get_arg_str('GET', 'task_intro', 8192);    // 任务内容
$respo_id = get_arg_str('GET', 'respo_id');              // 责任人ID
$respo_name = get_arg_str('GET', 'respo_name');          // 责任人
$check_id = get_arg_str('GET', 'check_id');              // 监管人ID
$check_name = get_arg_str('GET', 'check_name');          // 监管人
$is_public = get_arg_str('GET', 'is_public');            // 是否公开
$task_level = get_arg_str('GET', 'task_level');          // 任务等级
$task_value = get_arg_str('GET', 'task_value');          // 任务价值
$task_perc = get_arg_str('GET', 'task_perc');            // 任务进度
$task_status = get_arg_str('GET', 'task_status');        // 任务状态
$limit_time = get_arg_str('GET', 'limit_time');          // 任务期限

// 提交信息整理
$task_level = intval($task_level);
$task_value = intval($task_value);
$task_perc = intval($task_perc);
$task_status = intval($task_status);

$staff_id = $_SESSION['staff_id'];
$staff_name = $_SESSION['staff_name'];

// 责任人处理
$respo_id = '';
$respo_name = '';
if ($respo_name != '请选择')
  list($respo_cd, $respo_name) = explode(" ", $respo_name);

// 监管人处理
$check_id = '';
$check_name = '';
if ($check_name != '请选择')
  list($check_cd, $check_name) = explode(" ", $check_name);

$data = array();
$data['task_id'] = $task_id;                              // 任务ID
$data['task_name'] = $task_name;                          // 任务
$data['task_intro'] = $task_intro;                        // 任务内容
$data['respo_id'] = $respo_id;                            // 责任人ID
$data['respo_name'] = $respo_name;                        // 责任人
$data['check_id'] = $check_id;                            // 监管人ID
$data['check_name'] = $check_name;                        // 监管人
$data['is_public'] = $is_public;                          // 是否公开
$data['task_level'] = $task_level;                        // 任务等级
$data['task_value'] = $task_value;                        // 任务价值
$data['task_perc'] = $task_perc;                          // 任务进度
$data['task_status'] = $task_status;                      // 任务状态
$data['limit_time'] = $limit_time;                        // 任务期限

// 任务ID为空，表示创建任务
if ($task_id == '') {
  // 取得唯一标示符GUID
  $data['task_id'] = get_guid();                          // 任务ID
  $data['owner_id'] = $staff_id;                          // 创建人ID
  $data['owner_name'] = $staff_name;                      // 创建人

  // 任务创建
  $ret = ins_task($data);
  $msg = '【' . $task_name . '】任务已成功添加';
  // 任务信息创建失败
  if ($ret == '')
    exit_error('110', '任务信息创建失败');
} else {
  // 任务更新
  $ret = upd_task($data, $task_id);
  $msg = '【' . $task_name . '】任务已成功更新';
  // 任务信息更新失败
  if (!$ret)
    exit_error('110', '任务信息更新失败');
}

// 输出结果
exit_ok($msg);
?>
