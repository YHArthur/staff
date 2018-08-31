window.shareData = {
    // 分享标题
    title: "风赢科技行动",
    // 分享描述
    desc: "上海风赢网络科技有限公司员工行动详情【内部专用】",
    // 分享链接
    link: window.location.href,
    // 分享图标
    imgUrl: 'http://www.fnying.com/staff/wx/img/share.jpg',
    success: function () {},
    cancel: function () {}
};

$(function () {
    // 获取行动信息
    get_action_info();
});

// 完成状态格式化
function closeFormatter(is_closed) {
    if (is_closed == 1)
        return '已完成';
    return '待办';
}

// 行动明细展示
function show_action_info(response) {
    // 行动名称
    $('#action_title').html(response.action_title);
    // 预期结果
    $('#action_intro').html(response.action_intro);
    // 进展情况
    $('#result_memo').html(response.result_memo);
    // 责任人
    $('#respo_name').html(response.respo_name);
    // 完成状态
    $('#is_close').html(closeFormatter(response.is_closed));
    // 创建时间
    $('#ctime').html(response.ctime);
    // 完成时间
    $('#closed_time').html(response.closed_time);
    // 下载文件
    if (response.result_type == 'I' && response.result_name != '') {
      var file_type = response.result_name.split('.').pop();
      var html_str = '<a id="lnk_upload_file" href="' + response.result_name + '" target="_blank"><i class="weui-icon-download"></i>' + file_type + '附件下载</a>';
      $('#download_file').html(html_str);
    }
    
    // 微信分享处理
    window.shareData.title = response.action_title;
    window.shareData.desc = response.respo_name + ':' + response.action_desc;
    if (/MicroMessenger/i.test(navigator.userAgent)) {
        $.getScript("https://res.wx.qq.com/open/js/jweixin-1.2.0.js", function () {
            // 微信配置启动
            wx_config();
            wx.ready(function() {
                wx.onMenuShareTimeline(shareData);
                wx.onMenuShareAppMessage(shareData);
            });
        });
    }
}

// 获取行动信息
function get_action_info() {
    var api_url = 'action_info.php';
    var action_id = GetQueryString('id');
    CallApi(api_url, {"action_id": action_id}, function (response) {
        // 行动明细展示
        show_action_info(response);
    }, function (response) {
        AlertDialog(response.errmsg);
    });
};

