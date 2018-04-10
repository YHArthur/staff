// 页面初始化
function init() {
  $("#ocd_list").val('');
  $("#uid").html('');
  $("#uname").html('');
  $("#identity").html('');
  $("#phone").html('');
  $("#odate").html('');
  $("#order_rows").html('');
  $("#clear_rows").html('');
}

// 核销预约记录成功
function clear_code_suc() {
  // Toast('已核销');
  // 成功提示音
  SuccessAudio();
  // 页面初始化
  init();
  // 开启扫一扫
  $(".code-scan").click();
}

// 核销预约记录
function clear_code() {
  // 判断是否有预约记录被选中
  var ocd_list = $("#ocd_list").val();
  if (ocd_list.length == 0)
    return;

  // 用户登录判断
  if (!IsLogin()) {
    AlertDialog('抱歉，您没有核销权限');
    return;
  }

  // 调用预约码上传核销API
  OrderClear(ocd_list, clear_code_suc)
}

// 点击预约记录
function chk_ocd(ocd) {
  var ocd_list = $("#ocd_list").val();
  var ocd_ary = ocd_list == '' ? [] : ocd_list.split(",");
  var index = $.inArray(ocd, ocd_ary);
  // 存在则删除
  if (index >= 0) {
    ocd_ary.splice(index, 1);
  } else {
    ocd_ary.push(ocd);
  }
  ocd_list = ocd_ary.join(',');
  $('#ocd_list').val(ocd_list);
  if (ocd_ary.length > 0) {
    BtnEnable($("#clearBtn"), 'javascript:clear_code();', '核销');
  } else {
    BtnDisable($("#clearBtn"), '核销')
  }

}

// 用户信息及预约记录展示
function show_order_log(response) {

  var check, ocd, period, period_str, sname, staff_name, clear_time, clear_str, msg;

  // 用户信息展示
  $("#uname").html(response.uname);
  $("#identity").html(response.identity);
  $("#phone").html(response.area + '-' + response.phone);

  // 未核销预约信息展示
  $("#odate").html('【' + response.t_day + '】');
  var olist = response.order_rows;
  if (olist.length > 0) {
    olist.forEach(function(value, index, array) {
       ocd = value.ocd;
       period = value.period;
       sname = value.sname;
       period_str = period.substr(0,2) + ':' + period.substr(2,2) + '-' + period.substr(4,2) + ':' + period.substr(6,2);

       check = '\
        <label class="weui-cell weui-check__label" for="c' + index + '">\
          <div class="weui-cell__hd">\
            <input type="checkbox" class="weui-check" onclick="javascript:chk_ocd(\'' + ocd + '\');" name="order" value="' + ocd + '" id="c' + index + '">\
            <i class="weui-icon-checked"></i>\
          </div>\
          <div class="weui-cell__bd">' + period_str + ' ' + sname + '</div>\
        </label>\
        ';
        $("#order_rows").append(check);
    });
  }

  // 已核销预约信息展示
  var clist = response.clear_rows;
  if (clist.length > 0) {
    clist.forEach(function(value, index, array) {
       period = value.period;
       sname = value.sname;
       staff_name = value.staff_name;
       clear_time = value.clear_time;
       clear_str = FormatDateTime(new Date(clear_time * 1000), 'yyyy年M月dd日 hh:mm:ss');
       msg = '该预约已于 ' + clear_str + ' 被 ' + staff_name + ' 核销';

       period_str = period.substr(0,2) + ':' + period.substr(2,2) + '-' + period.substr(4,2) + ':' + period.substr(6,2);

       check = '\
        <label class="weui-cell weui-check__label" for="x' + index + '">\
          <div class="weui-cell__bd"><p>' + period_str + ' ' + sname  + '</p></div>\
          <div class="weui-cell__ft">\
              <input type="radio" class="weui-check" onclick="javascript:AlertDialog(\'' + msg + '\');" name="clear" id="x' + index + '">\
          </div>\
        </label>\
        ';
        $("#clear_rows").append(check);
    });
  }
}

// 预约成功记录取得
function get_order_log(uid) {

  // 显示二维码信息
  $("#uid").html(uid);
  
  // 判断uid是否合法
  if (!IsUid(uid)) {
    AlertDialog('二维码格式错误');
    return;
  }

  // 调用预约成功记录取得API
  OrderLog(uid, show_order_log);
}


$(function () {

  // 微信配置启动
  // wx_config(['scanQRCode']);
  // 启动完成OK
  SuccessAudio();

});

