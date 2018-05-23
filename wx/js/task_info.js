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
    var j= '';
    var nowtime =  Date.parse(new Date())/1000;
    var later_end =Math.ceil((nowtime - old.ctime)/(24*60*60));
    var later = "";
    if($('#task_level_l_l').length>0){
      $('#task_level_l_l').val(old.task_level);
    }
    if($('#task_status_l').length>0){
      $('#task_status_l').val(old.task_status);
    }
    for(var i=0;i<old.task_level;i++){
      j+='⭐';
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
          $('#task_status').addClass('executing');
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
      $('#task_level').val(j);
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
    old['task_level'] = j;
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

//任务信息修改
var post_data = {};
$('#showTooltips').click(function(){
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
})
