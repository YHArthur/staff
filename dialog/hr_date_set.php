<?php
require_once '../inc/common.php';
require_once '../db/hr_date_tag.php';

// 禁止游客访问
exit_guest();

// 未设置年月日
if (!isset($_GET["ymd"]))
  exit('ymd is not set');

$ymd = $_GET["ymd"];                  // 年月日

// 取得指定年月日的节假日标志记录
$date = get_hr_date_tag($ymd);
if (!$date)
  exit('ymd does not exist');

$date_type = $date['date_type'];      // 日期类型 0 工作日 1 休日 2 国定假日
$date_tag = $date['date_tag'];        // 日期标注

// 日期类型
$type_list = array('0'=>'工作日', '1'=>'休息日', '2'=>'国定假');
$type_input = get_radio_input('date_type', $type_list, $date_type);
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
          <input type="hidden" name="date_ymd" id="date_ymd" value="<?php echo $ymd?>">

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_date_type" class="layui-form-label">日期类型</label>
              <div class="layui-input-block">
                <?php echo $type_input?>
              </div>
            </div>
          </div>

          <div class="layui-form-item" id="div_result_name" style="display:hidden;">
              <label for="ct_date_tag" class="layui-form-label">日期标注</label>
              <div class="layui-input-block">
                <input type="text" class="layui-input" id="ct_date_tag" name="date_tag" required lay-verify="required" autocomplete="off" value="<?php echo $date_tag?>" placeholder="请输入日期标注">
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

    // 日期类型
    row['date_type'] = $("input[name='date_type']:checked").val();

    $.ajax({
        url: '/staff/api/hr_date_set.php',
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
            parent.window.initDateTag();
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