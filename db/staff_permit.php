<?php
//======================================
// 函数: 取得指定员工ID的员工权限记录
// 参数: $staff_id      员工ID
// 返回: 员工记录数组
//======================================
function get_staff($staff_id)
{
  $db = new DB_WWW();

  $sql = "SELECT * FROM staff_permit WHERE staff_id = '{$staff_id}'";
  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 创建员工权限信息
// 参数: $data          信息数组
// 返回: true           创建成功
// 返回: false          创建失败
//======================================
function ins_staff_permit($data)
{
  $db = new DB_WWW();

  $sql = $db->sqlInsert("staff_permit", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return 0;
  return $db->insertID();
}

//======================================
// 函数: 更新员工权限信息
// 参数: $data          信息数组
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_staff_permit($data, $log_id)
{
  $db = new DB_WWW();

  $where = "log_id = {$log_id}";
  $sql = $db->sqlUpdate("staff_permit", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>