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

var post_data;

// 获取日志明细的HTML
function get_log_html(row) {
  // 第一行 姓名+IP地址
  var row_title, guset_name;
  if (row.staff_name != '游客') {
    if (post_data.id) {
       row_title = row.staff_name;
    } else {
       row_title = '<a href="javascript:;" onclick="javascript:showStaff(\'' + row.staff_id + '\');">' + row.staff_name + '</a>';
    }
  } else {
    guset_name = row.uuid.substring(0,4);
    if (post_data.uuid) {
       row_title = guset_name;
    } else {
       row_title = '<a href="javascript:;" onclick="javascript:showUUID(\'' + row.uuid + '\');">' + guset_name + '</a>';
    }
  }
  
  if (post_data.ip) {
    row_title += '【' + row.ip + '】';
  } else {
     row_title += '【<a href="javascript:;" onclick="javascript:showIP(\'' + row.action_ip + '\');">' + row.ip + '</a>】';
  }

  // 第二行 时间+地理位置
  var row_desc = row.time;
  if ((parseFloat(row.latitude) * parseFloat(row.longitude)) != 0)
    // row_desc += ' <img src="img/map.jpg" height="12px" onclick="javascript:showMap(\'' + row.latitude + '\',\'' + row.longitude + '\',\'' + row.staff_name + '\',\'' + row.time + '\');">';
    row_desc = '<a href="javascript:;" onclick="javascript:showMap(\'' + row.latitude + '\',\'' + row.longitude + '\',\'' + row.staff_name + '\',\'' + row.time + '\');">' + row.time + '</a>';
    
  // 第三行 访问链接
   var row_url;
   if (post_data.url) {
     row_url = row.action_url;
   } else {
     row_url = '<a href="javascript:;" onclick="javascript:showURL(\'' + row.action_url + '\');">' + row.action_title + '</a>';
   }
   
  var  html ='\
  <div class="weui-panel">\
      <div class="weui-panel__bd">\
          <div class="weui-media-box weui-media-box_text">\
              <h4 class="weui-media-box__title">' + row_title + '</h4>\
              <div class="weui-media-box__desc">' + row_desc + '</div>\
              <ul class="weui-media-box__info">\
                  <li class="weui-media-box__info__meta">' + row_url + '</li>\
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

// 全部记录明细展示
function showAll() {
    post_data = {limit: 100};
    $(".page__title").html('访问记录');
    get_action_log_list(post_data);
}

// 单个员工访问记录明细展示
function showStaff(id) {
    post_data['id'] = id;
    $(".page__title").html('<a href="javascript:;"; onclick="javascript:showAll();">访问记录</a>');
    get_action_log_list(post_data);
}

// 单个UUID访问记录明细展示
function showUUID(id) {
    post_data['uuid'] = id;
    $(".page__title").html('<a href="javascript:;"; onclick="javascript:showAll();">访问记录</a>');
    get_action_log_list(post_data);
}

// 单个URL访问记录明细展示
function showURL(url) {
    post_data['url'] = url;
    $(".page__title").html('<a href="javascript:;"; onclick="javascript:showAll();">访问记录</a>');
    get_action_log_list(post_data);
}

// 单个IP访问记录明细展示
function showIP(ip) {
    post_data['ip'] = ip;
    $(".page__title").html('<a href="javascript:;"; onclick="javascript:showAll();">访问记录</a>');
    get_action_log_list(post_data);
}

// 微信地理位置
function showMap(latitude, longitude, staff_name, time){
    if (IsWeiXin()) {
        wx.openLocation({
            latitude: parseFloat(latitude), // 纬度，浮点数，范围为90 ~ -90
            longitude: parseFloat(longitude), // 经度，浮点数，范围为180 ~ -180。
            name: staff_name, // 位置名
            address: time, // 地址详情说明
            scale: 16, // 地图缩放级别,整形值,范围从1~28。默认为最大
            infoUrl: window.location.href // 在查看位置界面底部显示的超链接,可点击跳转
        });
    } else {
        var url = 'https://apis.map.qq.com/uri/v1/marker?marker=coord:' + latitude + ',' + longitude + ';title:' + staff_name + ';addr:' + time + '&referer=myapp';
        // AlertDialog(longitude + ',' + latitude + '<br>' + '<a href="' + url + '" target="_blank">地理位置</a>');
        window.open(url, 'map');
    }
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
function get_action_log_list(post_data) {
    var api_url = 'action_log_list.php';
    // API调用
    CallApi(api_url, post_data, function (response) {
        // 访问记录明细展示
        show_action_log_list(response);
    }, function (response) {
        AlertDialog(response.errmsg);
    });
}

$(function () {
    // 获得员工访问记录列表
    showAll();

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