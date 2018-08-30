<?php
require_once '../inc/common.php';
require_once '../db/staff_main.php';
require_once '../db/task.php';
require_once '../db/action.php';

// 未设置行动ID(默认添加)
if (!isset($_GET["action_id"]))
  exit('action id is not exist');

$action_id = $_GET["action_id"];                  // 行动ID
// 取得指定行动ID的行动记录
$action = get_action($action_id);
if (!$action)
  exit('action id does not exist');

$action_title = $action['action_title'];          // 行动标题
$action_intro = $action['action_intro'];          // 行动预期结果
$result_memo = $action['result_memo'];            // 结果描述
$result_type = $action['result_type'];            // 成果类型
$result_name = $action['result_name'];            // 成果名称
$connect_type = $action['connect_type'];          // 沟通类型
$connect_name = $action['connect_name'];          // 联络对象
$is_location = $action['is_location'];            // 是否限定地点
$location_name = $action['location_name'];        // 地点名称
$is_closed = $action['is_closed'];                // 是否完成
$closed_time = $action['closed_time'];            // 结束时间

$result_title = '进展情况';
if ($is_closed == 1)
  $result_title = '完成情况 <small>' . $closed_time . '</small>';

?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>行动进展</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">
    <div class="modal-body">
      <legend><?php echo $action_title?></legend>
      <blockquote class="layui-elem-quote"><?php echo $action_intro?></blockquote>
      <div>
      <?php if ($is_location != '0') {?>
          <label id="lbl_location_name">地点:</label>
          <?php echo $location_name?>
      <?php } ?>

      <?php if ($result_type == 'O') {?>
          <label id="lbl_result_name">外链:</label>
          <a href="<?php echo $result_name?>" target="_blank"><?php echo $result_name?></a>
      <?php } ?>
      </div>
      <?php if ($connect_type != '0') {
              if ($connect_type == '1') {
                $connect_lbl = '即时联络';
              } else if ($connect_type == '2') {
                $connect_lbl = '网络联络';
              } else if ($connect_type == '3') {
                $connect_lbl = '等待反馈';
              }
      ?>
          <label id="lbl_connect_name"><?php echo $connect_lbl?>:</label>
          <?php echo $connect_name?></a>
      <?php } ?>
      <hr>
      <legend><?php echo $result_title?></legend>
      <blockquote class="layui-elem-quote"><?php echo $result_memo?></blockquote>

      <div class="layui-form-item">
        <div class="col-xs-10"></div>
        <div class="col-xs-2">
          <button type="button" id="btn_close" class="btn btn-default btn-block">关闭</button>
        </div>
      </div>

    </div>
  </div>

  <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="http://libs.baidu.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="../js/layui/layui.js"></script>

  <script>
  // 关闭按钮点击事件
  $("#btn_close").click(function() {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
  });
  </script>


</body>
</html>