$(function () {
  
  // 分享标题
  var ShareTitle = '员工微信注册';
  // 分享描述
  var ShareDesc = '上海风赢网络科技有限公司员工微信账号注册页面【内部专用】';
  // 分享链接
  var ShareLink = window.location.href;
  // 分享图标
  var ShareimgUrl = 'http://www.fnying.com/staff/wx/img/share.jpg';
  
  // 微信配置启动
  wx_config();

  wx.ready(function() {

      wx.onMenuShareTimeline({
          title: ShareTitle,
          desc: ShareDesc,
          link: ShareLink,
          imgUrl: ShareimgUrl
      });

      wx.onMenuShareAppMessage({
          title: ShareTitle,
          desc: ShareDesc,
          link: ShareLink,
          imgUrl: ShareimgUrl
      });
  });
  
  // 注册按钮处理
  $('#sign_btn').click(function () {
    // 员工注册处理
    staff_sign();
  });


});

// 员工注册处理
function staff_sign() {
  var staff_name = $('#staff_name').val();
  var staff_phone = $('#staff_phone').val();
  // 检查输入合法性
  if (staff_name.length <= 0) {
    AlertDialog('请输入你的姓名');
    return;
  }
  if (staff_phone.length <= 0) {
    AlertDialog('请输入你的手机号码');
    return;
  }

  var $this = $(this);
  var api_url = 'sign.php';
  var post_data = {staff_name: staff_name, staff_phone: staff_phone};
  BtnOnClick($this, '正在提交...');
  // 员工注册处理
  CallApi(api_url, post_data, function (response) {
    BtnEnable($("#sign_btn"), 'javascript:;', '注册');
    AlertDialog(response.errmsg);
    window.location.href = "wait.php";
  }, function (response) {
    BtnEnable($("#sign_btn"), 'javascript:staff_sign();', '注册');
    AlertDialog(response.errmsg);
  });
}

