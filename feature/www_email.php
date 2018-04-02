<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'www_email';
$table = new DBTable('DB_WWW', $table_name);

// 展示字段列表
$table->show_columns = array("email", "uuid", "user_ip", "is_void", "utime", "ctime");


// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
