<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'task';
$table = new DBTable('DB_WWW', $table_name);

// 任务等级
$table->format_columns[] = array('field'=>'task_level', 'formatter'=>'taskLevelFormatter');

// 任务状态
$table->format_columns[] = array('field'=>'task_status', 'formatter'=>'taskStatusFormatter');

// 展示字段设置
$table->show_columns = array("task_titile", "task_level", "task_value", "task_status", "begin_time", "limit_time");

// 排序
$table->orderby = "task_status DESC, begin_time";

// 默认不可添加
$table->add_able = false;

// 默认不可修改
$table->upd_able = false;

// 任务修改
$table->add_columns[] = array('title'=>'任务修改', 'field'=>'upd_btn', 'align'=>'center', 'valign'=>'middle', 'events'=>'updBtnEvents', 'formatter'=>'updBtnFormatter');

// 额外增加的工具栏代码
$table->add_toolbar = <<<EOF
      <button id="add_btn" class="btn btn-warning">
        <i class="glyphicon glyphicon-plus-sign"></i> 添加任务
      </button>
EOF;

// 额外增加的JS代码
$table->add_javascript =  <<<EOF
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

    // 任务等级格式化
    function taskLevelFormatter(value, row, index) {
        var fmt = '?';
        switch (value) {
          case '0':
            fmt = '筹备中';
            break;
          case '1':
            fmt = '★';
            break;
          case '2':
            fmt = '★★';
            break;
          case '3':
            fmt = '★★★';
            break;
          case '4':
            fmt = '★★★★';
          case '5':
            fmt = '★★★★★';
            break;
        }
        return fmt;
    }

    // 任务状态格式化
    function taskStatusFormatter(value, row, index) {
        var fmt = '?';
        switch (value) {
          case '0':
            fmt = '废弃';
            break;
          case '1':
            fmt = '完成';
            break;
          case '2':
            fmt = '暂停';
            break;
          case '9':
            fmt = '执行中';
            break;
        }
        return fmt;
    }

    window.updBtnEvents = {
        'click .updbtn': function (e, value, row) {
          layer.open({
              type: 2,
              title: '任务修改-【' + row.task_id + '】',
              fix: false,
              maxmin: true,
              shadeClose: true,
              shade: 0.8,
              area: ['800px', '850px'],
              content: 'dialog/task.php?id=' + row.task_id
          });
        }
    };

EOF;

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
