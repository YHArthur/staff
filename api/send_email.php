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

$args = array('body');
chk_empty_args('GET', $args);

//获取内容
$body = get_arg_str('GET', 'body', 1024);    //邮件内容
$title = get_arg_str('GET', 'title');    //邮件主题
$name = 'boss';
$miss = array();
//获取邮箱列表
$email_list = get_email_list();
foreach($email_list as $email){
  $email = $email['email'];
  $ret = send_email($name, $email, $title, $body);
  if (!$ret){
    exit_error('110', 'Email地址确认邮件发送失败，请稍后再试');
    $miss[] = $email;
    continue;
  }
}
$msg = count($miss) + "封邮件发送失败";
exit_ok($msg);

?>
