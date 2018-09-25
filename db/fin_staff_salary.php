<?php
//======================================
// 函数: 取得指定员工ID的员工工资基数
// 参数: $staff_id      员工ID
// 返回: 员工工资基数
//======================================
function get_fin_staff_salary($staff_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM fin_staff_salary WHERE staff_id = '{$staff_id}'";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: 取得所有有效员工工资基数
// 参数: 无
// 返回: 员工工资基数数据集
//======================================
function get_all_fin_staff_salary()
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM fin_staff_salary";
  $sql .= " WHERE is_void = 0";
  $sql .= " ORDER BY staff_cd";

  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 员工工资基数创建
// 参数: $data          信息数组
// 返回: staff_id       员工ID
// 返回: true           创建成功
// 返回: false          创建失败
//======================================
function ins_fin_staff_salary($data)
{
  $db = new DB_SATFF();
  $data['is_void'] = 0;
  $data['utime'] = time();
  $data['ctime'] = date('Y-m-d H:i:s');

  $sql = $db->sqlInsert("fin_staff_salary", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}

//======================================
// 函数: 员工工资基数更新
// 参数: $data          更新数组
// 参数: $staff_id      员工ID
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_fin_staff_salary($data, $staff_id)
{
  $db = new DB_SATFF();

  $data['utime'] = time();
  $where = "staff_id = '{$staff_id}'";
  $sql = $db->sqlUpdate("fin_staff_salary", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>