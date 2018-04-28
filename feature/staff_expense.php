<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'staff_expense';
$table = new DBTable('DB_WWW', $table_name);

// 展示字段列表
$table->show_columns = array("staff_name", "exp_amount", "from_date", "to_date", "max_count", "now_count", "exp_memo", "ctime");
// 排序
$table->orderby = "crt_time DESC";

// 默认不可添加
$table->add_able = false;

// 默认不可修改
$table->upd_able = false;

// 修改经费
$table->add_columns[] = array('title'=>'修改经费', 'field'=>'upd_btn', 'align'=>'center', 'valign'=>'middle', 'events'=>'updBtnEvents', 'formatter'=>'updBtnFormatter');

// 额外增加的工具栏代码
$table->add_toolbar = <<<EOF
      <button id="add_btn" class="btn btn-warning">
        <i class="glyphicon glyphicon-plus-sign"></i> 添加经费条目
      </button>
EOF;

// 额外增加的JS代码
$table->add_javascript =  <<<EOF
    $(function () {
      // 经费条目按钮点击事件
      $('#add_btn').click(function() {
          layer.open({
              type: 2,
              title: '添加经费条目',
              shadeClose: true,
              shade: 0.8,
              area: ['800px', '450px'],
              content: 'dialog/staff_expense.php'
          });
      });
    });

    // 修改按钮
    function updBtnFormatter(value, row, index) {
        return '<button class="updbtn btn-warning" type="button" aria-label="修改"><i class="glyphicon glyphicon-edit"></i></button>';
    }

    // 任务期限格式化
    function dateTimeFormatter(value, row, index) {

        var date_time = new Date(value.replace(/-/g, "/"));
        var year = date_time.getYear();
        var month = date_time.getMonth() + 1;
        var day = date_time.getDate();
        var fmt = year+'年'+month+'月'+day+'日';
        return fmt;
    }

    window.updBtnEvents = {
        'click .updbtn': function (e, value, row) {
          layer.open({
              type: 2,
              title: '修改经费',
              fix: false,
              maxmin: true,
              shadeClose: true,
              shade: 0.8,
              area: ['800px', '450px'],
              content: 'dialog/staff_expense.php?id=' + row.exp_id
          });
        }
    };

EOF;

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
