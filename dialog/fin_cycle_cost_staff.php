<?php
require_once '../inc/common.php';
require_once '../db/fin_cycle_cost.php';
require_once '../db/staff_main.php';

// 禁止游客访问
exit_guest();

$tax_salary = 10000.00;                                  // 默认税前工资
$base_salary = 4000.00;                                  // 最低基本工资

$from_date = date('Y-m-d');                              // 支付开始日
$to_date = date('Y-03-31', strtotime("1 year"));         // 支付截止日

$pay_level = 1;                                          // 默认缴费档次

// 员工选项
$my_id = $_SESSION['staff_id'];
$staff_rows = get_staff_list();
$staff_list = get_staff_list_select($my_id, $staff_rows);
$staff_list['0'] = '请选择员工';
$staff_option = get_select_option($staff_list, $my_id);

// 缴费档次选项
$pay_list = array('1'=>'低', '2'=>'中', '3'=>'高');
$pay_input = get_radio_input('pay_level', $pay_list, $pay_level);
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>新员工周期支出添加</title>

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
              <label for="ct_pay_level" class="layui-form-label">缴费档次</label>
              <div class="layui-input-inline" style="width: 200px;">
                <?php echo $pay_input?>
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
              <label for="ct_tax_salary" class="layui-form-label">税前月薪</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_tax_salary" name="tax_salary" required lay-verify="required" autocomplete="off" value="<?php echo $tax_salary?>" placeholder="0.00">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_office_subsidy" class="layui-form-label">办公津贴</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_office_subsidy" name="office_subsidy" required lay-verify="required" autocomplete="off" value="0.00" placeholder="0.00">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_base_salary" class="layui-form-label">基本工资</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_base_salary" name="base_salary" required lay-verify="required" autocomplete="off" value="<?php echo $base_salary?>" placeholder="0.00" disabled>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_effic_salary" class="layui-form-label">绩效工资</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_effic_salary" name="effic_salary" required lay-verify="required" autocomplete="off" value="0.00" placeholder="0.00" disabled>
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline" style="width: 25px;"></div>
            <div class="layui-inline" style="width: 580px;">
              <table class="layui-table">
                <colgroup>
                  <col width="100">
                  <col width="180">
                  <col width="150">
                  <col>
                </colgroup>
                <thead>
                  <tr>
                    <th class="c" colspan="2">五险一金缴费项目</th>
                    <th class="c">单位缴纳</th>
                    <th class="c">个人缴纳</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td rowspan="5" class="c">五险</td>
                    <td>养老保险 (20%, 8%)</td>
                    <td class="r" id="com_pension_fee"></td>
                    <td class="r" id="per_pension_fee"></td>
                  </tr>
                  <tr>
                    <td>医疗保险 (9.5%, 2%)</td>
                    <td class="r" id="com_medical_fee"></td>
                    <td class="r" id="per_medical_fee"></td>
                  </tr>
                  <tr>
                    <td>失业保险 (0.5%, 0.5%)</td>
                    <td class="r" id="com_jobless_fee"></td>
                    <td class="r" id="per_jobless_fee"></td>
                  </tr>
                  <tr>
                    <td>工伤保险 (0.23%)</td>
                    <td class="r" id="com_injury_fee"></td>
                    <td class="r">-</td>
                  </tr>
                  <tr>
                    <td>生育保险 (1%)</td>
                    <td class="r" id="com_bear_fee"></td>
                    <td class="r">-</td>
                  </tr>
                  <tr>
                    <td class="c">一金</td>
                    <td>住房公积金 (7%, 7%)</td>
                    <td class="r" id="com_housing_fund_fee"></td>
                    <td class="r" id="per_housing_fund_fee"></td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <th class="c" colspan="2" id="insur_sum">合计缴费  ¥  (55.73%) ¥</th>
                    <td class="r" id="com_all_fee"></th>
                    <td class="r" id="per_all_fee"></th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label class="layui-form-label">税前总额</label>
              <div class="layui-input-inline" style="width: 190px;">
                <label class="layui-form-label" id="bef_tax_sum">¥ </label>
              </div>
            </div>

            <div class="layui-inline">
              <label class="layui-form-label">缴纳个税</label>
              <div class="input-group" style="width: 190px;">
                <label class="layui-form-label" id="tax_sum">¥ </label>
              </div>
            </div>

            <div class="layui-inline">
              <label class="layui-form-label">单位支出</label>
              <div class="input-group" style="width: 200px;">
                <label class="layui-form-label" id="com_pay_sum">¥ </label>
              </div>
            </div>

            <div class="layui-inline">
              <label class="layui-form-label">税后工资</label>
              <div class="input-group" style="width: 190px;">
                <label class="layui-form-label" id="aft_tax_sum">¥ </label>
              </div>
            </div>

            <div class="layui-inline">
              <label class="layui-form-label">实际收入</label>
              <div class="input-group" style="width: 190px;">
                <label class="layui-form-label" id="per_get_sum">¥ </label>
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

  $(function () {
    // 计算基础项目
    basecal();
    // 重新计算所有项目
    recal();
  });

  var edit_index = 0;
  var layer = new Object();
  var form = new Object();
  var laydate = new Object();

  //  使用Layui
  layui.use(['layer', 'form', 'laydate'], function(){
    layer = layui.layer;
    form = layui.form();
    laydate = layui.laydate;

    // 缴费档次点击事件
    $(".layui-form-radio").click(function() {
      // 计算基础项目
      basecal();
      // 计算其它项目
      recal();
    });
  });

  // 关闭按钮点击事件
  $("#btn_close").click(function() {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
  });

  // 计算基础项目
  function basecal() {
    // 缴费档次
    var pay_level = $("input[name='pay_level']:checked").val();
    // 税前月薪
    var tax_salary = $("#ct_tax_salary").val() * 1.00;
    // 最低办公津贴比例
    var min_subsidy_rate = 0.05;
    // 办公津贴比例
    var subsidy_rate = min_subsidy_rate;
    // 最低基本工资
    var min_base_salary = 4000.00;
    // 基本工资
    var base_salary = min_base_salary;
    // 缴费档次
    switch(pay_level)
    {
      // 中
      case '2':
        subsidy_rate = min_subsidy_rate * 2;
        base_salary = (tax_salary + min_base_salary) / 2
        break;
      // 高
      case '3':
        subsidy_rate = min_subsidy_rate;
        base_salary = tax_salary;
        break;
      // 低
      default:
        subsidy_rate = min_subsidy_rate * 3;
        base_salary = min_base_salary;
        break;
    }
    // 计算办公津贴
    var office_subsidy = tax_salary * subsidy_rate;
    $("#ct_office_subsidy").val(office_subsidy.toFixed(2));
    // 基本工资变更
    $("#ct_base_salary").val(base_salary.toFixed(2));
    // 计算绩效工资
    var effic_salary = tax_salary * (1 + min_subsidy_rate) - base_salary - office_subsidy;
    // 绩效工资变更
    $("#ct_effic_salary").val(effic_salary.toFixed(2));

  }

  // 重新计算其他项目
  function recal() {
    var com_pension_rate = 0.2;                        // 单位养老保险比例
    var per_pension_rate = 0.08;                       // 个人养老保险比例
    var com_medical_rate = 0.095;                      // 单位医疗保险比例
    var per_medical_rate = 0.02;                       // 个人医疗保险比例

    var com_jobless_rate = 0.005;                      // 单位失业保险比例
    var per_jobless_rate = 0.005;                      // 个人失业保险比例
    var com_injury_rate = 0.0023;                      // 单位工伤保险比例
    var com_bear_rate = 0.01;                          // 单位生育保险比例
    var com_housing_fund_rate = 0.07;                  // 单位公积金比例
    var per_housing_fund_rate = 0.07;                  // 个人公积金比例

    var com_all_rate = 0.3823;                         // 单位全体缴费比例
    var per_all_rate = 0.175;                          // 个人全体缴费比例

    // 税前月薪
    var tax_salary = $("#ct_tax_salary").val() * 1;
    // 基本工资
    var base_salary = $("#ct_base_salary").val() * 1;
    // 办公津贴
    var office_subsidy = $("#ct_office_subsidy").val() * 1;
    // 绩效工资
    var effic_salary = $("#ct_effic_salary").val() * 1;

    if (base_salary >= 0) {
      // 养老保险
      var com_pension_fee = base_salary * com_pension_rate;
      $("#com_pension_fee").html(com_pension_fee.toFixed(2));
      var per_pension_fee = base_salary * per_pension_rate;
      $("#per_pension_fee").html(per_pension_fee.toFixed(2));
      // 医疗保险
      var com_medical_fee = base_salary * com_medical_rate;
      $("#com_medical_fee").html(com_medical_fee.toFixed(2));
      var per_medical_fee = base_salary * per_medical_rate;
      $("#per_medical_fee").html(per_medical_fee.toFixed(2));
      // 失业保险
      var com_jobless_fee = base_salary * com_jobless_rate;
      $("#com_jobless_fee").html(com_jobless_fee.toFixed(2));
      var per_jobless_fee = base_salary * per_jobless_rate;
      $("#per_jobless_fee").html(per_jobless_fee.toFixed(2));
      // 工伤保险
      var com_injury_fee = base_salary * com_injury_rate;
      $("#com_injury_fee").html(com_injury_fee.toFixed(2));
      // 生育保险
      var com_bear_fee = base_salary * com_bear_rate;
      $("#com_bear_fee").html(com_bear_fee.toFixed(2));
      // 住房公积金
      var com_housing_fund_fee = base_salary * com_housing_fund_rate;
      $("#com_housing_fund_fee").html(com_housing_fund_fee.toFixed(2));
      var per_housing_fund_fee = base_salary * per_housing_fund_rate;
      $("#per_housing_fund_fee").html(per_housing_fund_fee.toFixed(2));
      // 合计
      var com_all_fee = base_salary * com_all_rate;
      $("#com_all_fee").html(com_all_fee.toFixed(2));
      var per_all_fee = base_salary * per_all_rate;
      $("#per_all_fee").html(per_all_fee.toFixed(2));
      var insur_sum = com_all_fee + per_all_fee;
      $("#insur_sum").html('合计缴费  ¥  ' + insur_sum.toFixed(2) + '  (55.73%)');
    }

    // 计算税前总额
    var bef_tax_sum = base_salary - per_all_fee;
    $("#bef_tax_sum").html('¥ ' + bef_tax_sum.toFixed(2));

    // 个人所得税计算
    var tax_sum = 0;
    // 计算记税总额
    var count_tax_sum = bef_tax_sum - 3500;
    if (count_tax_sum <= 0) {
      tax_sum = 0;
    } else if (count_tax_sum <= 1500) {
      tax_sum = count_tax_sum * 0.03;
    } else if (count_tax_sum <= 4500) {
      tax_sum = count_tax_sum * 0.1 - 105;
    } else if (count_tax_sum <= 9000) {
      tax_sum = count_tax_sum * 0.2 - 555;
    } else if (count_tax_sum <= 35000) {
      tax_sum = count_tax_sum * 0.25 - 1005;
    } else if (count_tax_sum <= 55000) {
      tax_sum = count_tax_sum * 0.3 - 2755;
    } else if (count_tax_sum <= 80000) {
      tax_sum = count_tax_sum * 0.35 - 5505;
    } else if (count_tax_sum > 80000) {
      tax_sum = count_tax_sum * 0.45 - 13505;
    }
    $("#tax_sum").html('¥ ' + tax_sum.toFixed(2));

    // 计算单位支出
    var com_pay_sum = base_salary * (1 + com_all_rate) + effic_salary + office_subsidy;
    $("#com_pay_sum").html('¥ ' + com_pay_sum.toFixed(2));

    // 计算税后工资
    var aft_tax_sum = bef_tax_sum - tax_sum + effic_salary;
    $("#aft_tax_sum").html('¥ ' + aft_tax_sum.toFixed(2));

    // 计算实际收入
    var per_get_sum = aft_tax_sum + office_subsidy;
    $("#per_get_sum").html('¥ ' + per_get_sum.toFixed(2));
  }

  // 税前月薪变更事件
  $("#ct_tax_salary").on('input',function(){
    // 计算基础项目
    basecal();
    // 计算其它项目
    recal();
  });


  // 办公津贴变更事件
  $("#ct_office_subsidy").on('input',function(){
    // 重新计算所有项目
    recal();
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
    if (row['tax_salary'] <= 0) {
      parent.layer.msg('请设定税前月薪');
      return;
    }

    // 基本工资
    if (row['base_salary'] <= 0) {
      parent.layer.msg('请设定基本工资');
      return;
    }

    // 基本工资
    if (row['base_salary'] < 3500) {
      parent.layer.msg('基本工资不能少于3500');
      return;
    }

    // 员工姓名
    row['staff_name'] = $("#ct_staff_id option:selected").text();

    $.ajax({
        url: '/staff/api/fin_cycle_cost_staff.php',
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