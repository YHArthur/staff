// 禁止在微信以外的浏览器里打开
window.onload = function(){
    if(!IsWeiXin()){
        window.location.href="http://www.fnying.com/staff/pc_forbiden.php?code=2";
    }
}

window.shareData = {
    // 分享标题
    title: "员工微信签到",
    // 分享描述
    desc: "上海风赢网络科技有限公司员工签到页面【内部专用】",
    // 分享链接
    link: window.location.href,
    // 分享图标
    imgUrl: 'http://www.fnying.com/staff/wx/img/share.jpg',
    success: function () {},
    cancel: function () {}
};

$(function () {
  // 展示员工签到记录
  staff_sign_log(10, 0);

  if (IsWeiXin()) {
      $.getScript("https://res.wx.qq.com/open/js/jweixin-1.2.0.js", function () {
          // 微信配置启动
          wx_config();
          wx.ready(function() {
              wx.onMenuShareTimeline(shareData);
              wx.onMenuShareAppMessage(shareData);
              wx.getLocation({
                  type: 'gcj02',
                  success: function (res) {
                      staff_sign(res.latitude, res.longitude);
                    
                  },
                  cancel: function (res) {
                      AlertDialog('地理位置获取失败，无法签到');
                  },
                  fail: function (res) {
                      AlertDialog('地理位置获取失败，无法签到');
                  }
              });
          });
      });
  }
});

// 员工签到处理
function staff_sign(latitude, longitude) {
    var api_url = 'office_sign.php';
    var post_data = {sign_type: '白金湾339签入', latitude: latitude, longitude: longitude};
    // 员工注册处理
    CallApi(api_url, post_data, function (response) {
        Toast('签到成功');
        $("#sign_rows").html('');
        staff_sign_log(10, 0);
    }, function (response) {
        AlertDialog(response.errmsg);
    });
}

// 展示员工签到记录
function staff_sign_log(limit, offset) {
    var api_url = 'office_sign_log.php';
    var post_data = {"limit": limit, "offset": offset};
    CallApi(api_url, post_data, function (response) {
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
    }, function (response) {
        AlertDialog(response.errmsg);
    });
}
