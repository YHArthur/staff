<?php
//======================================
// 函数: 取得指定会计科目ID的会计科目记录
// 参数: $sub_id        会计科目ID
// 返回: 会计科目记录数组
//======================================
function get_sub_cn($sub_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM fin_sub_cn WHERE sub_id = '{$sub_id}'";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: 取得会计科目记录总数
// 参数: 无
// 返回: 记录总数
//======================================
function get_sub_cn_total()
{
  $db = new DB_SATFF();

  $sql = "SELECT COUNT(sub_id) AS log_total FROM fin_sub_cn";
  $sql .= " WHERE is_void = 0";
  
  $total = $db->getField($sql, 'log_total');
  if ($total)
    return $total;
  return 0;
}

//======================================
// 函数: 取得会计科目列表
// 参数: 无
// 返回: 会计科目列表
//======================================
function get_sub_cn_list()
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM fin_sub_cn";
  $sql .= " WHERE is_void = 0";
  $sql .= " ORDER BY sub_sort";

  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 创建会计科目信息
// 参数: $data          信息数组
// 返回: true           创建成功
// 返回: false          创建失败
//======================================
function ins_sub_cn($data)
{
  $db = new DB_SATFF();

  $sql = $db->sqlInsert("fin_sub_cn", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}

//======================================
// 函数: 更新会计科目信息
// 参数: $data          信息数组
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_sub_cn($data, $sub_id)
{
  $db = new DB_SATFF();

  $where = "sub_id = '{$sub_id}'";
  $sql = $db->sqlUpdate("fin_sub_cn", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}

//======================================
// 函数: 设定会计科目列表下拉框所需的数组
// 参数: $sub_rows      会计科目列表数组
// 返回: 若会计科目列表存在我的ID，则把名称换成'我'，并排列第一
//======================================
function get_sub_cn_list_select($sub_rows)
{
  $sub_type = array('', '资产', '负债', '共同', '权益', '成本', '损益');
  $sub_list = array();
  foreach ($sub_rows as $staff) {
    $sub_id = $staff['sub_id'];
    $sub_title = $staff['sub_title'];
    $type_pos = intval(substr($sub_id, 0, 1));
    $sub_list[$sub_id] = $sub_type[$type_pos] . '-' . $sub_title;
  }
  return $sub_list;
}
?>
