var table_name = '任务一览';
var table = $('#table');

// 表格初始化
function initTable() {
    table.bootstrapTable({
        height: getHeight(),
        columns: [
            {
                title: '添加行动',
                field: 'add_btn',
                align: 'center',
                valign: 'middle',
                visible: false,
                events: addBtnEvents,
                formatter: addBtnFormatter
            },
            {
                title: '行动列表',
                field: 'act_btn',
                align: 'center',
                valign: 'middle',
                events: listBtnEvents,
                formatter: listBtnFormatter
            },
            {
                title: '个人',
                field: 'is_self',
                align: 'right',
                visible: false,
                sortable: true,
                formatter: selfFormatter,
                valign: 'middle'
            },
            {
                title: '任务',
                field: 'task_name',
                align: 'left',
                sortable: true,
                formatter: taskNameFormatter,
                valign: 'middle'
            },
            {
                title: '责任人',
                field: 'respo_name',
                align: 'center',
                sortable: true,
                valign: 'middle'
            },
            {
                title: '任务等级',
                field: 'task_level',
                align: 'center',
                visible: false,
                sortable: true,
                formatter: taskLevelFormatter,
                valign: 'middle'
            },
            {
                title: '任务价值',
                field: 'task_value',
                align: 'right',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '预期',
                field: 'upd_btn',
                align: 'center',
                valign: 'middle',
                events: updBtnEvents,
                formatter: updBtnFormatter
            },
            {
                title: '进展',
                field: 'is_closed',
                align: 'center',
                sortable: true,
                formatter: isClosedFormatter,
                events: closeBtnEvents,
                valign: 'middle'
            },
            {
                title: '任务进度',
                field: 'task_perc',
                align: 'right',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '任务期限',
                field: 'limit_time',
                align: 'right',
                sortable: true,
                formatter: limitTimeFormatter,
                valign: 'middle'
            },
            {
                title: '监督人',
                field: 'check_name',
                align: 'center',
                sortable: true,
                valign: 'middle'
            },
            {
                title: '任务ID',
                field: 'task_id',
                align: 'center',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '任务内容',
                field: 'task_intro',
                align: 'left',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '创建人ID',
                field: 'owner_id',
                align: 'center',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '创建人',
                field: 'owner_name',
                align: 'center',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '责任人ID',
                field: 'respo_id',
                align: 'center',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '监督人ID',
                field: 'check_id',
                align: 'center',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '上一任务ID',
                field: 'prvs_task_id',
                align: 'center',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '是否无效',
                field: 'is_void',
                align: 'right',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '更新时间',
                field: 'utime',
                align: 'right',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '创建时间',
                field: 'ctime',
                align: 'right',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '删除',
                field: 'deel_btn',
                align: 'center',
                valign: 'middle',
                events: delBtnEvents,
                formatter: delBtnFormatter
            }
        ]
    });

    // 窗口尺寸变化事件
    $(window).resize(function () {
        table.bootstrapTable('resetView', {
            height: getHeight()
        });
    });
}

// 添加行动按钮
function addBtnFormatter(value, row, index) {
    if (row.is_closed == '0')
        return '<button class="addbtn btn-warning" type="button" aria-label="添加"><i class="glyphicon glyphicon-plus"></i></button>';
    return '-';
}

// 添加行动按钮触发事件
window.addBtnEvents = {
    'click .addbtn': function (e, value, row) {
      layer.open({
          type: 2,
          title: '为【' + row.task_name + '】添加行动',
          fix: false,
          maxmin: true,
          shadeClose: true,
          shade: 0.8,
          area: ['800px', '780px'],
          content: 'dialog/action.php?task_id=' + row.task_id
      });
    }
};

// 修改任务按钮
function updBtnFormatter(value, row, index) {
    var my_id = $("#my_id").val();
    if (my_id == row.owner_id)
        return '<button class="updbtn btn-warning" type="button" aria-label="修改"><i class="glyphicon glyphicon-edit"></i></button>';
    return '-';
}

