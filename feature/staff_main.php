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

// 是否可添加记录
$table->add_able = false;

// 排序
$table->orderby = "is_void, join_date DESC, staff_cd DESC";

// 额外增加的JS代码
$table->add_javascript =  <<<EOF

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

EOF;

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
