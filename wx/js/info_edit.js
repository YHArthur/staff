
$(function () {
    // 获得员工本人情报
    get_my_info();
})
   
// 员工情报展示
function show_staff_info(response) {
    var birthday = response.birth_year + "-" + response.birth_day.replace('.', "-");
    
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

// 
$(".btn").onclick = function(){ 
    // 微信配置启动
    wx_config(chooseImage);
    wx.ready(function() {
        wx.chooseImage({
            count: 1, // 默认9
            sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
            sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
            success: function (res) {
                var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                var tempFilePaths = res.tempFilePaths;
                console.log(res);
                console.log(tempFilePaths);
        }
    })
  })
}
