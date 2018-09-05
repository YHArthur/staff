<?php
//======================================
// 函数: 取得指定工资年月和员工ID的员工工资发放记录
// 参数: $salary_ym     工资年月
// 参数: $staff_id      员工ID
// 返回: 员工工资基数
//======================================
function get_fin_staff_salary_log($salary_ym, $staff_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM fin_staff_salary_log WHERE salary_ym = '{$salary_ym}' AND staff_id = '{$staff_id}'";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: 员工工资发放记录存在检查
// 参数: $salary_ym     工资年月
// 参数: $staff_id      员工ID
// 返回: true           存在
// 返回: false          不存在
//======================================
function chk_fin_staff_salary_log_exist($salary_ym, $staff_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT staff_id FROM fin_staff_salary_log WHERE  salary_ym = '{$salary_ym}' AND staff_id = '{$staff_id}'";
  $db->query($sql);
  $rds = $db->recordCount();
  if ($rds == 0)
    return false;
  return true;
}

//======================================
// 函数: 员工工资发放记录创建
// 参数: $data          信息数组
// 返回: true           创建成功
// 返回: false          创建失败
//======================================
function ins_fin_staff_salary_log($data)
{
  $db = new DB_SATFF();
  $data['utime'] = time();
  $data['ctime'] = date('Y-m-d H:i:s');

  $sql = $db->sqlInsert("fin_staff_salary_log", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}

//======================================
// 函数:员工工资发放记录更新
// 参数: $data          更新数组
// 参数: $salary_ym     工资年月
// 参数: $staff_id      员工ID
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_fin_staff_salary_log($data, $salary_ym, $staff_id)
{
  $db = new DB_SATFF();

  $data['utime'] = time();
  $where = "salary_ym = '{$salary_ym}'";
  $where .= " AND staff_id = '{$staff_id}'";
  $sql = $db->sqlUpdate("fin_staff_salary_log", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>