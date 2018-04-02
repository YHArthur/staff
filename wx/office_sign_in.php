<?php
require_once '../inc/common.php';
require_once('../db/staff_weixin.php');

// 需要员工登录
need_staff_login();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
  <title>员工签到-风赢科技</title>
  <link rel="stylesheet" href="css/weui.css">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>

  <div class="page__hd">
    <h1 class="page__title">员工签到</h1>
    <p class="page__desc"></p>
  </div>

  <div class="weui-cells__title">签到信息</div>
  <div class="weui-cells weui-cells_form">
    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">姓名</label></div>
      <div class="weui-cell__bd"><label id="staff_name"><?php echo $_SESSION['staff_name'];?></label></div>
    </div>
    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">签到时间</label></div>
      <div class="weui-cell__bd"><label id="sign_time"><?php echo date('Y-m-d H:i:s');?></label></div>
    </div>
    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">签到地点</label></div>
      <div class="weui-cell__bd"><label id="sign_type">白金湾339</label></div>
    </div>
  </div>

  <div class="weui-cells__title">历史信息<label id="sign_history"></label></div>
  <div id="order_rows" class="weui-cells weui-cells_checkbox">
  </div>

  <div class="weui-msg__extra-area">©2018 上海风赢网络科技有限公司</div>

  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script src="js/common.js"></script>
  <script src="js/wx.js"></script>
  <script src="js/office_sign_in.js"></script>

</body>
</html>

