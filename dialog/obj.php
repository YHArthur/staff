<?php
require_once '../inc/common.php';
require_once '../db/obj.php';
require_once '../db/staff_main.php';

// 禁止游客访问
exit_guest();

$staff_id = $_SESSION['staff_id'];
$staff_name = $_SESSION['staff_name'];

// 未设置目标ID(默认添加)
if (!isset($_GET["id"])) {

  $obj_id = '';                                   // 目标ID
  $obj_name = '';                                 // 目标
  $obj_intro = '';                                // 目标内容
  $check_id = $staff_id;                          // 监管人ID

  $obj_level = 0;                                 // 目标等级
  $obj_value = 0;                                 // 衡量指标
  $obj_perc = 0;                                  // 目标进度
  $obj_status = 3;                                // 目标状态

  // 本周五
  $current_friday = strtotime('Sunday -2 day', strtotime(date('Y-m-d')));
  // 距离这周五不足两天半，则下周五
  if (($current_friday - time()) < 60*60*60)
    $current_friday += 60*60*24*7;
  // 默认目标期限（这周五或下周五）
  $limit_time = date('Y-m-d', $current_friday) . ' 18:00:00';

  $public_level = 0;                              // 公开等级

} else {

  $obj_id = $_GET["id"];                          // 目标ID
  // 取得指定目标ID的目标记录
  $obj = get_obj($obj_id);
  if (!$obj)
    exit('obj id is not exist');

  $obj_id = $obj['obj_id'];                       // 目标ID
  $obj_name = $obj['obj_name'];                   // 目标
  $obj_intro = $obj['obj_intro'];                 // 目标内容
  $check_id = $obj['check_id'];                   // 监管人ID

  $obj_level = $obj['obj_level'];                 // 目标等级
  $obj_value = $obj['obj_value'];                 // 衡量指标
  $obj_perc = $obj['obj_perc'];                   // 目标进度
  $obj_status = $obj['obj_status'];               // 目标状态
  $limit_time = $obj['limit_time'];               // 目标期限
  $public_level = $obj['public_level'];           // 公开等级

  // 将数据库存放的用户输入内容转换回再修改内容
  $obj_intro = html_to_str($obj_intro);
}

// 员工选项
$staff_rows = get_staff_list();
$staff_list = get_staff_list_select($staff_id, $staff_rows);
$staff_list['0'] = '请选择员工';
$check_option = get_select_option($staff_list, $check_id);

// 公开等级选项
$public_list = array('2'=>'用户', '1'=>'组内', '0'=>'相关');
$public_input = get_radio_input('public_level', $public_list, $public_level);

// 目标状态列表
$status_list = array('3'=>'等待','2'=>'执行','1'=>'完成','0'=>'废止');
$status_option = get_select_option($status_list, $obj_status);
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>目标管理</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">
    <div class="modal-body">
      <form id="ct_form" class="layui-form">

          <input type="hidden" name="obj_id" id="obj_id" value="<?php echo $obj_id?>">

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_limit_time" class="layui-form-label" style="width: 110px;">截止期限</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_limit_time" name="limit_time" value="<?php echo $limit_time?>" placeholder="截止期限" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD hh:mm:ss'})">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_check_id" class="layui-form-label">监督检查</label>
              <div class="layui-input-inline" style="width: 190px;">
                <select name="check_id" id="ct_check_id">
                <?php echo $check_option?>
                </select>
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_public_level" class="layui-form-label">公开等级</label>
              <div class="layui-input-inline" style="width: 190px">
                <?php echo $public_input?>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_obj_status" class="layui-form-label">目标状态</label>
              <div class="layui-input-inline" style="width: 100px;">
                <select name="obj_status" id="ct_obj_status">
                <?php echo $status_option?>
                </select>
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_obj_value" class="layui-form-label">衡量指标</label>
              <div class="layui-input-inline" style="width: 190px;">
                <input type="number" class="layui-input" name="obj_value" id="ct_obj_value" value="<?php echo $obj_value?>" placeholder="衡量指标">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_obj_perc" class="layui-form-label">目标进度</label>
              <div class="layui-input-inline" style="width: 100px;">
                <input type="number" class="layui-input" name="obj_value" id="ct_obj_value" value="<?php echo $obj_value?>" placeholder="目标进度">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_obj_name" class="layui-form-label">目标标题</label>
              <div class="layui-input-block">
                <input type="text" class="layui-input" id="ct_obj_name" name="obj_name" required lay-verify="required" autocomplete="off"  value="<?php echo $obj_name?>" placeholder="目标标题（30字以内）">
              </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_obj_intro_edit" class="layui-form-label">目标内容</label>
              <div class="layui-input-block">
                <textarea class="layui-textarea" id="ct_obj_intro_edit" name="obj_intro_edit" placeholder="目标内容"><?php echo $obj_intro?></textarea>
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
  var layedit = new Object();

  //  使用Layui
  layui.use(['layer', 'form', 'laydate', 'layedit'], function(){
    layer = layui.layer;
    form = layui.form();
    laydate = layui.laydate;
    layedit = layui.layedit;

    layedit.set({
      uploadImage: {
        url: 'http://www.fnying.com/upload/upload_image.php' //接口url
        ,type: '' //默认post
      }
    });
    edit_index = layedit.build('ct_obj_intro_edit');
  });

  // 关闭按钮点击事件
  $("#btn_close").click(function() {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
  });

  // 确认按钮点击事件
  $("#btn_ok").click(function() {
    var obj_name = $("#ct_obj_name").val().trim();
    if (obj_name.length == 0) {
      parent.layer.msg('请输入目标标题');
      return;
    }

    var obj_intro = layedit.getContent(edit_index).trim();
    if (obj_intro.length == 0) {
      parent.layer.msg('请输入目标内容');
      return;
    }

    var limit_time = $("#ct_limit_time").val().trim();
    if (limit_time.length == 0) {
      parent.layer.msg('请输入截止期限');
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

    // 监督检查
    row['check_name'] = $("#ct_check_id option:selected").text();
    // 目标内容
    row['obj_intro'] = layedit.getContent(edit_index);
    // 公开等级
    row['public_level'] = $("input[name='public_level']:checked").val();

    $.ajax({
        url: '/staff/api/obj.php',
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