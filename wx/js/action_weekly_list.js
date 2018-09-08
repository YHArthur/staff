window.shareData = {
    // 分享标题
    title: "风赢科技每周完成行动列表",
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
    var week;
    week = parseInt(GetQueryString('week'));
    if (isNaN(week))
       week = 0;
    $("#week").val(week);
    
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
  var week = parseInt($("#week").val());
  if (isNaN(week))
      week = 0;

  // 微信分享特别处理
  window.shareData.desc = response.week_begin + '-' + response.week_end + ' 合计完成：' + response.total + ' 件';
  window.shareData.link = changeUrlArg(window.location.href, 'week', week);
  
  $("#week_begin").html(response.week_begin);
  $("#week_end").html(response.week_end);
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
  if (response.week_begin > '18年9月3日') {
      // 显示上一周按钮
      var lastbtn = '\
        <div class="weui-flex__item button_sp_area">\
          <a id="lastWeekBtn" href="javascript:;" onclick="change_week(1)" class="weui-btn weui-btn_primary">上一周</a>\
        </div>\
      ';
      $("#btn_list").append(lastbtn);
  }
  
  
  // 显示下一周按钮
  if (week > 0) {
      var nextbtn = '\
      <div class="weui-flex__item button_sp_area">\
        <a id="nextWeekBtn" href="javascript:;" onclick="change_week(-1)" class="weui-btn weui-btn_default">下一周</a>\
      </div>\
      ';
      $("#btn_list").append(nextbtn);
  }

}

// 变更周事件
function change_week(num) {
  var week = parseInt($("#week").val());
  if (isNaN(week))
    week = 0;
  week += num;
  $("#week").val(week);
  // 获得完成行动列表
  get_action_list();
}

// 获得员工完成行动列表
function get_action_list() {
    var api_url = 'action_closed_list.php';
    var week = $("#week").val();
    if (isNaN(week))
        week = 0;
    // API调用
    CallApi(api_url, {"week":week, "limit":100}, function (response) {
        // 员工本周补贴明细展示
        show_action_list(response);
    }, function (response) {
        AlertDialog(response.errmsg);
    });
}
