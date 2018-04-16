<?php
require_once '../inc/common.php';
require_once '../inc/wx_login.php';
require_once '../db/staff_weixin.php';

// 需要微信登录
need_wx_login();

if (!session_id())
  session_start();

if (!isset($_SESSION['unionid']))
  exit_error('119', '该网页已失效，请重新刷新页面再试');

$unionid = $_SESSION['unionid'];

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
  <title>员工个人情报-风赢科技</title>
  <link rel="stylesheet" href="css/weui.css">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>

  <div class="page__hd">
    <h1 class="page__title"><?php echo $_SESSION['staff_name'] ?></h1>
    <p class="page__desc">个人简介</p>
  </div>

  <div class="weui-msg__extra-area">©2018 上海风赢网络科技有限公司</div>

</body>
</html>

