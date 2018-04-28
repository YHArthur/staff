<?php
//======================================
// 函数: 取得指定经费ID的经费记录
// 参数: $exp_id        经费ID
// 返回: 经费记录数组
//======================================
function get_staff_expense($exp_id)
{
  $db = new DB_WWW();

  $sql = "SELECT * FROM staff_expense WHERE exp_id = '{$exp_id}'";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: chk_exp_id_exist($exp_id)
// 功能: 经费ID存在检查
// 参数: $exp_id        经费ID
// 返回: true           经费ID存在
// 返回: false          经费ID不存在
//======================================
function chk_exp_id_exist($exp_id)
{
  $db = new DB_WWW();

  $sql = "SELECT exp_id FROM staff_expense WHERE exp_id = '{$exp_id}'";
  $db->query($sql);
  $rds = $db->recordCount();
  if ($rds == 0)
    return false;
  return true;
}

//======================================
// 函数: 取得经费总数
// 参数: 无
// 返回: 经费总数
//======================================
function get_staff_expense_total()
{
  $db = new DB_WWW();
  $time_now = date('Y-m-d H:i:s');

  $sql = "SELECT COUNT(exp_id) AS id_total FROM staff_expense WHERE is_public = 1 AND is_void = 0";
  $total = $db->getField($sql, 'id_total');
  if ($total)
    return $total;
  return 0;
}

//======================================
// 函数: 取得经费列表
// 参数: 无
// 参数: $limit         记录条数
// 参数: $offset        记录偏移量
// 返回: 经费列表明细
//======================================
function get_staff_expense_list($limit, $offset)
{
  $db = new DB_WWW();
  $time_now = date('Y-m-d H:i:s');

  $sql = "SELECT * FROM staff_expense WHERE is_public = 1 AND is_void = 0";
  $sql .= " ORDER BY staff_expense_status DESC, ctime";
  $sql .= " limit {$offset},{$limit}";

  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 办公经费创建
// 参数: $data          信息数组
// 返回: exp_id         创建成功的经费ID
// 返回: ''             办公经费创建失败
//======================================
function ins_staff_expense($data)
{
  $db = new DB_WWW();
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
  $db = new DB_WWW();

  $data['utime'] = time();
  $where = "exp_id = '{$exp_id}'";
  $sql = $db->sqlUpdate("staff_expense", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>