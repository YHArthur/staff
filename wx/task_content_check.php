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
    <form class="page input js_show" method="GET" id="action">
        <!-- 内容 -->
    	<div class="container">
    		<div class="page input js_show">
    			<div class="page__bd content-nav">
    				<div class="weui-cells weui-cells_form content-item">
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">项目名称</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input type="text" name="task_name" class="weui-input" id="task_name_check" style="width: 100%;">
                            </div>
                        </div>
                        <div class="weui-cell weui-cell_select">
                            <div class="weui-cell__hd">
                                <label class="weui-label">监督人</label>
                            </div>
                            <div class="weui-cell__bd">
                                <select class="weui-select" name="check_name" id="check_name" style="padding-left: 0;">
                                    <option value="袁浩">袁浩</option>
                                    <option value="邱知霖">邱知霖</option>
                                    <option value="居国红">居国红</option>
                                    <option value="邵源超">邵源超</option>
                                    <option value="李燕羽">李燕羽</option>
                                </select>
                            </div>
                        </div>
                        <div class="weui-cell weui-cell_select">
                            <div class="weui-cell__hd">
                                <label class="weui-label">任务等级</label>
                            </div>
                            <div class="weui-cell__bd">
                                <select class="weui-select" name="task_level" id="task_level_check" style="padding-left: 0;">
                                    <option selected value="0">可选</option>
                                    <option value="1">⭐</option>
                                    <option value="2">⭐⭐</option>
                                    <option value="3">⭐⭐⭐</option>
                                </select>
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">任务价值</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input type="text" name="task_value" class="weui-input" id="task_value">
                            </div>
                        </div>
                        <div class="weui-cell" style="display:none">
                            <div class="weui-cell__hd">
                                <label class="weui-label">任务ID</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input class="weui-label" id="task_id" name="task_id" value="<?php echo $_GET['task_id'] ?>">
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">任务进度</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input type="tsxt" name="task_perc" class="weui-input" id="task_perc_check">
                            </div>
                        </div>
                        <div class="weui-cell weui-cell_select">
                            <div class="weui-cell__hd">
                                <label class="weui-label">任务状态</label>
                            </div>
                            <div class="weui-cell__bd">
                                <select class="weui-select" id="task_status_check" name="task_status" style="padding-left: 0;">
                                    <option value="0">废止</option>
                                    <option value="1">完成</option>
                                    <option value="2">执行</option>
                                    <option value="3">等待</option>
                                </select>
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">任务期限</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input type="date" name="limit_time" class="weui-input" id="limit_time_check">
                            </div>
                        </div>                   
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">任务内容</label>
                            </div>
                        </div>
                        <div class="weui-cell content">
                            <div class="weui-cell__bd">
                                <textarea class="weui-textarea" id="task_intro" placeholder="请输入" rows="3" name="task_intro"></textarea>
                                <div class="weui-textarea-counter">
                                    <span>0</span>/1000
                                </div>
                            </div>
                        </div>
                        <div class="weui-btn-area">
                            <a  class="weui-btn weui-btn_primary" id="showTooltips">确定</a>
                    	</div>
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
