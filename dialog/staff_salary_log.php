<?php
require_once '../inc/common.php';
require_once '../db/fin_staff_salary_log.php';

// 禁止游客访问
exit_guest();

$staff_id = $_GET["id"];                                                  // 员工ID
$salary_ym = $_GET["ym"];                                                 // 工资年月
// 取得指定工资年月和员工ID的员工工资发放记录
$row = get_fin_staff_salary_log($salary_ym, $staff_id);
if (!$row)
  exit('staff_id and salary_ym is not exist');

$salary_date = substr($row['salary_date'], 0, 10);                        // 支付日期
$staff_cd = $row['staff_cd'];                                             // 员工工号
$staff_name = $row['staff_name'];                                         // 员工姓名
$pre_tax_salary = $row['pre_tax_salary'] / 100.0;                         // 税前工资
$base_salary = $row['base_salary'] / 100.0;                               // 基本工资
$effic_salary = $row['effic_salary'] / 100.0;                             // 绩效工资
$pension_base = $row['pension_base'] / 100.0;                             // 社保基数
$fund_base = $row['fund_base'] / 100.0;                                   // 公积金基数
$office_subsidy = $row['office_subsidy'] / 100.0;                         // 办公经费

$pension_fee = $row['pension_fee'] / 100.0;                               // 养老保险
$medical_fee = $row['medical_fee'] / 100.0;                               // 医疗保险
$jobless_fee = $row['jobless_fee'] / 100.0;                               // 失业保险
$fund_fee = $row['fund_fee'] / 100.0;                                     // 住房公积金
$bef_tax_sum = $row['bef_tax_sum'] / 100.0;                               // 税前总额
$tax_sum = $row['tax_sum'] / 100.0;                                       // 个人所得税
$aft_tax_sum = $row['aft_tax_sum'] / 100.0;                               // 税后工资
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>员工工资发放记录设定</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">
    <div class="modal-body">
      <form id="ct_form" class="layui-form">
          <input type="hidden" name="staff_id" id="staff_id" value="<?php echo $staff_id?>">

          <div class="layui-form-item">
            <div for="ct_staff_name" class="layui-inline">
              <label class="layui-form-label">员工姓名</label>
              <div class="layui-input-inline" style="width: 190px;">
                <input type="text" class="layui-input" id="ct_staff_name" name="staff_name" value="<?php echo $staff_name?>" placeholder="员工姓名" disabled>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_staff_cd" class="layui-form-label">员工工号</label>
              <div class="layui-input-inline" style="width: 190px">
                <input type="text" class="layui-input" id="ct_staff_cd" name="staff_cd" value="<?php echo $staff_cd?>" placeholder="员工工号" disabled>
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_salary_ym" class="layui-form-label" style="width: 110px;">工资年月</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_salary_ym" name="salary_ym" value="<?php echo $salary_ym?>" placeholder="工资年月" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM'})" disabled>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_salary_date" class="layui-form-label" style="width: 110px;">支付日期</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_salary_date" name="salary_date" value="<?php echo $salary_date?>" placeholder="支付日期" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD'})">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_pre_tax_salary" class="layui-form-label">税前工资</label>
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
            <div class="layui-inline">
              <label for="ct_pension_fee" class="layui-form-label">养老保险</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_pension_fee" name="pension_fee" required lay-verify="required" autocomplete="off" value="<?php echo $pension_fee?>" placeholder="0.00">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_medical_fee" class="layui-form-label">医疗保险</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_medical_fee" name="medical_fee" required lay-verify="required" autocomplete="off" value="<?php echo $medical_fee?>" placeholder="0.00">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_jobless_fee" class="layui-form-label">失业保险</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_jobless_fee" name="jobless_fee" required lay-verify="required" autocomplete="off" value="<?php echo $jobless_fee?>" placeholder="0.00">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_fund_fee" class="layui-form-label">住房公积金</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_fund_fee" name="fund_fee" required lay-verify="required" autocomplete="off" value="<?php echo $fund_fee?>" placeholder="0.00">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_bef_tax_sum" class="layui-form-label">税前总额</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_bef_tax_sum" name="bef_tax_sum" required lay-verify="required" autocomplete="off" value="<?php echo $bef_tax_sum?>" placeholder="0.00">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_tax_sum" class="layui-form-label">个人所得税</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_tax_sum" name="tax_sum" required lay-verify="required" autocomplete="off" value="<?php echo $tax_sum?>" placeholder="0.00">
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

    $.ajax({
        url: '/staff/api/staff_salary_log.php',
        type: 'POST',
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
