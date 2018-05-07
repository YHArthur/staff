<?php
require_once '../inc/common.php';
require_once '../db/permit.php';
require_once '../db/staff_main.php';
require_once '../db/staff_permit.php';

// 禁止游客访问
exit_guest();

// 未设置员工ID和权限ID(默认添加)
if (!isset($_GET["staff_id"]) || !isset($_GET["pm_id"])) {
  
  $staff_id = '0';                                            // 员工ID
  $pm_id = 0;                                                 // 权限ID
  $from_date = date('Y-m-d H:i:s');                           // 开始时间
  $to_date = date('Y-m-d H:i:s', strtotime("+90 days"));      // 结束时间
  $is_void = '0';                                             // 是否无效
  
} else {
  
  $staff_id = get_arg_str('GET', 'staff_id');                 // 员工ID
  $pm_id = get_arg_str('GET', 'pm_id');                       // 权限ID
  $pm_id = intval($pm_id);
  
  // 取得指定员工ID权限ID的员工权限记录
  $row = get_staff_permit($staff_id, $pm_id);
  if (!$row)
    exit('staff permit is not exist');

  $from_date = $row['from_date'];                             // 开始时间
  $to_date = $row['to_date'];                                 // 结束时间
  $is_void = $row['is_void'];                                 // 是否无效
}

// 员工选项
$my_id = $_SESSION['staff_id'];
$staff_rows = get_staff_list();
$staff_list = get_staff_list_select_without_me($my_id, $staff_rows);
$staff_list['0'] = '请选择员工';
$staff_option = get_select_option($staff_list, $staff_id);

// 权限选项
$pm_list = get_permit_list();
$pm_list[0] = '请选择权限';
$pm_option = get_select_option($pm_list, $pm_id);


// 是否无效选项
$void_list = array('1'=>'无效', '0'=>'有效');
$void_input = get_radio_input('is_void', $void_list, $is_void);
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>员工权限设定</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">
    <div class="modal-body">
      <form id="ct_form" class="layui-form">

          <div class="layui-form-item">
              <label for="ct_staff_id" class="layui-form-label" style="width: 150px;">设定员工</label>
              <div class="layui-input-inline">
                <select name="staff_id" id="ct_staff_id">
                <?php echo $staff_option?>
                </select>
              </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_pm_id" class="layui-form-label" style="width: 150px;">设定权限</label>
              <div class="layui-input-inline">
                <select name="pm_id" id="ct_pm_id">
                <?php echo $pm_option?>
                </select>
              </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_from_date" class="layui-form-label" style="width: 150px;">开始时间</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_from_date" name="from_date" value="<?php echo $from_date?>" placeholder="开始时间" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD hh:mm:ss'})">
              </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_to_date" class="layui-form-label" style="width: 150px;">结束时间</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_to_date" name="to_date" value="<?php echo $to_date?>" placeholder="结束时间" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD hh:mm:ss'})">
              </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_is_void" class="layui-form-label" style="width: 150px;">是否有效</label>
              <div class="layui-input-inline">
                <?php echo $void_input?>
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="col-xs-3"></div>
            <div class="col-xs-3">
              <button type="button" id="btn_close" class="btn btn-default btn-block">关闭</button>
            </div>
            <div class="col-xs-3">
              <button type="button" id="btn_ok" class="btn btn-primary btn-block submit">确认</button>
            </div>
            <div class="col-xs-3"></div>
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

  //  使用Layui
  layui.use(['layer', 'form', 'laydate'], function(){
    layer = layui.layer;
    form = layui.form();
    laydate = layui.laydate;
  });

  // 关闭按钮点击事件
  $("#btn_close").click(function() {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
  });

  // 确认按钮点击事件
  $("#btn_ok").click(function() {
    var staff_id = $("#ct_staff_id").val().trim();
    if (staff_id == 0) {
      parent.layer.msg('请设定员工');
      return;
    }

    var pm_id = $("#ct_pm_id").val().trim();
    if (pm_id == 0) {
      parent.layer.msg('请设定权限');
      return;
    }

    var from_date = $("#ct_from_date").val().trim();
    if (from_date.length == 0) {
      parent.layer.msg('请输入开始时间');
      return;
    }

    var to_date = $("#ct_to_date").val().trim();
    if (to_date.length == 0) {
      parent.layer.msg('请输入结束时间');
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

    // 员工姓名
    row['staff_name'] = $("#ct_staff_id option:selected").text();
    // 权限名称
    row['pm_name'] = $("#ct_pm_id option:selected").text();    
    // 是否无效
    row['is_void'] = $("input[name='is_void']:checked").val();

    $.ajax({
        url: '/staff/api/staff_permit.php',
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