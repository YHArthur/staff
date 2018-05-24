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
  <title>任务详情</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
  <link rel="stylesheet" href="css/weui.css">
  <link rel="stylesheet" href="css/example.css">
  <link rel="stylesheet" href="css/task_list.css">
</head>
<body>
  <form class="page input js_show" method="GET" id="action" enctype="multipart/form-data">
    <div class="container">
      <div class="page input js_show">
        <!-- 任务详情 -->
        <div class="weui-cell" style="display:none">
          <div class="weui-cell__hd">
            <label class="weui-label">任务ID</label>
          </div>
          <div class="weui-cell__bd">
            <input class="weui-input" name="task_id" id="task_id" readonly="readonly" value="<?php echo $_GET['task_id'] ?>">
          </div>
        </div>
        <div class="page__hd" style="padding: 20px 20px 0 20px;">
          <h1 class="page__title"><input type="text" readonly="readonly" id="task_status" id="task_status"><span id="task_name" class="task_name_c" name="task_name">蜂巢项目（hivebanks）中文版白皮书</span><input type="text" class="level" name="task_level" id="task_level" value="⭐⭐⭐" readonly="readonly"></h1>
          <p class="page__desc" style="font-size: 14px;color: #000;">【监督人】<input type="text" name="check_name" class="check_name_c" id="check_name" value="袁浩" readonly="readonly">【价值】<input type="text" name="task_value" class="task_value_c" id="task_value" value="100" readonly="readonly"><br>【任务期限】<input type="text" name="limit_time" class="limit_time_c" id="limit_time" value="05-21" readonly="readonly">【创建日期】<input type="text" name="ctime" class="ctime_c" id="ctime" value="04-21" readonly="readonly"></p>
          <div class="weui-slider-box">
            <div class="weui-slider"   id="task_perc">
              <div id="sliderInner" class="weui-slider__inner">
                <div id="sliderTrack" style="width: 50%;" class="weui-slider__track"></div>
                <div id="sliderHandler" style="left: 50%;" class="weui-slider__handler"></div>
              </div>
            </div>
            <div><span id="task_perc_l" name="task_perc" class="weui-slider-box__value">50</span></div>
            <a class="weui-btn weui-btn_mini weui-btn_default" id="change">修改</a>
          </div>
        </div>
        <div class="page__bd">
          <div class="weui-cell content">
            <div class="weui-cell__bd">
              <textarea class="weui-textarea" name="task_intro" id="task_intro" rows="20"></textarea>
            </div>
          </div>
          <div class="weui-btn-area">
            <a  class="weui-btn weui-btn_primary" id="showTooltips">确定</a>
          </div>
        </div>
      </div>
    </div>
  </form>

  <script src="js/zepto.min.js"></script>
  <script src="js/common.js"></script>
  <script src="js/task_info.js"></script>
  <script src="js/wx.js"></script>
  <script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
 
</body>
</html>
