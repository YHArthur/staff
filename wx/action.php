<?php
require_once '../inc/common.php';
require_once '../db/staff_weixin.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>风赢行动</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
  <link rel="stylesheet" href="css/weui.css">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>

  <div class="page__hd">
    <h1 class="page__title" id="action_title"></h1>
    <p class="page__desc" id="action_intro"></p>
  </div>

  <div class="weui-media-box weui-media-box_text">
    <div class="weui-media-box__desc" id="result_memo">进展情况</div>
  </div>

  <div class="weui-cells weui-cells_form">
    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">负责：</label></div>
      <div class="weui-cell__bd" id="respo_name"></div>
    </div>

    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">状态：</label></div>
      <div class="weui-cell__bd" id="is_close"></div>
    </div>

    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">创建：</label></div>
      <div class="weui-cell__bd" id="ctime"></div>
    </div>

    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">完成：</label></div>
      <div class="weui-cell__bd" id="closed_time"></div>
    </div>
  </div>

  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="js/common.js"></script>
  <script src="js/wx.js"></script>
  <script src="js/action_info.js"></script>

</body>
</html>
