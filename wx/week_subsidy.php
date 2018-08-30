<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
  <title>员工周补贴明细-风赢科技</title>
  <link rel="stylesheet" href="css/weui.css">
  <link rel="stylesheet" href="css/index.css">  
</head>
<body>

  <div class="page__hd">
    <h1 class="page__title">周补贴明细</h1>
    <p class="page__desc"><span id="week_begin"></span>-<span id="week_end"></span> <span id="staff_name"></span> 合计：<span id="week_sum"></span></p>
  </div>

  <input type="hidden" id="week" value='0'>
  
  <div id="subsidy_rows"></div>
  
  <div class="weui-flex button_sp_area" id="btn_list"></div>

  <div class="weui-msg__extra-area"><a href="../h5_menu.php">©2018 风赢科技</a></div>

  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="js/common.js"></script>
  <script src="js/week_subsidy.js"></script>

</body>
</html>

