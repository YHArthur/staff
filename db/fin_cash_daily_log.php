<?php
//======================================
// 函数: 取得指定日志ID的记录
// 参数: $log_id       日志ID
// 返回: 记录数组
//======================================
function get_fin_cash_daily_log($log_id)
{
  $db = new DB_SATFF();
  $sql = "SELECT * FROM fin_cash_daily_log WHERE log_id = {$log_id}";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: 取得现金日记账余额
// 参数: 无
// 返回: 取得现金日记账余额
//======================================
function get_fin_cash_amount_balance()
{
  $db = new DB_SATFF();

  $sql = "SELECT SUM(amount) AS sum_amount";
  $sql .= " FROM fin_cash_daily_log";
  $sql .= " WHERE is_pay = 0";
  $income_amount = $db->getField($sql, 'sum_amount');

  $sql = "SELECT SUM(amount) AS sum_amount";
  $sql .= " FROM fin_cash_daily_log";
  $sql .= " WHERE is_pay = 1";
  $pay_amount = $db->getField($sql, 'sum_amount');
  
  return $income_amount - $pay_amount;
}

//======================================
// 函数: 现金日记账创建
// 参数: $data          信息数组
// 返回: true           创建成功
// 返回: false          创建失败
//======================================
function ins_fin_cash_daily_log($data)
{
  $db = new DB_SATFF();
  $data['utime'] = time();
  $data['ctime'] = date('Y-m-d H:i:s');

  $sql = $db->sqlInsert("fin_cash_daily_log", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}

//======================================
// 函数: 现金日记账更新
// 参数: $data          更新数组
// 参数: $log_id        日志ID
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_fin_cash_daily_log($data, $log_id)
{
  $db = new DB_SATFF();
  $data['utime'] = time();
  $where = "log_id = '{$log_id}'";
  $sql = $db->sqlUpdate("fin_cash_daily_log", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>