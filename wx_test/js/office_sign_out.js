// 禁止在微信以外的浏览器里打开
window.onload = function(){
  if(!IsWeiXin()){
    window.location.href="http://www.fnying.com/staff/forbiden.php?code=2";
  }
}
  
$(function () {
  // 分享标题
  var ShareTitle = '员工微信签出';
  // 分享描述
  var ShareDesc = '上海风赢网络科技有限公司员工签出页面【内部专用】';
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
      
      wx.getLocation({
          type: 'gcj02',
          success: function (res) {
            staff_sign(res.latitude, res.longitude);
          },
          cancel: function (res) {
            AlertDialog('地理位置获取失败，无法签到');
          }
      });
  });


});

// 员工签到处理
function staff_sign(latitude, longitude) {
  var $this = $(this);
  var api_url = 'office_sign.php';
  var post_data = {sign_type: '白金湾339签出', latitude: latitude, longitude: longitude};
  // 员工签到处理
  CallApi(api_url, post_data, function (response) {
    Toast('签出成功');
  }, function (response) {
    AlertDialog(response.errmsg);
  });
}

