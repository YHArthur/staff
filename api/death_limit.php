<?php
require_once '../inc/common.php';
require_once '../db/fin_bank_daily_log.php';
require_once '../db/fin_cycle_cost.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 公司死亡倒计时 ==========================
Get参数
  无

返回
  live_months      存活月数

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

// 返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['live_months'] = $live_months;

// 正常返回
$rtn_str = json_encode($rtn_ary);

// 输出内容
php_end($rtn_str);
?>
