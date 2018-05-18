<?php
require_once '../inc/common.php';
require_once '../db/staff_weixin.php';
require_once '../db/staff_permit.php';


// 需要员工登录
need_staff_login();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <title>员工个人任务一览-风赢科技</title>
    <link rel="stylesheet" href="css/weui.css">
    <link rel="stylesheet" href="css/swiper-4.2.2.min.css">
    <link rel="stylesheet" href="css/task_list.css">
</head>
<body>

    <div class="info"></div>

    
    <div class="weui-msg__extra-area">©2018 上海风赢网络科技有限公司</div>
    <div id="staff_id" style="display:none"><?php echo $_SESSION['staff_id']?></div>
    <div id="staff_name" style="display:none"><?php echo $_SESSION['staff_name']?></div>

  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script src="js/swiper-4.2.2.min.js"></script>
  <script src="js/common.js"></script>
  <script src="js/task.js"></script>
  <script src="js/wx.js"></script>

</body>
</html>
