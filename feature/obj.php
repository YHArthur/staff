<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'obj';
$table = new DBTable('DB_WWW', $table_name);

// 公开等级
$table->format_columns[] = array('field'=>'public_level', 'formatter'=>'publicLevelFormatter');

// 目标状态
$table->format_columns[] = array('field'=>'obj_status', 'formatter'=>'objStatusFormatter');

// 目标期限
$table->format_columns[] = array('field'=>'limit_time', 'formatter'=>'limitTimeFormatter');

// 展示字段设置
$table->show_columns = array("public_level", "obj_name", "obj_status", "limit_time", "owner_name");

// 排序
$table->orderby = "obj_status DESC, obj_level DESC, limit_time";

// 默认不可添加
$table->add_able = false;

// 默认不可修改
$table->upd_able = false;

// 修改目标
$table->add_columns[] = array('title'=>'修改目标', 'field'=>'upd_btn', 'align'=>'center', 'valign'=>'middle', 'events'=>'updBtnEvents', 'formatter'=>'updBtnFormatter');

// 额外增加的工具栏代码
$table->add_toolbar = <<<EOF
      <button id="add_btn" class="btn btn-warning">
        <i class="glyphicon glyphicon-plus-sign"></i> 添加目标
      </button>
EOF;

// 额外增加的JS代码
$table->add_javascript =  <<<EOF
    $(function () {
      // 添加目标按钮点击事件
      $('#add_btn').click(function() {
          layer.open({
              type: 2,
              title: '添加目标',
              shadeClose: true,
              shade: 0.8,
              area: ['800px', '750px'],
              content: 'dialog/obj.php'
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
            fmt = '私人';
            break;
          case '1':
            fmt = '公开';
            break;
        }
        return fmt;
    }

    // 目标状态格式化
    function objStatusFormatter(value, row, index) {
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

    // 目标期限格式化
    function limitTimeFormatter(value, row, index) {

        var limit_time = new Date(value.replace(/-/g, "/"));
        var month = limit_time.getMonth() + 1;
        var day = limit_time.getDate();
        var fmt = month+'月'+day+'日';
        if (row.obj_status <= 1)
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
              title: '修改目标',
              fix: false,
              maxmin: true,
              shadeClose: true,
              shade: 0.8,
              area: ['800px', '750px'],
              content: 'dialog/obj.php?id=' + row.obj_id
          });
        }
    };

EOF;

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
