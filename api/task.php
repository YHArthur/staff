<?php
require_once "../inc/common.php";
require_once '../db/task.php';
require_once '../db/id_relation.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 任务设定 ==========================
POST参数
  task_id         任务ID
  task_name       任务
  task_intro      任务内容
  respo_id        责任人ID
  respo_name      责任人
  check_id        监管人ID
  check_name      监管人
  is_self         是否个人任务
  task_level      任务等级
  is_closed       是否完成
  is_limit        是否有期限
  limit_time      任务期限
  is_cycle        是否有周期
  cycle_nm        周期时间
  cycle_unit      周期单位

返回
  设定结果

说明
*/

// 禁止游客访问
api_exit_guest();

// 参数检查
$args = array('task_name', 'limit_time');
chk_empty_args('POST', $args);

// 提交参数整理
$task_id = get_arg_str('POST', 'task_id');                  // 任务ID
$task_name = get_arg_str('POST', 'task_name', 100);         // 任务
$task_intro = get_arg_str('POST', 'task_intro', 8192);      // 任务内容
$respo_id = get_arg_str('POST', 'respo_id');                // 责任人ID
$respo_name = get_arg_str('POST', 'respo_name');            // 责任人
$check_id = get_arg_str('POST', 'check_id');                // 监管人ID
$check_name = get_arg_str('POST', 'check_name');            // 监管人
$is_self = get_arg_str('POST', 'is_self');                  // 是否个人任务
$task_level = get_arg_str('POST', 'task_level');            // 任务等级
$is_limit = get_arg_str('POST', 'is_limit');                // 是否有期限
$limit_time = get_arg_str('POST', 'limit_time');            // 任务期限
$is_cycle = get_arg_str('POST', 'is_cycle');                // 是否有周期
$cycle_nm = get_arg_str('POST', 'cycle_nm');                // 周期时间
$cycle_unit = get_arg_str('POST', 'cycle_unit');            // 周期单位
$is_closed = get_arg_str('POST', 'is_closed');              // 是否完成

// 提交信息整理
$task_level = intval($task_level);
$is_limit = intval($is_limit);
$is_cycle = intval($is_cycle);
$cycle_nm = intval($cycle_nm);
$cycle_time_stamp = 0;

if ($is_cycle == 1) {
  switch ($cycle_unit) {
  case 'year':
      $cycle_time_stamp = $cycle_nm * 365 * 24 * 60 * 60;
      break;
  case 'month':
      $cycle_time_stamp = $cycle_nm * 30 * 24 * 60 * 60;
      break;
  case 'week':
      $cycle_time_stamp = $cycle_nm * 7 * 24 * 60 * 60;
      break;
  case 'day':
      $cycle_time_stamp = $cycle_nm * 24 * 60 * 60;
      break;
  default:
      $cycle_time_stamp = 0;
      break;
  }
}

$my_id = $_SESSION['staff_id'];
$my_name = $_SESSION['staff_name'];

// 责任人工号姓名处理
$respo_cd = '000';
if ($respo_name != '请选择员工') {
  list($respo_cd, $respo_name) = explode(" ", $respo_name);
} else {
  $respo_name = '';
}
if ($my_id == $respo_id)
  $respo_name = $my_name;

// 监管人工号姓名处理
$check_cd = '000';
if ($check_name != '请选择员工') {
  list($check_cd, $check_name) = explode(" ", $check_name);
} else {
  $check_name = '';
}
if ($my_id == $check_id)
  $check_name = $my_name;

// 无期限任务截止时间设为半年后
if ($is_limit == 0) {
  $half_year_later = strtotime('+6 month', strtotime(date('Y-m-d')));
  $limit_time = date('Y-m-d', $half_year_later) . ' 18:00:00';
}

// 个人任务监督检查设成自己
if ($is_self == 1) {
  $check_id = $my_id;
  $check_name = $my_name;
}

$data = array();
$data['task_id'] = $task_id;                              // 任务ID
$data['task_name'] = $task_name;                          // 任务
$data['task_intro'] = $task_intro;                        // 任务内容
$data['respo_id'] = $respo_id;                            // 责任人ID
$data['respo_name'] = $respo_name;                        // 责任人
$data['check_id'] = $check_id;                            // 监管人ID
$data['check_name'] = $check_name;                        // 监管人
$data['is_self'] = $is_self;                              // 是否个人任务
$data['task_level'] = $task_level;                        // 任务等级
$data['is_closed'] = $is_closed;                          // 是否完成
$data['is_limit'] = $is_limit;                            // 是否有期限
$data['limit_time'] = $limit_time;                        // 任务期限
$data['is_cycle'] = $is_cycle;                            // 是否有周期
$data['cycle_time_stamp'] = $cycle_time_stamp;            // 任务周期

// 任务ID为空，表示创建任务
if ($task_id == '') {
  // 取得唯一标示符GUID
  $task_id = get_guid();
  $data['task_id'] = $task_id;                            // 任务ID
  $data['owner_id'] = $my_id;                             // 创建人ID
  $data['owner_name'] = $my_name;                         // 创建人

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

// 任务关系人列表
$sids = array($my_id, $respo_id, $check_id);
// 增加ID关系
$ret = add_relation_ids('task_action', $task_id, $sids);
if ($ret == '')
  exit_error('110', '任务行动人列表添加失败');
$ret = add_relation_ids('task_follow', $task_id, $sids);
if ($ret == '')
  exit_error('110', '任务关注人列表添加失败');


// 输出结果
exit_ok($msg);
?>
