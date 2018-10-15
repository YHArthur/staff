<?php
//======================================
// 函数: 取得指定经费ID的经费记录
// 参数: $exp_id        经费ID
// 返回: 经费记录数组
//======================================
function get_staff_expense($exp_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM staff_expense WHERE exp_id = '{$exp_id}'";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: 取得待处理的所有员工办公经费记录
// 参数: 无
// 返回: 经费记录集
//======================================
function get_pending_staff_expense()
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM staff_expense";
  $sql .= " WHERE is_void = 0 AND now_count < max_count";
  $sql .= " ORDER BY from_date, exp_amount DESC";

  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 取得指定员工待处理的办公经费总额
// 参数: $staff_id      员工ID
// 返回: 待处理的办公经费总额
//======================================
function get_pending_staff_expense_amount_sum($staff_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT SUM(exp_amount * (max_count - now_count)) AS amount_sum";
  $sql .= " FROM staff_expense";
  $sql .= " WHERE staff_id = '{$staff_id}'";
  $sql .= " AND exp_amount < 0";
  $sql .= " AND is_void = 0";
  $sql .= " AND now_count < max_count";

  $amount_sum = $db->getField($sql, 'amount_sum');
  if ($amount_sum)
    return $amount_sum;
  return 0;
}

//======================================
// 函数: 办公经费创建
// 参数: $data          信息数组
// 返回: exp_id         创建成功的经费ID
// 返回: ''             办公经费创建失败
//======================================
function ins_staff_expense($data)
{
  $db = new DB_SATFF();
  $data['utime'] = time();
  $data['ctime'] = date('Y-m-d H:i:s');

  $sql = $db->sqlInsert("staff_expense", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return '';
  return $data['exp_id'];
}

//======================================
// 函数: 办公经费更新
// 参数: $data          更新数组
// 参数: $exp_id        经费ID
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_staff_expense($data, $exp_id)
{
  $db = new DB_SATFF();

  $data['utime'] = time();
  $where = "exp_id = '{$exp_id}'";
  $sql = $db->sqlUpdate("staff_expense", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>