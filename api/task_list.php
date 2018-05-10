<?php
require_once '../inc/common.php';
require_once '../db/task.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 任务一览 ==========================
GET参数
  staff_id      员工ID
  task_type     任务类型（1:创建,2:责任,4:监督,5:创建+监督,7:所有）默认7
  public_type   公开类型（1:私人,2:公开,3:所有）默认2
  limit         （记录条数，可选）默认10 最大100
  offset        （记录偏移量，可选）默认0 与limit参数一起分页使用。如设置 offset=20&limit=10 取第21-30条记录
  
返回
  total     总记录件数
  rows      记录数组
    task_id         任务ID
    task_name       任务
    task_intro      任务内容
    owner_id        创建人ID
    owner_name      创建人
    respo_id        责任人ID
    respo_name      责任人
    check_id        监督人ID
    check_name      监督人
    task_level      任务等级
    task_value      任务价值
    task_perc       任务进度
    task_status     任务状态
    limit_time      任务期限
    utime           更新时间
    ctime           创建时间
    
说明
  本人相关任务一览
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('staff_id');
chk_empty_args('GET', $args);

// 取得员工ID
$staff_id =  get_arg_str('GET', 'staff_id');
// 取得分页参数
list($limit, $offset) = get_paging_arg('GET');

// 任务类型
$task_type = get_arg_str('GET', 'task_type');
$task_type = intval($task_type);
if ($task_type == 0)
  $task_type = 7;

// 取得公开类型
$public_type = get_arg_str('GET', 'public_type');
$public_type = intval($public_type);
if ($public_type == 0)
  $public_type = 2;


// 取得员工相关任务总数
$total = get_staff_task_total($staff_id, $task_type, $public_type);
// 取得员工相关任务列表
$rows = get_staff_task_list($staff_id, $task_type, $public_type, $limit, $offset);

// 返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['total'] = $total;
$rtn_ary['rows'] = $rows;

// 正常返回
$rtn_str = json_encode($rtn_ary);

php_end($rtn_str);
?>
