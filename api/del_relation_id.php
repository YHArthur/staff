<?php
require_once "../inc/common.php";
require_once '../db/task.php';
require_once '../db/id_relation.php';

header("Access-Control-Allow-Origin: *");
header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 取消ID关系 ==========================
POST参数
  type            关系类型
  mid             主ID

返回
  取消结果

说明
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('type', 'mid');
chk_empty_args('POST', $args);

// 提交参数整理
$type = get_arg_str('POST', 'type');                        // 关系类型
$mid = get_arg_str('POST', 'mid');                          // 主ID

// 当前用户ID
$my_id = $_SESSION['staff_id'];
$sids = array($my_id);

switch ($type)
{
  case 'task_action':
  case 'task_follow':
    // 取得指定任务ID的任务记录
    $task = get_task($mid);
    if (!$task)
      exit_error('140', '任务ID不存在');
    // 取消ID关系
    $ret = del_relation_ids($type, $mid, $sids);
    if (!$ret)
      exit_error('110', '取消关系失败');
    break;
  default:
    exit_error('120', '取消的关系类型');
}

$msg = '操作成功';
// 输出结果
exit_ok($msg);
?>
