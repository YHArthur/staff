// 禁止在微信以外的浏览器里打开
window.onload = function(){
  if(!IsWeiXin()){
    window.location.href="http://www.fnying.com/staff/forbiden.php?code=2";
  }
}
  
$(function () {
  // 分享标题
  var ShareTitle = '员工微信签到';
  // 分享描述
  var ShareDesc = '上海风赢网络科技有限公司员工签到页面【内部专用】';
  // 分享链接
  var ShareLink = window.location.href;
  // 分享图标
  var ShareimgUrl = 'http://www.fnying.com/staff/wx/img/share.jpg';

  // 微信配置启动
  wx_config();

  wx.ready(function() {

      wx.onMenuShareTimeline({
          title: ShareTitle,
          desc: ShareDesc,
          link: ShareLink,
          imgUrl: ShareimgUrl
      });

      wx.onMenuShareAppMessage({
          title: ShareTitle,
          desc: ShareDesc,
          link: ShareLink,
          imgUrl: ShareimgUrl
      });
      
      wx.getLocation({
          type: 'gcj02',
          success: function (res) {
            staff_sign(res.latitude, res.longitude);
          },
          cancel: function (res) {
            AlertDialog('地理位置获取失败，无法签到');
          }
      });
  });


});

// 员工签到处理
function staff_sign(latitude, longitude) {
  var $this = $(this);
  var api_url = 'office_sign.php';
  var post_data = {sign_type: '白金湾339签入', latitude: latitude, longitude: longitude};
  // 员工注册处理
  CallApi(api_url, post_data, function (response) {
    Toast('签到成功');

    
} // 展示员工签到记录
    var limit = 10;
    var offset = 0;
        Qiandao(limit, offset, function (response) {
            var log_id,sign_type,staff_name,ctime;
            var count = response.rows;
            for (var i = 0; i < count.length; i++) {
               var jilu = '\
        <label class="weui-cell weui-check__label" for="x' +count[i].log_id+ '">\
          <div class="weui-cell__bd">' + count[i].sign_type + ' <span>' + count[i].staff_name + '</span></div>\
          <div class="weui-cell__ft">' + count[i].ctime+ '</div>\
        </label>\
        ';
                $("#tab").append(jilu);
            }
            alert(count);
        }, function (response) {
            console.log(response.errmsg)
        });


  }, function (response) {
    AlertDialog(response.errmsg);
  });

// 获取签到记录
function Qiandao(limit, offset, suc_func, error_func) {
    var api_url = 'http://www.fnying.com/staff/api/office_sign_log.php';
    var post_data = {"limit": limit, "offset": offset};
    CallApi(api_url, post_data, suc_func, error_func);
}
