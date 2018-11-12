<?php
require_once '../inc/common.php';

// 禁止游客访问
exit_guest();

$my_id = $_SESSION['staff_id'];
$my_name = $_SESSION['staff_name'];

// 未设置投票ID(默认添加)
if (!isset($_GET["vote_id"])) {
  $vote_id = 0;                                     // 投票ID
  $vote_title = '';                                 // 投票标题
  $owner_name = $my_name;                           // 创建人
  $vote_intro = '';                                 // 投票描述
  $limit_time = 0;                                  // 截止时间
  $is_multi = '0';                                  // 投票选项
  $multi_num = 1;                                   // 投票数量
  $is_anonymous = '0';                              // 投票种类

} else {

  $vote_id = $_GET["vote_id"];                      // 投票ID
  // 取得指定投票ID的投票记录
  $vote = get_vote($vote_id);
  if (!$vote)
    exit('vote id does not exist');

  $vote_title = $vote['vote_title'];                // 投票标题
  $owner_name = $vote['owner_name'];                // 创建人
  $vote_intro = $vote['vote_intro'];                // 投票描述
  $limit_time = $vote['limit_time'];                // 截止时间
  $is_multi = $vote['is_multi'];                    // 投票选项
  $is_anonymous = $vote['is_anonymous'];            // 投票种类
  // 将数据库存放的用户输入内容转换回再修改内容
  $vote_intro = html_to_str($vote_intro);
}

// 投票选项列表
$option_list = array('0'=>'单选', '1'=>'多选');
$option_input = get_radio_input('is_multi', $option_list, $is_multi);

// 投票种类列表
$type_list = array('0'=>'实名', '1'=>'匿名');
$type_input = get_radio_input('is_anonymous', $type_list, $is_anonymous);
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>投票管理</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">
    <div class="modal-body">
      <form id="ct_form" class="layui-form">
          <input type="hidden" name="vote_id" id="vote_id" value="<?php echo $vote_id?>">

          <div class="layui-form-item">
              <label for="ct_vote_title" class="layui-form-label">投票标题</label>
              <div class="layui-input-block">
                <input type="text" class="layui-input" id="ct_vote_title" name="vote_title" required lay-verify="required" autocomplete="off"  autofocus="autofocus" value="<?php echo $vote_title?>"  maxlength="30" placeholder="请输入投票标题（16个汉字以内）">
              </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_is_anonymous" class="layui-form-label">投票类型</label>
              <div class="layui-input-block">
                <?php echo $type_input?>
              </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_is_multi" class="layui-form-label">选项种类</label>
              <div class="layui-input-inline" style="width: 190px;">
                <?php echo $option_input?>
              </div>
            </div>

            <div class="layui-inline" id="div_multi_num">
              <label for="ct_multi_num" class="layui-form-label">多选数量</label>
              <div class="layui-input-inline" style="width: 235px;">
                <input type="number" class="layui-input" id="ct_multi_num" name="multi_num" autocomplete="off"  value="<?php echo $multi_num?>" placeholder="每人最多可选择几个项目">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_vote_intro_edit" class="layui-form-label">投票描述</label>
              <div class="layui-input-block">
                <textarea class="layui-textarea" id="ct_vote_intro_edit" name="vote_intro_edit" placeholder="投票描述"><?php echo $vote_intro?></textarea>
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
  var layedit = new Object();

  $(function () {
    // 是否多选初始化
    multiChange($("input[name='is_multi']:checked").val());
  });

  // 使用Layui
  layui.use(['layer', 'form', 'layedit'], function(){
    layer = layui.layer;
    form = layui.form();
    layedit = layui.layedit;

    layedit.set({
      uploadImage: {
        url: 'http://www.fnying.com/upload/upload_image.php' //接口url
        ,type: '' //默认post
      }
    });
    edit_index = layedit.build('ct_vote_intro_edit');

    // 是否多选变更事件
    form.on('radio(radio_is_multi)', function(data) {
       multiChange(data.value);
    });
  });

  // 是否多选处理
  function multiChange(ct) {
    // 隐藏多选数量输入框
    $("#div_multi_num").hide();
    // 可以多选
    if (ct != 0) {
      // 显示多选数量输入框
      $("#div_multi_num").show();
    }
  }

  // 关闭按钮点击事件
  $("#btn_close").click(function() {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
  });

  // 确认按钮点击事件
  $("#btn_ok").click(function() {
    // 必须输入投票标题
    var vote_title = $("#ct_vote_title").val().trim();
    if (vote_title.length == 0) {
      parent.layer.msg('请输入投票标题');
      return;
    }

    // 多选时必须输入多选数量
    var is_multi = $("input[name='is_multi']:checked").val();
    var multi_num = $("#ct_multi_num").val().trim();
    if (is_multi != '0' && multi_num.length == 0) {
      parent.layer.msg('请输入多选数量');
      return;
    }

    /*
    var vote_intro = layedit.getContent(edit_index).trim();
    if (vote_intro.length == 0) {
      parent.layer.msg('请输入预期结果');
      return;
    }
    */

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

    // 是否多选
    row['is_multi'] = $("input[name='is_multi']:checked").val();
    // 是否匿名
    row['is_anonymous'] = $("input[name='is_anonymous']:checked").val();
    // 投票描述
    row['vote_intro'] = layedit.getContent(edit_index);

    $.ajax({
        url: '/staff/api/vote.php',
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