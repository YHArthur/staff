<?php
require_once '../inc/common.php';
require_once '../inc/send_email.php';
require_once '../../php/db/www_email.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 群发邮件 ==========================
GET参数
  title          邮件标题
  body           邮件内容

返回
  errcode = 0 请求成功

说明
*/

php_begin();

$args = array('title', 'body');
chk_empty_args('GET', $args);

// 邮件标题
$title = get_arg_str('GET', 'title', 512);
// 邮件内容
$body = get_arg_str('GET', 'body', 2048);

$name = '';
$success_count = 0;
$failure_address = array();

// 取得有效订阅的所有邮件地址
$rows = get_valid_email_all();

foreach($rows as $row){
  $email = $row['email'];
  // 发送Email
  $ret = send_email($name, $email, $title, $body);
  if (!$ret){
    $failure_address[] = $email;
    continue;
  } else {
    $success_count++;
  }
}

$msg = '成功发送' . $success_count . '封邮件。';
// 有发送失败的情况
if (count($failure_address) > 0) {
  $msg .=  join(",",$failure_address) . '地址发送失败';
}

exit_ok($msg);
?>
