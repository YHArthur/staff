<?php
require_once '../inc/common.php';
require_once '../db/fin_bank_daily_log.php';
require_once '../db/fin_cycle_cost.php';
require_once '../db/action.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 公司死亡倒计时 ==========================
Get参数
  无

返回
  live_months             存活月数
  day_closed_actions      昨日完成行动数
  week_closed_actions     本周完成行动数

说明
*/

php_begin();

// 当前时间戳
$now_time = time();

// 取得公司银行余额
$rest_amount = get_fin_bank_rest_amount();

// 取得公司每月支付金额
$month_sum_amount = get_fin_month_sum_amount();

// 计算剩余时间
$live_months = round(($rest_amount / $month_sum_amount), 1);

// 昨日开始时间戳
$yester_day_begin = strtotime('-1 day', strtotime(date('Y-m-d')));
// 昨日结束时间戳
$yester_day_end = $yester_day_begin + 24*60*60 - 1;
// 周一时间戳
$week_monday_begin = strtotime('Sunday -6 day', strtotime(date('Y-m-d')));
// 周末时间戳
$week_sunday_end = $week_monday_begin + 7*24*60*60 - 1;

// 昨日完成行动数
$day_closed_actions = get_open_closed_action_total('', date('Y-m-d H:i:s', $yester_day_begin), date('Y-m-d H:i:s', $yester_day_end));
// 本周完成行动数
$week_closed_actions = get_open_closed_action_total('', date('Y-m-d H:i:s', $week_monday_begin), date('Y-m-d H:i:s', $week_sunday_end));

// 返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['live_months'] = $live_months;
$rtn_ary['day_closed_actions'] = $day_closed_actions;
$rtn_ary['week_closed_actions'] = $week_closed_actions;

// 正常返回
$rtn_str = json_encode($rtn_ary);

// 输出内容
php_end($rtn_str);
?>
