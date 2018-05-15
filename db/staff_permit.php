<?php
//======================================
// 函数: 取得指定员工ID的所有有效权限
// 参数: $staff_id      员工ID
// 返回: 记录数组
//======================================
function get_staff_permit_list($staff_id)
{
  $db = new DB_SATFF();
  $data_now = date('Y-m-d H:i:s');
  
  $sql = "SELECT pm_id FROM staff_permit";
  $sql .= " WHERE LEFT(pm_id, 1) = '" . Config::SYSTEM_ID . "'";
  $sql .= " AND staff_id = '{$staff_id}'";
  $sql .= " AND from_date <= '{$data_now}'";
  $sql .= " AND to_date >= '{$data_now}'";
  $sql .= " AND is_void = 0";
  $db->query($sql);
  $rows = $db->fetchAll();
  $permit_list = array();
  foreach ($rows AS $row) {
    $permit_list[] = $row['pm_id'];
  }
  return $permit_list;
}

//======================================
// 函数: 取得指定员工ID权限ID的员工权限记录
// 参数: $staff_id      员工ID
// 参数: $pm_id         权限ID
// 返回: 记录数组
//======================================
function get_staff_permit($staff_id, $pm_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM staff_permit WHERE staff_id = '{$staff_id}' AND pm_id = {$pm_id}";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: 创建员工权限信息
// 参数: $data          信息数组
// 返回: true           创建成功
// 返回: false          创建失败
//======================================
function ins_staff_permit($data)
{
  $db = new DB_SATFF();
  $data['utime'] = time();
  $data['ctime'] = date('Y-m-d H:i:s');
  
  $sql = $db->sqlInsert("staff_permit", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}

//======================================
// 函数: 更新员工权限信息
// 参数: $staff_id      员工ID
// 参数: $pm_id         权限ID
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_staff_permit($data, $staff_id, $pm_id)
{
  $db = new DB_SATFF();
  $data['utime'] = time();
  
  $where = "staff_id = '{$staff_id}' AND pm_id = {$pm_id}";
  $sql = $db->sqlUpdate("staff_permit", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>