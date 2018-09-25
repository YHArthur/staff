<?php
require_once '../inc/common.php';
require_once '../db/fin_staff_salary.php';
require_once '../db/staff_main.php';

// 禁止游客访问
exit_guest();

// 未设置员工ID(默认添加)
if (!isset($_GET["id"])) {

  $staff_id = '';                                                           // 员工ID
  $pre_tax_salary = 10000.00;                                               // 税前月薪
  $base_salary = 5000.00;                                                   // 最低基本工资
  $effic_salary = 4000.00;                                                  // 绩效工资
  $pension_base = $base_salary;                                             // 社保基数
  $fund_base = $base_salary;                                                // 公积金基数
  $office_subsidy = $pre_tax_salary * 1.05 - $base_salary - $effic_salary;  // 办公经费
  $from_date = date('Y-m-d');                                               // 入职时间
  $to_date = '0000-00-00 00:00:00';                                         // 离职时间
  $is_void = 0;                                                             // 是否无效

} else {

  $staff_id = $_GET["id"];                                                  // 员工ID
  // 取得指定员工ID的员工工资基数
  $row = get_fin_staff_salary($staff_id);
  if (!$row)
    exit('staff_id id is not exist');
  $pre_tax_salary = $row['pre_tax_salary'] / 100.0;                         // 税前月薪
  $base_salary = $row['base_salary'] / 100.0;                               // 基本工资
  $effic_salary = $row['effic_salary'] / 100.0;                             // 绩效工资
  $pension_base = $row['pension_base'] / 100.0;                             // 社保基数
  $fund_base = $row['fund_base'] / 100.0;                                   // 公积金基数
  $office_subsidy = $row['office_subsidy'] / 100.0;                         // 办公经费
  $from_date = $row['from_date'];                                           // 入职时间
  $to_date = $row['to_date'];                                               // 离职时间
  $is_void = $row['is_void'];                                               // 是否无效
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

  <title>员工工资基数设定</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">
    <div class="modal-body">
      <form id="ct_form" class="layui-form">

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_staff_id" class="layui-form-label">员工姓名</label>
              <div class="layui-input-inline" style="width: 190px;">
                <select name="staff_id" id="ct_staff_id">
                <?php echo $staff_option?>
                </select>
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
            <div class="layui-inline">
              <label for="ct_from_date" class="layui-form-label" style="width: 110px;">开始日期</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_from_date" name="from_date" value="<?php echo $from_date?>" placeholder="开始日期" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD'})">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_to_date" class="layui-form-label" style="width: 110px;">结束日期</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_to_date" name="to_date" value="<?php echo $to_date?>" placeholder="结束时间" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD'})">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_pre_tax_salary" class="layui-form-label">税前月薪</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_pre_tax_salary" name="pre_tax_salary" required lay-verify="required" autocomplete="off" value="<?php echo $pre_tax_salary?>" placeholder="0.00">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_office_subsidy" class="layui-form-label">办公经费</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_office_subsidy" name="office_subsidy" required lay-verify="required" autocomplete="off" value="<?php echo $office_subsidy?>" placeholder="0.00">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_base_salary" class="layui-form-label">基本工资</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_base_salary" name="base_salary" required lay-verify="required" autocomplete="off" value="<?php echo $base_salary?>" placeholder="0.00">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_effic_salary" class="layui-form-label">绩效工资</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_effic_salary" name="effic_salary" required lay-verify="required" autocomplete="off" value="<?php echo $effic_salary?>" placeholder="0.00">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_pension_base" class="layui-form-label">社保基数</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_pension_base" name="pension_base" required lay-verify="required" autocomplete="off" value="<?php echo $pension_base?>" placeholder="0.00">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_fund_base" class="layui-form-label">公积金基数</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_fund_base" name="fund_base" required lay-verify="required" autocomplete="off" value="<?php echo $fund_base?>" placeholder="0.00">
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

  // 确认按钮点击事件
  $("#btn_ok").click(function() {
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

    // 税前月薪
    if (row['pre_tax_salary'] <= 0) {
      parent.layer.msg('请设定税前月薪');
      return;
    }

    // 基本工资
    if (row['base_salary'] <= 0) {
      parent.layer.msg('请设定基本工资');
      return;
    }

    // 基本工资
    if (row['base_salary'] < 4275) {
      parent.layer.msg('基本工资不能少于4275');
      return;
    }

    // 员工姓名
    row['staff_name'] = $("#ct_staff_id option:selected").text();
    // 是否无效
    row['is_void'] = $("input[name='is_void']:checked").val();

    $.ajax({
        url: '/staff/api/staff_salary.php',
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
