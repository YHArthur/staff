<?php
require_once '../inc/common.php';

// 禁止游客访问
exit_guest();
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>每月银行余额</title>

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
              <label class="layui-form-label">总收入</label>
              <label class="layui-form-label" id="rec_amount_sum" style="width: 190px;">¥ </label>
            </div>

            <div class="layui-inline">
              <label class="layui-form-label">总支出</label>
              <label class="layui-form-label" id="pay_amount_sum" style="width: 190px;">¥ </label>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label class="layui-form-label">银行余额</label>
              <label class="layui-form-label" id="rest_amount_sum" style="width: 190px;">¥ </label>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline" style="width: 25px;"></div>
            <div class="layui-inline" style="width: 580px;" id='sum_table'></div>
          </div>

          <div class="layui-form-item">
            <div class="col-xs-10"></div>
            <div class="col-xs-2">
              <button type="button" id="btn_close" class="btn btn-default btn-block">关闭</button>
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

  // 获取具体年月集计的HTML
  function get_month_html(row){
    var html = '<tr>';
    html += '<td class="l">' + row.log_ym + '</td>';
    html += '<td class="r"> ¥ ' + row.rec_amount + '</td>';
    html += '<td class="r"> ¥ ' + row.pay_amount + '</td>';
    html += '<td class="r"> ¥ ' + row.rest_amount + '</td>';
    html += '</tr>';
    return html;
  }

  // 银行日记账集计结果展示
  function showBankSum(response) {
    $("#rec_amount_sum").html('¥ ' + response.rec_amount_sum);
    $("#pay_amount_sum").html('¥ ' + response.pay_amount_sum);
    $("#rest_amount_sum").html('¥ ' + response.rest_amount_sum);
    var rows = response.rows;
    var html_str = '';
    // 有数据
    if (rows.length > 0) {
      html_str = '<table class="table table-hover table-no-bordered table-striped">';
      html_str += '<tr>';
      html_str += '<th width="25%">年月</th><th width="25%">收入</th><th width="25%">支出</th><th width="25%">余额</th>';
      html_str += '</tr>';
      rows.forEach(function(row, index, array) {
          // 获取具体年月集计的HTML
          html_str += get_month_html(row);
      });
      html_str += '</table>';
    } else {
      html_str = '没有数据';
    }
    $("#sum_table").html(html_str);
  }

  // 获得银行日记账集计结果
  function getBankSum() {
      $.ajax({
          url: '/staff/api/fin_bank_daily_sum.php',
          type: 'get',
          success:function(response) {
            // AJAX正常返回
            if (response.errcode == '0') {
              // 银行日记账集计结果展示
              showBankSum(response);
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
      // 获得银行日记账集计结果
      getBankSum();
  });

  </script>


</body>
</html>