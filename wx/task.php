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
    <h2 class="page__title" id="task_name"></h2>
    <p class="page__desc">
      <span id="task_star"></span>
      <small id="ctime" class="weui-article"></small>
    </p>
    <p class="page__desc">
      <span id="action_btn">
        <button id="add_action_btn" href="" class="weui-btn weui-btn_mini weui-btn_primary">添加行动</button>
        <button id="join_task_btn" href="" class="weui-btn weui-btn_mini weui-btn_default">加入任务</button>
        <button id="left_task_btn" href="" class="weui-btn weui-btn_mini weui-btn_warn">离开任务</button>
      </span>
    </p>
  </div>

  <input type="hidden" name="task_id" id="task_id" value="">

  <div class="weui-media-box weui-media-box_text">
    <p class="page__desc" id="task_intro">任务介绍</p>
  </div>

  <div class="weui-media-box weui-media-box_text">
    <div class="weui-article" id="result_memo">进展情况</div>
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
      <div class="weui-cell__hd"><label class="weui-label">状态：</label></div>
      <div class="weui-cell__bd" id="is_close"></div>
    </div>

    <div class="weui-cell" id="div_close_time">
      <div class="weui-cell__hd"><label class="weui-label">完成：</label></div>
      <div class="weui-cell__bd" id="closed_time"></div>
    </div>

    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">监督：</label></div>
      <div class="weui-cell__bd" id="check_name"></div>
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
