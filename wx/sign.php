<?php
require_once '../inc/common.php';
require_once '../inc/wx_login.php';

// 需要微信登录
need_wx_login();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
  <title>员工微信注册-风赢科技</title>
  <link rel="stylesheet" href="css/weui.css">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>

  <div class="page__hd">
    <h1 class="page__title">员工微信注册</h1>
    <p class="page__desc">你的员工微信账号尚未注册，在使用相关功能前请先填写个人信息并提交审核。</p>
  </div>

  <div class="weui-cells__title">填写信息</div>
  <div class="weui-cells weui-cells_form">
    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">姓名</label></div>
      <div class="weui-cell__bd"><input class="weui-input" id="staff_name" type="text" placeholder="请输入你的姓名"></div>
    </div>
    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">手机</label></div>
      <div class="weui-cell__bd"><input class="weui-input" id="staff_phone" type="number" pattern="[0-9]*" placeholder="请输入你的手机号码"></div>
    </div>
  </div>
  <div class="weui-btn-area">
    <a class="weui-btn weui-btn_primary" href="javascript:" id="sign_btn">注册</a>
  </div>

  <div class="weui-footer weui-footer_fixed-bottom">
    <p class="weui-footer__text">风赢科技</p>
  </div>

  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script src="js/common.js"></script>
  <script src="js/wx.js"></script>
  <script src="js/sign.js"></script>

</body>
</html>

