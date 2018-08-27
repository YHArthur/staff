<?php
//======================================
// 功能: 取得指定电子回单号码的银行日记账记录
// 参数: $elec_reply_no       电子回单号码
// 返回: 银行日记账记录数组
// 说明:
//======================================
function get_fin_bank_daily_log($elec_reply_no)
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM fin_bank_daily_log WHERE elec_reply_no = '{$elec_reply_no}'";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 功能: 取得取得公司银行余额
// 参数: 无
// 返回: 银行余额
// 说明:
//======================================
function get_fin_bank_rest_amount()
{
  $db = new DB_SATFF();
  
  // 计算收入总额
  $sql = "SELECT SUM(amount) AS in_amount FROM fin_bank_daily_log WHERE is_pay = 0";
  $in_amount = $db->getField($sql, 'in_amount');
  // 计算支出总额
  $sql = "SELECT SUM(amount) AS out_amount FROM fin_bank_daily_log WHERE is_pay = 1";
  $out_amount = $db->getField($sql, 'out_amount');
  // 计算余额
  return $in_amount - $out_amount;
}

//======================================
// 功能: 设定银行日记账的科目(待办)
// 参数: $staff_id      员工ID
// 参数: $sub_id        科目ID
// 返回: true           设定成功
// 返回: false          设定失败
//======================================
function set_fin_bank_daily_log($staff_id, $sub_id)
{
  $db = new DB_SATFF();

  $sql = "UPDATE fin_bank_daily_log SET is_void = 0, utime = " . time() . " WHERE staff_id = '{$staff_id}' AND is_void = 1";
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}

//======================================
// 函数: 电子回单号码存在检查
// 参数: $elec_reply_no 电子回单号码
// 返回: true           存在
// 返回: false          不存在
//======================================
function exist_fin_bank_daily_log($elec_reply_no)
{
  $db = new DB_SATFF();

  $sql = "SELECT elec_reply_no FROM fin_bank_daily_log WHERE elec_reply_no = '{$elec_reply_no}'";
  $db->query($sql);
  $rds = $db->recordCount();
  if ($rds == 0)
    return false;
  return true;
}

//======================================
// 函数: 创建银行日记账
// 参数: $data          信息数组
// 返回: true           创建成功
// 返回: false          创建失败
//======================================
function ins_fin_bank_daily_log($data)
{
  // 更新时间戳
  $data['utime'] = time();
  // 创建时间
  $data['ctime'] = date('Y-m-d H:i:s');

  $db = new DB_SATFF();

  $sql = $db->sqlInsert("fin_bank_daily_log", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>