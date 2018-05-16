$(function () {
  task_show();
});
var staff_name = $('#staff_name').text();
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
post_data = {};
CallApi(api_url, post_data, function (response) {
  var rows = response.rows;
  var myTask_utime, myTask_task_id, myTask_task_name, myTask_task_intro, myTask_owner_id, myTask_owner_name, myTask_respo_id, myTask_respo_name, myTask_check_id,myTask_check_name,myTask_is_public,myTask_task_level,myTask_task_value,myTask_task_perc,myTask_task_status,myTask_limit_time;
  rows.forEach(function(row, index, array) {
    myTask_task_id = row.task_id;
    myTask_task_name = row.task_name;
    myTask_task_intro = row.task_intro;
    myTask_owner_id = row.owner_id;
    myTask_owner_name = row.owner_name;
    myTask_respo_id = row.respo_id;
    myTask_respo_name = row.respo_name;
    myTask_check_id = row.check_id;
    myTask_check_name = row.check_name;
    myTask_is_public = row.is_public;
    myTask_task_level = row.task_level;
    myTask_task_value = row.task_value;
    myTask_task_perc = row.task_perc;
    myTask_task_status = row.task_status;
    myTask_limit_time = row.limit_time;
    myTask_utime = row.utime;
    myTask_ctime = row.ctime;
    var ctime = Date.parse(myTask_ctime)/1000;
    var j= '';
    var nowtime =  Date.parse(new Date())/1000;
    var later_end =Math.ceil((nowtime - ctime)/(24*60*60));
    var later = "";
    for(var i=0;i<myTask_task_level;i++){
      j+='⭐';
    }
    if(myTask_task_status == 0){
      myTask_task_status='废止';
      later = "废止";
    }else if(myTask_task_status == 1){
      myTask_task_status='完成';
      later = "完成";
    }else if(myTask_task_status == 2){
      myTask_task_status='执行';
      later = "【延迟"+ later_end +"天】";
    }else if(myTask_task_status == 3){
      myTask_task_status='等待';
      later = "【等待"+ later_end +"天】";
    }
    var url = '?task_id='+myTask_task_id +'&respo_name='+ myTask_respo_name;
    var  mytask ='\
      <li class="execute_item">\
        <a href="task_content.php'+url+'">\
          <div class="weui-flex js_category pa" id="pa">\
            <div class="right">\
              <div class="weui-cell_hd child-1">\
                <label class="weui-label wid-170">'+ myTask_task_name +'</label>\
              </div>\
              <div class="weui-cell_bd child-1">\
                <label class="weui-label wid-170">'+ j +'</label>\
              </div>\
            </div>\
            <div class="left">\
              <div class="weui-cell_bd child-1">\
                <label class="weui-label  wid-100">'+ myTask_limit_time.substr(0,10) +'</label>\
              </div>\
              <div class="weui-cell_bd child-1">\
                <label class="weui-label wid-100">'+ later +'</label>\
              </div>\
            </div>\
            <label class="weui-cell_access" data-id="button" href="javascript:;">\
              <div class="weui-cell__ft"></div>\
            </label>\
          </div>\
        </a>\
      </li>\
            ';     
    if(row.check_name == staff_name){
      var  mycheck ='\
        <li class="supervise_item">\
          <a href="task_content.php'+url+'">\
            <div class="weui-flex js_category pa">\
              <div class="right">\
                <div class="weui-cell_hd child-1">\
                  <label class="weui-label wid-170">'+ myTask_task_name +'</label>\
                </div>\
                <div class="weui-cell_bd child-1">\
                  <label class="weui-label wid-170">'+ myTask_task_level +'</label>\
                </div>\
              </div>\
              <div class="left">\
                <div class="weui-cell_bd child-1">\
                  <label class="weui-label  wid-100">'+ myTask_limit_time.substr(0,10) +'</label>\
                </div>\
                <div class="weui-cell_bd child-1">\
                  <label class="weui-label wid-100">【延迟'+ later +'天】</label>\
                </div>\
              </div>\
              <label class="weui-cell_access" data-id="button" href="javascript:;">\
                <div class="weui-cell__ft"></div>\
              </label>\
            </div>\
          </a>\
        </li>\
            ';
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
