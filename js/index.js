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

// 执行SQL
function sql_execute(sql_db, sql_id, sql_args) {
  var jsdata={'sql_id':sql_id,'sql_args':sql_args};

  $.ajax({
    type:"POST",
    url:"sql_execute.php?db=" + sql_db,
    data:jsdata,
    success:function(msg) {
      if (msg == 1) {
        console.log(sql_id + ' ' + sql_args);
      } else {
        parent.layer.msg(msg);
      }
    },
    error:function(XMLHttpRequest, textStatus, errorThrown) {
      alert(textStatus);
    }
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


