<?php
//======================================
// 函数: 取得指定任务ID的任务记录
// 参数: $task_id       任务ID
// 返回: 任务记录数组
//======================================
function get_task($task_id)
{
  $db = new DB_WWW();

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
  $db = new DB_WWW();

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
// 参数: $task_type     任务类型（1:创建,2:责任,4:监督,5:创建+监督,7:所有）
// 参数: $public_type   公开类型（1:私人,2:公开,3:所有）
// 返回: 记录总数
//======================================
function get_staff_task_total($staff_id, $task_type = 7, $public_type = 2)
{
  $db = new DB_WWW();

  $tmp_level = intval($task_type);
  $tmp_public = intval($public_type) - 1;
  $type_ary = array();

  $sql = "SELECT COUNT(task_id) AS id_total FROM task ";
  $sql .= " WHERE is_void = 0";
  if ($tmp_level >= 4) {
    $tmp_level -= 4;
    $type_ary[] = "check_id = '{$staff_id}'";
  }
  if ($tmp_level >= 2) {
    $tmp_level -= 2;
    $type_ary[] = "respo_id = '{$staff_id}'";
  }
  if ($tmp_level >= 1) {
    $tmp_level -= 1;
    $type_ary[] = "owner_id = '{$staff_id}'";
  }
  if ($task_type > 0)
    $sql .= " AND (" . join(" OR ", $type_ary) . ")";
  
  if ($tmp_public < 2)
    $sql .= " AND is_public = {$tmp_public}";
  
  $total = $db->getField($sql, 'id_total');
  if ($total)
    return $total;
  return 0;
}

//======================================
// 函数: 取得员工相关任务列表
// 参数: $staff_id      员工ID
// 参数: $task_type     任务类型（1:创建,2:责任,4:监督,5:创建+监督,7:所有）
// 参数: $public_type   公开类型（1:私人,2:公开,3:所有）
// 参数: $limit         记录条数
// 参数: $offset        记录偏移量
// 返回: 记录列表
//======================================
function get_staff_task_list($staff_id, $task_type = 7, $public_type = 2, $limit, $offset)
{
  $db = new DB_WWW();

  $tmp_level = intval($task_type);
  $tmp_public = intval($public_type) - 1;
  $type_ary = array();
  
  $sql = "SELECT * FROM task";
  $sql .= " WHERE is_void = 0";
  if ($tmp_level >= 4) {
    $tmp_level -= 4;
    $type_ary[] = "check_id = '{$staff_id}'";
  }
  if ($tmp_level >= 2) {
    $tmp_level -= 2;
    $type_ary[] = "respo_id = '{$staff_id}'";
  }
  if ($tmp_level >= 1) {
    $tmp_level -= 1;
    $type_ary[] = "owner_id = '{$staff_id}'";
  }
  if ($task_type > 0)
    $sql .= " AND (" . join(" OR ", $type_ary) . ")";
  
  if ($tmp_public < 2)
    $sql .= " AND is_public = {$tmp_public}";
  $sql .= " ORDER BY task_status DESC, ctime";
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
  $db = new DB_WWW();
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
  $db = new DB_WWW();

  $data['utime'] = time();
  $where = "task_id = '{$task_id}'";
  $sql = $db->sqlUpdate("task", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>