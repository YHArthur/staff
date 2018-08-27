<?php
require_once "../inc/common.php";
require_once '../db/task.php';

header("Access-Control-Allow-Origin: *");
header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 任务进展更新 ==========================
POST参数
  task_id         任务ID
  result_memo     结果描述
  is_closed       是否完成

返回
  设定结果

说明
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('task_id', 'result_memo');
chk_empty_args('POST', $args);

// 提交参数整理
$task_id = get_arg_str('POST', 'task_id');                  // 任务ID
$result_memo = get_arg_str('POST', 'result_memo', 8192);    // 结果描述
$is_closed = get_arg_str('POST', 'is_closed');              // 是否完成

// 提交信息整理
$is_closed = intval($is_closed);

$my_id = $_SESSION['staff_id'];
$my_name = $_SESSION['staff_name'];

// 取得指定任务ID的任务记录
$task = get_task($task_id);
if (!$task)
  exit_error('140', '任务ID不存在');

$owner_id = $task['owner_id'];                              // 创建人ID
$respo_id = $task['respo_id'];                              // 责任人ID
$check_id = $task['check_id'];                              // 监督人ID

if ($owner_id != $my_id && $respo_id != $my_id && $check_id != $my_id)
  exit_error('130', '你没有更新该任务的权限');

$task_title = $task['task_name'];                           // 任务名称
$old_closed = intval($task['is_closed']);                   // 原来是否完成

$data = array();
$data['result_memo'] = $result_memo;                        // 结果描述
$data['is_closed'] = $is_closed;                            // 是否完成

if ($is_closed == 1 && $old_closed == 0)
  $data['closed_time'] = date('Y-m-d H:i:s');               // 结束时间

// 任务更新
$ret = upd_task($data, $task_id);
$msg = '【' . $task_title . '】任务已成功更新';
// 任务信息更新失败
if (!$ret)
  exit_error('110', '任务信息更新失败');

// 输出结果
exit_ok($msg);
?>
