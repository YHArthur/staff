$(function () {
  task_show();
});
var staff_id = $('#staff_id').text();
var mycheck_l = "";
var mytask_l = "";
var pre = '<div class="container" id="container">\
<div class="page home js_show" id="top-font">\
  <div class="page__hd">\
    <h1 class="page__title">任务管理</h1>\
  </div>\
</div>\
<div class="page navbar js_show top-10">\
  <div class="page_bd">\
    <div class="weui-tab">\
      <div class="weui-navbar" style="position:relative;">\
        <div class="weui-bar__item_on weui-navbar__item" id="execute">\
          我执行的\
        </div>\
        <div class="weui-navbar__item" id="supervise">\
          我监督的\
        </div>\
      </div>\
    </div>\
  <div class="page__bd page__bd_spacing top-50" id="nav">\
    ';
var  mytaskpre = '<!-- 我执行的 -->\
<ul class="execute">\
    ';
var  mycheckpre = '<!-- 我监督的 -->\
<ul class="supervise">\
    ';
function task_show(){
var api_url = 'task_list.php';
post_data = {'staff_id':staff_id};
CallApi(api_url, post_data, function (response) {
  var rows = response.rows;
  rows.forEach(function(row, index, array) {
   
    var fmt = limitTimeFormatter(row)
    var star= '';
  
    for(var i=0;i<row.task_level;i++){
      star+='⭐';
    }
   
    var url = '?task_id='+row.task_id;
    //我执行的
    var  mytask ="<li class='execute_item'>" + task_list(row,star,fmt) + "</li>";
    //我监督的
    if(row.check_id == staff_id){
      var  mycheck = "<li class='supervise_item'>" + task_list(row,star,fmt) + "</li>";
      }else{
        var mycheck ="";
        }
    mytask_l +=mytask;
    if(mycheck == ""){
      mycheck_l ="";
    }else{
      mycheck_l += mycheck;
      }
  });
  var mytaskend = "</ul>";
  var mycheckend = "</ul>";
  var end='</div></div></div></div>';
  var all= pre + mytaskpre + mytask_l + mytaskend + mycheckpre + mycheck_l + mycheckend + end + "<script src='js/task_list.js'></script>";
  $('.info').append(all);    
}, function (response) {
  console.log(response.errmsg);
  });
};

function task_list(row,star,fmt){
  var  mytask ='\
    <a href="task_content.php?task_id=' +row.task_id +'">\
    <div class="weui-flex js_category pa">\
      <div class="right">\
        <div class="weui-cell_hd child-1">\
          <label class="weui-label wid-170">'+ row.task_name +'</label>\
        </div>\
        <div class="weui-cell_bd child-1">\
          <label class="weui-label wid-170">'+ star +'</label>\
        </div>\
      </div>\
      <div class="left">\
        <div class="weui-cell_bd child-1">\
          <label class="weui-label wid-100">'+ fmt+'</label>\
        </div>\
      </div>\
      <label class="weui-cell_access" data-id="button" href="javascript:;">\
        <div class="weui-cell__ft"></div>\
      </label>\
    </div>\
  </a>\
  ';
  return mytask;
}

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
