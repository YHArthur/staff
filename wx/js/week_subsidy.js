$(function () {
    // 获得员工周补贴明细
    get_week_subsidy();
})

// 员工本周补贴明细展示
function show_week_subsidy(response) {
  var first_day, sign_info, rest_break, dt, time_begin, time_end, commute_subsidy, lunch_subsidy, dinner_subsidy;
  var weekday = ["星期天", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"];
  var rows = response.rows;
  $("#staff_name").html(response.staff_name);
  $("#week_begin").html(response.week_begin);
  $("#week_end").html(response.week_end);
  $("#week_sum").html('¥' + parseInt(response.sum) + '.00');
  $("#subsidy_rows").html('');
  $("#btn_list").html('');
  
  if (rows.length > 0) {
      first_day = rows[0].sign_date;
      rows.forEach(function(row, index, array) {
          sign_info = row.sign_date;
          dt = new Date(sign_info.replace(/-/g, "/"));
          subsidy_info = weekday[dt.getDay()];
          rest_break = false;
          if (subsidy_info == '星期六' || subsidy_info == '星期天')
            rest_break = true;
          
          time_begin = row.time_begin;
          if (time_begin != '') {
            sign_info += ' 签入: ' + time_begin;
            rest_break = false;
          }
          time_end = row.time_end;
          if (time_end != '') {
            sign_info += ' 签出: ' + time_end;
            rest_break = false;
          }
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
          
          if (rest_break == false)
            $("#subsidy_rows").append(subsidy_row);
      });
  }
  
  // 第一天大于签到系统启动时间并且大于员工加入时间
  if (first_day > '2018-04-02' && first_day > response.join_date) {
      // 显示上一周按钮
      var lastbtn = '\
        <div class="weui-flex__item button_sp_area">\
          <a id="lastWeekBtn" href="javascript:;" onclick="change_week(1)" class="weui-btn weui-btn_primary">上一周</a>\
        </div>\
      ';
      $("#btn_list").append(lastbtn);
  }
  
  var week = $("#week").val();
  if (isNaN(week))
    week = 0;

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
  // 获得员工周补贴明细
  get_week_subsidy();
}

// 获得员工周补贴明细
function get_week_subsidy() {
    var api_url = 'get_week_subsidy.php';
    var staff_id = GetQueryString('staff_id');
    var week = $("#week").val();
    if (isNaN(week))
      week = 0;
    // API调用
    CallApi(api_url, {"staff_id":staff_id,"week":week}, function (response) {
        // 员工本周补贴明细展示
        show_week_subsidy(response);
    }, function (response) {
        AlertDialog(response.errmsg);
    });
}

