var table_name = '任务一览';
var table = $('#table');
function initTable() {
    table.bootstrapTable({
        height: getHeight(),
        columns: [
            {
                title: '公开等级',
                field: 'public_level',
                align: 'right',
                sortable: true,
                formatter: publicLevelFormatter,
                valign: 'middle'
            },
            {
                title: '任务',
                field: 'task_name',
                align: 'left',
                sortable: true,
                valign: 'middle'
            },
            {
                title: '责任人',
                field: 'respo_name',
                align: 'left',
                sortable: true,
                valign: 'middle'
            },
            {
                title: '任务等级',
                field: 'task_level',
                align: 'right',
                sortable: true,
                formatter: taskLevelFormatter,
                valign: 'middle'
            },
            {
                title: '任务价值',
                field: 'task_value',
                align: 'right',
                sortable: true,
                valign: 'middle'
            },
            {
                title: '任务状态',
                field: 'task_status',
                align: 'right',
                sortable: true,
                formatter: taskStatusFormatter,
                valign: 'middle'
            },
            {
                title: '任务进度',
                field: 'task_perc',
                align: 'right',
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
                align: 'left',
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
                align: 'left',
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
                title: '修改任务',
                field: 'upd_btn',
                align: 'center',
                valign: 'middle',
                events: updBtnEvents,
                formatter: updBtnFormatter
            },
            {
                field: 'operate',
                title: '操作',
                align: 'center',
                valign: 'middle'
                // events: operateEvents,
                // formatter: operateFormatter
            }
        ]
    });

    // sometimes footer render error.
    setTimeout(function () {
        table.bootstrapTable('resetView');
    }, 500);
    
    // 点击行事件
    table.on('click-row.bs.table', function (e, row) {
    });

    // 窗口尺寸变化事件
    $(window).resize(function () {
        table.bootstrapTable('resetView', {
            height: getHeight()
        });
    });
}


$(function () {
  // 添加任务按钮点击事件
  $('#add_btn').click(function() {
      layer.open({
          type: 2,
          title: '添加任务',
          shadeClose: true,
          shade: 0.8,
          area: ['800px', '850px'],
          content: 'dialog/task.php'
      });
  });
});

// 修改按钮
function updBtnFormatter(value, row, index) {
    return '<button class="updbtn btn-warning" type="button" aria-label="修改"><i class="glyphicon glyphicon-edit"></i></button>';
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

// 任务状态格式化
function taskStatusFormatter(value, row, index) {
    var fmt = '?';
    switch (value) {
      case '0':
        fmt = '废止';
        break;
      case '1':
        fmt = '完成';
        break;
      case '2':
        fmt = '执行';
        break;
      case '3':
        fmt = '等待';
        break;
    }
    return fmt;
}

// 任务期限格式化
function limitTimeFormatter(value, row, index) {

    var limit_time = new Date(value.replace(/-/g, "/"));
    var month = limit_time.getMonth() + 1;
    var day = limit_time.getDate();
    var fmt = month+'月'+day+'日';
    if (row.task_status <= 1)
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

window.updBtnEvents = {
    'click .updbtn': function (e, value, row) {
      layer.open({
          type: 2,
          title: '修改任务',
          fix: false,
          maxmin: true,
          shadeClose: true,
          shade: 0.8,
          area: ['800px', '850px'],
          content: 'dialog/task.php?id=' + row.task_id
      });
    }
};

// 取得记录描述
function getRowDescriptions(row) {
    return '任务ID=' + row.task_id;
}

// 更多明细字段显示
function detailFormatter(index, row) {
    var html = [];
    $.each(row, function (key, value) {
        html.push('<p><b>' + key + ':</b> ' + value + '</p>');
    });
    return html.join('');
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
    return $(window).height() - $('h1').outerHeight(true) - $('nav').outerHeight(true);
}

// 员工信息及相邻员工信息展示
function show_staff_neighbor(response) {
  $("#cur_name").html(response.cur_name);
  $("#aft_name").html(response.aft_name);
  $("#bef_name").html(response.bef_name);
}

// 获取员工信息及相邻员工信息
function get_staff_neighbor() {
    var api_url = 'staff_neighbor.php';
    var cur_id = $("#cur_id").val();
    if (!cur_id)
      return;
    // API调用
    CallApi(api_url, {"staff_id":cur_id}, function (response) {
        // 员工信息及相邻员工信息展示
        show_staff_neighbor(response);
    }, function (response) {
        CallApiError(response);
    });
}

$(function () {
    // 获取员工信息及相邻员工信息
    get_staff_neighbor();

    var scripts = [
            location.search.substring(1) || 'js/bootstrap-table.min.js',
            'js/bootstrap-table-zh-CN.min.js',
            'js/bootstrap-table-export.js',
            'js/tableExport.js',
            'js/bootstrap-table-editable.js',
            'js/bootstrap-editable.js'
        ],
        eachSeries = function (arr, iterator, callback) {
            callback = callback || function () {};
            if (!arr.length) {
                return callback();
            }
            var completed = 0;
            var iterate = function () {
                iterator(arr[completed], function (err) {
                    if (err) {
                        callback(err);
                        callback = function () {};
                    }
                    else {
                        completed += 1;
                        if (completed >= arr.length) {
                            callback(null);
                        }
                        else {
                            iterate();
                        }
                    }
                });
            };
            iterate();
        };

    eachSeries(scripts, getScript, initTable);
});
