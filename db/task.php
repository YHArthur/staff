<?php
//======================================
// 函数: 取得指定任务ID的任务记录
// 参数: $task_id       任务ID
// 返回: 任务记录数组
//======================================
function get_task($task_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM task WHERE task_id = '{$task_id}'";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: chk_task_id_exist($task_id)
// 功能: 任务ID存在检查
// 参数: $task_id       任务ID
// 返回: true           任务ID存在
// 返回: false          任务ID不存在
//======================================
function chk_task_id_exist($task_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT task_id FROM task WHERE task_id = '{$task_id}'";
  $db->query($sql);
  $rds = $db->recordCount();
  if ($rds == 0)
    return false;
  return true;
}

//======================================
// 函数: 取得员工相关任务总数
// 参数: $staff_id      员工ID
// 参数: $task_status   任务状态（0 其他 1 已完成 2 未完成 9 全部状态）
// 参数: $public_level  公开等级（0 相关 1 组织 9 全部等级）
// 返回: 记录总数
//======================================
function get_staff_task_total($staff_id, $task_status = 2, $public_level = 1)
{
  $db = new DB_SATFF();

  $type_ary = array();
  $type_ary[] = "check_id = '{$staff_id}'";
  $type_ary[] = "respo_id = '{$staff_id}'";
  $type_ary[] = "owner_id = '{$staff_id}'";

  $sql = "SELECT COUNT(task_id) AS id_total FROM task ";
  $sql .= " WHERE is_void = 0";
  $sql .= " AND (" . join(" OR ", $type_ary) . ")";
  
  if ($task_status != 9)
    $sql .= " AND task_status = {$task_status}";
  if ($public_level != 9)
    $sql .= " AND public_level = {$public_level}";
  
  $total = $db->getField($sql, 'id_total');
  if ($total)
    return $total;
  return 0;
}

//======================================
// 函数: 取得员工相关任务列表
// 参数: $staff_id      员工ID
// 参数: $task_status   任务状态（0 其他 1 已完成 2 未完成 9 全部状态）
// 参数: $public_level  公开等级（0 相关 1 组织 9 全部等级）
// 参数: $limit         记录条数
// 参数: $offset        记录偏移量
// 返回: 记录列表
//======================================
function get_staff_task_list($staff_id, $task_status = 2, $public_level = 1, $limit, $offset)
{
  $db = new DB_SATFF();

  $type_ary = array();
  $type_ary[] = "check_id = '{$staff_id}'";
  $type_ary[] = "respo_id = '{$staff_id}'";
  $type_ary[] = "owner_id = '{$staff_id}'";
  
  $sql = "SELECT * FROM task";
  $sql .= " WHERE is_void = 0";
  $sql .= " AND (" . join(" OR ", $type_ary) . ")";
  if ($task_status != 9)
    $sql .= " AND task_status = {$task_status}";  
  if ($public_level != 9)
    $sql .= " AND public_level = {$public_level}";
  // 排序
  switch ($task_status) {
    // 执行中任务按重要度（从大到小），任务期限（从早到晚），更新时间（从晚到早）排序
    case 2:
      $sql .= " ORDER BY task_level DESC, limit_time, utime DESC";
      break;
    // 任务按更新时间（从晚到早）排序
    default:
      $sql .= " ORDER BY utime DESC";
      break;
  }
  $sql .= " limit {$offset},{$limit}";

  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 任务创建
// 参数: $data          信息数组
// 返回: task_id        创建成功的任务ID
// 返回: ''             任务创建失败
//======================================
function ins_task($data)
{
  $db = new DB_SATFF();
  $data['is_void'] = 0;
  $data['utime'] = time();
  $data['ctime'] = date('Y-m-d H:i:s');

  $sql = $db->sqlInsert("task", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return '';
  return $data['task_id'];
}

//======================================
// 函数: 任务更新
// 参数: $data          更新数组
// 参数: $task_id       任务ID
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_task($data, $task_id)
{
  $db = new DB_SATFF();

  $data['utime'] = time();
  $where = "task_id = '{$task_id}'";
  $sql = $db->sqlUpdate("task", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>