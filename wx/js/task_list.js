// 员工ID
var staff_id;

$(function () {
  staff_id = GetQueryString('staff_id');
  // 获取员工任务一览
  get_staff_task(staff_id);
  // get_staff_task('06956826-B7E1-2F8E-F897-C3C0124D939C');
  $('.weui-navbar__item').on('click', function () {
      $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
      $(jQuery(this).attr("href")).show().siblings('.weui-tab__content').hide();
  });
})

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
  switch (row.task_status) {
    case '0':
      fmt = '其他';
      break;
    case '1':
      fmt = '完成';
      if (staff_id != row.respo_id)
        fmt = row.respo_name;
      break;
    case '2':
      fmt = '执行';
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
  if (row.task_status <= 1)
    return fmt;

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
              <h4 class="weui-media-box__title">' + row.task_name + '</h4>\
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
function show_staff_task(response) {
  var open_count = 0;
  var close_count = 0;
  var other_count = 0;
  var html;
  
  $("#open").html('');
  $("#close").html('');
  $("#other").html('');
  
  var rows = response.rows;
  if (rows.length > 0) {
    rows.forEach(function(row, index, array) {
      html = get_task_html(row);
      if(row.task_status == '2' || row.task_status == '3') {
        // 执行或等待的任务
        $("#open_task").append(html);
        open_count++;
      } else if(row.task_status == '1') {
        // 完成的任务
        $("#close_task").append(html);
        close_count++;
      } else if(row.task_status == '0') {
        // 废止的任务
        $("#other_task").append(html);
        other_count++;
      }
    });
  } else {
    $(".page__desc").html('空空如也... ...')
  }
  
  // 导航栏设置
  if (open_count > 0) {
    $("#open_count").html(open_count);
    $("#open_count").addClass('weui-badge');
    $("#open_nav").addClass('weui-bar__item_on');
    $("#open_task").show();
  } else if (close_count > 0) {
    $("#close_nav").addClass('weui-bar__item_on');
    $("#close_task").show();
  } else if (other_count > 0) {
    $("#other_nav").addClass('weui-bar__item_on');
    $("#other_task").show();
  }
  
  // 导航栏隐藏
  if (open_count == 0)
    $("#open_nav").hide();
  if (close_count == 0)
    $("#close_nav").hide();
  if (other_count == 0)
    $("#other_nav").hide();
};

// 获取员工任务一览
function get_staff_task() {
  var api_url = 'task_list.php';
  // API调用
  CallApi(api_url, {"staff_id":staff_id, "task_status":9}, function (response) {
    // 员工任务一览明细展示
    show_staff_task(response);
  }, function (response) {
    AlertDialog(response.errmsg);
  });
}



