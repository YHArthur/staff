<?php
require_once "../inc/common.php";
require_once '../db/task.php';
require_once '../db/action.php';

header("Access-Control-Allow-Origin: *");
header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 行动设定 ==========================
POST参数
  action_id       行动ID
  task_id         任务ID
  action_title    行动标题
  action_intro    行动预期结果
  result_type     成果类型
  result_name     成果名称
  respo_id        责任人ID(TODO)
  respo_name      责任人(TODO)
  is_location     是否限定地点
  location_name   地点名称

返回
  设定结果

说明
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('task_id', 'action_title', 'result_type');
chk_empty_args('POST', $args);

// 提交参数整理
$action_id = get_arg_str('POST', 'action_id');              // 行动ID
$task_id = get_arg_str('POST', 'task_id');                  // 任务ID
$action_title = get_arg_str('POST', 'action_title');        // 行动标题
$action_intro = get_arg_str('POST', 'action_intro', 8192);  // 行动预期结果
$result_type = get_arg_str('POST', 'result_type');          // 成果类型
$result_name = get_arg_str('POST', 'result_name', 255);     // 成果名称
$connect_type = get_arg_str('POST', 'connect_type');        // 沟通类型
$connect_name = get_arg_str('POST', 'connect_name', 255);   // 联络对象
$respo_id = get_arg_str('POST', 'respo_id');                // 责任人ID(TODO)
$respo_name = get_arg_str('POST', 'respo_name');            // 责任人(TODO)
$is_location = get_arg_str('POST', 'is_location');          // 是否限定地点
$location_name = get_arg_str('POST', 'location_name');      // 地点名称

// 提交信息整理
$connect_type = intval($connect_type);
$is_location = intval($is_location);
// 地点名称处理
switch ($is_location)
{
case 0:
  $location_name = '';
  break;  
case 1:
  $location_name = '公司';
  break;  
case 2:
  $location_name = '家';
  break;
}

$my_id = $_SESSION['staff_id'];
$my_name = $_SESSION['staff_name'];

$data = array();
$data['task_id'] = $task_id;                                // 任务ID
$data['action_title'] = $action_title;                      // 任务
$data['action_intro'] = $action_intro;                      // 行动预期结果
$data['respo_id'] = $my_id;                                 // 责任人ID
$data['respo_name'] = $my_name;                             // 责任人
$data['result_type'] = $result_type;                        // 成果类型
$data['result_name'] = $result_name;                        // 成果名称
$data['connect_type'] = $connect_type;                      // 沟通类型
$data['connect_name'] = $connect_name;                      // 联络对象
$data['is_location'] = $is_location;                        // 是否限定地点
$data['location_name'] = $location_name;                    // 地点名称

// 任务ID为空，表示创建任务
if ($action_id == '') {
  // 取得唯一标示符GUID
  $data['action_id'] = get_guid();                          // 行动ID
  $data['owner_id'] = $my_id;                               // 创建人ID
  $data['owner_name'] = $my_name;                           // 创建人

  // 行动创建
  $ret = ins_action($data);
  $msg = '【' . $action_title . '】行动已成功添加';
  // 行动信息创建失败
  if ($ret == '')
    exit_error('110', '行动信息创建失败');
} else {
  // 行动更新
  $ret = upd_action($data, $action_id);
  $msg = '【' . $action_title . '】行动已成功更新';
  // 行动信息更新失败
  if (!$ret)
    exit_error('110', '行动信息更新失败');
  // 任务更新时间更新
  $data = array();
  upd_task($data, $task_id);
}

// 输出结果
exit_ok($msg);
?>
