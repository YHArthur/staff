window.shareData = {
    // 分享标题
    title: "风赢科技任务",
    // 分享描述
    desc: "上海风赢网络科技有限公司员工任务详情【内部专用】",
    // 分享链接
    link: window.location.href,
    // 分享图标
    imgUrl: 'http://www.fnying.com/staff/wx/img/share.jpg',
    success: function () {},
    cancel: function () {}
};

$(function () {
    // 获取任务信息
    get_task_info();
});

// 任务等级格式化
function taskLevelFormatter(task_level) {
    var fmt = '';
    for(var i=0;i<task_level;i++)
      fmt +='⭐';
    return fmt;
}

// 任务期限格式化
function limitTimeFormatter(limit_time, task_status) {
    var ltime = new Date(limit_time.replace(/-/g, "/"));
    var month = ltime.getMonth() + 1;
    var day = ltime.getDate();
    var fmt = month+'月'+day+'日';
    if (task_status <= 1)
        return fmt;

    // 相差日期计算
    var current_time = new Date();
    var diff_day = parseInt((ltime.getTime() - current_time.getTime()) / (1000 * 3600 * 24));
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

// 任务明细展示
function show_task_info(response) {
    // 任务名称
    $('#task_name').html(response.task_name);
    // 任务等级
    $('#task_star').html(taskLevelFormatter(response.task_level));
    // 任务内容
    $('#task_intro').html(response.task_intro);
    // 责任人
    $('#respo_name').html(response.respo_name);
    // 任务期限
    $('#limit_time').html(limitTimeFormatter(response.limit_time, response.task_status));
    // 监督人
    $('#check_name').html(response.check_name);
    // 创建时间
    $('#ctime').html(response.ctime);
    
    // 微信分享处理
    window.shareData.title = response.task_name;
    window.shareData.desc = response.respo_name + ':' + response.task_desc;
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

// 获取任务信息
function get_task_info() {
    var api_url = 'task_info.php';
    var task_id = GetQueryString('id');
    CallApi(api_url, {"task_id": task_id}, function (response) {
        // 任务明细展示
        show_task_info(response);
    }, function (response) {
        AlertDialog(response.errmsg);
    });
};

