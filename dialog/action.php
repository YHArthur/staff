<?php
require_once '../inc/common.php';
require_once '../db/staff_main.php';
require_once '../db/task.php';
require_once '../db/action.php';

// 禁止游客访问
exit_guest();

$my_id = $_SESSION['staff_id'];
$my_name = $_SESSION['staff_name'];

// 未设置行动ID(默认添加)
if (!isset($_GET["action_id"])) {
  // 设置了任务ID
  if (isset($_GET["task_id"])) {
    $task_id = $_GET["task_id"];                    // 任务ID
    // 取得指定任务ID的任务记录
    $task = get_task($task_id);
    if (!$task)
      exit('task id is not exist');
  } else {
   $task_id = $my_id;                                // 临时任务ID
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

} else {

  $action_id = $_GET["action_id"];                  // 行动ID
  // 取得指定行动ID的行动记录
  $action = get_action($action_id);
  if (!$action)
    exit('action id does not exist');

  $task_id = $action['task_id'];                    // 任务ID
  $action_title = $action['action_title'];          // 行动标题
  $action_intro = $action['action_intro'];          // 行动预期结果
  $owner_id = $action['owner_id'];                  // 创建人ID
  $respo_id = $action['respo_id'];                  // 责任人ID
  $result_type = $action['result_type'];            // 成果类型
  $result_name = $action['result_name'];            // 成果名称
  $connect_type = $action['connect_type'];          // 沟通类型
  $connect_name = $action['connect_name'];          // 联络对象
  $is_location = $action['is_location'];            // 是否限定地点
  $location_name = $action['location_name'];        // 地点名称
  $is_closed = $action['is_closed'];                // 是否完成
  // 将数据库存放的用户输入内容转换回再修改内容
  $action_intro = html_to_str($action_intro);
}

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
$connect_list = array('0'=>'无', '1'=>'即时', '2'=>'网络', '3'=>'等待');
$connect_input = get_radio_input('connect_type', $connect_list, $connect_type);

// 是否限定地点列表
$location_list = array('0'=>'不限定地点', '1'=>'公司', '2'=>'家', '3'=>'其它指定场所');
$location_option = get_select_option($location_list, $is_location);
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>行动管理</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">
    <div class="modal-body">
      <form id="ct_form" class="layui-form">
          <input type="hidden" name="action_id" id="action_id" value="<?php echo $action_id?>">

          <div class="layui-form-item">
              <label for="ct_task_id" class="layui-form-label">选择任务</label>
              <div class="layui-input-block">
                <select name="task_id" id="ct_task_id" lay-filter="select_task_id">
                <?php echo $task_id_option?>
                </select>
              </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_respo_id" class="layui-form-label">责任担当</label>
              <div class="layui-input-block">
                <select name="respo_id" id="ct_respo_id" lay-filter="select_respo_id">
                <?php echo $respo_option?>
                </select>
              </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_action_title" class="layui-form-label">行动标题</label>
              <div class="layui-input-block">
                <input type="text" class="layui-input" id="ct_action_title" name="action_title" required lay-verify="required" autocomplete="off"  autofocus="autofocus" value="<?php echo $action_title?>"  maxlength="30" placeholder="请输入行动标题（16个汉字以内）">
              </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_connect_type" class="layui-form-label">联络沟通</label>
              <div class="layui-input-block">
                <?php echo $connect_input?>
              </div>
          </div>

          <div class="layui-form-item" id="div_connect_name">
              <label for="ct_connect_name" class="layui-form-label" id="lbl_connect_name">联络对象</label>
              <div class="layui-input-block">
                <input type="text" class="layui-input" id="ct_connect_name" name="connect_name" required lay-verify="required" autocomplete="off"  value="<?php echo $connect_name?>" placeholder="请输入联络方的姓名或公司、组织名称">
              </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_is_location" class="layui-form-label">限定地点</label>
              <div class="layui-input-inline" style="width: 190px;">
                <select name="is_location" id="ct_is_location" lay-filter="select_is_location">
                <?php echo $location_option?>
                </select>
              </div>
            </div>

            <div class="layui-inline" id="div_location_name">
              <label for="ct_is_location" class="layui-form-label">地点名称</label>
              <div class="layui-input-inline" style="width: 235px;">
                <input type="text" class="layui-input" id="ct_location_name" name="location_name" autocomplete="off"  value="<?php echo $location_name?>" placeholder="地点名称">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_action_intro_edit" class="layui-form-label">预期结果</label>
              <div class="layui-input-block">
                <textarea class="layui-textarea" id="ct_action_intro_edit" name="action_intro_edit" placeholder="预期结果"><?php echo $action_intro?></textarea>
              </div>
          </div>

          <div class="layui-form-item">
            <div class="col-xs-6"></div>
            <div class="col-xs-2" class="layui-input-block">
            <?php if ($is_closed == '1' && ($my_id == $owner_id || $my_id == $respo_id)) { ?>
              <input type="checkbox" id="is_closed" value="1" title="完成" checked="checked">
            <?php }?>
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
    connectChange($("input[name='connect_type']:checked").val());
    // 限定地点初始化
    locationChange($("select[name='is_location']").val());
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
    edit_index = layedit.build('ct_action_intro_edit');

    // 沟通类型变更事件
    form.on('radio(radio_connect_type)', function(data) {
       connectChange(data.value);
    });

    // 限定地点变更事件
    form.on('select(select_is_location)', function(data) {
      locationChange(data.value);
    });
  });

  // 沟通类型处理
  function connectChange(ct) {
    // 隐藏联络对象输入框
    $("#div_connect_name").hide();
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
      // 显示联络对象输入框
      $("#div_connect_name").show();
    }
  }

  // 限定地点处理
  function locationChange(lc) {
    // 其它指定地点
    if (lc == "3") {
      // 显示地点名称
      $("#div_location_name").show();
    } else {
      // 隐藏地点名称输入框
      $("#div_location_name").hide();
    }
  }

  // 关闭按钮点击事件
  $("#btn_close").click(function() {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
  });

  // 确认按钮点击事件
  $("#btn_ok").click(function() {
    // 必须输入行动标题
    var action_title = $("#ct_action_title").val().trim();
    if (action_title.length == 0) {
      parent.layer.msg('请输入行动标题');
      return;
    }

    // 联络沟通有时必须输入联络对象
    var connect_type = $("input[name='connect_type']:checked").val();
    var connect_name = $("#ct_connect_name").val().trim();
    if (connect_type != '0' && connect_name.length == 0) {
      parent.layer.msg('请输入联络对象');
      return;
    }

    /*
    var action_intro = layedit.getContent(edit_index).trim();
    if (action_intro.length == 0) {
      parent.layer.msg('请输入预期结果');
      return;
    }
    */

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

    // 责任担当
    row['respo_name'] = $("#ct_respo_id option:selected").text();
    // 沟通类型
    row['connect_type'] = $("input[name='connect_type']:checked").val();
    // 行动预期结果
    row['action_intro'] = layedit.getContent(edit_index);
    // 是否完成
    row['is_closed'] = 0;
    var obj = $("#is_closed");
    if (obj && obj.is(':checked'))
      row['is_closed'] = 1;

    $.ajax({
        url: '/staff/api/action.php',
        type: 'post',
        data: row,
        success:function(response) {
          // AJAX正常返回
          if (response.errcode == '0') {
            parent.layer.alert(response.errmsg, {
              icon: 1,
              title: '提示信息',
              btn: ['OK']
            });
            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
            parent.table.bootstrapTable('refresh');
            parent.layer.close(index);
          } else {
            parent.layer.msg(response.errmsg, {
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