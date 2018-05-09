<?php
//======================================
// 函数: 取得指定目标ID的目标记录
// 参数: $obj_id        目标ID
// 返回: 目标记录数组
//======================================
function get_obj($obj_id)
{
  $db = new DB_WWW();

  $sql = "SELECT * FROM obj WHERE obj_id = '{$obj_id}'";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: chk_obj_id_exist($obj_id)
// 功能: 目标ID存在检查
// 参数: $obj_id        目标ID
// 返回: true           目标ID存在
// 返回: false          目标ID不存在
//======================================
function chk_obj_id_exist($obj_id)
{
  $db = new DB_WWW();

  $sql = "SELECT obj_id FROM obj WHERE obj_id = '{$obj_id}'";
  $db->query($sql);
  $rds = $db->recordCount();
  if ($rds == 0)
    return false;
  return true;
}

//======================================
// 函数: 取得目标总数
// 参数: 无
// 返回: 目标总数
//======================================
function get_obj_total()
{
  $db = new DB_WWW();
  $time_now = date('Y-m-d H:i:s');

  $sql = "SELECT COUNT(obj_id) AS id_total FROM obj WHERE is_public = 1 AND is_void = 0";
  $total = $db->getField($sql, 'id_total');
  if ($total)
    return $total;
  return 0;
}

//======================================
// 函数: 取得目标列表
// 参数: 无
// 参数: $limit         记录条数
// 参数: $offset        记录偏移量
// 返回: 目标列表明细
//======================================
function get_obj_list($limit, $offset)
{
  $db = new DB_WWW();
  $time_now = date('Y-m-d H:i:s');

  $sql = "SELECT * FROM obj WHERE is_public = 1 AND is_void = 0";
  $sql .= " ORDER BY obj_status DESC, ctime";
  $sql .= " limit {$offset},{$limit}";

  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 目标创建
// 参数: $data          信息数组
// 返回: obj_id         创建成功的目标ID
// 返回: ''             目标创建失败
//======================================
function ins_obj($data)
{
  $db = new DB_WWW();
  $data['is_void'] = 0;
  $data['utime'] = time();
  $data['ctime'] = date('Y-m-d H:i:s');

  $sql = $db->sqlInsert("obj", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return '';
  return $data['obj_id'];
}

//======================================
// 函数: 目标更新
// 参数: $data          更新数组
// 参数: $obj_id        目标ID
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_obj($data, $obj_id)
{
  $db = new DB_WWW();

  $data['utime'] = time();
  $where = "obj_id = '{$obj_id}'";
  $sql = $db->sqlUpdate("obj", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>