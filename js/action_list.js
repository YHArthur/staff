var table_name = '行动一览';
var table = $('#table');

// 表格初始化
function initTable() {
    table.bootstrapTable({
        height: getHeight(),
        columns: [
            {
                title: '地点',
                field: 'location_name',
                align: 'center',
                sortable: true,
                formatter: locationNameFormatter,
                valign: 'middle'
            },
            {
                title: '联络',
                field: 'connect_name',
                align: 'center',
                sortable: true,
                formatter: connectNameFormatter,
                valign: 'middle'
            },
            {
                title: '公开等级',
                field: 'public_level',
                align: 'right',
                visible: false,
                sortable: true,
                formatter: publicLevelFormatter,
                valign: 'middle'
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
                title: '行动概要',
                field: 'action_title',
                align: 'left',
                sortable: true,
                valign: 'middle'
            },
            {
                title: '所属任务',
                field: 'task_id',
                align: 'left',
                sortable: true,
                formatter: taskNameFormatter,
                valign: 'middle'
            },
            {
                title: '成果',
                field: 'result_name',
                align: 'center',
                sortable: true,
                formatter: resultNameFormatter,
                events: progBtnEvents,
                valign: 'middle'
            },
            {
                title: '设备限定',
                field: 'device_name',
                align: 'right',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '责任人',
                field: 'respo_name',
                align: 'center',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '结束时间',
                field: 'closed_time',
                align: 'center',
                visible: false,
                sortable: true,
                formatter: closedTimeFormatter,
                valign: 'middle'
            },
            {
                title: '预期结果',
                field: 'action_intro',
                align: 'left',
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
                title: '前置行动',
                field: 'prvs_action_id',
                align: 'center',
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
                title: '修改',
                field: 'upd_btn',
                align: 'center',
                valign: 'middle',
                events: updBtnEvents,
                formatter: updBtnFormatter
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

// 修改按钮
function updBtnFormatter(value, row, index) {
    var my_id = $("#my_id").val();
    if (my_id == row.owner_id)
        return '<button class="updbtn btn-warning" type="button" aria-label="修改"><i class="glyphicon glyphicon-edit"></i></button>';
    return '-';
}

// 修改按钮触发事件
window.updBtnEvents = {
    'click .updbtn': function (e, value, row) {
      layer.open({
          type: 2,
          title: '修改行动',
          fix: false,
          maxmin: true,
          shadeClose: true,
          shade: 0.8,
          area: ['800px', '850px'],
          content: 'dialog/action.php?action_id=' + row.action_id
      });
    }
};

// 操作
function delBtnFormatter(value, row, index) {
    return ' <button class="remove btn-danger" type="button" aria-label="删除"><i class="glyphicon glyphicon-remove"></i></button> ';
}

window.delBtnEvents = {
    'click .remove': function (e, value, row) {
      //询问框
      layer.confirm('是否删除 ' + getRowDescriptions(row) + ' 的记录？', {
          icon: 3,
          title: table_name + '操作确认',
          btn: ['确认','取消']
      }, function(){
          $.ajax({
              url: '/staff/api/task_action_delete.php?obj=action&id=' + row.action_id,
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



// 地点格式化
function locationNameFormatter(value, row, index) {
    if (row.is_location == '1')
      return value;
    return '-';
}

// 联络对象格式化
function connectNameFormatter(value, row, index) {
    if (row.connect_type != '0')
      return value;
    return '-';
}

// 公开等级格式化
function publicLevelFormatter(value, row, index) {
    var fmt = '?';
    switch (value) {
      case '0':
        fmt = '相关';
        break;
      case '1':
        fmt = '组织';
        break;
      case '2':
        fmt = '用户';
        break;
      case '3':
        fmt = '全体';
        break;
    }
    return fmt;
}

// 任务名称格式化
function taskNameFormatter(value, row, index) {
    if(row.task_name)
        return '<a href="http://www.fnying.com/staff/wx/task.php?id=' + row.task_id + '" target="_blank">' + row.task_name + '</a>';
    return '-';
}

// 成果名称格式化
function resultNameFormatter(value, row, index) {
    if(row.result_type == 'I') {
      if (row.result_memo) {
        return '<button class="progbtn btn-primary" type="button" aria-label="进展"><i class="glyphicon glyphicon-file"></i></button>';
      } else {
        return '-';
      }
    } else {
        return '<a href="' + value + '" target="_blank"><i class="glyphicon glyphicon-link"></i></a>';
    }
}

// 内置成果进展按钮触发事件
window.progBtnEvents = {
    'click .progbtn': function (e, value, row) {
        layer.open({
            type: 2,
            title: '【' + row.action_title + '】的行动进展',
            fix: false,
            maxmin: true,
            shadeClose: true,
            shade: 0.8,
            area: ['800px', '850px'],
            content: 'dialog/action_progress.php?action_id=' + row.action_id
        });
    }
};

// 是否完成格式化
function isClosedFormatter(value, row, index) {
    var fmt = '?';
    switch (value) {
      case '0':
        fmt = '<button class="closebtn btn-primary" type="button" aria-label="进展"><i class="glyphicon glyphicon-ok"></i></button>';
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
          title: '行动进展',
          fix: false,
          maxmin: true,
          shadeClose: true,
          shade: 0.8,
          area: ['800px', '850px'],
          content: 'dialog/action_result.php?action_id=' + row.action_id
      });
    }
};

// 结束时间格式化
function closedTimeFormatter(value, row, index) {

    if (!value)
      return '';

    var ct = new Date(value.replace(/-/g, "/"));
    var month = ct.getMonth() + 1;
    var day = ct.getDate();
    var fmt = month+'月'+day+'日';
    if (row.closed_time <= 1)
      return fmt;
    
    return value;

    // 相差日期计算
    var current_time = new Date();
    var diff_day = parseInt((is_closed.getTime() - current_time.getTime()) / (1000 * 3600 * 24));
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
    return '【' + row.action_title + '】';
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
  var aft_btn = '<button class="btn btn-info btn-block btn-lg" type="button" onclick="changeStaff(\'' + response.aft_id + '\')">' + response.aft_cd + ' ' + response.aft_name + '</button>';
  var bef_btn = '<button class="btn btn-info btn-block btn-lg" type="button" onclick="changeStaff(\'' + response.bef_id + '\')">' + response.bef_name + ' ' + response.bef_cd + '</button>';
  $("#cur_name").html(response.cur_name);
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
    // 行动页面初始化
    initAction();
}

// 刷新任务一览
function refreshTable(cur_id) {
    var opt = {url: "/staff/api/action_list.php?is_closed=9&staff_id=" + cur_id};

    table.bootstrapTable('refresh', opt);
    table.bootstrapTable('resetView', {height: getHeight()});
}

// 行动页面初始化
function initAction() {
    var cur_id = $("#cur_id").val();
    // 获取员工信息及相邻员工信息
    getStaffNeighbor(cur_id);
    // 刷新行动一览
    refreshTable(cur_id);
}

$(function () {
    // 添加任务按钮点击事件
    $('#add_btn').click(function() {
        layer.open({
            type: 2,
            title: '添加行动',
            shadeClose: true,
            shade: 0.8,
            area: ['800px', '850px'],
            content: 'dialog/action.php'
        });
    });

    // 表格初始化
    initTable();

    // 行动页面初始化
    initAction();
});
