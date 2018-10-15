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
// 函数: 取得现金日记账集计金额
// 参数: $staff_id     员工ID
// 参数: $is_pay       收支区分
// 返回: 取得现金日记账集计金额
//======================================
function get_cash_daily_amount_sum($is_pay, $staff_id = '')
{
  $db = new DB_SATFF();

  $sql = "SELECT SUM(amount) AS amount_sum";
  $sql .= " FROM fin_cash_daily_log";
  $sql .= " WHERE is_pay = {$is_pay}";
  if ($staff_id != '')
    $sql .= " AND debit_id = '{$staff_id}'";
  
  $amount_sum = $db->getField($sql, 'amount_sum');
  if ($amount_sum)
    return $amount_sum;
  return 0;
}

//======================================
// 函数: 取得员工列表和支付总额
// 参数: 无
// 返回: 记录数组
//======================================
function get_cash_daily_pay_amount_group()
{
  $db = new DB_SATFF();

  $sql = "SELECT debit_id AS staff_id, pay_name, SUM(amount) AS pay_amount";
  $sql .= " FROM fin_cash_daily_log";
  $sql .= " WHERE is_pay = 1";
  $sql .= " GROUP BY debit_id, pay_name";

  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
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