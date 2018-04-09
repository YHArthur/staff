// 禁止在微信以外的浏览器里打开
window.onload = function(){
  if(!IsWeiXin()){
    window.location.href="http://www.fnying.com/staff/forbiden.php?code=2";
  }
}
  
$(function () {
  // 分享标题
  var ShareTitle = '员工微信签到记录';
  // 分享描述
  var ShareDesc = '上海风赢网络科技有限公司员工签到记录页面【内部专用】';
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
  
  // 办公室签到记录取得
  staff_sign_log();

});

// 办公室签到记录展示
function show_sign_log(response) {
  var log_id, staff_name, sign_type, ctime;
  var clist = response.rows;
  if (clist.length > 0) {
    clist.forEach(function(value, index, array) {
       log_id = value.log_id;
       staff_name = value.staff_name;
       sign_type = value.sign_type.replace('白金湾339', '');
       ctime = value.ctime;
       
       check = '\
        <label class="weui-cell weui-check__label" for="x' + log_id + '">\
          <div class="weui-cell__bd">' + sign_type + ' <span>' + staff_name + '</span></div>\
          <div class="weui-cell__ft">' + ctime.substr(5, 11) + '</div>\
        </label>\
        ';
        $("#sign_rows").append(check);
    });
  }
}

// 办公室签到记录取得
function staff_sign_log() {
  var $this = $(this);
  var api_url = 'office_sign_log.php';
  var post_data = {};
  // API调用
  CallApi(api_url, post_data, function (response) {
    // 办公室签到记录展示
    show_sign_log(response);
  }, function (response) {
    AlertDialog(response.errmsg);
  });
}

