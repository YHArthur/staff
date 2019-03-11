<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
  <title>每日完成行动列表-风赢科技</title>
  <link rel="stylesheet" href="css/weui.css">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>

  <div class="page__hd">
    <h1 class="page__title">完成行动列表</h1>
    <p class="page__desc"><span id="action_day"></span> 合计完成：<span id="total"></span> 件</p>
  </div>

  <input type="hidden" id="day" value='1'>
  <div id="action_rows"></div>
  
  <div class="weui-flex button_sp_area" id="btn_list"></div>

  <div class="weui-msg__extra-area"><a href="../h5_menu.php">风赢科技</a></div>

  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="js/common.js"></script>
  <script src="js/wx.js"></script>
  <script src="js/action_daily_list.js"></script>

</body>
</html>
