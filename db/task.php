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
// 参数: $search        检索关键字
// 参数: $is_closed     是否完成（0 未完成 1 已完成,9 全部状态）
// 参数: $is_self       是否个人任务（0 公开 1 全部）
// 返回: 记录总数
//======================================
function get_staff_task_total($staff_id, $search = '', $is_closed = 0, $is_self = 0)
{
  $db = new DB_SATFF();

  $type_ary = array();
  $type_ary[] = "check_id = '{$staff_id}'";
  $type_ary[] = "respo_id = '{$staff_id}'";
  $type_ary[] = "owner_id = '{$staff_id}'";

  $sql = "SELECT COUNT(task_id) AS id_total FROM task ";
  $sql .= " WHERE is_void = 0";
  $sql .= " AND (" . join(" OR ", $type_ary) . ")";
  if (trim($search) != '')
    $sql .= " AND task_name like '%{$search}%'";
  if ($is_closed != 9)
    $sql .= " AND is_closed = {$is_closed}";
  if ($is_self != 1)
    $sql .= " AND is_self = {$is_self}";
  
  $total = $db->getField($sql, 'id_total');
  if ($total)
    return $total;
  return 0;
}

//======================================
// 函数: 取得员工相关任务下拉列表
// 参数: $staff_id      员工ID
// 返回: 记录列表
//======================================
function get_staff_task_list_select($staff_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT T.task_id, T.task_name";
  $sql .= " FROM task AS T";
  $sql .= " INNER JOIN id_relation AS R";
  $sql .= " ON T.task_id = R.mid";
  $sql .= " WHERE R.sid = '{$staff_id}'";
  $sql .= " AND R.rlt_type = 'task_action'";
  $sql .= " AND R.is_void = 0";
  $sql .= " AND T.is_closed = 0";
  $sql .= " AND T.is_void = 0";
  $sql .= " ORDER BY T.utime DESC";

  $db->query($sql);
  $rows = $db->fetchAll();
  $task_list = array();
  $task_list[$staff_id] = '临时行动';
  foreach ($rows as $row) {
    $task_id = $row['task_id'];
    $task_name = $row['task_name'];
    $task_list[$task_id] = $task_name;
  }

  return $task_list;
}

//======================================
// 函数: 取得员工相关任务列表
// 参数: $staff_id      员工ID
// 参数: $search        检索关键字
// 参数: $is_closed     是否完成（0 未完成 1 已完成,9 全部状态）
// 参数: $is_self       是否个人任务（0 公开 1 全部）
// 参数: $sort          排序字段
// 参数: $order         正序，倒序
// 参数: $limit         记录条数
// 参数: $offset        记录偏移量
// 返回: 记录列表
//======================================
function get_staff_task_list($staff_id, $search = '', $is_closed = 0, $is_self = 0, $sort, $order, $limit, $offset)
{
  $db = new DB_SATFF();

  $type_ary = array();
  $type_ary[] = "check_id = '{$staff_id}'";
  $type_ary[] = "respo_id = '{$staff_id}'";
  $type_ary[] = "owner_id = '{$staff_id}'";
  
  $sql = "SELECT * FROM task";
  $sql .= " WHERE is_void = 0";
  $sql .= " AND (" . join(" OR ", $type_ary) . ")";
  if (trim($search) != '')
    $sql .= " AND task_name like '%{$search}%'";
  if ($is_closed != 9)
    $sql .= " AND is_closed = {$is_closed}";  
  if ($is_self != 1)
    $sql .= " AND is_self = {$is_self}";
  $sql .= " ORDER BY ";
  if (trim($sort) != '')
    $sql .= " {$sort} {$order},";  
  $sql .= " utime DESC";
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

//======================================
// 函数: 任务删除(物理删除)
// 参数: $task_id       任务ID
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function del_task($task_id)
{
  $db = new DB_SATFF();

  $data = array();
  $data['utime'] = time();
  $data['is_void'] = 1;
  $where = "task_id = '{$task_id}'";
  $sql = $db->sqlUpdate("task", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>
