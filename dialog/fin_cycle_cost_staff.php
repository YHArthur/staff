<?php
require_once '../inc/common.php';
require_once '../db/fin_cycle_cost.php';
require_once '../db/staff_main.php';

// 禁止游客访问
exit_guest();

$tax_salary = 5000.00;                                   // 默认税前工资
$base_salary = 5000.00;                                  // 最低基本工资
$subsidy_rate = 0.05;                                    // 默认办公津贴比例
$from_date = date('Y-m-d');                              // 支付开始日
$to_date = date('Y-03-31', strtotime("1 year"));         // 支付截止日

$pay_level = 1;                                          // 默认缴费档次

$com_pension_rate = 0.2;                        // 单位养老保险比例
$per_pension_rate = 0.08;                       // 个人养老保险比例
$com_medical_rate = 0.095;                      // 单位医疗保险比例
$per_medical_rate = 0.02;                       // 个人医疗保险比例

$com_jobless_rate = 0.005;                      // 单位失业保险比例
$per_jobless_rate = 0.005;                      // 个人失业保险比例
$com_injury_rate = 0.0023;                      // 单位工伤保险比例
$com_bear_rate = 0.01;                          // 单位生育保险比例
$com_housing_fund_rate = 0.07;                  // 单位公积金比例
$per_housing_fund_rate = 0.07;                  // 个人公积金比例

$com_all_rate = 0.3823;                         // 单位全体缴费比例
$per_all_rate = 0.175;                          // 个人全体缴费比例

$bef_tax_sum = $tax_salary - $base_salary * $per_all_rate;    // 税前总额
$count_tax_sum = $bef_tax_sum - 3500;                         // 记税总额

// 个人所得税计算
if ($count_tax_sum <= 0) {
  $tax_sum = 0;
} elseif ($count_tax_sum <= 1500) {
  $tax_sum = $count_tax_sum * 0.03;
} elseif ($count_tax_sum <= 4500) {
  $tax_sum = $count_tax_sum * 0.1 - 105;
} elseif ($count_tax_sum <= 9000) {
  $tax_sum = $count_tax_sum * 0.2 - 555;
} elseif ($count_tax_sum <= 35000) {
  $tax_sum = $count_tax_sum * 0.25 - 1005;
} elseif ($count_tax_sum <= 55000) {
  $tax_sum = $count_tax_sum * 0.3 - 2755;
} elseif ($count_tax_sum <= 80000) {
  $tax_sum = $count_tax_sum * 0.35 - 5505;
} elseif ($count_tax_sum > 80000) {
  $tax_sum = $count_tax_sum * 0.45 - 13505;
}

// 单位支出
$com_pay_sum = $tax_salary * (1 + $com_all_rate + $subsidy_rate);

// 税后所得
$aft_tax_sum = $bef_tax_sum - $tax_sum;

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

  <title>员工周期支出添加</title>

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
                <input type="number" class="layui-input" id="ct_office_subsidy" name="office_subsidy" required lay-verify="required" autocomplete="off" value="<?php echo $tax_salary * $subsidy_rate?>" placeholder="0.00">
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
                <input type="number" class="layui-input" id="ct_effic_salary" name="effic_salary" required lay-verify="required" autocomplete="off" value="<?php echo $tax_salary - $base_salary?>" placeholder="0.00" disabled>
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline" style="width: 25px;"></div>
            <div class="layui-inline" style="width: 580px;">
              <table class="layui-table">
                <colgroup>
                  <col width="150">
                  <col width="150">
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
                    <td>养老保险</td>
                    <td class="r" id=""><?php echo $base_salary * $com_pension_rate?></td>
                    <td class="r" id=""><?php echo $base_salary * $per_pension_rate?></td>
                  </tr>
                  <tr>
                    <td>医疗保险</td>
                    <td class="r" id=""><?php echo $base_salary * $com_medical_rate?></td>
                    <td class="r" id=""><?php echo $base_salary * $per_medical_rate?></td>
                  </tr>
                  <tr>
                    <td>失业保险</td>
                    <td class="r" id=""><?php echo $base_salary * $com_jobless_rate?></td>
                    <td class="r" id=""><?php echo $base_salary * $per_jobless_rate?></td>
                  </tr>
                  <tr>
                    <td>工伤保险</td>
                    <td class="r" id=""><?php echo $base_salary * $com_injury_rate?></td>
                    <td class="r">-</td>
                  </tr>
                  <tr>
                    <td>生育保险</td>
                    <td class="r" id=""><?php echo $base_salary * $com_bear_rate?></td>
                    <td class="r">-</td>
                  </tr>
                  <tr>
                    <td class="c">一金</td>
                    <td>住房公积金</td>
                    <td class="r" id=""><?php echo $base_salary * $com_housing_fund_rate?></td>
                    <td class="r" id=""><?php echo $base_salary * $per_housing_fund_rate?></td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <th class="c" colspan="2">合计</th>
                    <td class="r" id=""><?php echo $base_salary * $com_all_rate?></th>
                    <td class="r" id=""><?php echo $base_salary * $per_all_rate?></th>
                  </tr> 
                </tfoot>
              </table>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label class="layui-form-label">税前总额</label>
              <div class="layui-input-inline" style="width: 190px;">
                <label class="layui-form-label">¥ <?php echo $bef_tax_sum?></label>
              </div>
            </div>

            <div class="layui-inline">
              <label class="layui-form-label">缴纳个税</label>
              <div class="input-group" style="width: 190px;">
                <label class="layui-form-label">¥ <?php echo $tax_sum?></label>
              </div>
            </div>

            <div class="layui-inline">
              <label class="layui-form-label">单位支出</label>
              <div class="input-group" style="width: 200px;">
                <label class="layui-form-label">¥ <?php echo $com_pay_sum?></label>
              </div>
            </div>

            <div class="layui-inline">
              <label class="layui-form-label">税后工资</label>
              <div class="input-group" style="width: 190px;">
                <label class="layui-form-label">¥ <?php echo $aft_tax_sum?></label>
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
    // 是否无效
    row['is_void'] = $("input[name='is_void']:checked").val();

    $.ajax({
        url: '/staff/api/staff_cost.php',
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