// 修改日期设定
function setDateTag(ymd, week) {
  var weekday = ["星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期天"];
  layer.open({
      type: 2,
      title: '【' + ymd + '】' + weekday[week] + '的日期设定',
      fix: false,
      maxmin: true,
      shadeClose: true,
      shade: 0.8,
      area: ['500px', '250px'],
      content: 'dialog/hr_date_set.php?ymd=' + ymd
  });
};

// 获取具体日期设定的HTML
function get_date_html(row, index){
  var html = '';
  var week = index % 7;
  var month = row.date_ymd.substr(5, 2);
  var day = parseInt(row.date_ymd.substr(8, 2));
  var cur_month = $("#cur_month").val().substr(4, 2);
  var btn_class = 'btn-success';
  var date_type = '<span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span>';
  if (row.date_type == '1') {
    date_type = '<span class="glyphicon glyphicon-glass" aria-hidden="true"></span>';
    btn_class = 'btn-warning';
  }
  if (row.date_type == '2') {
    date_type = '<span class="glyphicon glyphicon-plane" aria-hidden="true"></span>';
    btn_class = 'btn-danger';
  }
  var date_tag = parseInt(month) + '月';
  if (row.date_tag != '')
    date_tag = row.date_tag;
  // 非本月数据
  var btn_active = ' onclick="setDateTag(\'' + row.date_ymd + '\',' + week + ')"';
  if (month != cur_month) {
    btn_class = 'btn-default';
    btn_active = 'disabled="disabled"';
  }
  if (week == 0)
    html = '<tr>';

  html += '<td width="14%"><button class="datebtn ' + btn_class + ' btn-lg btn-block" type="button"' + btn_active + '>';
  html += '<div class="pull-left">' + date_type + '</div>';
  html += '<div><strong style="font-size:20px;">' + day + '</strong></div>';
  html += '<div class="pull-right" style="font-size:15px;">' + date_tag + '</div>'
  html += '</button></td>';
  
  if (week == 6)
    html += '</tr>';
  return html;
}

// 节假日标志信息展示
function showMonthDateTag(response) {
  var aft_btn = '<button class="btn btn-info btn-block btn-lg" type="button" onclick="changeMonth(\'' + response.last_month + '\')">' + parseInt(response.last_month.substr(4, 2)) + '月</button>';
  var bef_btn = '<button class="btn btn-info btn-block btn-lg" type="button" onclick="changeMonth(\'' + response.next_month + '\')">' + parseInt(response.next_month.substr(4, 2)) + '月</button>';
  $("#bef_month").html(aft_btn);
  $("#aft_month").html(bef_btn);
  $("#cur_month_str").html(response.cur_month.substr(0, 4) + '年' + parseInt(response.cur_month.substr(4, 2)) + '月');
  $("#tag_list").html('');

  var rows = response.rows;
  var html_str = '';
  // 有日期标注数据
  if (rows.length > 0) {
    rows.forEach(function(row, index, array) {
        // 获取具体日期设定的HTML
        html_str += get_date_html(row, index);
    });
  } else {
    html_str = '<tr><td colspan="7">暂时没有日期数据</td></tr>';
  }
  $("#tag_list").html(html_str);
}

// 获得指定年月节假日标志
function getMonthDateTag(cur_month) {
    var api_url = 'get_month_date_tag.php';
    if (!cur_month)
      return;
    // API调用
    CallApi(api_url, {"ym":cur_month}, function (response) {
        // 节假日标志信息展示
        showMonthDateTag(response);
    }, function (response) {
        CallApiError(response);
    });
}

// 变更当前年月
function changeMonth(ym) {
    $("#cur_month").val(ym);
    // 节假日设定页面初始化
    initDateTag();
}

// 节假日设定页面初始化
function initDateTag() {
    var cur_month = $("#cur_month").val();
    // 获得指定年月节假日标志
    getMonthDateTag(cur_month);
}

$(function () {
    // 添加任务按钮点击事件
    $('#add_btn').click(function() {
        layer.open({
            type: 2,
            title: '初始化节假日',
            shadeClose: true,
            shade: 0.8,
            area: ['500px', '250px'],
            content: 'dialog/hr_date_init.php'
        });
    });

    // 节假日设定页面初始化
    initDateTag();
});
