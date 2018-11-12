$(function () {
    // GET UUID
    function CNTGetUUID(len, radix) {
        var CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split('');
        var chars = CHARS;
        var uuid = [];
        var i;
        radix = radix || chars.length;

        if (len) {
            for (i = 0; i < len; i++) uuid[i] = chars[0 | Math.random() * radix];
        } else {
            var r;
            uuid[8] = uuid[13] = uuid[18] = uuid[23] = '-';
            uuid[14] = '4';
            for (i = 0; i < 36; i++) {
                if (!uuid[i]) {
                    r = 0 | Math.random() * 16;
                    uuid[i] = chars[(i == 19) ? (r & 0x3) | 0x8 : r];
                }
            }
        }
        return uuid.join('');
    }

    // Get Cookie
    function CNTGetCookie(name) {
        var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
        if (arr != null) return unescape(arr[2]);
        return null;
    }
    
    // Set Cookie
    function CNTSetCookie(name, value) {
        var now = new Date();
        var time = now.getTime();
        // one month
        time += 3600 * 24 * 30 * 1000;
        now.setTime(time);
        document.cookie = name + "=" + value + '; expires=' + now.toUTCString() + ';path=/';
    }

    // Ajax Count
    function CNTAjax(latitude, longitude) {
      var uuid = CNTGetCookie('UUID');
      if(!uuid){
          uuid = CNTGetUUID();
          CNTSetCookie("UUID", uuid);
      }
      var xmlhttp;
      if (window.XMLHttpRequest) {
          xmlhttp = new XMLHttpRequest();
      } else {
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      }
      var ref = escape(document.referrer);
      var url = escape(window.location.href);
      var title = shareData.title || '';
      xmlhttp.open("GET", "http://www.fnying.com/staff/api/cnt_staff_action.php?referrer=" + ref + "&url=" + url + "&title=" + title + "&uuid=" + uuid + "&latitude=" + latitude + "&longitude=" + longitude, true);
      xmlhttp.send();
    }    

    var ua = window.navigator.userAgent.toLowerCase();
    // Is WeChat
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
      $.getScript("https://res.wx.qq.com/open/js/jweixin-1.2.0.js", function () {
          // 微信配置启动
          wx_config();
          wx.ready(function() {
              wx.getLocation({
                  type: 'gcj02',
                  success: function (res) {
                    CNTAjax(res.latitude, res.longitude);
                  },
                  cancel: function (res) {
                    CNTAjax(0, 0);
                  },
                  fail: function (res) {
                    CNTAjax(0, 0);
                  }
              });
          });
      });
    } else {
      CNTAjax(0, 0);
    }
    

});