<?php
require_once '../inc/common.php';
require_once '../db/fin_staff_salary.php';

php_begin();

// 禁止游客访问
exit_guest();

$my_id = $_SESSION['staff_id'];                           // 当前用户ID

// 取得所有有效员工列表
$salary_staffs = get_all_fin_staff_salary();
$rtn_str = '';

// 取得员工列表面板
foreach ($salary_staffs as $rec) {
  $rtn_str .= "\n    " . '<li style="padding: 10px 15px;">';
  $rtn_str .= "\n      " . '<button id="' . $rec['staff_id'] . '" class="btn btn-staff btn-primary"><i class="glyphicon glyphicon-ok"></i></button>';
  $rtn_str .= "\n        " . $rec['staff_name'];
  $rtn_str .= "\n      " . '<small class="text-muted">税前工资:' . $rec['pre_tax_salary'] / 100 . ' 基本工资:' . $rec['base_salary'] / 100 . ' 绩效奖金：' . $rec['effic_salary'] / 100 . '</small>';
  $rtn_str .= "\n    " . '</li>';
}

if ($rtn_str == '')
  $rtn_str = '没有行动数据';

$staff_panes = '<div class="tab-pane active" id="all_staff"><ul class="nav">' . $rtn_str . '</ul></div>';

// 工资年月选项列表
$from_ym = '201803';
$salary_ym = date('Ym');
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

  <title>添加员工工资</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">

    <fieldset class="layui-elem-field">
      <br>
      <form id="ct_form" class="layui-form">
          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_salary_ym" class="layui-form-label">计算月份</label>
              <div class="layui-input-inline" style="width: 190px;">
                <select name="salary_ym" id="ct_salary_ym">
                  <?php echo $salary_ym_option?>
                </select>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_salary_date" class="layui-form-label" style="width: 110px;">发放时间</label>
              <div class="layui-input-inline" style="width: 190px;">
                <input type="Datatime" class="layui-input" id="ct_salary_date" name="salary_date" value="<?php echo $salary_date?>" placeholder="发放时间" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD'})">
              </div>
            </div>
          </div>
       </form>
    </fieldset>

    <div id="staff_list">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#all_staff" data-toggle="tab">员工列表&nbsp;&nbsp;<span class="badge">10</span></a></li>
      </ul>

      <div class="tab-content" style="padding-top: 10px; font-size:15px; color:#F06;">
        <?php echo $staff_panes;?>
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
  });

  $(function () {
    // 添加工资点击事件
    $(".btn-staff").click(function() {
      var ym = $("#ct_salary_ym").val();
      var dt = $("#ct_salary_date").val();
      var id = $(this).attr('id');
      var li = $(this).parent();
      var row = {};
      row['ym'] = ym;
      row['dt'] = dt;
      row['id'] = id;
      $.ajax({
          url: '/staff/api/add_taff_salary.php',
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
              li.hide();
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
  });
  </script>

</body>
</html>