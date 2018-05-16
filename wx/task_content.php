<?php
require_once '../inc/common.php';
require_once('../db/staff_weixin.php');
require_once '../db/staff_permit.php';

// 需要员工登录
need_staff_login();

?>
<!DOCTYPE html>
<html>
<head>
  <title>任务内容</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
  <link rel="stylesheet" href="css/weui.css">
  <link rel="stylesheet" href="css/task_list.css">
</head>
<body>
  <form class="page input js_show" action="../api/task_edit.php" method="GET" id="form" enctype="multipart/form-data">
    <div class="container">
      <div class="page input js_show">
        <!-- 任务详情 -->
        <div class="weui-cells weui-cells_form content-item" style="margin-top: 0;">
          <div class="weui-cell">
            <div class="weui-cell__hd">
              <label class="weui-label">项目</label>
            </div>
            <div class="weui-cell__bd">
              <input class="weui-input" id="task_name" name="task_name" style="width: 100%" readonly="readonly">
            </div>
          </div>
          <div class="weui-cell">
            <div class="weui-cell__hd">
              <label class="weui-label">监督人</label>
            </div>
            <div class="weui-cell__bd">
              <input class="weui-input" name="check_name" id="check_name" readonly="readonly">
            </div>
          </div>
          <div class="weui-cell">
            <div class="weui-cell__hd">
              <label class="weui-label">任务等级</label>
            </div>
            <div class="weui-cell__bd">
              <input class="weui-input" name="task_level" id="task_level_l" readonly="readonly">
            </div>
          </div>
          <div class="weui-cell">
            <div class="weui-cell__hd">
              <label class="weui-label">任务价值</label>
            </div>
            <div class="weui-cell__bd">
              <input class="weui-input" name="task_value" id="task_value" readonly="readonly">
            </div>
          </div>
          <div class="weui-cell">
            <div class="weui-cell__hd">
              <label class="weui-label">任务进度</label>
            </div>
            <div class="weui-cell__bd">
              <input class="weui-input" name="task_perc"  id="task_perc">
            </div>
          </div>
          <div class="weui-cell">
            <div class="weui-cell__hd">
              <label class="weui-label">任务状态</label>
            </div>
            <div class="weui-cell__bd">
              <input class="weui-input" name="task_status" id="task_status" readonly="readonly">
            </div>
          </div>
          <div class="weui-cell" style="display:none">
            <div class="weui-cell__hd">
              <label class="weui-label">任务ID</label>
            </div>
            <div class="weui-cell__bd">
              <input class="weui-input" name="task_id" id="task_id" readonly="readonly" value="<?php echo $_GET['task_id'] ?>">
            </div>
          </div>
          <div class="weui-cell" style="display:none">
            <div class="weui-cell__hd">
              <label class="weui-label">责任人</label>
            </div>
            <div class="weui-cell__bd">
              <input class="weui-label" name="respo_name" id="respo_name" value="<?php echo $_GET['respo_name'] ?>">
            </div>
          </div>
          <div class="weui-cell">
            <div class="weui-cell__hd">
              <label class="weui-label">任务期限</label>
            </div>
            <div class="weui-cell__bd">
              <input class="weui-input" name="limit_time" id="limit_time" readonly="readonly">
            </div>
          </div>
          <div class="weui-cell">
            <div class="weui-cell__hd">
              <label class="weui-label">创建时间</label>
            </div>
            <div class="weui-cell__bd">
              <input class="weui-input" name="ctime" id="ctime" readonly="readonly">
            </div>
          </div>
          <div class="weui-cell">
            <div class="weui-cell__hd">
              <label class="weui-label">任务内容</label>
            </div>
          </div>
          <div class="weui-cell content">
            <div class="weui-cell__bd">
              <textarea class="weui-textarea" name="task_intro" style="width: 100%;" id="task_intro"></textarea>
            </div>
          </div>
          <div class="weui-btn-area">
            <input class="weui-btn weui-btn_primary" type="submit" id="showTooltips" value="确定">
          </div>
        </div>
      </div>
    </div>
  </form>

  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script src="js/swiper-4.2.2.min.js"></script>
  <script src="js/common.js"></script>
  <script src="js/task_info.js"></script>
  <script src="js/wx.js"></script>

</body>
</html>
