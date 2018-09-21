<?php
require_once "../inc/common.php";
require_once '../db/fin_cash_daily_log.php';

header("Access-Control-Allow-Origin: *");
header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 现金日记账设定 ==========================
GET参数
  log_id          日志ID
  is_pay          收支区分
  pay_date        发生日期
  debit_id        借方ID
  staff_name      员工姓名
  credit_name     贷方名称
  pay_channel     收支渠道
  amount          金额
  abstract        摘要
  file_url        附件URL地址

返回
  设定结果

说明
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('debit_id', 'staff_name', 'credit_name', 'amount', 'pay_date', 'abstract');
chk_empty_args('GET', $args);

// 提交参数整理
$log_id = get_arg_str('GET', 'log_id');                   // 日志ID
$is_pay = get_arg_str('GET', 'is_pay');                   // 收支区分
$pay_date = get_arg_str('GET', 'pay_date');               // 发生日期
$debit_id = get_arg_str('GET', 'debit_id');               // 借方ID
$staff_name = get_arg_str('GET', 'staff_name');           // 员工姓名
$credit_name = get_arg_str('GET', 'credit_name');         // 贷方名称
$pay_channel = get_arg_str('GET', 'pay_channel');         // 收支渠道
$amount = get_arg_str('GET', 'amount');                   // 金额
$abstract = get_arg_str('GET', 'abstract', 255);          // 摘要
$file_url = get_arg_str('GET', 'file_url', 255);          // 附件URL地址

// 提交信息整理
$log_id = intval($log_id);
$is_pay = intval($is_pay);
$pay_channel = intval($pay_channel);
$amount = intval($amount * 100);

$my_id = $_SESSION['staff_id'];
$my_name = $_SESSION['staff_name'];

// 员工工号姓名处理
$staff_cd = '000';
if ($staff_name != '请选择员工') {
  list($staff_cd, $staff_name) = explode(" ", $staff_name);
} else {
  $staff_name = '';
}
if ($my_id == $debit_id)
  $staff_name = $my_name;

$data = array();
$data['log_id'] = $log_id;                                // 日志ID
$data['pay_date'] = $pay_date;                            // 发生日期
$data['abstract'] = $abstract;                            // 摘要
$data['amount'] = $amount;                                // 金额
$data['pay_channel'] = $pay_channel;                      // 收支渠道
$data['is_pay'] = $is_pay;                                // 收支区分
$data['debit_id'] = $debit_id;                            // 借方ID

// 收入
if ($is_pay == 0) {
  $data['pay_name'] = $credit_name;                       // 付款方名称
  $data['rcpt_name'] = $staff_name;                       // 收款方名称
// 支出
} else {
  $data['pay_name'] = $staff_name;                        // 付款方名称
  $data['rcpt_name'] = $credit_name;                      // 收款方名称
}

$data['file_url'] = $file_url;                            // 附件URL
$data['record_id'] = $my_id;                              // 记录人ID
$data['record_name'] = $my_name;                          // 记录人

  
// 日志ID为0，表示创建
if ($log_id == '0') {
  // 现金日记账创建
  $ret = ins_fin_cash_daily_log($data);
  $msg = '【' . $abstract . '】现金日记账已成功添加';
  // 现金日记账信息创建失败
  if ($ret == '')
    exit_error('110', '现金日记账信息创建失败');
} else {
  // 现金日记账更新
  $ret = upd_fin_cash_daily_log($data, $log_id);
  $msg = '【' . $abstract . '】现金日记账已成功更新';
  // 现金日记账信息更新失败
  if (!$ret)
    exit_error('110', '现金日记账信息更新失败');
}

// 输出结果
exit_ok($msg);
?>