// 修改任务按钮触发事件
window.updBtnEvents = {
    'click .updbtn': function (e, value, row) {
      layer.open({
          type: 2,
          title: '修改任务',
          fix: false,
          maxmin: true,
          shadeClose: true,
          shade: 0.8,
          area: ['800px', '780px'],
          content: 'dialog/task.php?id=' + row.task_id
      });
    }
};

// 删除任务按钮
function delBtnFormatter(value, row, index) {
    var my_id = $("#my_id").val();
    if (my_id == row.owner_id)
      return ' <button class="remove btn-danger" type="button" aria-label="删除"><i class="glyphicon glyphicon-remove"></i></button> ';
    return '-';
}

// 删除任务触发事件
window.delBtnEvents = {
    'click .remove': function (e, value, row) {
      //询问框
      layer.confirm('是否删除 ' + getRowDescriptions(row) + ' 的记录？', {
          icon: 3,
          title: table_name + '操作确认',
          btn: ['确认','取消']
      }, function(){
          $.ajax({
              url: '/staff/api/obj_delete.php?obj=task&id=' + row.task_id,
              type: 'get',
              success: function (msg) {
                if (msg.errcode == '0') {
                  layer.alert(msg.errmsg, {
                    icon: 1,
                    title: '提示信息',
                    btn: ['OK']
                  });
                  table.bootstrapTable('refresh');
                } else {
                  layer.msg(msg.errmsg, {
                    icon: 2,
                    title: '错误信息',
                    btn: ['好吧']
                  });
                }
              },
              error:function(XMLHttpRequest, textStatus, errorThrown) {
                // AJAX异常
                show_NG_msg(textStatus, errorThrown);
              }
          })
      } , function(){
      });
    }
};

// 行动列表按钮
function listBtnFormatter(value, row, index) {
    return '<button class="actbtn btn-info" type="button" aria-label="行动"><i class="glyphicon glyphicon-list-alt"></i></button>';
}

// 行动列表按钮触发事件
window.listBtnEvents = {
    'click .actbtn': function (e, value, row) {
      layer.open({
          type: 2,
          title: '【' + row.task_name + '】的行动列表',
          fix: false,
          maxmin: true,
          shadeClose: true,
          shade: 0.8,
          area: ['800px', '780px'],
          content: 'dialog/action_list.php?task_id=' + row.task_id
      });
    }
};

// 是否个人格式化
function selfFormatter(value, row, index) {
    var fmt = '?';
    switch (value) {
      case '0':
        fmt = '-';
        break;
      case '1':
        fmt = '个人';
        break;
    }
    return fmt;
}

// 任务名称格式化
function taskNameFormatter(value, row, index) {
    if(row.task_name) {
        // 任务等级
        var task_star = '';
        if (row.task_level > 2)
          task_star = ' ★';
        // 个人任务
        var task_self = '';
        if (row.is_self == 1)
          task_self = '● ';
        var link_style = '';
        if (row.task_void == 1)
          link_style = 'void_text';
        return task_self + '<a class="' + link_style + '" href="http://www.fnying.com/staff/wx/task.php?id=' + row.task_id + '" target="_blank">' + row.task_name + '</a>' + task_star;
    }
    return '-';
}

// 任务等级格式化
function taskLevelFormatter(value, row, index) {
    var fmt = '?';
    switch (value) {
      case '0':
        fmt = '可选';
        break;
      case '1':
        fmt = '一般';
        break;
      case '2':
        fmt = '重要';
        break;
      case '3':
        fmt = '非常重要';
        break;
    }
    return fmt;
}

// 是否完成格式化
function isClosedFormatter(value, row, index) {
    var fmt = '?';
    switch (value) {
      case '0':
        var my_id = $("#my_id").val();
        if (my_id == row.owner_id || my_id == row.respo_id || my_id == row.check_id) {
          fmt = '<button class="closebtn btn-primary" type="button" aria-label="进展"><i class="glyphicon glyphicon-ok"></i></button>';
        } else {
          fmt = '执行中';
        }
        break;
      case '1':
        fmt = '完成';
        break;
    }
    return fmt;
}

