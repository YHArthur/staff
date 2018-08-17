<?php
require_once '../inc/common.php';
require_once '../db/task.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 任务详情 ==========================
GET参数
  task_id         任务ID

返回
  task_name       任务名称
  task_desc       任务简介
  task_intro      任务内容
  owner_id        创建人ID
  owner_name      创建人
  respo_id        责任人ID
  respo_name      责任人
  check_id        监督人ID
  check_name      监督人
  task_level      任务等级（0 可选 1 一般 2 重要 3 非常重要）
  task_value      任务价值
  task_perc       任务进度
  is_closed       是否完成（0 未完成 1 已完成）
  limit_time      任务期限
  utime           更新时间
  ctime           创建时间
  is_login        是否登录
  is_owner        是否创建人
  is_respo        是否责任人
  is_check        是否监管人

说明
*/

php_begin();

// 参数检查
$args = array('task_id');
chk_empty_args('GET', $args);

// 提交参数整理
$task_id = get_arg_str('GET', 'task_id');

// 取得指定任务ID的任务记录
$task = get_task($task_id);

// 记录不存在
if (!$task)
  exit_error('140', '任务ID不存在');

$task_name = $task['task_name'];          // 任务
$task_intro = $task['task_intro'];        // 任务内容
// 任务简介
$task_desc = mb_substr(strip_tags($task_intro), 0, 30, 'UTF-8');
$owner_id = $task['owner_id'];            // 创建人ID
$owner_name = $task['owner_name'];        // 创建人
$respo_id = $task['respo_id'];            // 责任人ID
$respo_name = $task['respo_name'];        // 责任人
$check_id = $task['check_id'];            // 监管人ID
$check_name = $task['check_name'];        // 监管人
$is_self = $task['is_self'];              // 是否个人任务
$task_level = $task['task_level'];        // 任务等级
$task_value = $task['task_value'];        // 任务价值
$task_perc = $task['task_perc'];          // 任务进度
$is_closed = $task['is_closed'];          // 是否完成
$limit_time = $task['limit_time'];        // 任务期限
$utime = $task['utime'];                  // 更新时间
$ctime = $task['ctime'];                  // 创建时间

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

// 是否监管人判定
$is_check = '0';
if ($my_id == $check_id)
  $is_check = '1';

//返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['task_name'] = $task_name;
$rtn_ary['task_desc'] = $task_desc;
$rtn_ary['task_intro'] = $task_intro;
$rtn_ary['owner_id'] = $owner_id;
$rtn_ary['owner_name'] = $owner_name;
$rtn_ary['respo_id'] = $respo_id;
$rtn_ary['respo_name'] = $respo_name;
$rtn_ary['check_id'] = $check_id;
$rtn_ary['check_name'] = $check_name;
$rtn_ary['is_self'] = $is_self;
$rtn_ary['task_level'] = $task_level;
$rtn_ary['task_value'] = $task_value;
$rtn_ary['task_perc'] = $task_perc;
$rtn_ary['is_closed'] = $is_closed;
$rtn_ary['limit_time'] = $limit_time;
$rtn_ary['utime'] = $utime;
$rtn_ary['ctime'] = $ctime;
$rtn_ary['is_login'] = $is_login;
$rtn_ary['is_owner'] = $is_owner;
$rtn_ary['is_respo'] = $is_respo;
$rtn_ary['is_check'] = $is_check;

$rtn_str = json_encode($rtn_ary);
php_end($rtn_str);
?>
