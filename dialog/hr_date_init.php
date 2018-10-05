<?php
require_once '../inc/common.php';

// 禁止游客访问
exit_guest();

$cur_year = date('Y');

// 年份选项
$year_list = array('2018'=>'2018 二零一八年', '2019'=>'2019 二零一九年');
$year_list[0] = '请选择年份';
$year_option = get_select_option($year_list, $cur_year);
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>初始化节假日</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">
    <div class="modal-body">
      <form id="ct_form" class="layui-form">

          <div class="layui-form-item">
              <label for="ct_year" class="layui-form-label" style="width: 150px;">选择年份</label>
              <div class="layui-input-inline">
                <select name="year" id="ct_year">
                <?php echo $year_option?>
                </select>
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

  //  使用Layui
  layui.use(['layer', 'form'], function(){
    layer = layui.layer;
    form = layui.form();
  });

  // 关闭按钮点击事件
  $("#btn_close").click(function() {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
  });

  // 确认按钮点击事件
  $("#btn_ok").click(function() {
    var year = $("#ct_year").val().trim();
    if (year == 0) {
      parent.layer.msg('请选择要初始化的年份');
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

    $.ajax({
        url: '/staff/api/hr_date_init.php',
        type: 'get',
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