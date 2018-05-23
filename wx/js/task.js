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
   
    var ctime = Date.parse(row.ctime)/1000;
    var j= '';
    var nowtime =  Date.parse(new Date())/1000;
    var later_end =Math.ceil((nowtime - ctime)/(24*60*60));
    var later = "";
    for(var i=0;i<row.task_level;i++){
      j+='⭐';
    }
    switch(parseInt(row.task_status)){
      case 0:
        later = "废止";
        break;
      case 1:
        later = "完成";
        break;
      case 2:
        later = "【延迟"+ later_end +"天】";
        break;
      case 3:
        later = "【等待"+ later_end +"天】";
        break;
    }
    var url = '?task_id='+row.task_id;
    //我执行的
    var  mytask ="<li class='execute_item'>" + task_list(row,j,url,later) + "</li>";
    //我监督的
    if(row.check_id == staff_id){
      var  mycheck = "<li class='supervise_item'>" + task_list(row,j,url,later) + "</li>";
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

function task_list(row,j,url,later){
  var  mytask ='\
    <a href="task_content.php'+url+'">\
    <div class="weui-flex js_category pa">\
      <div class="right">\
        <div class="weui-cell_hd child-1">\
          <label class="weui-label wid-170">'+ row.task_name +'</label>\
        </div>\
        <div class="weui-cell_bd child-1">\
          <label class="weui-label wid-170">'+ j +'</label>\
        </div>\
      </div>\
      <div class="left">\
        <div class="weui-cell_bd child-1">\
          <label class="weui-label  wid-100">'+ row.limit_time.substr(0,10) +'</label>\
        </div>\
        <div class="weui-cell_bd child-1">\
          <label class="weui-label wid-100">'+ later+'</label>\
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
