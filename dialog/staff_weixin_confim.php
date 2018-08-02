<?php
require_once '../inc/common.php';
require_once '../db/staff_weixin.php';
require_once '../db/staff_main.php';

// 禁止游客访问
exit_guest();

// 未设置员工ID和权限ID(默认添加)
if (!isset($_GET["staff_id"]))
  exit('staff_id is not exist');

$staff_id = get_arg_str('GET', 'staff_id');       // 员工ID

// 取得注册后待申请的员工微信账号记录
$row = get_staff_weixin_sign($staff_id);
if (!$row)
  exit('staff permit is not exist');

$staff_name = $row['staff_name'];                 // 员工姓名
$staff_phone = $row['staff_phone'];               // 员工电话
$staff_avata = $row['staff_avata'];               // 员工头像
$nick_name = $row['wx_name'];                     // 微信昵称
$ctime = $row['ctime'];                           // 申请时间

// 取得员工新工号
$staff_cd = get_new_staff_cd();                   // 员工工号
$staff_position = '';                             // 员工职位
$staff_sex = '0';                                 // 员工性别
$staff_mbti = '';                                 // 员工性格
$staff_memo = '';                                 // 员工个人简介
$identity = '';                                   // 身份证件号
$birth_ymd = '1994-01-01';                        // 出生年份
$join_date = date('Y-m-d');                       // 加入时间
$is_void = '0';                                   // 是否无效

// 员工性别选项
$sex_list = array('1'=>'男', '2'=>'女');
$sex_input = get_radio_input('staff_sex', $sex_list, $staff_sex);

// 员工性格列表
$mbti_list = array('ISTJ'=>'ISTJ','ISFJ'=>'ISFJ','INFJ'=>'INFJ','INTJ'=>'INTJ','ISTP'=>'ISTP','ISFP'=>'ISFP','INFP'=>'INFP','INTP'=>'INTP','ESTJ'=>'ESTJ','ESFJ'=>'ESFJ','ENFJ'=>'ENFJ','ENTJ'=>'ENTJ','ESTP'=>'ESTP','ESFP'=>'ESFP','ENFP'=>'ENFP','ENTP'=>'ENTP');
$mbti_option = get_select_option($mbti_list, $staff_mbti);

// 是否无效选项
$void_list = array('1'=>'无效', '0'=>'有效');
$void_input = get_radio_input('is_void', $void_list, $is_void);
?>

