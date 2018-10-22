window.shareData = {
    // 分享标题
    title: "风赢科技员工访问记录列表",
    // 分享描述
    desc: "",
    // 分享链接
    link: window.location.href,
    // 分享图标
    imgUrl: 'http://www.fnying.com/staff/wx/img/share.jpg',
    success: function () {},
    cancel: function () {}
};

$(function () {
    // 获得员工访问记录列表
    get_action_log_list();

    // 微信分享处理
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
})

// 获取日志明细的HTML
function get_log_html(row){
  var  html ='\
  <div class="weui-panel">\
      <div class="weui-panel__bd">\
          <div class="weui-media-box weui-media-box_text">\
              <h4 class="weui-media-box__title">' + row.staff_name + '</h4>\
              <div class="weui-media-box__desc">' + row.time + '</div>\
              <ul class="weui-media-box__info">\
                  <li class="weui-media-box__info__meta">' + row.action_url + '</li>\
                  <li class="weui-media-box__info__meta">' + row.ip + '</li>\
                  <li class="weui-media-box__info__meta">' + row.from_url + '</li>\
              </ul>\
          </div>\
      </div>\
      <div class="weui-panel__ft">\
          <a href="http://' + row.url + '" class="weui-cell weui-cell_access weui-cell_link">\
              <div class="weui-cell__bd">查看链接</div>\
              <span class="weui-cell__ft"></span>\
          </a>\
      </div>\
  </div>\
  ';
  return html;
}

// 前置0
function addPreZero(num, size){
    return ('000000000' + num).slice(-1 * size);
}

// 取得当前时间
function getNowDate() {
    var date = new Date();
    var month = addPreZero(date.getMonth() + 1, 2);
    var day = addPreZero(date.getDate(), 2);
    var hours = addPreZero(date.getHours(), 2);
    var minutes = addPreZero(date.getMinutes(), 2);
    var seconds = addPreZero(date.getSeconds(), 2);

    return date.getFullYear() + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
}

// 访问记录明细展示
function show_action_log_list(response) {
  var time_str = getNowDate();
  $("#time_now").html(time_str);
  $("#log_rows").html('');

  var rows = response.rows;
  if (rows.length > 0) {
    rows.forEach(function(row, index, array) {
      // 获取日志明细的HTML
      html = get_log_html(row);
      $("#log_rows").append(html);
    });
  }
}

// 获得员工访问记录列表
function get_action_log_list() {
    var api_url = 'action_log_list.php';
    // API调用
    CallApi(api_url, {"limit":100}, function (response) {
        // 访问记录明细展示
        show_action_log_list(response);
    }, function (response) {
        AlertDialog(response.errmsg);
    });
}
