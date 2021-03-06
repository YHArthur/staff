<?php
//======================================
// 函数: 取得指定员工ID和开始年月的员工工资基数
// 参数: $staff_id      员工ID
// 参数: $from_month    开始年月
// 返回: 员工工资基数
//======================================
function get_fin_staff_salary($staff_id, $from_month)
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM fin_staff_salary";
  $sql .= " WHERE staff_id = '{$staff_id}'";
  $sql .= " AND from_month = '{$from_month}'";
  
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: 取得指定员工ID和有效年月的员工工资基数
// 参数: $staff_id      员工ID
// 参数: $valid_month   有效年月
// 返回: 员工工资基数
//======================================
function get_fin_staff_salary_valid($staff_id, $valid_month)
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM fin_staff_salary";
  $sql .= " WHERE staff_id = '{$staff_id}'";
  $sql .= " AND from_month <= '{$valid_month}'";
  $sql .= " AND to_month >= '{$valid_month}'";
  $sql .= " ORDER BY utime DESC LIMIT 1";
  
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: 取得指定ID列表的当月员工工资基数
// 参数: $staff_ids     员工ID数组
// 参数: $year_month    指定年月
// 返回: 员工工资基数数据集
//======================================
function get_fin_staff_salary_list($staff_ids, $year_month)
{
  $db = new DB_SATFF();
  // 员工ID数组处理
  $ids = array();
  foreach ($staff_ids AS $id) {
    $ids[] = "'{$id}'";
  }
  $staff_list = join(",", $ids);
  
  $sql = "SELECT * FROM fin_staff_salary";
  $sql .= " WHERE staff_id in ({$staff_list})";
  $sql .= " AND from_month <= '{$year_month}'";
  $sql .= " AND to_month >= '{$year_month}'";
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
// 参数: $from_month    开始月
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_fin_staff_salary($data, $staff_id, $from_month)
{
  $db = new DB_SATFF();

  $data['utime'] = time();
  $where = "staff_id = '{$staff_id}'";
  $where .= " AND from_month = '{$from_month}'";
  $sql = $db->sqlUpdate("fin_staff_salary", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>