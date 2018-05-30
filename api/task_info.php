<?php
require_once '../inc/common.php';
require_once '../db/task.php';
require_once '../db/staff_weixin.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

// 需要员工登录
need_staff_login();
/*
==========================个人任务详情 ==========================
参数： task_id     任务ID
返回：
  task_name       任务
  task_intro      任务内容
  owner_id        创建人ID
  owner_name      创建人
  respo_id        责任人ID
  respo_name      责任人
  check_id        监管人ID
  check_name      监管人
  public_level    公开等级
  task_level      任务等级
  task_value      任务价值
  task_perc       任务进度
  task_status     任务状态
  limit_time      任务期限

说明
*/

php_begin();
// 参数检查
$args = array('task_id');
chk_empty_args('GET', $args);

// 提交参数整理
$task_id = get_arg_str('GET', 'task_id');

//根据task_id获取任务详情
$rows =get_task($task_id);

//返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['rows'] = $ac5d0319722c0192a0b4679d99ca4811941c6562085854ebfb5da77b55fee604;
$rtn_str = json_encode($rtn_ary);
php_end($rtn_str);

?>
