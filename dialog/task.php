<?php
require_once '../inc/common.php';
require_once '../db/task.php';
require_once '../db/staff_main.php';

// 禁止游客访问
exit_guest();

// 未设置任务ID(默认添加)
if (!isset($_GET["id"])) {

  $task_id = '';                                  // 任务ID
  $task_name = '';                                // 任务
  $task_intro = '';                               // 任务内容
  $respo_id = '';                                 // 责任人ID
  $respo_name = '';                               // 责任人
  $check_id = '';                                 // 监管人ID
  $check_name = '';                               // 监管人

  $task_level = 0;                                // 任务等级
  $task_value = 0;                                // 任务价值
  $task_perc = 0;                                 // 任务进度
  $task_status = 3;                               // 任务状态
  $limit_time = date('Y-m-d') . ' 18:00:00';      // 任务期限
  $is_public = 1;                                 // 是否公开

} else {

  $task_id = $_GET["id"];                         // 任务ID
  // 取得指定任务ID的任务记录
  $task = get_task($task_id);
  if (!$task)
    exit('task id is not exist');

  $task_id = $task['task_id'];                    // 任务ID
  $task_name = $task['task_name'];                // 任务
  $task_intro = $task['task_intro'];              // 任务内容
  $respo_id = $task['respo_id'];                  // 责任人ID
  $respo_name = $task['respo_name'];              // 责任人
  $check_id = $task['check_id'];                  // 监管人ID
  $check_name = $task['check_name'];              // 监管人

  $task_level = $task['task_level'];              // 任务等级
  $task_value = $task['task_value'];              // 任务价值
  $task_perc = $task['task_perc'];                // 任务进度
  $task_status = $task['task_status'];            // 任务状态
  $limit_time = $task['limit_time'];              // 任务期限
  $is_public = $task['is_public'];                // 是否公开

  // 将数据库存放的用户输入内容转换回再修改内容
  $task_intro = html_to_str($task_intro);
}

// 员工选项
$staff_id = $_SESSION['staff_id'];
$staff_name = $_SESSION['staff_name'];
$staff_rows = get_staff_list();
$staff_list = get_staff_list_select($staff_id, $staff_rows);
$blank_array = array('0' => '请选择');
$respo_option = get_select_option(array_merge($blank_array, $staff_list), $respo_id);
$check_option = get_select_option(array_merge($blank_array, $staff_list), $check_id);

// 对外公开选项
$public_list = array('1'=>'公开', '0'=>'私人');
$public_input = get_radio_input('is_public', $public_list, $is_public);

// 任务等级列表
$level_list = array('0'=>'可选','1'=>'一般','2'=>'重要','3'=>'非常重要');
$level_option = get_select_option($level_list, $task_level);

// 任务状态列表
$status_list = array('3'=>'等待','2'=>'执行','1'=>'完成','0'=>'废止');
$status_option = get_select_option($status_list, $task_status);
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>任务管理</title>

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
            <div class="layui-inline">
              <label for="ct_task_level" class="layui-form-label">重要程度</label>
              <div class="layui-input-inline" style="width: 190px;">
                <select name="task_level" id="ct_task_level">
                <?php echo $level_option?>
                </select>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_limit_time" class="layui-form-label" style="width: 110px;">截止期限</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_limit_time" name="limit_time" value="<?php echo $limit_time?>" placeholder="截止期限" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD hh:mm:ss'})">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_respo_id" class="layui-form-label">责任担当</label>
              <div class="layui-input-inline" style="width: 190px;">
                <select name="respo_id" id="ct_respo_id">
                <?php echo $respo_option?>
                </select>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_check_id" class="layui-form-label">监督检查</label>
              <div class="layui-input-inline" style="width: 190px;">
                <select name="check_id" id="ct_check_id">
                <?php echo $check_option?>
                </select>
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_is_public" class="layui-form-label">对外公开</label>
              <div class="layui-input-inline" style="width: 190px">
                <?php echo $public_input?>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_task_status" class="layui-form-label">任务状态</label>
              <div class="layui-input-inline" style="width: 100px;">
                <select name="task_status" id="ct_task_status">
                <?php echo $status_option?>
                </select>
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_task_value" class="layui-form-label">任务价值</label>
              <div class="layui-input-inline" style="width: 190px;">
                <input type="number" class="layui-input" name="task_value" id="ct_task_value" value="<?php echo $task_value?>" placeholder="任务价值">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_task_perc" class="layui-form-label">任务进度</label>
              <div class="layui-input-inline" style="width: 100px;">
                <input type="number" class="layui-input" name="task_value" id="ct_task_value" value="<?php echo $task_value?>" placeholder="任务进度">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_task_name" class="layui-form-label">任务标题</label>
              <div class="layui-input-block">
                <input type="text" class="layui-input" id="ct_task_name" name="task_name" required lay-verify="required" autocomplete="off"  value="<?php echo $task_name?>" placeholder="任务标题（30字以内）">
              </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_task_intro_edit" class="layui-form-label">任务内容</label>
              <div class="layui-input-block">
                <textarea class="layui-textarea" id="ct_task_intro_edit" name="task_intro_edit" placeholder="任务内容"><?php echo $task_intro?></textarea>
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
  var laydate = new Object();
  var layedit = new Object();

  //  使用Layui
  layui.use(['layer', 'form', 'laydate', 'layedit'], function(){
    layer = layui.layer;
    form = layui.form();
    laydate = layui.laydate;
    layedit = layui.layedit;

    layedit.set({
      uploadImage: {
        url: 'http://www.fnying.com/upload/upload_image.php' //接口url
        ,type: '' //默认post
      }
    });
    edit_index = layedit.build('ct_task_intro_edit');
  });

  // 关闭按钮点击事件
  $("#btn_close").click(function() {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
  });

  // 确认按钮点击事件
  $("#btn_ok").click(function() {
    var task_name = $("#ct_task_name").val().trim();
    if (task_name.length == 0) {
      parent.layer.msg('请输入任务标题');
      return;
    }

    var task_intro = layedit.getContent(edit_index).trim();
    if (task_intro.length == 0) {
      parent.layer.msg('请输入任务内容');
      return;
    }

    var limit_time = $("#ct_limit_time").val().trim();
    if (limit_time.length == 0) {
      parent.layer.msg('请输入截止期限');
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

    // 责任担当
    row['respo_name'] = $("#ct_respo_id option:selected").text();
    // 监督检查
    row['check_name'] = $("#ct_check_id option:selected").text();
    // 任务内容
    row['task_intro'] = layedit.getContent(edit_index);
    // 对外公开
    row['is_public'] = $("input[name='is_public']:checked").val();

    $.ajax({
        url: '/staff/api/task.php',
        type: 'get',
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