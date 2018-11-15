<?php
require_once '../inc/common.php';
require_once '../db/staff_weixin.php';
require_once '../db/staff_permit.php';

need_staff_login();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
  <title>员工访问记录-风赢科技</title>
  <link rel="stylesheet" href="css/weui.css">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>

  <div class="page__hd">
    <h1 class="page__title">访问记录</h1>
    <p class="page__desc"><span id="time_now"></span></p>
  </div>

  <div id="log_rows"></div>
  
  <div class="weui-msg__extra-area"><a href="../h5_menu.php">©2018 风赢科技</a></div>

  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="js/common.js"></script>
  <script src="js/wx.js"></script>
  <script src="js/staff_action_log_list.js"></script>

</body>
</html>
