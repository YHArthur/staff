<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'fin_sub_cn';
$table = new DBTable('DB_WWW', $table_name);

// 是否无效
$table->format_columns[] = array('field'=>'is_void', 'formatter'=>'isVoidFormatter');

// 排序
$table->orderby = "is_void, sub_id";

// 额外增加的JS代码
$table->add_javascript =  <<<EOF
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
