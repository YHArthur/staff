<?php
require_once '../inc/common.php';
require_once '../db/fin_cash_daily_log.php';
require_once '../db/staff_main.php';
require_once '../db/fin_sub_cn.php';

// 禁止游客访问
exit_guest();

$my_id = $_SESSION['staff_id'];
$my_name = $_SESSION['staff_name'];

// 未设置周期支出费用ID(默认添加)
if (!isset($_GET["id"])) {
  $log_id = 0;                                    // 日志ID
  $is_pay = 1;                                    // 收支区分
  $pay_date = date('Y-m-d');                      // 发生日期
  $pay_channel = '1';                             // 收支渠道
  $amount = 0;                                    // 收支金额
  $debit_id = $my_id;                             // 借方ID
  $credit_name = '';                              // 贷方名称
  $abstract = '';                                 // 日记摘要
  $file_url = '';                                 // 截图URL地址
  $result_type = 0;                               // 附件有无

} else {

  $log_id = $_GET["id"];                          // 日志ID
  // 取得指定日志ID的记录
  $row = get_fin_cash_daily_log($log_id);
  if (!$row)
    exit('log id is not exist');

  $is_pay = $row['is_pay'];                       // 收支区分
  $pay_date = $row['pay_date'];                   // 发生日期
  $pay_channel = $row['pay_channel'];             // 收支渠道
  $amount = $row['amount'] / 100.0;               // 收支金额
  $debit_id = $row['debit_id'];                   // 借方ID
  $abstract = $row['abstract'];                   // 日记摘要
  $file_url = $row['file_url'];                   // 截图URL地址
  $result_type = 0;                               // 附件有无
  $credit_name = $row['rcpt_name'];               // 收款方名称
  if ($is_pay == 0)
    $credit_name = $row['pay_name'];              // 付款方名称
  if (!empty($result_type))
    $result_type = 1;
}

// 员工选项
$staff_rows = get_staff_list();
$staff_list = get_staff_list_select($my_id, $staff_rows);
$staff_list['0'] = '请选择员工';
$staff_option = get_select_option($staff_list, $debit_id);

// 是否限定地点列表
$channel_list = array('0'=>'现金','1'=>'微信','2'=>'支付宝','3'=>'银行卡');
$channel_option = get_select_option($channel_list, $pay_channel);

// 收支区分选项
$pay_list = array('1'=>'支出', '0'=>'收入');
$pay_input = get_radio_input('is_pay', $pay_list, $is_pay);

