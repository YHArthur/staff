<?php
require_once '../inc/common.php';
require_once '../db/id_relation.php';
require_once '../db/task.php';
require_once '../db/action.php';

php_begin();

// 禁止游客访问
exit_guest();

// 未设置任务ID
if (!isset($_GET["task_id"]))
  exit('task_id is empty');

$my_id = $_SESSION['staff_id'];                           // 当前用户ID
$task_id = get_arg_str('GET', 'task_id');                 // 任务ID

// 取得指定任务ID的任务记录
$task = get_task($task_id);
if (!$task)
  exit('task_id does not exist');

// 取得所有任务行动人列表
$action_ids = get_relation_ids('task_action', $task_id);
// 行动人包含当前用户ID
$task_action = '0';
if (in_array($my_id, $action_ids))
  $task_action = '1';

$respo_id = $task['respo_id'];                            // 责任人ID
$respo_name = $task['respo_name'];                        // 责任人

// 当前用户是否责任人
$my_action = '0';
if ($respo_id == $my_id)
  $my_action = '1';

// 取得任务所有行动责任人列表
$action_members = get_action_respo_list_by_task($task_id);
$member_tabs = "";
$member_panes = "";
$respo_member_has_action = false;

// 循环任务所有行动责任人列表
foreach ($action_members as $rec) {
  // 找到任务责任人的未完成行动条数
  if ($rec['staff_id'] == $respo_id) {
    $respo_member_has_action = true;
    $member_tabs = '<li class="active"><a href="#' . $rec['staff_id'] . '" data-toggle="tab">' . $rec['staff_name'] . '&nbsp;&nbsp;<span class="badge">' . $rec['action_total'] . '</span></a></li>' . $member_tabs;
    $member_panes = get_action_pane($task_id, $respo_id, ' active') . $member_panes;
  } else {
    $member_tabs .= '<li><a href="#' . $rec['staff_id'] . '" data-toggle="tab">' . $rec['staff_name'] . '&nbsp;&nbsp;<span class="badge">' . $rec['action_total'] . '</span></a></li>';
    $member_panes .= get_action_pane($task_id, $rec['staff_id'], '');
  }
}

// 行动责任人列表为空或未找到不包含任务责任人
if ($member_tabs == "" || !$respo_member_has_action) {
  $member_tabs = '<li class="active"><a href="#' . $respo_id . '" data-toggle="tab">' . $respo_name . '</a></li>' . $member_tabs;
  $member_panes = get_action_pane($task_id, $respo_id, ' active') . $member_panes;
}

//  取得行动列表面板
function get_action_pane($task_id, $respo_id, $active_class) {

  $rtn_str = '';

  // 取得任务行动责任人相关行动列表
  $actions = get_action_list_by_task_respo($task_id, $respo_id);
  foreach ($actions as $rec) {
    // 未完成
    if ($rec['is_closed'] == 0) {
      $rtn_str .= "\n    " . '<li style="padding: 10px 15px;">';
      $rtn_str .= "\n      " . '<button id="' . $rec['action_id'] . '" class="btn btn-action btn-primary"><i class="glyphicon glyphicon-ok"></i></button>';
      $rtn_str .= "\n        " . $rec['action_title'];
      $rtn_str .= "\n    " . '</li>';
    } else {
      // 已完成
      $rtn_str .= "\n    " . '<li style="padding: 10px 15px;">';
      $rtn_str .= "\n      " . '<button id="' . $rec['action_id'] . '" class="btn btn-action btn-success"><i class="glyphicon glyphicon-glass"></i></button>';
      $rtn_str .= "\n        " . $rec['action_title'];
      $rtn_str .= "\n      " . '<small class="text-muted">' . $rec['closed_time'] . '</small>';
      $rtn_str .= "\n    " . '</li>';
    }
  }

  if ($rtn_str == '')
    $rtn_str = '没有行动数据';

  return '<div class="tab-pane' . $active_class . '" id="' . $respo_id . '"><ul class="nav">' . $rtn_str . '</ul></div>';
}
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>任务行动列表</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">

    <fieldset class="layui-elem-field">
      <div class="layui-field-box">
        <button id="add_action_btn" class="layui-btn layui-btn-normal"><i class="glyphicon glyphicon-plus"></i> 添加行动</button>
        <button id="join_task_btn" class="layui-btn layui-btn-warning"><i class="glyphicon glyphicon-copy"></i> 加入任务</button>
        <button id="left_task_btn" class="layui-btn layui-btn-danger"><i class="glyphicon glyphicon-paste"></i> 离开任务</button>
        <input type="hidden" name="task_id" id="task_id" value="<?php echo $task_id?>">
        <input type="hidden" name="task_action" id="task_action" value="<?php echo $task_action?>">
        <input type="hidden" name="my_action" id="my_action" value="<?php echo $my_action?>">
      </div>
    </fieldset>

    <div id="action_list">
      <ul class="nav nav-tabs">
        <?php echo $member_tabs;?>
      </ul>

      <div class="tab-content" style="padding-top: 10px; font-size:15px; color:#F06;">
        <?php echo $member_panes;?>
      </div>
    </div>
  </div>

  <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="http://libs.baidu.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="../js/layer/layer.js"></script>

  <script type="text/javascript">
  $(function () {
    // 按钮初始化
    var ta = $("#task_action").val();
    var ma = $("#my_action").val();
    btnChange(ta, ma);

    // 按钮初始化
    function btnChange(ta, ma) {
      $("#add_action_btn").hide();
      $("#join_task_btn").hide();
      $("#left_task_btn").hide();
      if (ta == "1") {
        // 显示添加行动按钮
        $("#add_action_btn").show();
        // 显示离开按钮
        if (ma != "1")
          $("#left_task_btn").show();
      } else {
        // 显示加入任务按钮
        $("#join_task_btn").show();
      }
    }

    // 添加行动点击事件
    $("#add_action_btn").click(function() {
      var task_id = $("#task_id").val();
      parent.layer.open({
          type: 2,
          title: '添加行动',
          shadeClose: true,
          shade: 0.8,
          area: ['800px', '800px'],
          content: 'dialog/action.php?task_id=' + task_id
      });
    });

    // 加入任务点击事件
    $("#join_task_btn").click(function() {
      var task_id = $("#task_id").val();
      var ma = $("#my_action").val();
      var row = {};
      row['type'] = 'task_action';
      row['mid'] = task_id;
      $.ajax({
          url: '/staff/api/add_relation_id.php',
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
              // 按钮变更
              btnChange('1', ma);
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

    // 离开任务点击事件
    $("#left_task_btn").click(function() {
      var task_id = $("#task_id").val();
      var ma = $("#my_action").val();
      var row = {};
      row['type'] = 'task_action';
      row['mid'] = task_id;
      $.ajax({
          url: '/staff/api/del_relation_id.php',
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
              // 按钮变更
              btnChange('0', ma);
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

    // 行动按钮点击事件
    $(".btn-action").click(function() {
      var action_id = $(this).attr("id");
      var newWindow = window.open("_blank");
      newWindow.location = "http://www.fnying.com/staff/wx/action.php?id="+action_id;
    });

  });

  </script>

</body>
</html>