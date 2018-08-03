<?php
require_once '../inc/common.php';
require_once '../db/task_action.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 行动一览 ==========================
GET参数
  staff_id      员工ID
  is_closed     是否完成（0:未完成,1:已完成,9:全部状态）默认0
  search        检索关键字
  limit         （记录条数，可选）默认10 最大100，行动状态为9的情况下0，1两种状态的行动各取limit件
  offset        （记录偏移量，可选）默认0 与limit参数一起分页使用。
                如设置 offset=20&limit=10 取第21-30条记录，行动状态为9的情况下无效

返回
  total     总记录件数
  rows      记录数组
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
  员工行动一览
  行动按是否完成（从小到大），更新时间（从晚到早）排序
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

// 行动状态
$is_closed = get_arg_str('GET', 'is_closed');
$is_closed = intval($is_closed);

// 检索关键字
$search = get_arg_str('GET', 'search');

// 包含私人行动
$is_self = 0;
if ($_SESSION['staff_id'] == $staff_id)
  $is_self = 1;

// 取得员工相关行动总数
$total = get_staff_action_total($staff_id, $search, $is_closed, $is_self);

// 取得员工相关行动列表
$rows = get_staff_action_list($staff_id, $search, $is_closed, $is_self, $limit, $offset);

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
