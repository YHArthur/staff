<?php
require_once '../inc/common.php';
require_once '../db/task.php';
require_once '../db/staff_main.php';

// 禁止游客访问
exit_guest();

$my_id = $_SESSION['staff_id'];

// 未设置任务ID(默认添加)
if (!isset($_GET["id"])) {

  $task_id = '';                                  // 任务ID
  $task_name = '';                                // 任务
  $task_intro = '';                               // 任务描述
  $owner_id = $my_id;                             // 创建人ID
  $respo_id = $my_id;                             // 责任人ID
  $check_id = $my_id;                             // 监管人ID

  $is_limit = 1;                                  // 是否有期限
  $is_cycle = 0;                                  // 是否有周期
  $is_self = 0;                                   // 是否个人任务
  $task_level = 2;                                // 任务等级
  $task_value = 0;                                // 任务价值
  $task_perc = 0;                                 // 任务进度
  $is_closed = 0;                                 // 是否完成
  // 本周五
  $current_friday = strtotime('Sunday -2 day', strtotime(date('Y-m-d')));
  // 距离这周五不足两天半，则下周五
  if (($current_friday - time()) < 60*60*60)
    $current_friday += 60*60*24*7;
  // 默认任务期限（这周五或下周五）
  $limit_time = date('Y-m-d', $current_friday) . ' 18:00:00';
  // 周期时间默认一个月
  $cycle_time = 30 * 24 * 60 * 60;
  $is_self = 0;                                   // 是否个人任务

} else {

  $task_id = $_GET["id"];                         // 任务ID
  // 取得指定任务ID的任务记录
  $task = get_task($task_id);
  if (!$task)
    exit('task id is not exist');

  $task_id = $task['task_id'];                    // 任务ID
  $task_name = $task['task_name'];                // 任务
  $task_intro = $task['task_intro'];              // 任务描述
  $owner_id = $task['owner_id'];                  // 创建人ID
  $respo_id = $task['respo_id'];                  // 责任人ID
  $check_id = $task['check_id'];                  // 监管人ID

  $is_limit = $task['is_limit'];                  // 是否有期限
  $is_cycle = $task['is_cycle'];                  // 是否有周期
  $is_self = $task['is_self'];                    // 是否个人任务
  $task_level = $task['task_level'];              // 任务等级
  $task_value = $task['task_value'];              // 任务价值
  $task_perc = $task['task_perc'];                // 任务进度
  $is_closed = $task['is_closed'];                // 是否完成
  $limit_time = $task['limit_time'];              // 任务期限
  $cycle_time = $task['cycle_time_stamp'];        // 周期时间

  // 将数据库存放的用户输入内容转换回再修改内容
  $task_intro = html_to_str($task_intro);
}

// 周期数据
$cycle_array = get_time_interval($cycle_time);
$cycle_nm = $cycle_array['nm'];
$cycle_ut = $cycle_array['ut'];

// 员工选项
$staff_rows = get_staff_list();
$staff_list = get_staff_list_select($my_id, $staff_rows);
$staff_list['0'] = '请选择员工';
$respo_option = get_select_option($staff_list, $respo_id);
$check_option = get_select_option($staff_list, $check_id);

// 是否有期限列表
$limit_list = array('0'=>'无期限', '1'=>'有期限');
$limit_input = get_radio_input('is_limit', $limit_list, $is_limit);

// 是否有周期列表
$cycle_list = array('0'=>'无周期', '1'=>'有周期');
$cycle_input = get_radio_input('is_cycle', $cycle_list, $is_cycle);

// 是否个人任务列表
$self_list = array('0'=>'公开', '1'=>'个人');
$self_input = get_radio_input('is_self', $self_list, $is_self);

// 任务等级列表
$level_list = array('0'=>'可选','1'=>'一般','2'=>'重要','3'=>'非常重要');
$level_option = get_select_option($level_list, $task_level);

