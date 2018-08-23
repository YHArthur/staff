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

  <title>银行日记账导入</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">
    <div class="modal-body">
      <form class="layui-form">

        <legend>请下载最新的工商银行TXT回单文件并上传</legend>
        <blockquote class="layui-elem-quote">进入中国工商银行企业网上银行，点击账户管理，再点击明细查询。
        设置查询开始日期，设置表头并全选所有项目，选择全部明细，点击下载回单，选择txt格式文件并下载至本地，然后上传。</blockquote>
        
        <div class="layui-form-item">
          <div class="layui-inline">
            <input type="file" name="file" lay-type="file" class="layui-upload-file">
          </div>
          <div class="layui-inline" style="width: 330px;">
            <div class="layui-progress layui-progress-big" lay-filter="fileup" lay-showPercent="true">
              <div class="layui-progress-bar layui-bg-blue" lay-percent="0%"></div>
            </div>
          </div>
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
  layui.use(['upload', 'element'], function(){
    var pr_element = layui.element();
    layui.upload({
      url: 'http://www.fnying.com/upload/upload_txt.php'
      ,title: '请上传txt文件'
      ,ext: 'txt'
      ,success: function(res) {
        pr_element.progress('fileup', '30%');
        log_analysis(res.data.src, pr_element);
      }
    });
  });

  // 银行日记账解析处理
  function log_analysis(filename, pr_element) {
      pr_element.progress('fileup', '100%');
      var row = {filename: filename};
      $.ajax({
          url: '/staff/api/fin_bank_daily_log.php',
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
  }

  // 关闭按钮点击事件
  $("#btn_close").click(function() {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
  });
  </script>


</body>
</html>