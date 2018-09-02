window.shareData = {
    // 分享标题
    title: "风赢科技任务",
    // 分享描述
    desc: "上海风赢网络科技有限公司员工任务详情【内部专用】",
    // 分享链接
    link: window.location.href,
    // 分享图标
    imgUrl: 'http://www.fnying.com/staff/wx/img/share.jpg',
    success: function () {},
    cancel: function () {}
};

$(function () {
    // 获取任务信息
    get_task_info();
});

// 任务等级
function taskLevelFormatter(task_level) {
    var fmt = '';
    for(var i=0;i<task_level;i++)
      fmt +='⭐';
    return fmt;
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

// 是否完成格式化
function isClosedFormatter(row) {
    var fmt = '?';
    switch (row.is_closed) {
      case '0':
        fmt = '待办';
        break;
      case '1':
        fmt = '完成';
        break;
    }
    return fmt;
}

// 任务期限格式化
function limitTimeFormatter(row) {
    var ltime = new Date(row.limit_time.replace(/-/g, "/"));
    var month = ltime.getMonth() + 1;
    var day = ltime.getDate();
    var fmt = month+'月'+day+'日';
    // 已完成
    if (row.is_closed == '1')
        return fmt;
    // 长期
    if (row.is_limit == '0')
      return '长期';

    // 相差日期计算
    var current_time = new Date();
    var diff_day = parseInt((ltime.getTime() - current_time.getTime()) / (1000 * 3600 * 24));
    if (diff_day == 0) {
        fmt += '【<span class="bg-warning">当天</span>】';
        return fmt;
    } else if (diff_day < 0) {
        fmt += '【<span class="bg-danger">延迟 ';
        diff_day *= -1;
    } else {
        fmt += '【<span>还剩 ';
    }
    if (diff_day <= 7) {
        fmt += diff_day + ' 天</span>】';
    } else if (diff_day <= 30) {
        fmt += parseInt(diff_day / 7) + ' 周</span>】';
    } else {
        fmt += parseInt(diff_day / 30) + ' 个月</span>】';
    }
    return fmt;
}

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
                  <li class="weui-media-box__info__meta weui-media-box__info__meta_extra">' + isClosedFormatter(row) + '</li>\
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

// 完成状态格式化
function closeFormatter(is_closed) {
    if (is_closed == 1)
        return '已完成';
    return '执行中';
}

// 任务明细展示
function show_task_info(response) {
    // 任务名称
    $('#task_name').html(response.task_name);
    // 任务等级
    $('#task_star').html(taskLevelFormatter(response.task_level));
    // 任务内容
    $('#task_intro').html(response.task_intro);
    // 进展情况
    $('#result_memo').html(response.result_memo);
    // 责任人
    $('#respo_name').html(response.respo_name);
    // 任务期限
    $('#limit_time').html(limitTimeFormatter(response));
    // 监督人
    $('#check_name').html(response.check_name);
    // 创建时间
    $('#ctime').html(response.ctime);
    // 完成状态
    $('#is_close').html(closeFormatter(response.is_closed));
    if (response.is_closed == 1) {
      // 完成时间
      $('#closed_time').html(response.closed_time);
    } else {
      $('#div_close_time').hide();
    }
    
    // 行动列表
    var rows = response.action_rows;
    if (rows.length > 0) {
      $("#action_title").html('行动列表');
      $("#action_list").html('');
      rows.forEach(function(row) {
        html = get_action_html(row);
        $("#action_list").append(html);
      });
    }

    // 微信分享处理
    window.shareData.title = response.task_name;
    window.shareData.desc = response.respo_name + ':' + response.task_desc;
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
}

// 获取任务信息
function get_task_info() {
    var api_url = 'task_info.php';
    var task_id = GetQueryString('id');
    CallApi(api_url, {"task_id": task_id}, function (response) {
        // 任务明细展示
        show_task_info(response);
    }, function (response) {
        AlertDialog(response.errmsg);
    });
};

