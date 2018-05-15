<?php
require_once '../inc/common.php';
require_once '../inc/send_email.php';
require_once '../../php/db/www_email.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 群发邮件 ==========================
GET参数
  title       邮件标题
  body        邮件内容

返回
  errcode = 0 请求成功

说明
*/

php_begin();

// 参数检查
$args = array('body');
chk_empty_args('GET', $args);


// 提交参数整理
$body = get_arg_str('GET', 'body', 1024);

$name = 'boss';
$email = '1176932572@qq.com';
$title = '这是一封测试邮件';

$ret = get_www_email($email);
var_dump($ret);

// 发送报名成功邮件
// $ret = send_email($name, $email, $title, $body);
// if (!$ret)
  // exit_error('110', 'Email地址确认邮件发送失败，请稍后再试');

// 正常返回
exit_ok();
?>