// 周期单位选项列表
$cycle_unit_list = array('year'=>'年','month'=>'月','week'=>'周','day'=>'日');
$cycle_unit_option = get_select_option($cycle_unit_list, $cycle_ut);
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
              <label for="ct_respo_id" class="layui-form-label">责任担当</label>
              <div class="layui-input-inline" style="width: 190px;">
                <select name="respo_id" id="ct_respo_id">
                <?php echo $respo_option?>
                </select>
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_is_limit" class="layui-form-label">期限有无</label>
              <div class="layui-input-inline" style="width: 190px;">
                <?php echo $limit_input?>
              </div>
            </div>

            <div class="layui-inline" id="div_limit_time">
              <label for="ct_limit_time" class="layui-form-label" style="width: 110px;">截止时间</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_limit_time" name="limit_time" value="<?php echo $limit_time?>" placeholder="截止时间" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD hh:mm:ss'})">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_is_cycle" class="layui-form-label">周期有无</label>
              <div class="layui-input-inline" style="width: 190px;">
                <?php echo $cycle_input?>
              </div>
            </div>

            <div class="layui-inline div_cycle_time" style="margin-right:0px;">
              <label for="ct_cycle_time" class="layui-form-label" style="width: 110px;">周期时间</label>
              <div class="layui-input-inline" style="width:75px; margin-right: 0px;">
                <input type="number" class="layui-input" id="ct_cycle_nm" name="cycle_nm" value="<?php echo $cycle_nm?>" placeholder="周期时间">
              </div>
            </div>

            <div class="layui-inline div_cycle_time">
              <label for="ct_cycle_unit" class="layui-form-label" style="width:0px;">-</label>
              <div class="layui-input-inline" style="width:80px">
                <select name="cycle_unit" id="ct_cycle_unit">
                <?php echo $cycle_unit_option?>
                </select>
              </div>
            </div>

          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_is_self" class="layui-form-label">是否公开</label>
              <div class="layui-input-inline" style="width: 190px">
                <?php echo $self_input?>
              </div>
            </div>

            <div class="layui-inline" id="div_check">
              <label for="ct_check_id" class="layui-form-label">监督检查</label>
              <div class="layui-input-inline" style="width: 190px;">
                <select name="check_id" id="ct_check_id">
                <?php echo $check_option?>
                </select>
              </div>
            </div>
          </div>

          <!--
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
          -->

          <div class="layui-form-item">
              <label for="ct_task_name" class="layui-form-label">任务标题</label>
              <div class="layui-input-block">
                <input type="text" class="layui-input" id="ct_task_name" name="task_name" required lay-verify="required" autocomplete="off"  value="<?php echo $task_name?>" placeholder="任务标题（30字以内）">
              </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_task_intro_edit" class="layui-form-label">任务描述</label>
              <div class="layui-input-block">
                <textarea class="layui-textarea" id="ct_task_intro_edit" name="task_intro_edit" placeholder="任务描述"><?php echo $task_intro?></textarea>
              </div>
          </div>

          <div class="layui-form-item">
            <div class="col-xs-6"></div>
            <div class="col-xs-2" class="layui-input-block">
            <?php if ($is_closed == '1' && ($my_id == $owner_id || $my_id == $check_id)) { ?>
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
  var laydate = new Object();
  var layedit = new Object();

  $(function () {
    var val;
    // 期限有无初始化
    val = $("input[name='is_limit']:checked").val();
    limitChange(val);
    // 周期有无初始化
    val = $("input[name='is_cycle']:checked").val();
    cycleChange(val);
    // 是否公开初始化
    val = $("input[name='is_self']:checked").val();
    selfChange(val);
  });

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

    // 期限有无变更事件
    form.on('radio(radio_is_limit)', function(data) {
      limitChange(data.value);
    });

    // 周期有无变更事件
    form.on('radio(radio_is_cycle)', function(data) {
      cycleChange(data.value);
    });

    // 是否公开变更事件
    form.on('radio(radio_is_self)', function(data) {
      selfChange(data.value);
    });
  });

  // 期限有无处理
  function limitChange(val) {
    // 有期限
    if (val == "1") {
      // 显示任务期限
      $("#div_limit_time").show();
    } else {
      // 隐藏任务期限输入框
      $("#div_limit_time").hide();
    }
  }

  // 周期有无处理
  function cycleChange(val) {
    // 有周期
    if (val == "1") {
      // 显示周期时间
      $(".div_cycle_time").show();
    } else {
      // 隐藏周期时间输入框
      $(".div_cycle_time").hide();
    }
  }

  // 是否公开处理
  function selfChange(val) {
    // 公开
    if (val == "0") {
      // 显示监督检查
      $("#div_check").show();
    } else {
      // 隐藏监督检查下拉框
      $("#div_check").hide();
    }
  }

  // 时间戳转日期
  function timestampToTime(timestamp) {
    var date = new Date(timestamp);
    Y = date.getFullYear() + '-';
    M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
    D = date.getDate() + ' ';
    h = date.getHours() + ':';
    m = date.getMinutes() + ':';
    s = date.getSeconds();
    return Y+M+D+h+m+s;
  }

  // 有效日期时间判断
  function is_valid_datetime(datetime) {
    // 当前时间戳
    var t0= new Date().getTime();
    // 字符串转时间戳
    var t1 = new Date(datetime).getTime();
    // 时间戳转日期后再转时间戳
    var t2 = new Date(timestampToTime(t1)).getTime();
    return (t1 > t0 && t1 == t2);
  }

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
      parent.layer.msg('请输入任务描述');
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
    // 期限有无
    row['is_limit'] = $("input[name='is_limit']:checked").val();
    // 周期有无
    row['is_cycle'] = $("input[name='is_cycle']:checked").val();
    // 是否公开
    row['is_self'] = $("input[name='is_self']:checked").val();
    // 任务描述
    row['task_intro'] = layedit.getContent(edit_index);
    // 是否完成
    row['is_closed'] = 0;
    var obj = $("#is_closed");
    if (obj && obj.is(':checked'))
      row['is_closed'] = 1;

    // 截止期限
    if (row['is_limit'] == '1' && is_valid_datetime(row['limit_time']) == 0) {
      parent.layer.msg('请输入正确的截止期限');
      return;
    }

    // 周期时间
    if (row['is_cycle'] == '1' && parseInt(row['cycle_nm']) <= 0) {
      parent.layer.msg('请输入正确的周期时间');
      return;
    }

    $.ajax({
        url: '/staff/api/task.php',
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