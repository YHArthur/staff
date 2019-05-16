// 员工ID 
var staff_id;

$(function () {
  $(".page__desc").html('正在加载行动列表... ...');
  staff_id = GetQueryString('staff_id');
  // 获取员工执行中行动一览
  get_staff_action(staff_id, 0);
  // 获取员工已完成行动一览
  get_staff_action(staff_id, 1);
  $('.weui-navbar__item').on('click', function () {
      $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
      $(jQuery(this).attr("href")).show().siblings('.weui-tab__content').hide();
  });
})

// 前置0
function addPreZero(num, size){
 return ('000000000' + num).slice(-1 * size);
}

// 取得当前时间
function getNowDate() {
    var weekday = new Array("日", "一", "二", "三", "四", "五", "六");
    var date = new Date();
    var month = addPreZero(date.getMonth() + 1, 2);
    var day = addPreZero(date.getDate(), 2);
    var week = '星期' + weekday[date.getDay()];
    var hours = addPreZero(date.getHours(), 2);
    var minutes = addPreZero(date.getMinutes(), 2);
    var seconds = addPreZero(date.getSeconds(), 2);

    return date.getFullYear() + "-" + month + "-" + day + " " + week + ' ' + hours + ":" + minutes + ":" + seconds;
}

// 行动名称格式化
function actionNameFormatter(row) {
  // 个人任务
  var task_self = '● ';
  if (row.is_self == '0')
    task_self = '';
  return task_self + row.action_title;
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
        var staff_id = GetQueryString('staff_id');
        if (row.respo_id == staff_id) {
          fmt = '待办';
        } else {
          fmt = row.respo_name + ' 待办';
        }
        break;
      case '1':
        var closed_time = new Date(row.closed_time.replace(/-/g, "/"));
        var month = closed_time.getMonth() + 1;
        var day = closed_time.getDate();
        fmt = month+'月'+day+'日 完成';
        break;
    }
    return fmt;
}

// 获取行动明细的HTML
function get_action_html(row){
  var  html ='\
  <div class="weui-panel">\
      <div class="weui-panel__bd">\
          <div class="weui-media-box weui-media-box_text">\
              <h4 class="weui-media-box__title">' + actionNameFormatter(row) + '</h4>\
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

// 员工行动一览明细展示
function show_staff_action(response, is_closed) {
  var count = 0;
  var html;
  var action_div;
  
  if (is_closed == 0) {
    $("#open").html('');
    action_div = $("#open_action");
  } else if (is_closed == 1) {
    $("#close").html('');
    action_div = $("#close_action");
  }
  
  var rows = response.rows;
  if (rows.length > 0) {
    var html_str = '<a id="btn_new" href="action_new.php" class="weui-btn weui-btn_plain-primary">新的行动</a>';
    $("#time_stamp").html(getNowDate());
    $(".page__desc").html(html_str);
    rows.forEach(function(row, index, array) {
      html = get_action_html(row);
      action_div.append(html);
      count++;
    });
    // 导航栏显示
    if (is_closed == 0) {
      $("#open_count").html(count);
      $("#open_count").addClass('weui-badge');
      $("#open_nav").addClass('weui-bar__item_on');
      $("#open_action").show();
    } else if (is_closed == 1) {
      // if (!$("#open_nav").hasClass("weui-bar__item_on"))
        // $("#close_nav").addClass('weui-bar__item_on');
      $("#close_action").show();
    }
  } else {
    // 导航栏隐藏
    if (is_closed == 0) {
      $("#open_nav").hide();
    } else if (is_closed == 1) {
      $("#close_nav").hide();
    }
  }

};

// 获取员工行动一览
function get_staff_action(staff_id, is_closed) {
  var api_url = 'action_list.php';
  // API调用
  CallApi(api_url, {"staff_id":staff_id, "is_closed":is_closed, "limit":100}, function (response) {
    // 员工行动一览明细展示
    show_staff_action(response, is_closed);
  }, function (response) {
    AlertDialog(response.errmsg);
  });
}
