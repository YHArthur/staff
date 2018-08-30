<?php
require_once '../inc/common.php';
require_once '../db/action.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 行动详情 ==========================
GET参数
  action_id         行动ID

返回
  action_title      行动名称
  action_desc       行动简介
  result_memo       行动结果
  owner_id          创建人ID
  owner_name        创建人
  respo_id          责任人ID
  respo_name        责任人
  is_closed         是否完成（0 未完成 1 已完成）
  limit_time        行动期限
  utime             更新时间
  ctime             创建时间
  is_login          是否登录
  is_owner          是否创建人
  is_respo          是否责任人

说明
*/

php_begin();

// 参数检查
$args = array('action_id');
chk_empty_args('GET', $args);

// 提交参数整理
$action_id = get_arg_str('GET', 'action_id');

// 取得指定行动ID的行动记录
$action = get_action($action_id);

// 记录不存在
if (!$action)
  exit_error('140', '行动ID不存在');

$task_id = $action['task_id'];                // 所属任务ID
$task_name = $action['task_name'];            // 任务标题
$is_self = $action['is_self'];                // 是否个人任务
$action_title = $action['action_title'];      // 行动标题
$action_intro = $action['action_intro'];      // 预期结果
// 行动简介
$action_desc = mb_substr(strip_tags($action_intro), 0, 30, 'UTF-8');
$result_type = $action['result_type'];        // 成果类型（I/O）
$result_name = $action['result_name'];        // 成果名称
$connect_type = $action['connect_type'];      // 沟通类型
$connect_name = $action['connect_name'];      // 联络对象
$result_memo = $action['result_memo'];        // 行动结果
$owner_id = $action['owner_id'];              // 创建人ID
$owner_name = $action['owner_name'];          // 创建人
$respo_id = $action['respo_id'];              // 责任人ID
$respo_name = $action['respo_name'];          // 责任人
$prvs_action_id = $action['prvs_action_id'];  // 前置行动ID
$is_device = $action['is_device'];            // 是否限定设备
$device_name = $action['device_name'];        // 设备名称
$is_location = $action['is_location'];        // 是否限定地点
$location_name = $action['location_name'];    // 地点名称
$is_closed = $action['is_closed'];            // 是否完成
$closed_time = $action['closed_time'];        // 结束时间
$utime = $action['utime'];                    // 更新时间
$ctime = $action['ctime'];                    // 创建时间

// 登录员工ID取得
if (!session_id())
  session_start();
$my_id = '0';
if (isset($_SESSION['staff_id']))
  $my_id = $_SESSION['staff_id'];

// 是否登录判定
$is_login = '0';
if ($my_id != '0')
  $is_login = '1';

// 是否创建人判定
$is_owner = '0';
if ($my_id == $owner_id)
  $is_owner = '1';

// 是否责任人判定
$is_respo = '0';
if ($my_id == $respo_id)
  $is_respo = '1';

//返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['task_id'] = $task_id;
$rtn_ary['task_name'] = $task_name;
$rtn_ary['is_self'] = $is_self;
$rtn_ary['action_title'] = $action_title;
$rtn_ary['action_intro'] = $action_intro;
$rtn_ary['action_desc'] = $action_desc;
$rtn_ary['result_type'] = $result_type;
$rtn_ary['result_name'] = $result_name;
$rtn_ary['connect_type'] = $connect_type;
$rtn_ary['connect_name'] = $connect_name;
$rtn_ary['result_memo'] = $result_memo;
$rtn_ary['owner_id'] = $owner_id;
$rtn_ary['owner_name'] = $owner_name;
$rtn_ary['respo_id'] = $respo_id;
$rtn_ary['respo_name'] = $respo_name;
$rtn_ary['prvs_action_id'] = $prvs_action_id;
$rtn_ary['is_device'] = $is_device;
$rtn_ary['device_name'] = $device_name;
$rtn_ary['is_location'] = $is_location;
$rtn_ary['location_name'] = $location_name;
$rtn_ary['is_closed'] = $is_closed;
$rtn_ary['closed_time'] = $closed_time;
$rtn_ary['utime'] = $utime;
$rtn_ary['ctime'] = $ctime;
$rtn_ary['is_login'] = $is_login;
$rtn_ary['is_owner'] = $is_owner;
$rtn_ary['is_respo'] = $is_respo;

$rtn_str = json_encode($rtn_ary);
php_end($rtn_str);
?>
