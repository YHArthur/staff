<?php
require_once '../inc/common.php';
require_once '../db/staff_weixin.php';

// 需要员工登录
need_staff_login();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <title>员工个人信息修改-风赢科技</title>
    <link rel="stylesheet" href="css/weui.css">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
<form class="page input js_show" id="action" method="GET" id="form" enctype="multipart/form-data">  
  <div class="weui-cells weui-cells_form">
    <a class="weui-cell weui-cell_access btn" href="javascript:;">
      <div class="weui-cell__hd"><label class="weui-label">头像</label></div>
      <div class="weui-cell__bd">
        <img class= "avata" src="" >
        <input class="weui-input" type="text" id="staff_avata"  style="display:none"   name="staff_avata" >
      </div>
    <div class="weui-cell__ft"></div>
    </a>
    <div class="weui-cell weui-cell_select weui-cell_select-after">
      <div class="weui-cell__hd"><label class="weui-label">性别</label></div>
      <div class="weui-cell__bd">
        <select class="weui-select" id="staff_sex"   name="staff_sex">
          <option value="0">未设定</option>
          <option value="1">男</option>
          <option value="2">女</option>
        </select>
      </div>
    </div>

    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">生日</label></div>
      <div class="weui-cell__bd">
        <input class="weui-input" type="date" name="birthday"  id="birthday" placeholder="请选择">
      </div>
    </div>

    <div class="weui-cell weui-cell_select weui-cell_select-after">
      <div class="weui-cell__hd"><label class="weui-label">MBTI</label></div>
      <div class="weui-cell__bd">
        <select class="weui-select" id="staff_mbti"  name="staff_mbti">
          <option value="0">未设定</option>
          <option value="ISTJ">ISTJ</option>
          <option value="ISFJ">ISFJ</option>
          <option value="INFJ">INFJ</option>
          <option value="INTJ">INTJ</option>
          <option value="ISTP">ISTP</option>
          <option value="ISFP">ISFP</option>
          <option value="INFP">INFP</option>
          <option value="INTP">INTP</option>
          <option value="ESTJ">ESTJ</option>
          <option value="ESFJ">ESFJ</option>
          <option value="ENFJ">ENFJ</option>
          <option value="ENTJ">ENTJ</option>
          <option value="ESTP">ESTP</option>
          <option value="ESFP">ESFP</option>
          <option value="ENFP">ENFP</option>
          <option value="ENTP">ENTP</option>
        </select>
      </div>
    </div>

    <div class="weui-cells__title">个人简介</div>
    <div class="weui-cells weui-cells_form">
      <div class="weui-cell">
        <div class="weui-cell__bd">
          <textarea class="weui-textarea" id="staff_memo" name="staff_memo" placeholder="请输入个人简介" rows="3"></textarea>
          <div class="weui-textarea-counter"><span>0</span>/150</div>
        </div>
      </div>
    </div>

    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">英文名</label></div>
      <div class="weui-cell__bd">
        <input class="weui-input" type="text" id="nick_name"  name="nick_name" placeholder="请输入英文名">
      </div>
    </div>

    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">职位</label></div>
      <div class="weui-cell__bd">
        <input class="weui-input" type="text" id="staff_position"  name="staff_position" placeholder="请输入职位">
      </div>
    </div>

    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">手机号码</label></div>
      <div class="weui-cell__bd">
        <input class="weui-input" type="tel" id="staff_phone" name="staff_phone" placeholder="请输入手机号码">
      </div>
    </div>

    <div class="weui-cell">
      <div class="weui-cell__hd"><label class="weui-label">证件号码</label></div>
      <div class="weui-cell__bd">
        <input class="weui-input" type="text" id="identity" name="identity" placeholder="请输入身份证或护照号码">
      </div>
    </div>
  </div>
  <div class="weui-btn-area">
    <a id="clearBtn" href="javascript:void(0);" class="weui-btn weui-btn_disabled weui-btn_primary">确认</a>
  </div>
</form> 

  <div class="weui-msg__extra-area"><a href="../h5_menu.php">©2018 风赢科技</a></div>

  <script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script src="js/common.js"></script>
  <script src="js/wx.js"></script>
  <script src="js/info_edit.js"></script>
  
  
</body>
</html>
