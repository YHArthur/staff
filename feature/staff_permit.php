<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'staff_permit';
$table = new DBTable('DB_WWW', $table_name);

// 展示字段列表
$table->show_columns = array("staff_name", "pm_id", "pm_name", "is_void", "ctime");
// 排序
$table->orderby = "CTIME DESC";

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