// 附件有无选项
$type_list = array('0'=>'无', '1'=>'有');
$type_input = get_radio_input('result_type', $type_list, $result_type);
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>现金日记账管理</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">
    <div class="modal-body">
      <form id="ct_form" class="layui-form">

          <input type="hidden" name="log_id" id="log_id" value="<?php echo $log_id?>">
          <input type="hidden" name="file_url" id="file_url" value="<?php echo $file_url?>">

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_is_pay" class="layui-form-label">收支区分</label>
              <div class="layui-input-inline" style="width: 190px">
                <?php echo $pay_input?>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_pay_date" class="layui-form-label" style="width: 110px;">发生日期</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_pay_date" name="pay_date" value="<?php echo $pay_date?>" placeholder="发生日期" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD'})">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_debit_id" class="layui-form-label" id="lbl_debit_id">付款方</label>
              <div class="layui-input-inline" style="width: 190px;">
                <select name="debit_id" id="ct_debit_id">
                <?php echo $staff_option?>
                </select>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_credit_name" class="layui-form-label" id="lbl_credit_name">收款方</label>
              <div class="layui-input-inline" style="width: 190px;">
                <input type="text" class="layui-input" id="ct_credit_name" name="credit_name" required lay-verify="required" autocomplete="on"  value="<?php echo $credit_name?>" placeholder="收款方名称">
              </div>
            </div>

          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_pay_channel" class="layui-form-label">收支渠道</label>
              <div class="layui-input-inline" style="width: 190px;">
                <select name="pay_channel" id="ct_pay_channel">
                <?php echo $channel_option?>
                </select>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_amount" class="layui-form-label">发生金额</label>
              <div class="input-group" style="width: 190px;">
                <div class="input-group-addon">¥</div>
                <input type="number" class="layui-input" id="ct_amount" name="amount" required lay-verify="required" autocomplete="off"  value="<?php echo $amount?>" placeholder="发生金额">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_abstract" class="layui-form-label">内容摘要</label>
              <div class="layui-input-block" style="width: 520px;">
                <input type="text" class="layui-input" id="ct_abstract" name="abstract" required lay-verify="required" autocomplete="off"  value="<?php echo $abstract?>" placeholder="现金日记账内容摘要">
              </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_result_type" class="layui-form-label">相关附件</label>
              <div class="layui-input-block">
                <?php echo $type_input?>
              </div>
            </div>

            <div class="layui-inline" id="div_upload_file" style="width: 180px; display:hidden;">
                <input type="file" name="file" lay-type="file" class="layui-upload-file">
            </div>

            <div class="layui-inline" id="div_upload_result" style="width: 120px; display:hidden;">
              <div class="layui-progress layui-progress-big" lay-filter="fileup" lay-showPercent="true">
                <div class="layui-progress-bar layui-bg-blue" lay-percent="0%"></div>
              </div>
            </div>
            
            <div class="layui-inline" style="width: 120px;">
                <a id="lnk_upload_file" class="layui-btn layui-btn-small" style="display:hidden;" href="<?php echo $file_url?>" target="_blank"><i class="layui-icon">&#xe61e;</i></a>
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

  $(function () {
      // 附件有无初始化
      resultChange($("input[name='result_type']:checked").val());
  });

  //  使用Layui
  layui.use(['layer', 'form', 'laydate', 'upload', 'element'], function(){
      layer = layui.layer;
      form = layui.form();
      laydate = layui.laydate;

      var pr_element = layui.element();
      layui.upload({
          url: 'http://www.fnying.com/upload/upload_doc.php'
          ,title: '本地文档上传'
          ,ext: 'txt|pdf|html|css|doc|docx|xls|xlsx|ppt|pptx|jpg|png|bmp|jpeg'
          ,before: function(input) {
            // 显示上传进度条
            $("#div_upload_result").show();
            pr_element.progress('fileup', '30%');
          }
          ,success: function(res) {
            var file_url = res.data.src;
            pr_element.progress('fileup', '100%');
            $("#file_url").val(file_url);
            $("#lnk_upload_file").attr("href", file_url);
            $("#lnk_upload_file").show();
            $("#div_upload_result").hide();
          }
      });

      // 收支区分变更事件
      form.on('radio(radio_is_pay)', function(data) {
          payChange(data.value);
      });

      // 附件有无变更事件
      form.on('radio(radio_result_type)', function(data) {
          resultChange(data.value);
      });
  });

  // 收支区分变更事件
  function payChange(opt) {
    // 支出
    if (opt == '1') {
      // 借记标签变更
      $("#lbl_debit_id").html('付款方');
      // 贷方标签变更
      $("#lbl_credit_name").html('收款方');
      // 贷方名称输入内容变更
      $("#ct_credit_name").attr('placeholder', '收款方名称');
    } else {
      // 借记标签变更
      $("#lbl_debit_id").html('收款方');
      // 贷方标签变更
      $("#lbl_credit_name").html('付款方');
      // 贷方名称输入内容变更
      $("#ct_credit_name").attr('placeholder', '付款方名称');
    }
  }

  // 附件有无变更事件
  function resultChange(opt) {
    // 隐藏上传文件框
    $("#div_upload_file").hide();
    $("#div_upload_result").hide();
    $("#lnk_upload_file").hide();

    // 有附件
    if (opt == '1') {
      file_url = $("#file_url").val().trim();
      // 显示本地文档上传
      $("#div_upload_file").show();
      if (file_url.length != 0) {
        // 显示已上传的本地文档
        $("#lnk_upload_file").show();
      }
    }
  }

  // 支出金额
  var amount = $("#ct_amount").val() * 1;
  // 支出金额变更
  $("#ct_amount").val(amount.toFixed(2));
    
  // 关闭按钮点击事件
  $("#btn_close").click(function() {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
  });

  // 确认按钮点击事件
  $("#btn_ok").click(function() {
    var pay_date = $("#ct_pay_date").val().trim();
    if (pay_date.length == 0) {
      parent.layer.msg('请输入发生日期');
      return;
    }

    var credit_name = $("#ct_credit_name").val().trim();
    if (credit_name.length == 0) {
      parent.layer.msg('请输入对方名称');
      return;
    }

    var abstract = $("#ct_abstract").val().trim();
    if (abstract.length == 0) {
      parent.layer.msg('请输入内容摘要');
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

    // 发生金额
    if (row['amount'] == 0) {
      parent.layer.msg('发生金额不能为0');
      return;
    }

    // 收支区分
    row['is_pay'] = $("input[name='is_pay']:checked").val();
    // 员工姓名
    row['staff_name'] = $("#ct_debit_id option:selected").text();

    $.ajax({
      url: '/staff/api/fin_cash_daily_log.php',
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