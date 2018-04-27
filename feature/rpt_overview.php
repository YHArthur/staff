<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'rpt_overview';
$table = new DBTable('DB_WWW', $table_name);

// 展示字段列表
$table->show_columns = array("rpt_title", "rpt_sort", "url_key", "rpt_unit", "rpt_count", "rpt_time");
// 排序
$table->orderby = "rpt_sort";

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
