<?php
//======================================
// 函数: 取得员工访问统计总数
// 参数: $staff_id      员工ID（可以省略）
// 参数: $action_url    访问URL（可以省略）
// 参数: $action_ip     访问IP（可以省略）
// 返回: 记录总数
//======================================
function get_cnt_staff_action_total($staff_id = '', $uuid = '', $action_url = '', $action_ip = 0)
{
  $db = new DB_SATFF();

  $sql = "SELECT COUNT(logid) AS log_total";
  $sql .= " FROM cnt_staff_action";
  $sql .= " WHERE action_ip > 0";
  if ($staff_id != '')
    $sql .= " AND staff_id = '{$staff_id}'";
  if ($action_url != '')
    $sql .= " AND action_url = '{$action_url}'";
  if ($action_ip != 0)
    $sql .= " AND action_ip = {$action_ip}";

  $total = $db->getField($sql, 'log_total');
  if ($total)
    return $total;
  return 0;
}

//======================================
// 函数: 取得员工访问统计列表
// 参数: $staff_id      员工ID（可以省略）
// 参数: $action_url    访问URL（可以省略）
// 参数: $action_ip     访问IP（可以省略）
// 参数: $limit         记录条数
// 参数: $offset        记录偏移量
// 返回: 记录列表
//======================================
function get_cnt_staff_action_list($staff_id = '', $uuid = '', $action_url = '', $action_ip = 0, $limit, $offset)
{
  $db = new DB_SATFF();

  $sql = "SELECT *";
  $sql .= " FROM cnt_staff_action";
  $sql .= " WHERE action_ip > 0";
  if ($staff_id != '')
    $sql .= " AND staff_id = '{$staff_id}'";
  if ($uuid != '')
    $sql .= " AND uuid = '{$uuid}'";
  if ($action_url != '')
    $sql .= " AND action_url = '{$action_url}'";
  if ($action_ip != 0)
    $sql .= " AND action_ip = {$action_ip}";
  $sql .= " ORDER BY logid DESC";
  $sql .= " limit {$offset},{$limit}";

  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 创建员工内部访问记录
// 参数: $data          信息数组
// 返回: id             新的记录ID
// 返回: 0              创建失败
//======================================
function ins_cnt_staff_action($data)
{
  // 提交时间
  $data['action_time'] = time();

  $db = new DB_SATFF();

  $sql = $db->sqlInsert("cnt_staff_action", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return 0;
  return $db->insertID();
}
?>
