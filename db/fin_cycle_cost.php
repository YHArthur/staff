<?php
//======================================
// 函数: 取得指定支出费用ID的记录
// 参数: $cost_id       支出费用ID
// 返回: 记录数组
//======================================
function get_fin_cycle_cost($cost_id)
{
  $db = new DB_SATFF();
  $sql = "SELECT * FROM fin_cycle_cost WHERE cost_id = '{$cost_id}'";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: 周期支出费用创建
// 参数: $data          信息数组
// 返回: cost_id        创建成功的支出费用ID
// 返回: ''             创建失败
//======================================
function ins_fin_cycle_cost($data)
{
  $db = new DB_SATFF();
  $data['utime'] = time();
  $data['ctime'] = date('Y-m-d H:i:s');

  $sql = $db->sqlInsert("fin_cycle_cost", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return '';
  return $data['cost_id'];
}

//======================================
// 函数: 周期支出费用更新
// 参数: $data          更新数组
// 参数: $cost_id       支出费用ID
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_fin_cycle_cost($data, $cost_id)
{
  $db = new DB_SATFF();
  $data['utime'] = time();
  $where = "cost_id = '{$cost_id}'";
  $sql = $db->sqlUpdate("fin_cycle_cost", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>