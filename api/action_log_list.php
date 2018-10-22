<?php
require_once '../inc/common.php';
require_once '../db/cnt_staff_action.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 员工访问统计一览 ==========================
GET参数
  staff_id      访问ID（可选）
  action_url    访问URL（可选）
  action_ip     访问IP（可选，长整数）
  limit         （记录条数，可选）默认10 最大100
  offset        （记录偏移量，可选）默认0 与limit参数一起分页使用。
                如设置 offset=20&limit=10 取第21-30条记录

返回
  total     总记录件数
  rows      记录数组
    logid         访问日志ID
    from_url      来源URL
    from_prm      来源URL参数
    staff_id      访问ID
    staff_name    员工姓名
    action_url    访问URL
    action_prm    访问URL参数
    action_time   访问时间戳
    action_ip     访问IP
    url           完整的访问URL
    time          可视化的访问时间
    ip            可视化的访问IP

说明
  员工访问统计一览
  最新完成员工，行动完成时间排序
*/

// 禁止游客访问
exit_guest();

// 参数检查
// $args = array('staff_id');
// chk_empty_args('GET', $args);

// 参数取得
$staff_id = get_arg_str('GET', 'id');
$action_url = get_arg_str('GET', 'url');
$action_ip =  get_arg_str('GET', 'ip');

// 取得分页参数
list($limit, $offset) = get_paging_arg('GET');

// 参数整理
if (!empty($action_ip))
  $action_ip = ip2long($action_ip);

// 取得员工访问统计总数
$total = get_cnt_staff_action_total($staff_id, $action_url, $action_ip);
// 取得员工访问统计列表
$rows = get_cnt_staff_action_list($staff_id, $action_url, $action_ip, $limit, $offset);

$rtn_rows = array();
foreach($rows as $row) {
  $row['time'] = date('Y-m-d H:i:s', $row['action_time']);
  $row['url'] = $row['action_url'];
  if (!empty($row['action_prm']))
    $row['url'] .= '?' . $row['action_prm'];
  $row['ip'] = long2ip($row['action_ip']);
  $rtn_rows[] = $row;
}

// 返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['total'] = $total;
$rtn_ary['rows'] = $rtn_rows;

// 正常返回
$rtn_str = json_encode($rtn_ary);

php_end($rtn_str);
?>
