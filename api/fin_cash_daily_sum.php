<?php
require_once "../inc/common.php";
require_once '../db/fin_cash_daily_log.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 现金日记账个人余额展示 ==========================
GET参数

返回
  rec_amount_sum  总收到金额
  pay_amount_sum  总支付金额
  rest_amount_sum 总现金余额
  total             总记录件数  
  rows              记录数组
    staff_id          员工ID
    staff_name        员工姓名
    rec_amount        收到金额
    pay_amount        支付金额
    rest_amount       现金余额

说明
*/

// 禁止游客访问
exit_guest();

// 取得总收到金额
$rec_amount_sum = get_cash_daily_amount_sum(0, '');

// 取得总支付金额
$pay_amount_sum = get_cash_daily_amount_sum(1, '');

// 计算总现金余额
$rest_amount_sum = $rec_amount_sum - $pay_amount_sum;

// 取得员工列表和支付总额
$rows = get_cash_daily_pay_amount_group();

$rtn_rows = array();
foreach($rows as $row) {
  $staff_id = $row['staff_id'];
  $staff_pay_amount = $row['pay_amount'] / 100.0;
  // 取得员工总收到金额
  $staff_rec_amount = get_cash_daily_amount_sum(0, $staff_id) / 100.0;
  $row['pay_amount'] = $staff_pay_amount;
  $row['rec_amount'] = $staff_rec_amount;
  // 计算
  $row['rest_amount'] = $staff_rec_amount - $staff_pay_amount;
  $rtn_rows[] = $row;
}

//返回数据做成
$rtn_ary = array();
$rtn_ary['errcode'] = '0';
$rtn_ary['errmsg'] = '';
$rtn_ary['rec_amount_sum'] = $rec_amount_sum / 100.0;
$rtn_ary['pay_amount_sum'] = $pay_amount_sum / 100.0;
$rtn_ary['rest_amount_sum'] = $rest_amount_sum / 100.0;
$rtn_ary['total'] = count($rtn_rows);
$rtn_ary['rows'] = $rtn_rows;
$rtn_str = json_encode($rtn_ary);

php_end($rtn_str);
?>
