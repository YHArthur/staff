window.shareData = {
    // 分享标题
    title: "风赢科技每日完成行动列表",
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
    var day;
    day = parseInt(GetQueryString('day'));
    if (isNaN(day))
       day = 1;
    $("#day").val(day);

    // 获得完成行动列表
    get_action_list();
    
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

// 获取行动明细的HTML
function get_action_html(row){
  var  html ='\
  <div class="weui-panel">\
      <div class="weui-panel__bd">\
          <div class="weui-media-box weui-media-box_text">\
              <h4 class="weui-media-box__title">' + row.action_title + '</h4>\
              <div class="weui-media-box__desc">' + row.action_intro + '</div>\
              <ul class="weui-media-box__info">\
                  <li class="weui-media-box__info__meta">' + locationNameFormatter(row) + '</li>\
                  <li class="weui-media-box__info__meta">' + connectNameFormatter(row) + '</li>\
                  <li class="weui-media-box__info__meta weui-media-box__info__meta_extra">' + row.respo_name + '</li>\
              </ul>\
          </div>\
      </div>\
      <div class="weui-panel__ft">\
          <a href="action.php?id=' + row.action_id + '" class="weui-cell weui-cell_access weui-cell_link">\
              <div class="weui-cell__bd">查看行动</div>\
              <span class="weui-cell__ft"></span>\
          </a>\
      </div>\
  </div>\
  ';
  return html;
}

// 地点格式化
function locationNameFormatter(row) {
    if (row.is_location == '1')
      return row.location_name;
    return '-';
}

// 联络对象格式化
function connectNameFormatter(row) {
    if (row.connect_type != '0')
      return row.connect_name;
    return '';
}

// 参数替换
function changeUrlArg(url, arg, val){
    var pattern = arg+'=([^&]*)';
    var replaceText = arg+'='+val;
    return url.match(pattern) ? url.replace(eval('/('+ arg+'=)([^&]*)/gi'), replaceText) : (url.match('[\?]') ? url+'&'+replaceText : url+'?'+replaceText);
}

// 完成行动明细展示
function show_action_list(response) {
  var rows = response.rows;
  var day = parseInt($("#day").val());
  if (isNaN(day))
      day = 1;

  // 微信分享特别处理
  window.shareData.desc = response.action_day + ' 合计完成：' + response.total + ' 件';
  window.shareData.link = changeUrlArg(window.location.href, 'day', day);
  
  $("#action_day").html(response.action_day);
  $("#total").html(response.total);
  $("#action_rows").html('');
  $("#btn_list").html('');
  
  if (rows.length > 0) {
    rows.forEach(function(row, index, array) {
      html = get_action_html(row);
      $("#action_rows").append(html);
    });
  } else {
      $("#action_rows").html('');
  }

  // 第一天大于系统启动时间
  if (getDate(day) > '2018-09-03') {
      // 显示上一天按钮
      var lastbtn = '\
        <div class="weui-flex__item button_sp_area">\
          <a id="lastWeekBtn" href="javascript:;" onclick="change_day(1)" class="weui-btn weui-btn_primary">' + getDate(day + 1) + '</a>\
        </div>\
      ';
      $("#btn_list").append(lastbtn);
  }
  
  // 显示下一天按钮
  if (day > 0) {
      var nextbtn = '\
      <div class="weui-flex__item button_sp_area">\
        <a id="nextWeekBtn" href="javascript:;" onclick="change_day(-1)" class="weui-btn weui-btn_default">' + getDate(day - 1) + '</a>\
      </div>\
      ';
      $("#btn_list").append(nextbtn);
  }

}

// 前置0
function addPreZero(num, size){
    return ('000000000' + num).slice(-1 * size);
}

// 取得计算后的年月日
function getDate(num) {
    var date = new Date();
    date.setDate(date.getDate() - num);
    var month = addPreZero(date.getMonth() + 1, 2);
    var day = addPreZero(date.getDate(), 2);

    return date.getFullYear() + "-" + month + "-" + day;
}

// 变更日期事件
function change_day(num) {
  var day = parseInt($("#day").val());
  if (isNaN(day))
    day = 0;
  day += num;
  $("#day").val(day);
  // 获得完成行动列表
  get_action_list();
}

// 获得员工完成行动列表
function get_action_list() {
    var api_url = 'action_closed_list.php';
    var day = $("#day").val();
     if (isNaN(day))
        day = 1;
    // API调用
    CallApi(api_url, {"day":getDate(day), "limit":100}, function (response) {
        // 完成行动明细展示
        show_action_list(response);
    }, function (response) {
        AlertDialog(response.errmsg);
    });
}
