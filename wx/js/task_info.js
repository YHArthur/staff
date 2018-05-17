$(function () {
task_info();
});
var task_id = $('#task_id').val();

function task_info(){
var api_url = 'task_info.php';
post_data = {"task_id":task_id};
CallApi(api_url, post_data, function (response) {
  var row = response.rows;
  var  myTask_utime, myTask_task_id, myTask_task_name, myTask_task_intro, myTask_owner_id, myTask_owner_name, myTask_respo_id, myTask_respo_name, myTask_check_id,myTask_check_name,myTask_is_public,myTask_task_level,myTask_task_value,myTask_task_perc,myTask_task_status,myTask_limit_time;
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
  var j= '';
  var nowtime =  Date.parse(new Date())/1000;
  var later_end =Math.ceil((nowtime - myTask_utime)/(24*60*60));
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
  $('#task_name').val(myTask_task_name);
  $('#check_name').val(myTask_check_name);

  if($('#task_level').length>0){
    $('#task_level').val(myTask_task_level);
  }else{
    $('#task_level_l').val(j);
  }
  
  $('#task_value').val(myTask_task_value);
  $('#task_perc').val(myTask_task_perc);
  $('#task_status').val(myTask_task_status);
  $('#limit_time').val(myTask_limit_time.substr(0,10));
  $('#ctime').val(myTask_ctime.substr(0,10));
  $('#task_intro').val(myTask_task_intro.replace(/<[^>]+>/g,""));
  }, function (response) {
    console.log(response.errmsg);
  });
};  
