<?php
require_once '../inc/common.php';
require_once '../db/task.php';
require_once '../db/staff_main.php';

header("cache-control:no-cache,must-revalidate");
header("Content-type:text/html;charset=utf-8");

if (!session_id())
  session_start();

if (!isset($_SESSION['staff_id']))
  exit_error('119', '网页已失效，请刷新页面再试');

// 参数检查
$args = array('task_id');
chk_empty_args('GET', $args);
$task_id =  get_arg_str('GET', 'task_id');
$ret = "";
// 更新数据数组
$data = array();

if(!empty($_GET['task_name']))
  $data['task_name'] = get_arg_str('GET', 'task_name',255);

if(!empty($_GET['task_intro']))
  $data['task_intro'] = get_arg_str('GET', 'task_intro', 8192);

if(!empty($_GET['respo_name']))
  $data['respo_name'] = get_arg_str('GET', 'respo_name');

if(!empty($_GET['check_name']))
  $data['check_name'] = get_arg_str('GET', 'check_name');

if(!empty($_GET['task_level']))
  $data['task_level'] = get_arg_str('GET', 'task_level');

if(!empty($_GET['task_value']))
  $data['task_value'] = get_arg_str('GET', 'task_value');

if(!empty($_GET['task_perc']))
  $data['task_perc'] = get_arg_str('GET', 'task_perc');

if(!empty($_GET['limit_time']))
  $data['limit_time'] = get_arg_str('GET', 'limit_time');

// 更新任务信息
if (count($data))
   $ret = upd_task($data, $task_id);

if (!$ret)
  exit_error('110', '任务信息无修改');
// 正常返回
exit_ok('修改成功');

?>
