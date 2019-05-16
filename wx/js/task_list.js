// 员工ID
var staff_id;

$(function () {
  $(".page__desc").html('正在加载任务列表... ...');
  staff_id = GetQueryString('staff_id');
  // 获取员工执行中任务一览
  get_staff_task(staff_id, 0);
  // 获取员工已完成任务一览
  get_staff_task(staff_id, 1);
  $('.weui-navbar__item').on('click', function () {
      $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
      $(jQuery(this).attr("href")).show().siblings('.weui-tab__content').hide();
  });
})

// 任务名称格式化
function taskNameFormatter(row) {
  // 个人任务
  var task_self = '';
  if (row.is_self == 1)
    task_self = '● ';
  return task_self + row.task_name;
}

// 任务等级
function taskLevelFormatter(task_level) {
  var fmt = '';
  for(var i=0;i<task_level;i++)
    fmt +='⭐';
  return fmt;
}

// 任务状态格式化
function taskStatusFormatter(row) {
  var fmt = '?';
  switch (row.is_closed) {
    case '0':
      fmt = '执行';
      if (staff_id != row.respo_id)
        fmt = row.respo_name;
      break;
    case '1':
      fmt = '完成';
      if (staff_id != row.respo_id)
        fmt = row.respo_name;
      break;
  }
  return fmt;
}

// 任务期限格式化
function limitTimeFormatter(row) {
  var limit_time = new Date(row.limit_time.replace(/-/g, "/"));
  var month = limit_time.getMonth() + 1;
  var day = limit_time.getDate();
  var fmt = month+'月'+day+'日';
  // 已完成
  if (row.is_closed == '1')
      return fmt;
  // 长期
  if (row.is_limit == '0')
    return '长期';

  // 相差日期计算
  var current_time = new Date();
  var diff_day = parseInt((limit_time.getTime() - current_time.getTime()) / (1000 * 3600 * 24));
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

// 获取任务明细的HTML
function get_task_html(row){
  var  html ='\
  <div class="weui-panel">\
      <div class="weui-panel__bd">\
          <div class="weui-media-box weui-media-box_text">\
              <h4 class="weui-media-box__title">' + taskNameFormatter(row) + '</h4>\
              <div class="weui-media-box__desc">' + row.task_intro + '</div>\
              <ul class="weui-media-box__info">\
                  <li class="weui-media-box__info__meta">' + taskLevelFormatter(row.task_level) + '</li>\
                  <li class="weui-media-box__info__meta">' + limitTimeFormatter(row) + '</li>\
                  <li class="weui-media-box__info__meta weui-media-box__info__meta_extra">' + taskStatusFormatter(row) + '</li>\
              </ul>\
          </div>\
      </div>\
      <div class="weui-panel__ft">\
          <a href="task.php?id=' + row.task_id + '" class="weui-cell weui-cell_access weui-cell_link">\
              <div class="weui-cell__bd">查看任务</div>\
              <span class="weui-cell__ft"></span>\
          </a>\
      </div>\
  </div>\
  ';
  return html;
}

// 员工任务一览明细展示
function show_staff_task(response, is_closed) {
  var count = 0;
  var html;
  var task_div;
  
  if (is_closed == 0) {
    $("#open").html('');
    task_div = $("#open_task");
  } else if (is_closed == 1) {
    $("#close").html('');
    task_div = $("#close_task");
  }
  
  var rows = response.rows;
  if (rows.length > 0) {
    $(".page__desc").html('');
    rows.forEach(function(row, index, array) {
      html = get_task_html(row);
      task_div.append(html);
      count++;
    });
    // 导航栏显示
    if (is_closed == 0) {
      $("#open_count").html(count);
      $("#open_count").addClass('weui-badge');
      $("#open_nav").addClass('weui-bar__item_on');
      $("#open_task").show();
    } else if (is_closed == 1) {
      // if (!$("#open_nav").hasClass("weui-bar__item_on"))
        // $("#close_nav").addClass('weui-bar__item_on');
      $("#close_task").show();
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

// 获取员工任务一览
function get_staff_task(staff_id, is_closed) {
  var api_url = 'task_list.php';
  // API调用
  CallApi(api_url, {"staff_id":staff_id, "is_closed":is_closed, "limit":100}, function (response) {
    // 员工任务一览明细展示
    show_staff_task(response, is_closed);
  }, function (response) {
    AlertDialog(response.errmsg);
  });
}