<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>员工微信账号审核</title>

  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/layui/css/layui.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>

  <div class="container">
    <div class="modal-body">
      <form id="ct_form" class="layui-form">

          <input type="hidden" name="staff_id" id="staff_id" value="<?php echo $staff_id?>">

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_staff_avata" class="layui-form-label">微信信息</label>
              <div class="layui-input-inline">
                <img src="<?php echo $staff_avata?>" class="avata"> <?php echo $nick_name?>
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_nick_name" class="layui-form-label">英文昵称</label>
              <div class="layui-input-inline">
                <input type="text" class="layui-input" id="ct_nick_name" name="nick_name" required lay-verify="required" autocomplete="off"  value="<?php echo $nick_name?>" placeholder="英文昵称">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_staff_name" class="layui-form-label">员工姓名</label>
              <div class="layui-input-inline">
                <input type="text" class="layui-input" id="ct_staff_name" name="staff_name" required lay-verify="required" autocomplete="off"  value="<?php echo $staff_name?>" placeholder="员工姓名">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_ctime" class="layui-form-label">申请时间</label>
              <div class="layui-input-inline">
                <input type="text" class="layui-input" id="ct_ctime" name="ctime" value="<?php echo $ctime?>" disabled="disabled">
              </div>
            </div>
          </div>


          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_staff_phone" class="layui-form-label">员工电话</label>
              <div class="layui-input-inline">
                <input type="number" class="layui-input" id="ct_staff_phone" name="staff_phone" required lay-verify="required" autocomplete="off"  value="<?php echo $staff_phone?>" placeholder="员工电话">
              </div>
            </div>
              
            <div class="layui-inline">
              <label for="ct_join_date" class="layui-form-label">加入时间</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_join_date" name="join_date" value="<?php echo $join_date?>" placeholder="加入时间" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD'})">
              </div>
            </div>
            
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_staff_cd" class="layui-form-label">员工工号</label>
              <div class="layui-input-inline">
                <input type="number" class="layui-input" id="ct_staff_cd" name="staff_cd" required lay-verify="required" autocomplete="off"  value="<?php echo $staff_cd?>" placeholder="员工工号">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_staff_sex" class="layui-form-label">员工性别</label>
              <div class="layui-input-inline">
                <?php echo $sex_input?>
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_staff_position" class="layui-form-label">员工职位</label>
              <div class="layui-input-inline">
                <input type="text" class="layui-input" id="ct_staff_position" name="staff_position" required lay-verify="required" autocomplete="off"  value="<?php echo $staff_position?>" placeholder="员工职位">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_staff_mbti" class="layui-form-label">员工性格</label>
              <div class="layui-input-inline">
                <select name="staff_mbti" id="ct_staff_mbti">
                <?php echo $mbti_option?>
                </select>
              </div>
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-inline">
              <label for="ct_identity" class="layui-form-label">身份证件</label>
              <div class="layui-input-inline">
                <input type="text" class="layui-input" id="ct_identity" name="identity" required lay-verify="required" autocomplete="off"  value="<?php echo $identity?>" placeholder="身份证件号码">
              </div>
            </div>

            <div class="layui-inline">
              <label for="ct_birth_ymd" class="layui-form-label">出生日期</label>
              <div class="layui-input-inline">
                <input type="Datatime" class="layui-input" id="ct_birth_ymd" name="birth_ymd" value="<?php echo $birth_ymd?>" placeholder="出生年月日" onclick="layui.laydate({elem: this, istime: true, format: 'YYYY-MM-DD'})">
              </div>
            </div>
          </div>

          <div class="layui-form-item">
              <label for="ct_staff_memo_edit" class="layui-form-label">个人简介</label>
              <div class="layui-input-block">
                <textarea class="layui-textarea" id="ct_staff_memo_edit" name="staff_memo_edit" placeholder="个人简介"><?php echo $staff_memo?></textarea>
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
    edit_index = layedit.build('ct_staff_memo_edit');
  });

  // 关闭按钮点击事件
  $("#btn_close").click(function() {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    parent.layer.close(index);
  });

  // 确认按钮点击事件
  $("#btn_ok").click(function() {
    var staff_name = $("#ct_staff_name").val().trim();
    if (staff_name == 0) {
      parent.layer.msg('请设定员工姓名');
      return;
    }

    var staff_phone = $("#ct_staff_phone").val().trim();
    if (staff_phone == 0) {
      parent.layer.msg('请设定员工手机');
      return;
    }

    var join_date = $("#ct_join_date").val().trim();
    if (join_date.length == 0) {
      parent.layer.msg('请输入加入时间');
      return;
    }

    var birthday = $("#ct_birth_ymd").val().split("-");
    var day = new Date(birthday[0], birthday[1]-1, birthday[2]);
    if(isNaN(day.getMonth()) || birthday[1] != day.getMonth()+1)
    {
      parent.layer.msg('请正确选择出生日期');
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

    // 员工性别
    row['staff_sex'] = $("input[name='staff_sex']:checked").val();
    // 员工性格
    row['staff_mbti'] = $("#ct_staff_mbti option:selected").text();
    // 出生年份处理
    row['birth_year'] = day.getFullYear();
    var m = day.getMonth() + 1;
    if (m >= 1 && m <= 9) m = "0" + m;
    var d = day.getDate();
    if (d >= 0 && d <= 9) d = "0" + d;
    // 生日处理
    row['birth_day'] = m + '.' + d;
    // 个人简介
    row['staff_memo'] = layedit.getContent(edit_index);

    $.ajax({
        url: '/staff/api/staff_weixin_confim.php',
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