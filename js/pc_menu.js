// 调用PHP返回内容填充页面
function ShowPhp(php, id) {
  $.ajax({
    type:"GET",
    url:php,
    dataType:"text",
    success:function(html) {
      $("#" + id).html(html);
    },
    error:function(XMLHttpRequest, textStatus, errorThrown) {
      // AJAX异常
      errhtml = "<h1>PHP错误：" + php + "</h1>";
      errhtml += "<h2>" + textStatus + ":<h2>";
      errhtml += "<p>" + errorThrown + "</p>";
      $("#" + id).html(errhtml);
    }
  });
}

// 弹出成功信息框
function show_OK_msg(title, msg) {
  layer.alert(msg, {
    icon: 1,
    title: title,
    btn: ['OK']
  });
}

// 弹出失败信息框
function show_NG_msg(title, msg) {
  layer.msg(msg, {
    icon: 2,
    title: title,
    btn: ['好吧']
  });
}
  

// 取得当前时间
function getNowDate() {
    var date = new Date();
    var month = date.getMonth() + 1;
    var day = date.getDate();
    if (month >= 1 && month <= 9)
        month = "0" + month;
    if (day >= 0 && day <= 9)
        day = "0" + day;
    return date.getFullYear() + "-" + month + "-" + day + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
}


// 菜单点击
function menu_click(sub, func) {
  $("#main_status").html('<h1 class="page-header"><img src="img/ajax-loader.gif" /> 正在努力地加载数据中，请稍候……</h1>');
  status_php = sub + "/" + func + ".php";
  setTimeout(ShowPhp(status_php, "main_status"),1000);
}

// 联系管理员
function contact_click() {
  layer.open({
      type: 2,
      title: '联系管理员',
      shadeClose: true,
      shade: 0.8,
      area: ['420px', '480px'],
      content: 'dialog/contact.php'
  });
}

$(function () {
  // 主菜单点击处理
  $(".panel-title").click(function() {
    $(".panel-title").removeClass("active");
    $(this).addClass("active");
  });

  // 子菜单点击处理
  $(".nav-sidebar li").click(function() {
    $(".nav-sidebar li").removeClass("active");
    var nav_button_dis = $(".navbar-toggle").css('display');
    if (nav_button_dis != 'none'){
      $(".navbar-toggle").click();
    }
    $(this).addClass("active");
  });
});

// 调用API失败通用处理
function CallApiError(response) {
    show_NG_msg('错误代码：' + response.errcode, response.errmsg);
    return false;
}

// 调用API共通函数
function CallApi(api_url, post_data, suc_func, error_func) {

    var api_site = 'http://www.fnying.com/staff/api/';

    post_data = post_data || {};
    suc_func = suc_func || $.noop;
    error_func = error_func || CallApiError;

    //console.log('Call API:' + api_url);
    //console.log(JSON.stringify(post_data));

    $.ajax({
        url: api_site + api_url,
        dataType: "jsonp",
        data: post_data,
        success: function(response) {
            //console.log(JSON.stringify(response));
            // API返回失败
            if (response.errcode != 0) {
                error_func(response);
            } else {
                // 成功处理数据
                suc_func(response);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            // API错误异常
            var response = {"errcode": -1, "errmsg": '系统异常，请稍候再试'};
            // 异常处理
            error_func(response);
        }
    });
}

function getScript(url, callback) {
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.src = url;

    var done = false;
    // Attach handlers for all browsers
    script.onload = script.onreadystatechange = function() {
        if (!done && (!this.readyState ||
                this.readyState == 'loaded' || this.readyState == 'complete')) {
            done = true;
            if (callback)
                callback();

            // Handle memory leak in IE
            script.onload = script.onreadystatechange = null;
        }
    };

    head.appendChild(script);

    // We handle everything using the script element injection
    return undefined;
}



