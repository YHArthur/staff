<?php
//======================================
// 函数: 取得交通补助金额
// 参数: $time_begin    出勤开始时间Y-m-d H:i:s
// 参数: $time_end      出勤结束时间Y-m-d H:i:s
// 返回: 交通补助金额
//======================================
function get_commute_subsidy($time_begin, $time_end)
{
  // 交通补助条件，当日出勤时间超过4小时
  $subsidy_condition = 60*60*4;
  // 非同日数据
  if (substr($time_begin, 0, 10) != substr($time_end, 0, 10))
    return 0;
  // 计算员工出勤时间
  $time_diff = strtotime($time_end) - strtotime($time_begin);
  // 当日出勤时间未达成交通补助条件
  if ($time_diff < $subsidy_condition)
    return 0;
  return 10;
}

//======================================
// 函数: 取得午餐补助金额
// 参数: $time_begin    出勤开始时间Y-m-d H:i:s
// 参数: $time_end      出勤结束时间Y-m-d H:i:s
// 返回: 午餐补助金额
//======================================
function get_lunch_subsidy($time_begin, $time_end)
{
  // 午餐补助开始时间要求
  $lunch_time_begin = '10:30:00';
  // 午餐补助结束时间要求
  $lunch_time_end = '18:00:00';
  // 非同日数据
  if (substr($time_begin, 0, 10) != substr($time_end, 0, 10))
    return 0;
  // 当日出勤时间未达成午餐补助条件
  if (substr($time_begin, 11) > $lunch_time_begin || substr($time_end, 11) < $lunch_time_end)
    return 0;
  return 15;
}

//======================================
// 函数: 取得晚餐补助金额
// 参数: $time_begin    出勤开始时间Y-m-d H:i:s
// 参数: $time_end      出勤结束时间Y-m-d H:i:s
// 返回: 晚餐补助金额
//======================================
function get_dinner_subsidy($time_begin, $time_end)
{
  // 晚餐补助开始时间要求
  $dinner_time_begin = '12:00:00';
  // 晚餐补助结束时间要求
  $dinner_time_end = '20:30:00';
  // 非同日数据
  if (substr($time_begin, 0, 10) != substr($time_end, 0, 10))
    return 0;
  // 当日出勤时间未达成晚餐补助条件
  if (substr($time_begin, 11) > $dinner_time_begin || substr($time_end, 11) < $dinner_time_end)
    return 0;
  return 15;
}

?>