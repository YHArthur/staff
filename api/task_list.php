<?php
require_once '../inc/common.php';
require_once '../db/task.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 任务一览 ==========================
GET参数
  staff_id      员工ID
  is_closed     是否完成（0 未完成 1 已完成,9 全部状态）
  sort          排序字段
  order         正序，倒序
  search        检索关键字
  limit         （记录条数，可选）默认10 最大100
  offset        （记录偏移量，可选）默认0 与limit参数一起分页使用。
                如设置 offset=20&limit=10 取第21-30条记录，任务状态为9的情况下无效

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
    task_level      任务等级（0 可选 1 一般 2 重要 3 非常重要）
    task_value      任务价值
    task_perc       任务进度
    is_closed       是否完成（0 未完成 1 已完成,9 全部状态）
    limit_time      任务期限
    utime           更新时间
    ctime           创建时间

说明
  员工任务一览
  执行中任务按重要度（从大到小），任务期限（从早到晚），更新时间（从晚到早）排序
  已完成和其他任务按更新时间（从晚到早）排序
  当前登录的staff_id与创建人ID，责任人ID或监督人ID一致时，包含is_public=0的数据，否则只显示is_public=1的数据
*/

// 禁止游客访问
api_exit_guest();

// 参数检查
$args = array('staff_id');
chk_empty_args('GET', $args);

// 取得员工ID
$staff_id =  get_arg_str('GET', 'staff_id');
// 取得分页参数
list($limit, $offset) = get_paging_arg('GET');

// 任务状态
$is_closed = get_arg_str('GET', 'is_closed');
$is_closed = intval($is_closed);

$sort = get_arg_str('GET', 'sort');         // 排序字段
$order = get_arg_str('GET', 'order');       // 正序，倒序
$search = get_arg_str('GET', 'search');     // 检索关键字

// 是否个人任务
$is_self = 0;
if ($_SESSION['staff_id'] == $staff_id)
  $is_self = 1;

// 取得员工相关任务总数
$total = get_staff_task_total($staff_id, $search, $is_closed, $is_self);
// 取得员工相关任务列表
$rows = get_staff_task_list($staff_id, $search, $is_closed, $is_self, $sort, $order, $limit, $offset);

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
