<?php
require_once '../inc/common.php';
require_once '../db/staff_weixin.php';
require_once '../db/staff_main.php';
require_once '../db/task.php';

// 需要员工登录
need_staff_login();

$my_id = $_SESSION['staff_id'];
$my_name = $_SESSION['staff_name'];

// 设置了任务ID
if (isset($_GET["task_id"])) {
    $task_id = $_GET["task_id"];                    // 任务ID
    // 取得指定任务ID的任务记录
    $task = get_task($task_id);
    if (!$task)
      exit('task id is not exist');
} else {
  $task_id = $my_id;                               // 临时任务ID
}

$action_id = '';                                  // 行动ID
$action_title = '';                               // 行动标题
$action_intro = '';                               // 行动预期结果
$owner_id = $my_id;                               // 创建人ID
$respo_id = $my_id;                               // 责任人ID
$result_type = 'I';                               // 成果类型(默认内置)
$result_name = '';                                // 成果名称
$connect_type = 0;                                // 沟通类型
$connect_name = '';                               // 联络对象
$is_location = 1;                                 // 是否限定地点
$location_name = '';                              // 地点名称
$is_closed = 0;                                   // 是否完成

// 任务选项
$task_list = get_staff_task_list_select($my_id);
$task_id_option = get_select_option($task_list, $task_id);

// 员工选项
$staff_rows = get_staff_list();
$staff_list = get_staff_list_select($my_id, $staff_rows);
$staff_list['0'] = '请选择员工';
$respo_option = get_select_option($staff_list, $respo_id);

// 成果类型列表
$type_list = array('I'=>'内置', 'O'=>'外链');
$type_input = get_radio_input('result_type', $type_list, $result_type);

// 沟通类型列表
$connect_list = array('0'=>'无', '1'=>'即时', '2'=>'网络');
$connect_option = get_select_option($connect_list, $connect_type);

// 是否限定地点列表
$location_list = array('0'=>'不限定地点','1'=>'公司','2'=>'家','3'=>'其它指定场所');
$location_option = get_select_option($location_list, $is_location);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <title>新的行动-风赢科技</title>
    <link rel="stylesheet" href="css/weui.css">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>

  <div class="weui-cells weui-cells_form">
    <form id="ct_form">
      <div class="weui-cell weui-cell_select weui-cell_select-after">
        <div class="weui-cell__hd"><label class="weui-label">选择任务</label></div>
        <div class="weui-cell__bd">
          <select class="weui-select" id="ct_task_id" name="task_id">
            <?php echo $task_id_option?>
          </select>
        </div>
      </div>

      <div class="weui-cell weui-cell_select weui-cell_select-after">
        <div class="weui-cell__hd"><label class="weui-label">责任担当</label></div>
        <div class="weui-cell__bd">
          <select class="weui-select" id="ct_respo_id"  name="respo_id">
          <?php echo $respo_option?>
          </select>
        </div>
      </div>

      <div class="weui-cell weui-cell_select weui-cell_select-after">
        <div class="weui-cell__hd"><label class="weui-label">联络沟通</label></div>
        <div class="weui-cell__bd">
          <select class="weui-select" id="ct_connect_type"  name="connect_type">
          <?php echo $connect_option?>
          </select>
        </div>
      </div>

      <div class="weui-cell" id="div_connect_name">
        <div class="weui-cell__hd"><label class="weui-label" id="lbl_connect_name">联络对象</label></div>
        <div class="weui-cell__bd">
          <input class="weui-input" type="text" id="ct_connect_name" name="connect_name" placeholder="联络方的名称">
        </div>
      </div>

      <div class="weui-cell weui-cell_select weui-cell_select-after">
        <div class="weui-cell__hd"><label class="weui-label">限定地点</label></div>
        <div class="weui-cell__bd">
          <select class="weui-select" id="ct_is_location"  name="is_location">
          <?php echo $location_option?>
          </select>
        </div>
      </div>

      <div class="weui-cell" id="div_location_name">
        <div class="weui-cell__hd"><label class="weui-label">地点名称</label></div>
        <div class="weui-cell__bd">
          <input class="weui-input" type="text" id="ct_location_name" name="location_name" placeholder="指定地点名称">
        </div>
      </div>

      <div class="weui-cell">
        <div class="weui-cell__hd"><label class="weui-label">行动标题</label></div>
        <div class="weui-cell__bd">
          <input class="weui-input" type="text" id="ct_action_title" name="action_title" placeholder="行动标题（16个汉字以内）">
        </div>
      </div>

      <div class="weui-cells__title"></div>
      <div class="weui-cells weui-cells_form">
        <div class="weui-cell">
          <div class="weui-cell__bd">
            <textarea class="weui-textarea" id="ct_action_intro" name="action_intro" placeholder="行动预期结果" rows="9"></textarea>
            <!--<div class="weui-textarea-counter"><span>0</span>/150</div>-->
          </div>
        </div>
      </div>

    </form>
  </div>

  <div class="weui-btn-area">
      <a id="btn_ok" href="javascript:void(0);" class="weui-btn weui-btn_primary">确认</a>
  </div>

  <div class="weui-msg__extra-area"><a href="../h5_menu.php">风赢科技</a></div>

  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script src="js/common.js"></script>
  <script src="js/wx.js"></script>
  <script src="js/action_new.js"></script>

</body>
</html>
