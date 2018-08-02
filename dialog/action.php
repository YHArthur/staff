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
if (!isset($_GET["id"])) {
  // 必须设置任务ID
  if (!isset($_GET["task_id"]))
    exit('task id is not exist');
    
  $task_id = $_GET["task_id"];                      // 任务ID
  // 取得指定任务ID的任务记录
  $task = get_task($task_id);
  if (!$task)
    exit('task id is not exist');
  
  $action_title = '';                               // 行动标题
  $action_intro = '';                               // 行动预期结果
  $respo_id = $staff_id;                            // 责任人ID
  $result_type = 'D';                               // 成果类型
  $result_name = '';                                // 成果名称
  $is_location = 0;                                 // 是否限定地点
  $location_name = '';                              // 地点名称


} else {

  $action_id = $_GET["id"];                         // 行动ID
  // 取得指定行动ID的行动记录
  $action = get_action($action_id);
  if (!$action)
    exit('action id does not exist');

  $task_id = $action['task_id'];                    // 任务ID
  $action_title = $action['action_title'];          // 行动标题
  $action_intro = $action['action_intro'];          // 行动预期结果
  $respo_id = $action['respo_id'];                  // 责任人ID
  $result_type = $task['result_type'];              // 成果类型
  $result_name = $task['result_name'];              // 成果名称
  $is_location = $task['is_location'];              // 是否限定地点
  $location_name = $task['location_name'];          // 地点名称

  // 将数据库存放的用户输入内容转换回再修改内容
  $action_intro = html_to_str($action_intro);
}

// 员工选项
$staff_rows = get_staff_list();
$staff_list = get_staff_list_select($staff_id, $staff_rows);
$staff_list['0'] = '请选择员工';
$respo_option = get_select_option($staff_list, $respo_id);

// 成果类型列表
$type_list = array('D'=>'文档', 'C'=>'联络', 'W'=>'等待');
$type_input = get_radio_input('result_type', $type_list, $result_type);

// 是否限定地点列表
$location_list = array('0'=>'不限定地点','1'=>'公司','2'=>'家','3'=>'其它指定场所');
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

          <input type="hidden" name="task_id" id="task_id" value="<?php echo $task_id?>">

          <div class="layui-form-item">
              <label for="ct_action_title" class="layui-form-label">行动标题</label>
              <div class="layui-input-block">
                <input type="text" class="layui-input" id="ct_action_title" name="action_title" required lay-verify="required" autocomplete="off"  value="<?php echo $action_title?>" placeholder="行动标题（30字以内）">
              </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_result_type" class="layui-form-label">成果类型</label>
              <div class="layui-input-block">
                <?php echo $type_input?>
              </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_result_name" class="layui-form-label" id="lbl_result_name">文档名称</label>
              <div class="layui-input-block">
                <input type="text" class="layui-input" id="ct_result_name" name="result_name" required lay-verify="required" autocomplete="off"  value="<?php echo $result_name?>" placeholder="输入文档名称或文档访问URL">
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
            <div class="col-xs-8"></div>
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
    // 默认成果类型为文档
    resultChange('D');
    // 隐藏指定地点名称输入框
    $("#div_location_name").hide();
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
    
    // 成果类型点击事件
    form.on('radio(radio_result_type)', function(data) {
      // 成果类型变更事件
      resultChange(data.value);
    });
    
    // 限定地点变更事件
    form.on('select(select_is_location)', function(data) {
      locationChange(data);
    }); 

  });

  // 成果类型变更事件
  function resultChange(tp) {
    // 成果类型
    var result_name = '文档名称';
    var result_placeholder = '输入文档名称或文档访问URL';
    switch(tp)
    {
      // 联络
      case 'C':
        result_name = '联络对象';
        result_placeholder = '请输入联络方的姓名或公司、组织名称';
        break;
      // 等待
      case 'W':
        result_name = '等待对象';
        result_placeholder = '请输入需等待对方联络的姓名或公司、组织名称';
        break;
      // 文档
      default:
        break;
    }
    // 标签变更
    $("#lbl_result_name").html(result_name);
    // 输入内容变更
    $("#ct_result_name").attr('placeholder', result_placeholder);
  }

  // 限定地点变更事件
  function locationChange(data) {
    if (data.value != "3") {
      // 隐藏地点名称
      $("#div_location_name").hide();
    } else {
      // 显示地点名称
      $("#div_location_name").show();
    }
  }

  // 关闭按钮点击事件
  $("#btn_close").click(function() {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
  });

  // 确认按钮点击事件
  $("#btn_ok").click(function() {
    var action_title = $("#ct_action_title").val().trim();
    if (action_title.length == 0) {
      parent.layer.msg('请输入行动标题');
      return;
    }

    var action_intro = layedit.getContent(edit_index).trim();
    if (action_intro.length == 0) {
      parent.layer.msg('请输入预期结果');
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

    // 成果类型
    row['result_type'] = $("input[name='result_type']:checked").val();
    // 任务内容
    row['action_intro'] = layedit.getContent(edit_index);

    $.ajax({
        url: '/staff/api/action.php',
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