// 完成按钮触发事件
window.closeBtnEvents = {
    'click .closebtn': function (e, value, row) {
      layer.open({
          type: 2,
          title: '任务进展',
          fix: false,
          maxmin: true,
          shadeClose: true,
          shade: 0.8,
          area: ['800px', '780px'],
          content: 'dialog/task_result.php?task_id=' + row.task_id
      });
    }
};

// 任务期限格式化
function limitTimeFormatter(value, row, index) {
    if (row.is_limit == 0)
      return '长期';

    var limit_time = new Date(value.replace(/-/g, "/"));
    var month = limit_time.getMonth() + 1;
    var day = limit_time.getDate();
    var fmt = month+'月'+day+'日';
    if (row.is_closed <= 1)
      return fmt;

    // 相差日期计算
    var current_time = new Date();
    var diff_day = parseInt((limit_time.getTime() - current_time.getTime()) / (1000 * 3600 * 24));
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

// 取得记录描述
function getRowDescriptions(row) {
    return '【' + row.task_name + '】';
}

// 更多明细字段显示
function detailFormatter(index, row) {
    var html = [];
    $.each(row, function (key, value) {
        html.push('<p><b>' + key + ':</b> ' + value + '</p>');
    });
    return html.join('');
}

function responseHandler(res) {
    $.each(res.rows, function (i, row) {
        // row.state = $.inArray(row.id, selections) !== -1;
    });
    return res;
}

// 链接格式化
function urlFormatter(value, row, index) {
    if(value) {
      return '<a href="' + value + '" target="_blank">' + value + '</a>';
    }
}

// 图片格式化
function imageFormatter(value, row, index) {
    if(value) {
        return '<a href="' + value + '" target="_blank"><img src="' + value + '" class="avata"></a>';
    }
}

// 获取表格高度
function getHeight() {
    return $(window).height() - 145;
}

// 员工信息及相邻员工信息展示
function showStaffNeighbor(response) {
  var my_id = $("#my_id").val();
  var aft_btn = '<button class="btn btn-info btn-block btn-lg" type="button" onclick="changeStaff(\'' + response.aft_id + '\')">' + response.aft_cd + ' ' + response.aft_name + '</button>';
  var bef_btn = '<button class="btn btn-info btn-block btn-lg" type="button" onclick="changeStaff(\'' + response.bef_id + '\')">' + response.bef_name + ' ' + response.bef_cd + '</button>';
  if (my_id != response.cur_id) {
    $("#cur_name").html(response.cur_name);
  } else {
    $("#cur_name").html('我');
  }
  $("#aft_name").html(aft_btn);
  $("#bef_name").html(bef_btn);
}

// 获取员工信息及相邻员工信息
function getStaffNeighbor(cur_id) {
    var api_url = 'staff_neighbor.php';
    if (!cur_id)
      return;
    // API调用
    CallApi(api_url, {"staff_id":cur_id}, function (response) {
        // 员工信息及相邻员工信息展示
        showStaffNeighbor(response);
    }, function (response) {
        CallApiError(response);
    });
}

// 变更当前员工
function changeStaff(staff_id) {
    $("#cur_id").val(staff_id);
    // 任务页面初始化
    initTask();
}

// 刷新任务一览
function refreshTable(cur_id) {
    var opt = {url: "/staff/api/task_list.php?is_closed=9&staff_id=" + cur_id};

    table.bootstrapTable('refresh', opt);
    table.bootstrapTable('resetView', {height: getHeight()});
}

// 任务页面初始化
function initTask() {
    var cur_id = $("#cur_id").val();
    // 获取员工信息及相邻员工信息
    getStaffNeighbor(cur_id);
    // 刷新任务一览
    refreshTable(cur_id);
}

$(function () {
    // 添加任务按钮点击事件
    $('#add_btn').click(function() {
        layer.open({
            type: 2,
            title: '添加任务',
            shadeClose: true,
            shade: 0.8,
            area: ['800px', '780px'],
            content: 'dialog/task.php'
        });
    });

    // 表格初始化
    initTable();

    // 任务页面初始化
    initTask();
});
