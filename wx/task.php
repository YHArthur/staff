<?php
require_once '../inc/common.php';
require_once '../db/staff_weixin.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>风赢任务</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
  <link rel="stylesheet" href="css/weui.css">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>

  <div class="page__hd">
    <h1 class="page__title" id="task_name"></h1>
    <p class="page__desc" id="task_star"></p>
  </div>

  <div class="weui-media-box weui-media-box_text">
    <div class="weui-media-box__desc" id="task_intro">任务介绍</div>
  </div>

  <div class="weui-cells weui-cells_form">
    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">担当：</label></div>
      <div class="weui-cell__bd" id="respo_name"></div>
    </div>

    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">期限：</label></div>
      <div class="weui-cell__bd" id="limit_time"></div>
    </div>

    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">监督：</label></div>
      <div class="weui-cell__bd" id="check_name"></div>
    </div>

    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">创建：</label></div>
      <div class="weui-cell__bd" id="ctime"></div>
    </div>
  </div>

  <div class="weui-cells__title" id="action_title"></div>
  <div class="weui-panel weui-panel_access" id="action_list"></div>
  
  <div class="weui-msg__extra-area"><a href="../h5_menu.php">©2018 风赢科技</a></div>

  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="js/common.js"></script>
  <script src="js/wx.js"></script>
  <script src="js/task_info.js"></script>

</body>
</html>
