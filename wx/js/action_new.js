  $(function () {
    $("#div_connect_name").hide();
    $("#div_location_name").hide();
  });

  $('#ct_connect_type').on('change',function(){
      // 沟通类型初始化
      connectChange($("#ct_connect_type").val());
  });

    $('#ct_is_location').on('change',function(){
      // 限定地点初始化
      locationChange($("#ct_is_location").val());
  });

  // 沟通类型处理
  function connectChange(ct) {
      // 隐藏联络对象输入框
      $("#div_connect_name").hide();
      // 沟通类型不是无
      if (ct != 0) {
          // 默认成果标签和成果提示文字
          var result_name = '联络对象';
          var result_placeholder = '联络方名称';
          // 等待
          if (ct == 3) {
              result_name = '等待对象';
              result_placeholder = '等待方名称';
          }
          // 联络对象标签变更
          $("#lbl_connect_name").html(result_name);
          // 联络对象输入内容变更
          $("#ct_connect_name").attr('placeholder', result_placeholder);
          // 显示联络对象输入框
          $("#div_connect_name").show();
      }
  }

  // 限定地点处理
  function locationChange(lc) {
    // 其它指定地点
    if (lc == "3") {
      // 显示地点名称
      $("#div_location_name").show();
    } else {
      // 隐藏地点名称输入框
      $("#div_location_name").hide();
    }
  }

  // 确认按钮点击事件
  $("#btn_ok").click(function() {
    // 必须输入行动标题
    var action_title = $("#ct_action_title").val().trim();
    if (action_title.length == 0) {
      AlertDialog('请输入行动标题');
      return;
    }

    // 联络沟通有时必须输入联络对象
    var connect_type = $("#ct_connect_type").val();
    var connect_name = $("#ct_connect_name").val().trim();
    if (connect_type != '0' && connect_name.length == 0) {
      AlertDialog('请输入联络对象');
      return;
    }

    var row = {};
    var form = $("#ct_form");

    form.find('input[name]').each(function () {
        row[$(this).attr('name')] = $(this).val();
    });

    form.find('select[name]').each(function () {
        row[$(this).attr('name')] = $(this).val();
    });

    form.find('textarea[name]').each(function () {
        row[$(this).attr('name')] = $(this).val();
    });

    // 责任担当
    row['respo_name'] = $("#ct_respo_id option:selected").text();
    // 是否完成
    row['is_closed'] = 0;

    $.ajax({
        url: '/staff/api/action.php',
        type: 'post',
        data: row,
        success:function(response) {
          // AJAX正常返回
          if (response.errcode == '0') {
            AlertDialog(response.errmsg);
            // 跳转上一页
            if (typeof document.referrer != '') {
              window.location.href = document.referrer;
            } else {
              window.location.href = 'action_list.php';
            }
          } else {
            AlertDialog(response.errmsg);
          }
        },
        error:function(XMLHttpRequest, textStatus, errorThrown) {
          // AJAX异常
          AlertDialog(textStatus);
        }
    });

  });
