<?php
//======================================
// 函数: 取得指定年月日的节假日标志记录
// 参数: $ymd           指定年月日
// 返回: 节假日标志记录
//======================================
function get_hr_date_tag($ymd)
{
  $db = new DB_SATFF();

  $sql = "SELECT * FROM hr_date_tag WHERE date_ymd = '{$ymd}'";
  $db->query($sql);
  $row = $db->fetchRow();
  return $row;
}

//======================================
// 函数: 取得指定日期类型人事标注的日期列表
// 参数: $from_ymd      开始年月日
// 参数: $to_ymd        结束年月日
// 参数: $date_type     日期类型 0 工作日 1 休日 2 国定假日 (默认0)
// 返回: 日期列表
//======================================
function get_hr_date_tag_by_type($from_ymd, $to_ymd, $date_type=0)
{
  $db = new DB_SATFF();

  $sql = "SELECT date_ymd, date_type, date_tag FROM hr_date_tag";
  $sql .= " WHERE date_ymd >= '{$from_ymd}'";
  $sql .= " AND date_ymd <= '{$to_ymd}'";
  $sql .= " AND date_type = {$date_type}";
  $sql .= " ORDER BY date_ymd";

  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 取得人事节假日标志列表
// 参数: $from_ymd      开始年月日
// 参数: $to_ymd        结束年月日
// 返回: 数组列表
//======================================
function get_hr_date_tag_list($from_ymd, $to_ymd)
{
  $db = new DB_SATFF();

  $sql = "SELECT date_ymd, date_type, date_tag FROM hr_date_tag";
  $sql .= " WHERE date_ymd >= '{$from_ymd}'";
  $sql .= " AND date_ymd <= '{$to_ymd}'";
  $sql .= " ORDER BY date_ymd";

  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 功能: 初始化年份存在检查
// 参数: $year          年
// 返回: true           存在
// 返回: false          不存在
//======================================
function chk_hr_date_tag_year_exist($year)
{
  $db = new DB_SATFF();

  $sql = "SELECT date_ymd FROM hr_date_tag WHERE LEFT(date_ymd, 4) = '{$year}'";
  $db->query($sql);
  $rds = $db->recordCount();
  if ($rds == 0)
    return false;
  return true;
}

//======================================
// 函数: 取得人事节假日记录总数
// 参数: $year          年
// 参数: $month         月
// 返回: 按date_type分别集计的记录总数
//======================================
function get_hr_date_type_total($year, $month='00')
{
  $db = new DB_SATFF();

  $sql = "SELECT date_type, COUNT(date_ymd) AS log_total FROM hr_date_tag";
  if ($month == '00') {
    $sql .= " WHERE LEFT(date_ymd, 4) = '{$year}'";
  } else {
    $sql .= " WHERE LEFT(date_ymd, 7) = '{$year}-{$month}'";
  }
  $sql .= " GROUP BY date_type";
  
  $db->query($sql);
  $rows = $db->fetchAll();
  return $rows;
}

//======================================
// 函数: 节假日标志创建
// 参数: $date_ymd      年月日
// 参数: $date_type     日期类型
// 参数: $date_tag      日期标注
// 返回: true           创建成功
// 返回: false          创建失败
//======================================
function ins_hr_date_tag($date_ymd, $date_type, $date_tag)
{
  $db = new DB_SATFF();
  $data = array();
  $data['date_ymd'] = $date_ymd;
  $data['date_type'] = $date_type;
  $data['date_tag'] = $date_tag;

  $sql = $db->sqlInsert("hr_date_tag", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}

//======================================
// 函数: 节假日标志更新
// 参数: $date_ymd      年月日
// 参数: $date_type     日期类型
// 参数: $date_tag      日期标注
// 返回: true           更新成功
// 返回: false          更新失败
//======================================
function upd_hr_date_tag($date_ymd, $date_type, $date_tag)
{
  $db = new DB_SATFF();

  $data['date_type'] = $date_type;
  $data['date_tag'] = $date_tag;
  $where = "date_ymd = '{$date_ymd}'";
  $sql = $db->sqlUpdate("hr_date_tag", $data, $where);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return false;
  return true;
}
?>