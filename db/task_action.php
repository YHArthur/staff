<?php
//======================================
// 函数: 取得指定行动ID的行动记录
// 参数: $action_id     行动ID
// 返回: 行动记录数组
//======================================
function get_action($action_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM task_action WHERE action_id = '{$action_id}'";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: 取得任务所有行动责任人列表
// 参数: $task_id       所属任务ID
// 返回: 记录列表
//======================================
function get_action_respo_list_by_task($task_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT respo_id AS staff_id,";
  $sql .= " MIN(respo_name) AS staff_name,";
  $sql .= " COUNT(action_id) AS action_total";
  $sql .= " FROM task_action";
  $sql .= " WHERE is_void = 0";
  $sql .= " AND task_id = '{$task_id}'";
  $sql .= " GROUP BY respo_id";
  $sql .= " ORDER BY action_total DESC";

  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 取得任务行动责任人相关行动总数
// 参数: $task_id       所属任务ID
// 参数: $respo_id      责任人ID
// 返回: 记录总数
//======================================
function get_action_total_by_task_respo($task_id, $respo_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT COUNT(action_id) AS id_total FROM task_action ";
  $sql .= " WHERE is_void = 0";
  $sql .= " AND task_id = '{$task_id}'";
  $sql .= " AND respo_id = '{$respo_id}'";

  $total = $db->getField($sql, 'id_total');
  if ($total)
    return $total;
  return 0;
}

//======================================
// 函数: 取得任务行动责任人相关行动列表
// 参数: $task_id       所属任务ID
// 参数: $respo_id      责任人ID
// 参数: $limit         记录条数（TODO）
// 参数: $offset        记录偏移量（TODO）
// 返回: 记录列表
//======================================
function get_action_list_by_task_respo($task_id, $respo_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM task_action";
  $sql .= " WHERE is_void = 0";
  $sql .= " AND task_id = '{$task_id}'";
  $sql .= " AND respo_id = '{$respo_id}'";
  // 行动按是否完成（从小到大），更新时间（从晚到早）排序
  $sql .= " ORDER BY is_closed, utime DESC";
  // $sql .= " limit {$offset},{$limit}";

  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 取得员工相关行动总数
// 参数: $staff_id      员工ID
// 参数: $search        检索关键字
// 参数: $is_closed     是否完成（0 未完成 1 已完成 9 全部）
// 参数: $is_self       是否本人（0 非本人 1 本人）TODO
// 返回: 记录总数
//======================================
function get_staff_action_total($staff_id, $search, $is_closed, $is_self)
{
  $db = new DB_SATFF();

  $sql = "SELECT COUNT(action_id) AS id_total";
  $sql .= " FROM task_action AS A";
  if ($is_self == 0) {
    $sql .= " INNER JOIN task AS T";
  } else {
    $sql .= " LEFT JOIN task AS T";
  }
  $sql .= " ON A.task_id = T.task_id";
  $sql .= " WHERE A.is_void = 0";
  if ($is_self == 0)
    $sql .= " AND T.is_void = 0";
  $sql .= " AND A.respo_id = '{$staff_id}'";
  if (trim($search) != '')
    $sql .= " AND A.action_title like '%{$search}%'";
  if ($is_closed != 9)
    $sql .= " AND A.is_closed = %{$is_closed}%";
  if ($is_self != 1)
    $sql .= " AND T.is_self = 0";
  
  $total = $db->getField($sql, 'id_total');
  if ($total)
    return $total;
  return 0;
}

//======================================
// 函数: 取得任务行动责任人相关行动列表
// 参数: $staff_id      员工ID
// 参数: $search        检索关键字
// 参数: $is_closed     是否完成（0 未完成 1 已完成 9 全部）
// 参数: $is_self       是否本人（0 非本人 1 本人）TODO
// 参数: $sort          排序字段
// 参数: $order         正序，倒序
// 参数: $limit         记录条数（TODO）
// 参数: $offset        记录偏移量（TODO）
// 返回: 记录列表
//======================================
function get_staff_action_list($staff_id, $search, $is_closed, $is_self, $sort, $order, $limit, $offset)
{
  $db = new DB_SATFF();

  $sql = "SELECT A.*, T.task_name, T.is_self";
  $sql .= " FROM task_action AS A";
  if ($is_self == 0) {
    $sql .= " INNER JOIN task AS T";
  } else {
    $sql .= " LEFT JOIN task AS T";
  }
  $sql .= " ON A.task_id = T.task_id";
  $sql .= " WHERE A.is_void = 0";
  if ($is_self == 0)
    $sql .= " AND T.is_void = 0";
  $sql .= " AND A.respo_id = '{$staff_id}'";
  if (trim($search) != '')
    $sql .= " AND A.action_title like '%{$search}%'";
  if ($is_closed != 9)
    $sql .= " AND A.is_closed = %{$is_closed}%";
  if ($is_self != 1)
    $sql .= " AND T.is_self = 0";
  // 行动按是否完成（从小到大），更新时间（从晚到早）排序
  $sql .= " ORDER BY ";
  if (trim($sort) != '')
    $sql .= " A.{$sort} {$order},";
  $sql .= " A.is_closed, A.utime DESC";
  $sql .= " limit {$offset},{$limit}";

  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 行动创建
// 参数: $data          信息数组
// 返回: action_id      创建成功的行动ID
// 返回: ''             行动创建失败
//======================================
function ins_action($data)
{
  $db = new DB_SATFF();
  $data['is_void'] = 0;
  $data['utime'] = time();
  $data['ctime'] = date('Y-m-d H:i:s');

  $sql = $db->sqlInsert("task_action", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return '';
  return $data['action_id'];
}

//======================================
// 函数: 行动更新
// 参数: $data          更新数组
// 参数: $action_id     行动ID
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_action($data, $action_id)
{
  $db = new DB_SATFF();

  $data['utime'] = time();
  $where = "action_id = '{$action_id}'";
  $sql = $db->sqlUpdate("task_action", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}

//======================================
// 函数: 行动删除(物理删除)
// 参数: $action_id     行动ID
// 返回: true           删除成功
// 返回: false          删除失败
//======================================
function del_action($action_id)
{
  $db = new DB_SATFF();

  $data = array();
  $data['utime'] = time();
  $data['is_void'] = 1;
  $where = "action_id = '{$action_id}'";
  $sql = $db->sqlUpdate("task_action", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>
