<?php
require_once '../inc/common.php';
require_once '../db/staff_expense.php';
require_once '../db/staff_main.php';

// 禁止游客访问
exit_guest();

// 未设置经费ID(默认添加)
if (!isset($_GET["id"])) {

  $exp_id = '';                                   // 经费ID
  $staff_id = '';                                 // 员工ID
  $staff_name = '';                               // 员工姓名
  $exp_amount = 0;                                // 变动金额
  $from_date = date('Y-m-d') . ' 00:00:00';       // 开始时间
  $to_date = date('Y-m-d') . ' 00:00:00';         // 结束时间
  $max_count = 0;                                 // 最大变动次数
  $now_count = 0;                                 // 当前变动次数
  $exp_memo = '';                                 // 变动原因
  $is_void = 0;                                   // 是否无效

} else {

  $exp_id = $_GET["id"];                          // 经费ID
  // 取得指定经费ID的经费记录
  $expense = get_staff_expense($exp_id);
  if (!$expense)
    exit('expense id is not exist');

  $exp_id = $expense['exp_id'];                   // 经费ID
  $staff_id = $expense['staff_id'];               // 员工ID
  $staff_name = $expense['staff_name'];           // 员工姓名
  $exp_amount = $expense['exp_amount'];           // 变动金额
  $from_date = $expense['from_date'];             // 开始时间
  $to_date = $expense['to_date'];                 // 结束时间
  $max_count = $expense['max_count'];             // 最大变动次数
  $now_count = $expense['now_count'];             // 当前变动次数
  $exp_memo = $expense['exp_memo'];               // 变动原因
  $is_void = $expense['is_void'];                 // 是否无效
}

// 员工选项
$my_id = $_SESSION['staff_id'];
$staff_rows = get_staff_list();
$staff_list = get_staff_list_select($my_id, $staff_rows);
$staff_list['0'] = '请选择员工';
$staff_option = get_select_option($staff_list, $staff_id);

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

  <title>办公经费管理</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">
    <div class="modal-body">
      <form id="ct_form" class="layui-form">

          <input type="hidden" name="exp_id" id="exp_id" value="<?php echo $exp_id?>">

          <div class="layui-form-item">
              <label for="ct_exp_memo" class="layui-form-label">经费内容</label>
              <div class="layui-input-block">
                <input type="text" class="layui-input" id="ct_exp_memo" name="exp_memo" required lay-verify="required" autocomplete="off"  value="<?php echo $exp_memo?>" placeholder="经费内容">
              </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_staff_id" class="layui-form-label">相关员工</label>
              <div class="layui-input-inline" style="width: 190px;">
                <select name="staff_id" id="ct_staff_id">
                <?php echo $staff_option?>
                </select>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_exp_amount" class="layui-form-label">变动金额</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">RMB</div>
                <input type="number" class="layui-input" id="ct_exp_amount" name="exp_amount" required lay-verify="required" autocomplete="off"  value="<?php echo $exp_amount?>" placeholder="变动金额">
                <div class="input-group-addon">分</div>
              </div>
            </div>

          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_from_date" class="layui-form-label" style="width: 110px;">开始时间</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_from_date" name="from_date" value="<?php echo $from_date?>" placeholder="开始时间" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD hh:mm:ss', choose:countMax})">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_to_date" class="layui-form-label" style="width: 110px;">结束时间</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_to_date" name="to_date" value="<?php echo $to_date?>" placeholder="结束时间" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD hh:mm:ss', choose:countMax})">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_max_count" class="layui-form-label">变动次数</label>
              <div class="layui-input-inline" style="width: 190px;">
                <input type="number" class="layui-input" name="max_count" id="ct_max_count" value="<?php echo $max_count?>" placeholder="最大变动次数" disabled>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_now_count" class="layui-form-label">当前次数</label>
              <div class="layui-input-inline" style="width: 190px;">
                <input type="number" class="layui-input" name="now_count" id="ct_now_count" value="<?php echo $now_count?>" placeholder="当前变动次数">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_is_void" class="layui-form-label">是否有效</label>
              <div class="layui-input-inline" style="width: 190px">
                <?php echo $void_input?>
              </div>
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
    // 字符串转时间戳
    var t1 = new Date(datetime).getTime();
    // 时间戳转日期后再转时间戳
    var t2 = new Date(timestampToTime(t1)).getTime();
    return (t1 == t2);
  }

  // 计算最大变动次数
  function countMax() {
    var from_date = $("#ct_from_date").val();
    var to_date = $("#ct_to_date").val();
    if (is_valid_datetime(from_date) && is_valid_datetime(to_date)) {
      var f_time = new Date(from_date.replace(/-/g, "/"));
      var t_time = new Date(to_date.replace(/-/g, "/"));
      // 相差日期计算
      var diff_day = parseInt((t_time.getTime() - f_time.getTime()) / (1000 * 3600 * 24 * 30));
      if (diff_day >= 0) {
        $("#ct_max_count").val(diff_day);
      } else {
        $("#ct_max_count").val(0);
      }
    }
  }

  // 确认按钮点击事件
  $("#btn_ok").click(function() {
    var exp_memo = $("#ct_exp_memo").val().trim();
    if (exp_memo.length == 0) {
      parent.layer.msg('请输入经费内容');
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

    // 员工ID
    if (row['staff_id'] == 0) {
      parent.layer.msg('请选择员工');
      return;
    }

    // 变动金额
    if (row['exp_amount'] == 0) {
      parent.layer.msg('变动金额不能为0');
      return;
    }

    // 变动次数
    if (row['max_count'] == 0) {
      parent.layer.msg('变动次数不能为0');
      return;
    }

    // 员工姓名
    row['staff_name'] = $("#ct_staff_id option:selected").text();
    // 是否无效
    row['is_void'] = $("input[name='is_void']:checked").val();

    $.ajax({
        url: '/staff/api/staff_expense.php',
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