<?php
//======================================
// 功能: 取得指定微信统一标识的员工微信账号记录
// 参数: $unionid       微信统一标识
// 返回: 员工微信账号记录数组
// 说明:
//======================================
function get_staff_weixin($unionid)
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM staff_weixin WHERE unionid = '{$unionid}'";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 功能: 取得注册后待申请的员工微信账号记录
// 参数: $staff_id      员工ID
// 返回: 员工微信账号记录数组
// 说明:
//======================================
function get_staff_weixin_sign($staff_id)
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM staff_weixin WHERE staff_id = '{$staff_id}' AND is_void = 1";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 功能: 审核通过待申请的员工微信账号记录
// 参数: $staff_id      员工ID
// 返回: true           操作成功
// 返回: false          操作成功
//======================================
function confim_staff_weixin($staff_id)
{
  $db = new DB_SATFF();

  $sql = "UPDATE staff_weixin SET is_void = 0, utime = " . time() . " WHERE staff_id = '{$staff_id}' AND is_void = 1";
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}

//======================================
// 函数: 微信统一标识存在检查
// 参数: $unionid       微信统一标识
// 返回: true           存在
// 返回: false          不存在
//======================================
function exist_staff_weixin($unionid)
{
  $db = new DB_SATFF();

  $sql = "SELECT unionid FROM staff_weixin WHERE unionid = '{$unionid}'";
  $db->query($sql);
  $rds = $db->recordCount();
  if ($rds == 0)
    return false;
  return true;
}

//======================================
// 函数: 创建员工微信账号
// 参数: $data          信息数组
// 返回: true           创建成功
// 返回: false          创建失败
//======================================
function ins_staff_weixin($data)
{
  // 更新时间戳
  $data['utime'] = time();
  // 创建时间
  $data['ctime'] = date('Y-m-d H:i:s');

  $db = new DB_SATFF();

  $sql = $db->sqlInsert("staff_weixin", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}

//======================================
// 功能: 更新员工微信账号信息
// 参数: $data          信息数组
// 参数: $unionid       微信统一标识
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_staff_weixin($data, $unionid)
{
  // 更新时间
  $data['utime'] = time();

  $db = new DB_SATFF();

  $where = "unionid = '{$unionid}'";
  $sql = $db->sqlUpdate("staff_weixin", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>