<?php
//======================================
// 函数: 取得指定员工ID的员工记录
// 参数: $staff_id      员工ID
// 返回: 员工记录数组
//======================================
function get_staff($staff_id)
{
  $db = new DB_WWW();

  $sql = "SELECT * FROM staff_main WHERE staff_id = '{$staff_id}'";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: 创建员工信息
// 参数: $data          信息数组
// 返回: true           创建成功
// 返回: false          创建失败
//======================================
function ins_staff($data)
{
  $db = new DB_WWW();

  $sql = $db->sqlInsert("staff_main", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return 0;
  return $db->insertID();
}

//======================================
// 函数: 更新员工信息
// 参数: $data          信息数组
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_staff($data, $staff_id)
{
  $db = new DB_WWW();

  $where = "staffid = '{$staff_id}'";
  $sql = $db->sqlUpdate("staff_main", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}

//======================================
// 函数: get_star_sign_12($m, $d)
// 功能: 根据月，日取得12星座
// 参数: $m             月（2位数字，不足2位第一位补0）
// 参数: $d             日（2位数字，不足2位第一位补0）
// 返回: 对应的星座
// 返回: 错误的日月返回不明
//======================================
function get_star_sign_12($m, $d)
{
  $signs = array();
  $signs[] = array("01.20","02.18","水瓶座");
  $signs[] = array("02.19","03.20","双鱼座");
  $signs[] = array("03.21","04.19","白羊座");
  $signs[] = array("04.20","05.20","金牛座");
  $signs[] = array("05.21","06.21","双子座");
  $signs[] = array("06.22","07.22","巨蟹座");
  $signs[] = array("07.23","08.22","狮子座");
  $signs[] = array("08.23","09.22","处女座");
  $signs[] = array("09.23","10.23","天秤座");
  $signs[] = array("10.24","11.22","天蝎座");
  $signs[] = array("11.23","12.21","射手座");
  $signs[] = array("12.22","12.31","摩羯座");
  $signs[] = array("01.01","01.19","摩羯座");

  $md = "{$m}.{$d}";
  foreach ($signs as $sign) {
    if($md >= $sign[0] && $md <= $sign[1])
      return $sign[2];
  }

  return '不明';
}
?>