// 设置cookies函数
function SetCookie(name, value) {
    var now = new Date();
    var time = now.getTime();
    // 有效期7天
    time += 3600 * 1000 * 24 * 7;
    now.setTime(time);
    document.cookie = name + "=" + escape(value) + '; expires=' + now.toUTCString();
}

// 取cookies函数
function GetCookie(name) {
    var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
    if (arr != null) return unescape(arr[2]);
    return null;
}

// 删除cookie函数
function DelCookie(name) {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval = GetCookie(name);
    if (cval != null) document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
}

// 取得URL参数
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}

// 手机号码格式检查
function IsPhone(s) {
    var patrn = /^0?1[3|4|5|7|8][0-9]\d{8}$/;
    return patrn.exec(s);
}

// Email格式检查
function IsEmail(s) {
    var patrn = /^(?:\w+\.?)*\w+@(?:\w+\.)*\w+$/;
    return patrn.exec(s);
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

// 取得Script
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

// 取得浏览器信息
function GetBrowserInfo() {
    var ua = navigator.userAgent, tem,
        M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
    if (/trident/i.test(M[1])) {
        tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
        return 'IE ' + (tem[1] || '');
    }
    if (M[1] === 'Chrome') {
        tem = ua.match(/\b(OPR|Edge)\/(\d+)/);
        if (tem != null) return tem.slice(1).join(' ').replace('OPR', 'Opera');
    }
    M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
    if ((tem = ua.match(/version\/(\d+)/i)) != null) M.splice(1, 1, tem[1]);
    return M.join(' ');
}

/**
 * 禁用按钮
 * @param $this 按钮对象
 * @param btnText 按钮文本内容 默认为"处理中"
 * @return {boolean}
 */
function DisableClick($this, btnText) {
    if (!$this) {
        console.warn("$this 不能为空");
        return true;
    }
    var status = Number($this.attr('data-clickStatus') || 1);
    if (status == 0) {
        return true;
    }

    btnText = btnText ? btnText : "处理中";
    $this.attr('data-clickStatus', 0);
    $this.html(btnText);
    return false;
}

/**
 * 激活按钮
 * @param $this 按钮对象
 * @param btnText 按钮文本内容 默认为"处理中"
 */
function ActiveClick($this, btnText) {
    if (!$this) {
        console.warn("$this 不能为空");
        return;
    }
    btnText = btnText ? btnText : "确认";
    $this.attr('data-clickStatus', 1);
    $this.html(btnText);
}
