window.shareData = {
    // 分享标题
    title: "员工微信签到记录",
    // 分享描述
    desc: "上海风赢网络科技有限公司员工签到记录页面【内部专用】~",
    // 分享链接
    link: window.location.href,
    // 分享图标
    imgUrl: 'http://www.fnying.com/staff/wx/img/share.jpg',
    success: function () {},
    cancel: function () {}
};

$(function () {
    if (/MicroMessenger/i.test(navigator.userAgent)) {
        $.getScript("https://res.wx.qq.com/open/js/jweixin-1.2.0.js", function () {
            // 微信配置启动
            wx_config();
            wx.ready(function() {
                wx.onMenuShareTimeline(shareData);
                wx.onMenuShareAppMessage(shareData);
            });
        });
    }
    // 办公室签到记录取得
    staff_sign_log();
});

// 办公室签到记录展示
function show_sign_log(response) {
  var log_id, staff_name, sign_type, ctime;
  var rows = response.rows;
  if (rows.length > 0) {
      rows.forEach(function(row, index, array) {
          log_id = row.log_id;
          staff_name = row.staff_name;
          sign_type = row.sign_type.replace('白金湾339', '');
          ctime = row.ctime;

          sign_row = '\
          <label class="weui-cell weui-check__label" for="x' + log_id + '">\
          <div class="weui-cell__bd">' + sign_type + ' <span>' + staff_name + '</span></div>\
          <div class="weui-cell__ft">' + ctime.substr(5, 11) + '</div>\
          </label>\
          ';
          $("#sign_rows").append(sign_row);
      });
  }
}

// 办公室签到记录取得
function staff_sign_log() {
    var api_url = 'office_sign_log.php';
    // API调用
    CallApi(api_url, {}, function (response) {
        // 办公室签到记录展示
        show_sign_log(response);
    }, function (response) {
        AlertDialog(response.errmsg);
    });
}

