<?php
require_once '../inc/common.php';
require_once '../db/action.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 完成行动一览 ==========================
GET参数
  staff_id      员工ID
  week          前几周（默认0）
  day           日期
  limit         （记录条数，可选）默认10 最大100
  offset        （记录偏移量，可选）默认0 与limit参数一起分页使用。
                如设置 offset=20&limit=10 取第21-30条记录

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
  员工完成行动一览
  行动按最新完成员工，行动完成时间排序
*/

// 禁止游客访问
exit_guest();

// 参数检查
// $args = array('staff_id');
// chk_empty_args('GET', $args);

// 参数取得
$staff_id =  get_arg_str('GET', 'staff_id');
$week =  get_arg_str('GET', 'week');
$day =  get_arg_str('GET', 'day');

// 取得分页参数
list($limit, $offset) = get_paging_arg('GET');

// 参数整理
$week = intval($week);

// 未设置日期，默认当前周
if (empty($day)) {
  // 周一开始时间计算
  $timestamp_from = strtotime('Sunday -6 day', strtotime(date('Y-m-d'))) - $week*7*24*60*60;
  $timestamp_to = $timestamp_from + 7*24*60*60 - 1;
} else {
  $timestamp_from = strtotime($day);
  $timestamp_to = $timestamp_from + 24*60*60 - 1;
}

// 取得一段时间内可公开的已完成行动总数
$total = get_open_closed_action_total($staff_id, date('Y-m-d H:i:s', $timestamp_from), date('Y-m-d H:i:s', $timestamp_to));
// 取得一段时间内可公开的已完成行动列表
$rows = get_open_closed_action_list($staff_id, date('Y-m-d H:i:s', $timestamp_from), date('Y-m-d H:i:s', $timestamp_to), $limit, $offset);

// 返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['total'] = $total;
$rtn_ary['rows'] = $rows;

// 未设置日期，默认周
if (empty($day)) {
  $rtn_ary['week_begin'] = date('y年n月j日', $timestamp_from);
  $rtn_ary['week_end'] = date('n月j日', $timestamp_to);
} else {
  $week_ary = array("日","一","二","三","四","五","六");
  $action_day = date('y年n月j日', $timestamp_from);
  $action_day .= '星期' . $week_ary[date("w", $timestamp_from)];
  $rtn_ary['action_day'] = $action_day;
}

// 正常返回
$rtn_str = json_encode($rtn_ary);

php_end($rtn_str);
?>
