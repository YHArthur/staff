<?php
//======================================
// 函数: 取得员工办公室签到记录总数
// 参数: $staff_id      员工ID（''表示全体）
// 返回: 记录总数
//======================================
function get_staff_office_sign_total($staff_id = '')
{
  $db = new DB_SATFF();

  $sql = "SELECT COUNT(log_id) AS log_total FROM staff_office_sign";
  $sql .= " WHERE is_void = 0";
  if ($staff_id != '')
    $sql .= " AND staff_id = '{$staff_id}'";
  
  $total = $db->getField($sql, 'log_total');
  if ($total)
    return $total;
  return 0;
}

//======================================
// 函数: 取得员工办公室签到记录列表
// 参数: $limit         取得记录条数
// 参数: $offset        取得记录偏移量
// 参数: $staff_id      员工ID（''表示全体）
// 返回: 记录列表
//======================================
function get_staff_office_sign_list($limit, $offset, $staff_id = '')
{
  $db = new DB_SATFF();

  $sql = " SELECT * FROM staff_office_sign";
  $sql .= " WHERE is_void = 0";
  if ($staff_id != '')
    $sql .= " AND staff_id = '{$staff_id}'";
  $sql .= " ORDER BY ctime DESC";
  $sql .= " limit {$offset},{$limit}";
  
  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 取得员工指定时间开始的办公室签到记录列表
// 参数: $staff_id      员工ID
// 参数: $from_time     开始时间戳
// 返回: 记录列表
//======================================
function get_staff_office_sign_from_time_list($staff_id, $from_time)
{
  $db = new DB_SATFF();

  $sql = " SELECT * FROM staff_office_sign";
  $sql .= " WHERE is_void = 0";
  $sql .= " AND staff_id = '{$staff_id}'";
  $sql .= " AND utime >= {$from_time}";
  $sql .= " ORDER BY utime";
  
  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 创建员工办公室签到记录
// 参数: $data          信息数组
// 返回: true           创建成功
// 返回: false          创建失败
//======================================
function ins_staff_office_sign($data)
{
  // 更新时间戳
  $data['utime'] = time();
  // 创建时间
  $data['ctime'] = date('Y-m-d H:i:s');
  $db = new DB_SATFF();

  $sql = $db->sqlInsert("staff_office_sign", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return 0;
  return $db->insertID();
}
?>