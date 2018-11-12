window.shareData = {
    // 分享标题
    title: "风赢科技员工管理平台",
    // 分享描述
    desc: "上海风赢网络科技有限公司员工管理平台【内部专用】",
    // 分享链接
    link: window.location.href,
    // 分享图标
    imgUrl: 'http://www.fnying.com/staff/wx/img/share.jpg',
    success: function () {},
    cancel: function () {}
};

$(function () {
  if (IsWeiXin()) {
      $.getScript("https://res.wx.qq.com/open/js/jweixin-1.2.0.js", function () {
          // 微信配置启动
          wx_config();
          wx.ready(function() {
              wx.onMenuShareTimeline(shareData);
              wx.onMenuShareAppMessage(shareData);
          });
      });
  }
});
