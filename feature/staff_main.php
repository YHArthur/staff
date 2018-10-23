<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'staff_main';
$table = new DBTable('DB_WWW', $table_name);

// 员工性别
$table->format_columns[] = array('field'=>'staff_sex', 'formatter'=>'staffSexFormatter');

// 出生年份
$table->format_columns[] = array('field'=>'birth_year', 'formatter'=>'birthYearFormatter');

// 是否无效
$table->format_columns[] = array('field'=>'is_void', 'formatter'=>'isVoidFormatter');

// 展示字段列表
$table->show_columns = array("staff_cd", "staff_name", "staff_sex", "birth_year", "join_date", "is_void", "ctime");

// 不可添加
$table->add_able = false;

// 不可修改
$table->upd_able = false;

// 排序
$table->orderby = "is_void, staff_cd DESC";

// 修改按钮
$table->add_columns[] = array('title'=>'修改', 'field'=>'upd_btn', 'align'=>'center', 'valign'=>'middle', 'events'=>'updBtnEvents', 'formatter'=>'updBtnFormatter');

// 额外增加的JS代码
$table->add_javascript =  <<<EOF

    // 修改按钮
    function updBtnFormatter(value, row, index) {
        return '<button class="updbtn btn-warning" type="button" aria-label="修改"><i class="glyphicon glyphicon-edit"></i></button>';
    }
    
    // 出生年份
    function birthYearFormatter(value, row, index) {
        var d = new Date()
        var fmt = d.getFullYear() - value;
        return fmt + '岁';
    }

    // 员工性别
    function staffSexFormatter(value, row, index) {
        var fmt = '不明';
        switch (value) {
          case '1':
            fmt = '男';
            break;
          case '2':
            fmt = '女';
            break;
        }
        return fmt;
    }

    // 是否无效
    function isVoidFormatter(value, row, index) {
        var fmt = '?';
        switch (value) {
          case '0':
            fmt = '<span class="label label-success">有效</span>';
            break;
          case '1':
            fmt = '<span class="label label-danger">无效</span>';
            break;
        }
        return fmt;
    }

    window.updBtnEvents = {
        'click .updbtn': function (e, value, row) {
          layer.open({
              type: 2,
              title: '员工情报修改',
              fix: false,
              maxmin: true,
              shadeClose: true,
              shade: 0.8,
              area: ['800px', '800px'],
              content: 'dialog/staff_main.php?id=' + row.staff_id
          });
        }
    };

EOF;

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
