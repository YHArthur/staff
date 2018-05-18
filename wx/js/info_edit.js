
$(function () {
    // 获得员工本人情报
    get_my_info();
    
})
var old = {};
// 员工情报展示
function show_staff_info(response) {
    var birthday = response.birth_year + "-" + response.birth_day.replace('.', "-");
    for(index in response){
      old[index] = response[index];
    }
    old['birthday'] = birthday;
   
    $('.avata').attr('src', response.staff_avata);
    $('#staff_sex').val(response.staff_sex);
    $('#birthday').val(birthday);
    $('#staff_mbti').val(response.staff_mbti);
    $('#staff_memo').val(response.staff_memo);
    $('#nick_name').val(response.nick_name);
    $('#staff_position').val(response.staff_position);
    $('#staff_phone').val(response.staff_phone);
    $('#identity').val(response.identity);
}

// 获得员工本人情报
function get_my_info() {
    var api_url = 'get_my_info.php';
    CallApi(api_url, {}, function (response) {
       show_staff_info(response);
    }, function (response) {
        AlertDialog(response.errmsg);
    });
}


var apilist =new Array();
apilist = ["chooseImage","previewImage","uploadImage","downloadImage"];
  $(".btn").click(function(){ 
  // 微信配置启动
    wx_config(apilist);
    wx.ready(function(){
    wx.chooseImage({
      count: 1, // 默认9
      success: function (res) {
      var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
        wx.previewImage({
        current: '', // 当前显示图片的http链接
        urls: localIds // 需要预览的图片http链接列表
      });
      wx.uploadImage({
        localId: localIds.toString(), // 需要上传的图片的本地ID，由chooseImage接口获得,修改为toString()
        isShowProgressTips: 1, // 默认为1，显示进度提示
        success: function (res) {
          var serverId = res.serverId; // 返回图片的服务器端ID
          post_data = {"media_id":serverId};
          $.ajax({
            url: 'http://wx.fnying.com/get_media.php',
            dataType: "jsonp",
            data: post_data,
            success: function (response) {
              var row = response.media_url;
              $('.avata').attr('src', row);
              $('#staff_avata').val(row);
            }
          })
        },
        });
      }
    })
  })
})

//获取被修改的内容数组
var row = {};
function get_info_change(){
  $('#action').find('input[name]').each(function () {
    row[$(this).attr('name')] = $(this).val();
  });

  $('#action').find('select[name]').each(function () {
    row[$(this).attr('name')] = $(this).val();
  });

  $('#action').find('textarea[name]').each(function () {
    row[$(this).attr('name')] = $(this).val();
  });
  $('#clearBtn').removeClass("weui-btn_disabled");
  $('#clearBtn').addClass("weui-btn");
 
}

var post_data = {};
$('#clearBtn').click(function(){
  for(index in  row){
    if(old[index] != row[index]){
      post_data[index] = row[index]; 
    }
  }
  var api_url = 'staff_info_edit.php';
  CallApi(api_url, post_data, function (response) {
    if(response.errmsg == "修改成功"){
      window.open("http://test.fnying.com/staff/wx/main.php");
    }
    //修改成功后跳转的页面
}, function (response) {
    console.log(response.errmsg)
    alert("信息修改失败。请刷新页面重试!");
  });
})  
 
