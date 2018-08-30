<?php
require_once '../inc/common.php';
require_once '../db/task.php';
require_once '../db/action.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 任务详情 ==========================
GET参数
  task_id           任务ID

返回
  task_name         任务名称
  task_desc         任务简介
  task_intro        任务内容
  owner_id          创建人ID
  owner_name        创建人
  respo_id          责任人ID
  respo_name        责任人
  check_id          监督人ID
  check_name        监督人
  is_self           是否个人任务（0 公开 1 个人）
  task_level        任务等级（0 可选 1 一般 2 重要 3 非常重要）
  task_value        任务价值
  task_perc         任务进度
  result_memo       结果描述
  is_closed         是否完成（0 未完成 1 已完成）
  closed_time       完成时间
  is_limit          是否有期限（0 长期 1 有期限）
  limit_time        任务期限
  is_cycle          是否有周期（0 无 1 有周期）
  cycle_time_stamp  周期时间
  prvs_task_id      上一任务ID
  utime             更新时间
  ctime             创建时间
  is_login          是否登录
  is_owner          是否创建人
  is_respo          是否责任人
  is_check          是否监管人
  action_total      行动总数
  action_rows       行动数组
    action_id       行动ID
    task_id         所属任务ID
    task_name       任务名称
    action_title    行动标题
    action_intro    行动预期结果
    action_memo     行动备注
    result_type     成果类型（I/O/U）
    result_name     成果名称
    result_memo     结果描述
    owner_id        创建人ID
    owner_name      创建人
    respo_id        责任人ID
    respo_name      责任人
    prvs_action_id  前置行动ID
    closed_time     结束时间
    is_device       是否限定设备（0 不限定 1 限定）
    device_name     设备名称
    is_location     是否限定地点（0 不限定 1 限定）
    location_name   地点名称
    is_closed       是否完成（0 未完成 1 已完成 9 全部）
    utime           更新时间
    ctime           创建时间

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

$task_name = $task['task_name'];                // 任务
$task_intro = $task['task_intro'];              // 任务内容
// 任务简介
$task_desc = mb_substr(strip_tags($task_intro), 0, 30, 'UTF-8');
$owner_id = $task['owner_id'];                  // 创建人ID
$owner_name = $task['owner_name'];              // 创建人
$respo_id = $task['respo_id'];                  // 责任人ID
$respo_name = $task['respo_name'];              // 责任人
$check_id = $task['check_id'];                  // 监管人ID
$check_name = $task['check_name'];              // 监管人
$is_self = $task['is_self'];                    // 是否个人任务
$task_level = $task['task_level'];              // 任务等级
$task_value = $task['task_value'];              // 任务价值
$task_perc = $task['task_perc'];                // 任务进度
$result_memo= $task['result_memo'];             // 结果描述
$is_closed = $task['is_closed'];                // 是否完成
$closed_time = $task['closed_time'];            // 完成时间
$is_limit = $task['is_limit'];                  // 是否有期限
$limit_time = $task['limit_time'];              // 任务期限
$is_cycle = $task['is_cycle'];                  // 是否有周期
$cycle_time_stamp = $task['cycle_time_stamp'];  // 周期时间
$prvs_task_id = $task['prvs_task_id'];          // 上一任务ID
$utime = $task['utime'];                        // 更新时间
$ctime = $task['ctime'];                        // 创建时间

$action_total = get_action_total_by_task_id($task_id);   // 行动总数
$action_rows = get_action_list_by_task_id($task_id);     // 行动数组

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
$rtn_ary['closed_time'] = $closed_time;
$rtn_ary['is_limit'] = $is_limit;
$rtn_ary['limit_time'] = $limit_time;
$rtn_ary['is_cycle'] = $is_cycle;
$rtn_ary['cycle_time_stamp'] = $cycle_time_stamp;
$rtn_ary['prvs_task_id'] = $prvs_task_id;
$rtn_ary['utime'] = $utime;
$rtn_ary['ctime'] = $ctime;
$rtn_ary['is_login'] = $is_login;
$rtn_ary['is_owner'] = $is_owner;
$rtn_ary['is_respo'] = $is_respo;
$rtn_ary['is_check'] = $is_check;
$rtn_ary['action_total'] = $action_total;
$rtn_ary['action_rows'] = $action_rows;

$rtn_str = json_encode($rtn_ary);
php_end($rtn_str);
?>
