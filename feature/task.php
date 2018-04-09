<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'task';
$table = new DBTable('DB_WWW', $table_name);

// 排序
$table->orderby = "task_status DESC, begin_time";

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
