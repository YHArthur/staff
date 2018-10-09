<?php
require_once '../inc/common.php';

php_begin();

// 禁止游客访问
exit_guest();

// 工资年月选项列表
$from_ym = '201803';
$salary_ym = date('Ym', strtotime('-1 month'));
$salary_date = date('Y-m-06');
$salary_ym_list = array();

for ($i=0; $i<=12; $i++) {
  $tmp_year = substr($from_ym, 0, 4);
  $tmp_mon = substr($from_ym, 4, 2);
  $salary_ym_list[$from_ym] = $tmp_year . '年' . $tmp_mon . '月';
  $from_ym = date("Ym", mktime(0,0,0,$tmp_mon+1,1,$tmp_year));
}

$salary_ym_option = get_select_option($salary_ym_list, $salary_ym);
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>设定员工工资</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
  <style type="text/css">
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {vertical-align: middle;}
  </style>
</head>

<body>

  <div class="container">

    <fieldset class="layui-elem-field">
      <br>
      <form id="ct_form" class="layui-form">
          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_salary_ym" class="layui-form-label">工资月份</label>
              <div class="layui-input-inline" style="width: 190px;">
                <select name="salary_ym" id="ct_salary_ym" lay-filter="select_salary_ym">
                  <?php echo $salary_ym_option?>
                </select>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_salary_date" class="layui-form-label" style="width: 110px;">支付日期</label>
              <div class="layui-input-inline" style="width: 190px;">
                <input type="Datatime" class="layui-input" id="ct_salary_date" name="salary_date" value="<?php echo $salary_date?>" placeholder="支付日期" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD'})">
              </div>
            </div>
          </div>
       </form>
    </fieldset>

    <div>
      <ul class="nav nav-tabs">
        <li class="active"><a href="#all_staff" data-toggle="tab">员工列表&nbsp;&nbsp;<span class="badge" id="staff_count">0</span></a></li>
      </ul>

      <div class="tab-content" style="padding-top: 10px; font-size:15px; color:#F06;">
        <div class="tab-pane active" id="all_staff"><ul class="nav" id="staff_list"></ul></div>
      </div>
    </div>
  </div>

  <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="http://libs.baidu.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="../js/layui/layui.js"></script>

  <script type="text/javascript">
  var edit_index = 0;
  var layer = new Object();
  var form = new Object();
  var laydate = new Object();

  //  使用Layui
  layui.use(['layer', 'form', 'laydate'], function(){
    layer = layui.layer;
    form = layui.form();
    laydate = layui.laydate;
    
    // 工资月份变更事件
    form.on('select(select_salary_ym)', function(data) {
      getMonthStaff(data.value);
    });
  });

  // 获取具体员工工资设定的HTML
  function get_staff_html(row){
    var html = '<tr>';
    var lack_str = '';
    var staff_id = row.staff_id;
    var pre_tax_salary = row.pre_tax_salary / 100;
    var effic_salary = row.effic_salary / 100;
    var btn_str = '<button class="btn btn-success btn-staff" id="' + staff_id + '"><i class="glyphicon glyphicon-plus"></i></button>';
    // 已经设定过工资
    if (row.salary_log) {
      pre_tax_salary = row.salary_log.pre_tax_salary / 100;
      effic_salary = row.salary_log.effic_salary / 100;
      btn_str = '<button class="btn btn-danger btn-staff" id="' + staff_id + '"><i class="glyphicon glyphicon-refresh"></i></button>';
    }
    if (row.lack_days > 0) {
      // lack_str = row.lack_days + '天:';
      var lack_days = row.lack_days_list.split(',');
      lack_days.forEach(function(day, index, array) {
        lack_str += parseInt(day.substr(8, 2)) + '日,';
      });
      lack_str = lack_str.substr(0, lack_str.length - 1);
    }
    html += '<td>' + row.staff_cd + '</td>';
    html += '<td>' + row.staff_name + '</td>';
    html += '<td class="r"><input type="number" class="layui-input" id="p_' + staff_id + '" name="" autocomplete="off" value="' + pre_tax_salary + '" placeholder="税前工资"></td>';
    html += '<td class="r">¥' + row.base_salary / 100 + '</td>';
    html += '<td class="r"><input type="number" class="layui-input" id="e_' + staff_id + '" name="" autocomplete="off" value="' + effic_salary + '" placeholder="绩效奖金"></td>';
    html += '<td>' + lack_str + '</td>';
    html += '<td>' + btn_str + '</td>';
    html += '</tr>';
    return html;
  }

  // 员工工资列表展示
  function showMonthStaff(response) {
    $("#ct_salary_date").val(response.salary_date);
    $("#staff_count").html(response.total);
    $("#staff_list").html('');
    var rows = response.rows;
    var html_str = '';
    // 有数据
    if (rows.length > 0) {
      html_str = '<table class="table table-hover table-no-bordered table-striped">';
      html_str += '<tr>';
      html_str += '<th width="10%">工号</th><th width="10%">姓名</th><th width="15%">税前工资</th><th width="15%">基本工资</th><th width="15%">绩效奖金</th><th>欠勤日</th><th width="10%">操作</th>';
      html_str += '</tr>';
      rows.forEach(function(row, index, array) {
          // 获取具体员工工资设定的HTML
          html_str += get_staff_html(row);
      });
      html_str += '</table>';
    } else {
      html_str = '没有数据';
    }
    $("#staff_list").html(html_str);
  }

  // 获得指定年月员工工资列表
  function getMonthStaff(salary_ym) {
      $.ajax({
          url: '/staff/api/get_month_staff_salary.php',
          type: 'get',
          data: {"ym":salary_ym},
          success:function(response) {
            // AJAX正常返回
            if (response.errcode == '0') {
              // 员工工资列表展示
              showMonthStaff(response);
              // 添加工资点击事件
              $(".btn-staff").click(setStaffSalary);
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
  }

  // 员工工资列表初始化
  function initMonthStaff() {
      var salary_ym = $("#ct_salary_ym").val();
      // 获得指定年月员工工资列表
      getMonthStaff(salary_ym);
  }

  // 设定员工工资
  function setStaffSalary() {
      var ym = $("#ct_salary_ym").val();
      var dt = $("#ct_salary_date").val();
      var id = $(this).attr('id');
      var pts = $("#p_" + id).val();
      var es = $("#e_" + id).val();
      var tr = $(this).parent().parent();
      var row = {};
      row['ym'] = ym;
      row['dt'] = dt;
      row['id'] = id;
      row['pts'] = pts;
      row['es'] = es;
      $.ajax({
          url: '/staff/api/set_staff_salary.php',
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
              // 员工隐藏
              tr.hide();
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
  }

  $(function () {
    // 员工工资列表初始化
    initMonthStaff();
  });
  </script>

</body>
</html>