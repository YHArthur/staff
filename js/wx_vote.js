var table_name = '微信投票项目一览';
var table = $('#table');

// 表格初始化
function initTable() {
    table.bootstrapTable({
        height: getHeight(),
        columns: [
            {
                title: 'ID',
                field: 'vote_id',
                align: 'center',
                sortable: true,
                valign: 'middle'
            },
            {
                title: '标题',
                field: 'vote_title',
                align: 'left',
                sortable: true,
                valign: 'middle'
            },
            {
                title: '创建人',
                field: 'owner_name',
                align: 'left',
                sortable: true,
                valign: 'middle'
            },
            {
                title: '描述',
                field: 'vote_info',
                align: 'left',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '截止时间',
                field: 'limit_time',
                align: 'center',
                sortable: true,
                valign: 'middle'
            },
            {
                title: '选项',
                field: 'is_multi',
                align: 'center',
                sortable: true,
                valign: 'middle',
                formatter: isMultiFormatter
            },
            {
                title: '类型',
                field: 'is_anonymous',
                align: 'center',
                sortable: true,
                valign: 'middle',
                formatter: isAnonymousFormatter
            },
            {
                title: '创建人ID',
                field: 'owner_unionid',
                align: 'left',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '创建人头像',
                field: 'owner_avate',
                align: 'center',
                visible: false,
                sortable: true,
                valign: 'middle'
            },
            {
                title: '创建时间',
                field: 'ctime',
                align: 'center',
                visible: true,
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
                field: 'del_btn',
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
    return '<button class="updbtn btn-warning" type="button" aria-label="修改"><i class="glyphicon glyphicon-edit"></i></button>';
}

// 修改按钮触发事件
window.updBtnEvents = {
    'click .updbtn': function (e, value, row) {
      layer.open({
          type: 2,
          title: getRowDescriptions(row) + '的投票项目修改',
          fix: false,
          maxmin: true,
          shadeClose: true,
          shade: 0.8,
          area: ['800px', '780px'],
          content: 'dialog/wx_vote.php?vote_id=' + row.vote_id
      });
    }
};

// 删除按钮
function delBtnFormatter(value, row, index) {
    return ' <button class="remove btn-danger" type="button" aria-label="删除"><i class="glyphicon glyphicon-remove"></i></button> ';
}

// 删除按钮触发事件
window.delBtnEvents = {
    'click .remove': function (e, value, row) {
      //询问框
      layer.confirm('是否删除 ' + getRowDescriptions(row) + ' 的记录？', {
          icon: 3,
          title: table_name + '操作确认',
          btn: ['确认','取消']
      }, function(){
          $.ajax({
              url: '/staff/api/wx_vote_delete.php?vote_id=' + row.vote_id,
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

// 是否多选格式化
function isMultiFormatter(value, row, index) {
    var fmt = '?';
    switch (value) {
      case '0':
        fmt = '单选';
        break;
      case '1':
        fmt = '多选';
        break;
    }
    return fmt;
}

// 是否匿名格式化
function isAnonymousFormatter(value, row, index) {
    var fmt = '?';
    switch (value) {
      case '0':
        fmt = '实名';
        break;
      case '1':
        fmt = '匿名';
        break;
    }
    return fmt;
}

// 投票标题格式化
function voteTitleFormatter(value, row, index) {
    return '<a href="http://wx.fnying.com/vote/show.php?id=' + row.vote_id + '" target="_blank">' + row.vote_title + '</a>';
}

// 结束时间格式化
function closedTimeFormatter(value, row, index) {

    if (row.is_closed == 0)
      return '未完';

    var closed_time = new Date(value.replace(/-/g, "/"));
    // 相差日期计算
    var current_time = new Date();
    var diff_day = parseInt((current_time.getTime() - closed_time.getTime()) / (1000 * 3600 * 24));
    if (diff_day == 0) {
      fmt = '<span class="text-success">今天</span>';
      return fmt;
    } else if (diff_day <= 7) {
      fmt = '<span class="text-info">' + diff_day + '天前</span>';
    } else if (diff_day <= 30) {
      fmt = '<span class="text-warning">' + parseInt(diff_day / 7) + '周前</span>';
    } else {
      fmt = '<span class="text-muted">' + parseInt(diff_day / 30) + '个月前</span>';
    }
    return fmt;
}

// 取得记录描述
function getRowDescriptions(row) {
    return '【' + row.vote_title + '】';
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

$(function () {
    // 添加投票按钮点击事件
    $('#add_btn').click(function() {
        layer.open({
            type: 2,
            title: '创建新的投票',
            shadeClose: true,
            shade: 0.8,
            area: ['800px', '780px'],
            content: 'dialog/wx_vote.php'
        });
    });

    // 表格初始化
    initTable();
    var opt = {url: "/staff/api/action_list.php"};
    table.bootstrapTable('refresh', opt);
    table.bootstrapTable('resetView', {height: getHeight()});

});
