<?php
//======================================
// 函数: 取得系统所有权限
// 参数: 无
// 返回: 数组列表
//======================================
function get_permit_all()
{
  $db = new DB_WWW();

  $sql = "SELECT * FROM permit WHERE LEFT(pm_id, 1) = '" . Config::SYSTEM_ID . "' ORDER BY pm_id";
  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 取得权限列表
// 参数: 无
// 返回: 数组列表
//======================================
function get_permit_list()
{
  // 取得系统所有权限
  $rows = get_permit_all();
  
  $permit_list = array();
  foreach ($rows AS $row) {
    $pm_id = $row['pm_id'];
    $pm_name = '';
    if (substr($pm_id, 1) == '000') {
        $pm_name = '系统管理员';
    } else if (substr($pm_id, 3) == '0') {
        $pm_name = $row['pm_nm'];
    } else {
        $pm_name = '　' . $row['pm_nm'];
    }
    $permit_list[$pm_id] = $pm_name;
  }
  return $permit_list;
}
?>