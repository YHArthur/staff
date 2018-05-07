<?php
//======================================
// 功能: 员工经费变动记录存在检查
// 参数: $exp_id        变动ID
// 参数: $exp_stamp     变动时间
// 返回: true           存在
// 返回: false          不存在
//======================================
function chk_staff_expense_log_exist($exp_id, $exp_stamp)
{
  $db = new DB_WWW();

  $sql = "SELECT hash_id FROM staff_expense_log WHERE exp_id = '{$exp_id}' AND exp_stamp = {$exp_stamp}";
  $db->query($sql);
  $rds = $db->recordCount();
  if ($rds == 0)
    return false;
  return true;
}

//======================================
// 函数: 取得指定员工最后一次经费变动记录
// 参数: $staff_id      员工ID
// 返回: 经费变动记录数组
//======================================
function get_staff_last_expense_log($staff_id)
{
  $db = new DB_WWW();

  $sql = "SELECT * FROM staff_expense_log";
  $sql .= " WHERE staff_id = '{$staff_id}'";
  $sql .= " ORDER BY exp_stamp DESC";
  $sql .= " limit 1";  
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: 取得指定员工经费变动记录总数
// 参数: $staff_id      员工ID
// 返回: 记录总数
//======================================
function get_staff_expense_log_total($staff_id)
{
  $db = new DB_WWW();

  $sql = "SELECT COUNT(hash_id) AS id_total FROM staff_expense_log WHERE staff_id = '{$staff_id}'";
  $total = $db->getField($sql, 'id_total');
  if ($total)
    return $total;
  return 0;
}

//======================================
// 函数: 取得指定员工经费变动记录列表
// 参数: $staff_id      员工ID
// 参数: $limit         记录条数
// 参数: $offset        记录偏移量
// 返回: 列表明细
//======================================
function get_staff_expense_log_list($staff_id, $limit, $offset)
{
  $db = new DB_WWW();

  $sql = "SELECT * FROM staff_expense_log";
  $sql .= " WHERE staff_id = '{$staff_id}'";
  $sql .= " ORDER BY exp_stamp DESC";
  $sql .= " limit {$offset},{$limit}";

  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 办公经费创建
// 参数: $data          信息数组
// 返回: hash_id        创建成功的经费变动HASH值
// 返回: ''             创建失败
//======================================
function ins_staff_expense_log($data)
{
  $db = new DB_WWW();
  $hash_str = $data['prvs_hash'] . $data['staff_id'] . $data['exp_id'] . $data['exp_amount'] . $data['exp_stamp'];
  $data['hash_id'] = hash('sha256', $hash_str);
  $sql = $db->sqlInsert("staff_expense_log", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return '';
  return $data['hash_id'];
}
?>