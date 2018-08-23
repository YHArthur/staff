<?php
require_once '../inc/common.php';
require_once '../db/fin_bank_daily_log.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 银行日记账导入处理 ==========================
GET参数
  filename       处理文件名

返回
  errcode = 0 请求成功

说明
*/

php_begin();

$args = array('filename');
chk_empty_args('GET', $args);

// 处理文件名
$filename = '../../upload/' . get_arg_str('GET', 'filename');

$file = fopen($filename,'r') or die("Unable to open file!");

// 检查文件字符编码
// $contents = file_get_contents($filename);
// $encoding = mb_detect_encoding($contents, array('GB2312','GBK','UTF-16','UCS-2','UTF-8','BIG5','ASCII'));
// var_dump($encoding);

// 第一行
$first_line = fgets($file);
$first_line = trim(iconv('CP936', 'UTF-8', $first_line));

$sample_line = '电子回单号码~付方账号~付方账户名称~付方开户银行名称~收方账号~收方账户名称~收方开户银行名称~金额~币种~摘要~用途~交易流水号~时间戳~备注~验证码~记账网点~记账柜员~记账日期~补打次数~业务（产品）种类';

if ($first_line != $sample_line)
  exit_error('-1', '文件格式不正确');

$success_count = 0;
$failure_count = 0;
$repeat_count = 0;
$data = array();

// 公司账号
$com_account = '1001181309300005323';

// 循环处理文件
while(!feof($file)) {
  $line = fgets($file);
  $line = trim(iconv('CP936', 'UTF-8', $line));
  $line_array = split('~', $line);

  // 空行不处理
  if ($line == '')
    continue;

  // 数组个数不正确
  if (count($line_array) != 20) {
    $failure_count++;
    continue;
  }

  // 解析一行
  list($elec_reply_no, $pay_account, $pay_account_name, $pay_account_bank, $rcpt_account, $rcpt_account_name, $rcpt_account_bank, $amount_str, $currency, $abstract, $target, $tx_serial_no, $bank_time_stamp, $memo, $bank_ver_code, $bank_branch_no, $bank_rec_staff_no, $bank_rec_date, $print_reply_time, $tx_type) = $line_array;

  // 电子回单号码存在检查
  if (exist_fin_bank_daily_log($elec_reply_no)) {
    $repeat_count++;
    continue;
  }

  // 金额切割
  list($amount_cn, $amount_yan)  = explode(',', $amount_str, 2);
  // 实际金额转成分
  $amount_yan = str_replace('元', '', $amount_yan);
  $amount_yan = str_replace('￥', '', $amount_yan);
  $amount_yan = str_replace(',', '', $amount_yan);
  $amount = $amount_yan * 100;
  
  // 银行时间戳转发生时间
  $log_datetime = substr($bank_time_stamp, 0, 10) . ' ' . str_replace('.', ':', substr($bank_time_stamp, 11, 8));

  // 是否支付
  $is_pay = 0;
  if ($com_account == $pay_account)
    $is_pay = 1;
  
  $data['elec_reply_no'] = $elec_reply_no;                  // 电子回单号码
  $data['pay_account'] = $pay_account;                      // 付款方账号
  $data['pay_account_name'] = $pay_account_name;            // 付款方账户名称
  $data['pay_account_bank'] = $pay_account_bank;            // 付款方开户银行名称
  $data['rcpt_account'] = $rcpt_account;                    // 收款方账号
  $data['rcpt_account_name'] = $rcpt_account_name;          // 收款方账户名称
  $data['rcpt_account_bank'] = $rcpt_account_bank;          // 收款方开户银行名称
  $data['amount_cn'] = $amount_str;                         // 中文金额
  $data['amount'] = $amount;                                // 数字金额(分)
  $data['currency'] = $currency;                            // 币种
  $data['abstract'] = $abstract;                            // 摘要
  $data['target'] = $target;                                // 用途
  $data['tx_serial_no'] = $tx_serial_no;                    // 交易流水号
  $data['bank_time_stamp'] = $bank_time_stamp;              // 银行时间戳
  $data['log_datetime'] = $log_datetime;                    // 发生时间
  $data['memo'] = $memo;                                    // 备注
  $data['bank_ver_code'] = $bank_ver_code;                  // 银行验证码
  $data['bank_branch_no'] = $bank_branch_no;                // 银行分支机构代码
  $data['bank_rec_staff_no'] = $bank_rec_staff_no;          // 银行记账员工编号
  $data['bank_rec_date'] = $bank_rec_date;                  // 银行记账日期
  $data['print_reply_time'] = $print_reply_time;            // 回单打印次数
  $data['tx_type'] = $tx_type;                              // 银行业务种类
  $data['is_pay'] = $is_pay;                                // 是否支付

  // 创建银行日记账
  $ret = ins_fin_bank_daily_log($data);
  // 行动信息创建失败
  if ($ret == '')
    exit_error('110', '银行日记账创建失败');
  // 新增件数增加
  $success_count++;
}

fclose($file);

$msg = '新增 ' . $success_count . ' 条数据';
// 有解析失败的情况
if ($failure_count > 0) {
  $msg .=  '，有 ' . $failure_count . ' 条数据无法处理';
}
// 有重复处理的情况
if ($failure_count > 0) {
  $msg .=  '，有 ' . $repeat_count . ' 条数据已经处理过了';
}

exit_ok($msg);
?>
