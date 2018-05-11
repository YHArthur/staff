$(function () {
    // 获得员工本周补贴明细
    get_current_subsidy();
})

// 员工本周补贴明细展示
function show_current_subsidy(response) {
  var sign_info, dt, time_begin, time_end, commute_subsidy, lunch_subsidy, dinner_subsidy;
  var weekday = ["星期天", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"];
  var rows = response.rows;
  $(".page__desc").html('合计：¥' + parseInt(response.sum) + '.00');
  
  if (rows.length > 0) {
      rows.forEach(function(row, index, array) {
          sign_info = row.sign_date;
          dt = new Date(sign_info.replace(/-/g, "/"));
          subsidy_info = weekday[dt.getDay()];
          
          time_begin = row.time_begin;
          if (time_begin != '')
            sign_info += ' 签入: ' + time_begin;
          time_end = row.time_end;
          if (time_end != '')
            sign_info += ' 签出: ' + time_end;
          
          if (row.commute_subsidy > 0)
            subsidy_info += '【交通补助】'
          if (row.lunch_subsidy > 0)
            subsidy_info += '【午餐补助】'
          if (row.dinner_subsidy > 0)
            subsidy_info += '【晚餐补助】'

          subsidy_row = '\
          <div class="weui-cells__title">' + sign_info + '</div>\
          <div class="weui-cell">\
              <div class="weui-cell__bd">'+ subsidy_info + '</div>\
          </div>\
          ';
          $("#subsidy_rows").append(subsidy_row);
      });
  }
}

// 获得员工本周补贴明细
function get_current_subsidy() {
    var api_url = 'get_current_subsidy.php';
    var staff_id = GetQueryString('staff_id');
    // API调用
    CallApi(api_url, {"staff_id":staff_id}, function (response) {
        // 员工本周补贴明细展示
        show_current_subsidy(response);
    }, function (response) {
        AlertDialog(response.errmsg);
    });
}

 