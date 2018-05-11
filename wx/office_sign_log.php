<?php
// require_once '../inc/common.php';
// require_once('../db/staff_weixin.php');

// 需要员工登录
// need_staff_login();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
  <title>风赢科技员工管理平台</title>
  <link rel="stylesheet" href="css/weui.css">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>

  <div class="page__hd">
    <h1 class="page__title">签到记录</h1>
    <p class="page__desc">白金湾339</p>
  </div>

  <div class="weui-cells__title">最新签到信息</div>
  <div id="sign_rows" class="weui-cells"></div>

  <div class="weui-msg__extra-area">©2018 风赢科技</div>

  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script src="js/common.js"></script>
  <script src="js/wx.js"></script>
  <script src="js/office_sign_log.js"></script>

</body>
</html>

