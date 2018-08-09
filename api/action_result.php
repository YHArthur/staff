<?php
require_once "../inc/common.php";
require_once '../db/task_action.php';

header("Access-Control-Allow-Origin: *");
header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 行动进展更新 ==========================
POST参数
  action_id       行动ID
  connect_type    沟通类型
  connect_name    联络对象
  result_memo     结果描述
  is_closed       是否完成

返回
  设定结果

说明
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('action_id', 'result_memo');
chk_empty_args('POST', $args);

// 提交参数整理
$action_id = get_arg_str('POST', 'action_id');              // 行动ID
$connect_type = get_arg_str('POST', 'connect_type');        // 沟通类型
$connect_name = get_arg_str('POST', 'connect_name', 255);   // 联络对象
$result_memo = get_arg_str('POST', 'result_memo', 8192);    // 结果描述
$is_closed = get_arg_str('POST', 'is_closed');              // 是否完成

// 提交信息整理
$connect_type = intval($connect_type);
$is_closed = intval($is_closed);

$my_id = $_SESSION['staff_id'];
$my_name = $_SESSION['staff_name'];

// 取得指定行动ID的行动记录
$action = get_action($action_id);
if (!$action)
  exit_error('140', '行动ID不存在');

$owner_id = $action['owner_id'];                            // 创建人ID
$respo_id = $action['respo_id'];                            // 责任人ID
if ($owner_id != $my_id && $respo_id != $my_id)
  exit_error('130', '你没有更新该行动的权限');

$action_title = $action['action_title'];                    // 行动标题
$old_closed = intval($action['is_closed']);                 // 原来是否完成

$data = array();
$data['connect_type'] = $connect_type;                      // 沟通类型
$data['connect_name'] = $connect_name;                      // 联络对象
$data['result_memo'] = $result_memo;                        // 结果描述
$data['is_closed'] = $is_closed;                            // 是否完成

if ($is_closed == 1 && $old_closed == 0)
  $data['closed_time'] = date('Y-m-d H:i:s');               // 结束时间

// 行动更新
$ret = upd_action($data, $action_id);
$msg = '【' . $action_title . '】行动已成功更新';
// 行动信息更新失败
if (!$ret)
  exit_error('110', '行动信息更新失败');

// 输出结果
exit_ok($msg);
?>
