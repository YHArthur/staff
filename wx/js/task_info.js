// 获得当前任务信息并展示
$(function () {
  task_info();
  var old = {};
  //事件绑定
  $('#action').find('input[name]').bind("change", function () {
    get_info_change()
  });
  $('#action').find('select[name]').bind("change", function () {
    get_info_change()
  });
  $('#action').find('textarea[name]').bind("change", function () {
    get_info_change()
  });
});

var task_id = $('#task_id').val();
function task_info(){
  var api_url = 'task_info.php';
  post_data = {"task_id":task_id};
  CallApi(api_url, post_data, function (response) {
    old = response.rows;
    var fmt = limitTimeFormatter(old)
    var star= '';

    if($('#task_level_l_l').length>0){
      $('#task_level_l_l').val(old.task_level);
    }
    if($('#task_status_l').length>0){
      $('#task_status_l').val(old.task_status);
    }
    for(var i=0;i<old.task_level;i++){
      star+='⭐';
    }
    if($('#task_status').length>0){
      switch(parseInt(old.task_status)){
        case 0:
          $('#task_status').addClass('abolish');
          break;
        case 1:
          $('#task_status').addClass('complete');
          break;
        case 2:
          var diff_day=CurrentStatus(old);
          if(diff_day<0){
            $('#task_status').addClass('delate');
          }else{
            $('#task_status').addClass('executing');
          }
          /*$('#task_status').addClass('executing');*/
          break;
        case 3:
          $('#task_status').addClass('wait');
          break;
      }
    }else if($('#task_status_check').length>0){
      $('#task_status_check').val(old.task_status)
    }

    //任务名称
    if($('#task_name').length>0){
      $('#task_name').text(old.task_name);
    }else{
      $('#task_name_check').val(old.task_name);
    }

    $('#check_name').val(old.check_name);

    //任务等级
    if($('#task_level').length>0){
      $('#task_level').val(star);
    }else if($('#task_level_check').length>0){
      $('#task_level_check').val(old.task_level);
    }

    $('#task_value').val(old.task_value);
    //任务进度
    if($('#task_perc').length>0){
      $('#task_perc_l').text(parseInt(old.task_perc));
      $(function(){
        var $sliderTrack = $('#sliderTrack'),
            $sliderHandler = $('#sliderHandler'),
            $task_perc = $('#task_perc_l');
  
        var totalLen = $('#sliderInner').width(),
            startLeft = 0,
            startX = 0;
        $sliderTrack.css('width',old.task_perc + '%');
        $sliderHandler.css('left',old.task_perc + '%');
        
        $sliderHandler.on('touchstart', function (e) {
                startLeft = parseInt($sliderHandler.css('left')) * totalLen / 100;
                startX = e.changedTouches[0].clientX;
            })
            .on('touchmove', function(e){
                var dist = startLeft + e.changedTouches[0].clientX - startX,
                    percent;
                dist = dist < 0 ? 0 : dist > totalLen ? totalLen : dist;
                task_perc =  parseInt(dist / totalLen * 100);
                $sliderTrack.css('width',task_perc + '%');
                $sliderHandler.css('left',task_perc + '%');
                $task_perc.text(task_perc);
                get_info_change();
                e.preventDefault();
            });
      });
    }else if($('#task_perc_check').length>0){
      $('#task_perc_check').val(old.task_perc);
    }

    //截至时间
    if($('#limit_time').length>0){
      $('#limit_time').val(old.limit_time.substr(5,5));
    }else if($('#limit_time_check').length>0){
      $('#limit_time_check').val(old.limit_time.substr(0,10));
    }
  
    //创建时间
    if($('#ctime').length>0){
      $('#ctime').val(old.ctime.substr(5,5));
    }

    $('#task_intro').val(old.task_intro.replace(/<[^>]+>/g,""));
    old['limit_time'] = old.limit_time.substr(5,5);
    old['task_intro'] = old.task_intro.replace(/<[^>]+>/g,"");
    old['task_level'] = star;
    old['ctime'] = old.ctime.substr(5,5);
    }, function (response) {
      AlertDialog(response.errmsg);
    });
};  
  
//获取当前页面被修改的内容数组
var NEW = {};
function get_info_change(){
  $('#action').find('input[name]').each(function () {
    NEW[$(this).attr('name')] = $(this).val();
  });

  $('#action').find('select[name]').each(function () {
    NEW[$(this).attr('name')] = $(this).val();
  });

  $('#action').find('textarea[name]').each(function () {
    NEW[$(this).attr('name')] = $(this).val();
  });
  $('#action').find('span[name]').each(function () {
    NEW[$(this).attr('name')] = $(this).text();
  });
}
$('#change').click(function(){
  get_info_change();
  task_edit(old,NEW);
})

//任务信息修改
var post_data = {};
$('#showTooltips').click(function(){
  task_edit(old,NEW);
})
function task_edit(old,NEW){
  for(index in  NEW){
    if(old[index] != NEW[index]){
      post_data[index] = NEW[index]; 
    }
  }
  var api_url = 'task_edit.php';
  CallApi(api_url, post_data, function (response) {
      console.log(response);
      AlertDialog(response.errmsg);
    }, function (response) {
      console.log(response);
      AlertDialog(response.errmsg);
    });
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

function CurrentStatus(row) {

  var limit_time = new Date(row.limit_time.replace(/-/g, "/"));
  
  // 相差日期计算
  var current_time = new Date();
  var diff_day = parseInt((limit_time.getTime() - current_time.getTime()) / (1000 * 3600 * 24));
  return diff_day;
}
