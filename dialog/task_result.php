<?php
require_once '../inc/common.php';
require_once '../db/staff_main.php';
require_once '../db/task.php';

// 禁止游客访问
exit_guest();

$my_id = $_SESSION['staff_id'];
$my_name = $_SESSION['staff_name'];

// 未设置任务ID(默认添加)
if (!isset($_GET["task_id"]))
  exit('task id is not exist');

$task_id = $_GET["task_id"];                  // 任务ID
// 取得指定任务ID的任务记录
$task = get_task($task_id);
if (!$task)
  exit('task id does not exist');

if ($my_id != $task['owner_id'] && $my_id != $task['respo_id'] && $my_id != $task['check_id'])
  exit('no permit');

$task_name = $task['task_name'];                // 任务标题
$task_intro = $task['task_intro'];              // 任务内容
$result_memo = $task['result_memo'];            // 结果描述
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>任务结果更新</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">
    <div class="modal-body">
      <legend><?php echo $task_name?></legend>
      <blockquote class="layui-elem-quote"><?php echo $task_intro?></blockquote>
      <hr>
      <form id="ct_form" class="layui-form">
          <input type="hidden" name="task_id" id="task_id" value="<?php echo $task_id?>">

          <div class="layui-form-item">
              <label for="ct_result_memo_edit" class="layui-form-label">进展状况</label>
              <div class="layui-input-block">
                <textarea class="layui-textarea" id="ct_result_memo_edit" name="result_memo_edit" placeholder="进展状况"><?php echo $result_memo?></textarea>
              </div>
          </div>

          <div class="layui-form-item">
            <div class="col-xs-6"></div>
            <div class="col-xs-2" class="layui-input-block">
            <?php if ($my_id == $task['owner_id'] || $my_id == $task['check_id']) { ?>
              <input type="checkbox" id="is_closed" value="1" title="完成">
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

  });

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

    // 结果描述
    row['result_memo'] = layedit.getContent(edit_index);
    // 是否完成
    row['is_closed'] = 0;
    var obj = $("#is_closed");
    if (obj && obj.is(':checked'))
      row['is_closed'] = 1;
    
    $.ajax({
        url: '/staff/api/task_result.php',
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