<?php
require_once "../inc/common.php";
require_once '../db/task.php';
require_once '../db/action.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 对象删除 ==========================
POST参数
  obj             删除对象
  id              对象ID

返回
  删除结果

说明
  目前支持的删除对象为任务和行动

*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('obj', 'id');
chk_empty_args('GET', $args);

// 提交参数整理
$obj = get_arg_str('GET', 'obj');                         // 删除对象
$id = get_arg_str('GET', 'id');                           // 任务ID或行动ID

$staff_id = $_SESSION['staff_id'];

// 删除对象判断
switch ($obj)
{
case 'task':
  // 取得指定任务ID的任务记录
  $task = get_task($id);
  if (!$task)
    exit_error('140', '任务ID不存在');
  // 创建人ID取得
  $owner_id = $task['owner_id'];
  if ($owner_id != $staff_id)
    exit_error('130', '你没有删除该任务的权限');
  // 任务删除
  $ret = del_task($id);
  $msg = '【' . $task['task_name'] . '】任务已成功删除';
  // 任务信息创建失败
  if ($ret == '')
    exit_error('110', '任务信息删除失败');
  break;

case 'action':
  // 取得指定行动ID的行动记录
  $action = get_action($id);
  if (!$action)
    exit_error('140', '行动ID不存在');
  // 创建人ID取得
  $owner_id = $action['owner_id'];
  if ($owner_id != $staff_id)
    exit_error('130', '你没有删除该行动的权限');
  // 行动删除
  $ret = del_action($id);
  $msg = '【' . $action['action_title'] . '】行动已成功删除';
  // 任务信息创建失败
  if ($ret == '')
    exit_error('110', '行动信息删除失败');
  break;

default:
  exit_error('120', '错误的删除对象');
  break;
}

// 输出结果
exit_ok($msg);
?>
