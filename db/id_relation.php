<?php
//======================================
// 函数: 取得同一关系类型与主ID关联的有效副ID列表
// 参数: $rlt_type      关系类型
// 参数: $mid           主ID
// 返回: 数组列表
//======================================
function get_relation_ids($rlt_type, $mid)
{
  $db = new DB_SATFF();

  $sql = "SELECT sid FROM id_relation WHERE mid = '{$mid}' AND rlt_type = '{$rlt_type}' AND is_void = 0 ORDER BY id";
  $db->query($sql);
  $rows = $db->fetchAll();

  $relation_ids = array();
  foreach ($rows AS $row) {
    $relation_ids[] = $row['sid'];
  }

  return $relation_ids;
}

//======================================
// 函数: 取得同一关系类型与主ID关联的所有副ID列表（包括无效的）
// 参数: $rlt_type      关系类型
// 参数: $mid           主ID
// 返回: 数组列表
//======================================
function get_relation_ids_all($rlt_type, $mid)
{
  $db = new DB_SATFF();

  $sql = "SELECT sid FROM id_relation WHERE mid = '{$mid}' AND rlt_type = '{$rlt_type}' ORDER BY id";
  $db->query($sql);
  $rows = $db->fetchAll();

  $relation_ids = array();
  foreach ($rows AS $row) {
    $relation_ids[] = $row['sid'];
  }

  return $relation_ids;
}

//======================================
// 函数: 增加ID关系
// 参数: $rlt_type      关系类型
// 参数: $mid           主ID
// 参数: $sids          副ID数组（可能有重复）
// 返回: true           增加成功
// 返回: false          增加失败
//======================================
function add_relation_ids($rlt_type, $mid, $sids)
{
  $db = new DB_SATFF();

  // 取得主ID关联的副ID列表
  $old_sids = get_relation_ids($rlt_type, $mid);
  // 计算需要添加的副ID
  $new_sids = array_diff($old_sids, $sids);
  if (count($new_sids) > 0) {
    $ctime = date('Y-m-d H:i:s');
    $add_sql = array();
    foreach ($new_sids AS $sid) {
      $add_sql[] = "('{$rlt_type}', '{$mid}', '{$sid}', '{$ctime}'),";
    }
    $sql = 'INSERT INTO id_relation(rlt_type, mid, sid, ctime) VALUES';
    $sql .= join(",", $add_sql);
    $q_id = $db->query($sql);
    if ($q_id == 0)
      return false;
  }

  // 计算需要恢复的副ID
  $upd_sids = array_diff($new_sids, $sids);
  if (count($upd_sids) > 0) {
    foreach ($upd_sids AS $sid) {
      $upd_sql[] = "'{$sid}'";
    }
    $sql = "UPDATE id_relation SET is_void = 0 WHERE rlt_type = '{$rlt_type}' AND mid = '{$mid}' AND sid = IN (";
    $sql .= join(",", $upd_sql) . ")";
    $q_id = $db->query($sql);
    if ($q_id == 0)
      return false;
  }
  return true;
}

//======================================
// 函数: 取消ID关系
// 参数: $rlt_type      关系类型
// 参数: $mid           主ID
// 参数: $sids          副ID数组（可能有重复）
// 返回: true           取消成功
// 返回: false          取消失败
//======================================
function del_relation_ids($rlt_type, $mid, $sids)
{
  $db = new DB_SATFF();

  $void_time = date('Y-m-d H:i:s');
  foreach ($sids AS $sid) {
    $upd_sql[] = "'{$sid}'";
  }
  $sql = "UPDATE id_relation SET is_void = 1, void_time = '{$void_time}'";
  $sql .= " WHERE rlt_type = '{$rlt_type}' AND mid = '{$mid}' AND sid = IN (";
  $sql .= join(",", $upd_sql) . ")";
  $q_id = $db->query($sql);
  if ($q_id == 0)
    return false;
  return true;
}

?>