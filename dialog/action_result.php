<?php
require_once '../inc/common.php';
require_once '../db/staff_main.php';
require_once '../db/task.php';
require_once '../db/task_action.php';

// 禁止游客访问
exit_guest();

$staff_id = $_SESSION['staff_id'];
$staff_name = $_SESSION['staff_name'];

// 未设置行动ID(默认添加)
if (!isset($_GET["action_id"]))
  exit('action id is not exist');

$action_id = $_GET["action_id"];                  // 行动ID
// 取得指定行动ID的行动记录
$action = get_action($action_id);
if (!$action)
  exit('action id does not exist');

if ($staff_id != $action['respo_id'])
  exit('no permit');

$action_title = $action['action_title'];          // 行动标题
$action_intro = $action['action_intro'];          // 行动预期结果
$result_memo = $action['result_memo'];            // 结果描述
$result_type = $action['result_type'];            // 成果类型
$result_name = $action['result_name'];            // 成果名称
$connect_type = $action['connect_type'];          // 沟通类型
$connect_name = $action['connect_name'];          // 联络对象
$is_location = $action['is_location'];            // 是否限定地点
$location_name = $action['location_name'];        // 地点名称

// 有沟通对象
if ($connect_type != '0') {
  $connect_list = array('1'=>'即时', '2'=>'网络', '3'=>'等待');
  $connect_input = get_radio_input('connect_type', $connect_list, $connect_type);
}
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>行动结果更新</title>

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
      <hr>
      <form id="ct_form" class="layui-form">
          <input type="hidden" name="action_id" id="action_id" value="<?php echo $action_id?>">
          <?php if ($connect_type != '0') {
                  $result_lbl = '联络对象';
                  $result_placeholder = '请输入联络方的姓名或公司、组织名称';
                  if ($connect_type == '3') {
                    $result_lbl = '等待对象';
                    $result_placeholder = '请输入需等待对方联络的姓名或公司、组织名称';
                  }
          ?>
          <div class="layui-form-item" id="div_connect_name">
            <div class="layui-inline">
              <label for="ct_connect_type" class="layui-form-label">联络沟通</label>
              <div class="layui-input-inline" style="width: 235px;">
                <?php echo $connect_input?>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_connect_name" class="layui-form-label" id="lbl_connect_name"><?php echo $result_lbl?></label>
              <div class="layui-input-inline">
                <input type="text" class="layui-input" id="ct_connect_name" name="connect_name" required lay-verify="required" autocomplete="off"  value="<?php echo $connect_name?>" placeholder="<?php echo $result_placeholder?>">
              </div>
            </div>
          </div>
          <?php } ?>

          <div class="layui-form-item">
              <label for="ct_result_memo_edit" class="layui-form-label">进展状况</label>
              <div class="layui-input-block">
                <textarea class="layui-textarea" id="ct_result_memo_edit" name="result_memo_edit" placeholder="进展状况"><?php echo $result_memo?></textarea>
              </div>
          </div>

          <div class="layui-form-item">
            <div class="col-xs-6"></div>
            <div class="col-xs-2" class="layui-input-block">
              <input type="checkbox" id="is_closed" value="1" title="完成">
            </div>
            <div class="col-xs-2">
              <button type="button" id="btn_close" class="btn btn-default btn-block">关闭</button>
            </div>
            <div class="col-xs-2">
              <button type="button" id="btn_ok" class="btn btn-primary btn-block submit">确认</button>
            </div>
          </div>

        </form>
    </div>
  </div>

  <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="http://libs.baidu.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="../js/layui/layui.js"></script>

  <script>
  var edit_index = 0;
  var layer = new Object();
  var form = new Object();
  var layedit = new Object();

  $(function () {
    // 沟通类型初始化
    var ct = $("input[name='connect_type']:checked").val();
    connectChange(ct);
  });

  // 使用Layui
  layui.use(['layer', 'form', 'layedit'], function(){
    layer = layui.layer;
    form = layui.form();
    layedit = layui.layedit;

    layedit.set({
      uploadImage: {
        url: 'http://www.fnying.com/upload/upload_image.php' //接口url
        ,type: '' //默认post
      }
    });
    edit_index = layedit.build('ct_result_memo_edit');


    // 沟通类型变更事件
    form.on('radio(radio_connect_type)', function(data) {
       connectChange(data.value);
    });
  });

  // 沟通类型处理
  function connectChange(ct) {
    // 沟通类型不是无
    if (ct != 0) {
      // 默认成果标签和成果提示文字
      var result_name = '联络对象';
      var result_placeholder = '请输入联络方的姓名或公司、组织名称';
      // 等待
      if (ct == 3) {
          result_name = '等待对象';
          result_placeholder = '请输入需等待对方联络的姓名或公司、组织名称';
      }
      // 联络对象标签变更
      $("#lbl_connect_name").html(result_name);
      // 联络对象输入内容变更
      $("#ct_connect_name").attr('placeholder', result_placeholder);
    }
  }

  // 关闭按钮点击事件
  $("#btn_close").click(function() {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
  });

  // 确认按钮点击事件
  $("#btn_ok").click(function() {
    var result_memo = layedit.getContent(edit_index).trim();
    if (result_memo.length == 0) {
      parent.layer.msg('请输入进展状况');
      return;
    }

    var row = {};
    var form = $("#ct_form");

    form.find('input[name]').each(function () {
        row[$(this).attr('name')] = $(this).val();
    });

    form.find('select[name]').each(function () {
        row[$(this).attr('name')] = $(this).val();
    });

    form.find('textarea[name]').each(function () {
        row[$(this).attr('name')] = $(this).val();
    });

    // 沟通类型
    row['connect_type'] = $("input[name='connect_type']:checked").val();
    // 结果描述
    row['result_memo'] = layedit.getContent(edit_index);
    // 是否完成
    row['is_closed'] = 0;
    var obj = $("#is_closed");
    if (obj.is(':checked'))
      row['is_closed'] = 1;
    
    $.ajax({
        url: '/staff/api/action_result.php',
        type: 'post',
        data: row,
        success:function(msg) {
          // AJAX正常返回
          if (msg.errcode == '0') {
            parent.layer.alert(msg.errmsg, {
              icon: 1,
              title: '提示信息',
              btn: ['OK']
            });
            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
            parent.table.bootstrapTable('refresh');
            parent.layer.close(index);
          } else {
            parent.layer.msg(msg.errmsg, {
              icon: 2,
              title: '错误信息',
              btn: ['好吧']
            });
          }
        },
        error:function(XMLHttpRequest, textStatus, errorThrown) {
          // AJAX异常
          parent.layer.msg(textStatus, {
              icon: 2,
              title: errorThrown,
              btn: ['好吧']
          });
        }
    });

  });
  </script>


</body>
</html>