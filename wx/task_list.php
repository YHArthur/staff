<?php
require_once '../inc/common.php';
require_once '../db/staff_weixin.php';

// 需要员工登录
need_staff_login();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
  <title>员工任务一览-风赢科技</title>
  <link rel="stylesheet" href="css/weui.css">
  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet" href="css/task_list.css">
</head>
<body>

  <div class="page__hd">
    <h1 class="page__title">任务一览</h1>
    <p class="page__desc"></p>
  </div>
  
  <div class="weui-tab">
    <div class="weui-navbar">
        <div href="#open_task" class="weui-navbar__item" id="open_nav">
            执行中
            <span id='open_count'></span>
        </div>
        <div href="#close_task" class="weui-navbar__item" id="close_nav">
            已完成
        </div>
        <div href="#other_task" class="weui-navbar__item" id="other_nav">
            其他
        </div>
    </div>
    <div class="weui-tab__panel">
        <div id="open_task" class="weui-tab__content"></div>
        <div id="close_task" class="weui-tab__content"></div>
        <div id="other_task" class="weui-tab__content"></div>
    </div>
  </div>

  <div class="weui-msg__extra-area">©2018 风赢科技</div>

  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="js/common.js"></script>
  <script src="js/task_list.js"></script>
  <script>

  $(function () {
      // 获取员工任务一览
      get_staff_task('<?php echo $_SESSION['staff_id'];?>');
      // get_staff_task('06956826-B7E1-2F8E-F897-C3C0124D939C');
      $('.weui-navbar__item').on('click', function () {
          $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
          $(jQuery(this).attr("href")).show().siblings('.weui-tab__content').hide();
      });
  })
  </script>

</body>
</html>
