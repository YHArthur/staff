<?php
require_once '../inc/common.php';
require_once '../db/fin_cycle_cost.php';
require_once '../db/staff_main.php';
require_once '../db/fin_sub_cn.php';

// 禁止游客访问
exit_guest();

// 未设置周期支出费用ID(默认添加)
if (!isset($_GET["id"])) {

  $cost_id = '';                                  // 周期支出费用ID
  $staff_id = '';                                 // 员工ID
  $staff_name = '';                               // 员工姓名
  $cost_amount = 0;                               // 支出金额
  $from_date = date('Y-m-d');                     // 开始时间
  $to_date = date('Y-m-d');                       // 结束时间
  $sub_id = '0';                                  // 会计科目ID
  $cost_memo = '';                                // 支出摘要
  $month_gap = 1;                                 // 间隔月
  $term_day = 0;                                  // 支付日
  $is_fix = 1;                                    // 是否固定
  $is_void = 0;                                   // 是否无效

} else {

  $cost_id = $_GET["id"];                         // 周期支出费用ID
  // 取得指定周期支出费用ID的经费记录
  $cost = get_fin_cycle_cost($cost_id);
  if (!$cost)
    exit('cost id is not exist');

  $cost_id = $cost['cost_id'];                    // 周期支出费用ID
  $staff_id = $cost['staff_id'];                  // 员工ID
  $staff_name = $cost['staff_name'];              // 员工姓名
  $cost_amount = $cost['cost_amount'] / 100.0;    // 支出金额
  $from_date = $cost['from_date'];                // 开始时间
  $to_date = $cost['to_date'];                    // 结束时间
  $sub_id = $cost['sub_id'];                      // 会计科目ID
  $cost_memo = $cost['cost_memo'];                // 支出摘要
  $month_gap = $cost['month_gap'];                // 间隔月
  $term_day = $cost['term_day'];                  // 支付日
  $is_fix = $cost['is_fix'];                      // 是否固定
  $is_void = $cost['is_void'];                    // 是否无效
}

// 科目选项
$sub_rows = get_sub_cn_list();
$sub_list = get_sub_cn_list_select($sub_rows);
$sub_list['0'] = '请选择科目';
$sub_option = get_select_option($sub_list, $sub_id);

// 员工选项
$my_id = $_SESSION['staff_id'];
$staff_rows = get_staff_list();
$staff_list = get_staff_list_select($my_id, $staff_rows);
$staff_list['0'] = '请选择员工';
$staff_option = get_select_option($staff_list, $staff_id);

// 是否固定选项
$fix_list = array('1'=>'固定', '0'=>'平均');
$fix_input = get_radio_input('is_fix', $fix_list, $is_fix);

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

  <title>周期支出管理</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">
    <div class="modal-body">
      <form id="ct_form" class="layui-form">

          <input type="hidden" name="cost_id" id="cost_id" value="<?php echo $cost_id?>">

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_cost_memo" class="layui-form-label">支出摘要</label>
              <div class="layui-input-inline" style="width: 190px;">
                <input type="text" class="layui-input" id="ct_cost_memo" name="cost_memo" required lay-verify="required" autocomplete="off"  value="<?php echo $cost_memo?>" placeholder="支出摘要">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_staff_id" class="layui-form-label">支出科目</label>
              <div class="layui-input-inline" style="width: 190px;">
                <select name="sub_id" id="ct_sub_id">
                <?php echo $sub_option?>
                </select>
              </div>
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
              <label for="ct_cost_amount" class="layui-form-label">支出金额</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_cost_amount" name="cost_amount" required lay-verify="required" autocomplete="off"  value="<?php echo $cost_amount?>" placeholder="支出金额">
              </div>
            </div>

          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_from_date" class="layui-form-label" style="width: 110px;">开始日期</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_from_date" name="from_date" value="<?php echo $from_date?>" placeholder="开始日期" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD'})">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_to_date" class="layui-form-label" style="width: 110px;">结束日期</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_to_date" name="to_date" value="<?php echo $to_date?>" placeholder="结束日期" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD'})">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_month_gap" class="layui-form-label">间隔月</label>
              <div class="layui-input-inline" style="width: 190px;">
                <input type="number" class="layui-input" name="month_gap" id="ct_month_gap" value="<?php echo $month_gap?>" placeholder="间隔月">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_term_day" class="layui-form-label">支付日</label>
              <div class="layui-input-inline" style="width: 190px;">
                <input type="number" class="layui-input" name="term_day" id="ct_term_day" value="<?php echo $term_day?>" placeholder="支付日">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_is_fix" class="layui-form-label">是否固定</label>
              <div class="layui-input-inline" style="width: 190px">
                <?php echo $fix_input?>
              </div>
            </div>

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

  // 支出金额
  var cost_amount = $("#ct_cost_amount").val() * 1;
  // 支出金额变更
  $("#ct_cost_amount").val(cost_amount.toFixed(2));
    
  // 关闭按钮点击事件
  $("#btn_close").click(function() {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
  });

  // 确认按钮点击事件
  $("#btn_ok").click(function() {
    var cost_memo = $("#ct_cost_memo").val().trim();
    if (cost_memo.length == 0) {
      parent.layer.msg('请输入支出摘要');
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

    // 支出金额
    if (row['cost_amount'] == 0) {
      parent.layer.msg('支出金额不能为0');
      return;
    }

    // 员工姓名
    row['staff_name'] = $("#ct_staff_id option:selected").text();
    // 是否固定
    row['is_fix'] = $("input[name='is_fix']:checked").val();
    // 是否无效
    row['is_void'] = $("input[name='is_void']:checked").val();

    $.ajax({
      url: '/staff/api/fin_cycle_cost.php',
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