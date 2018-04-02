<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'staff_office_sign';
$table = new DBTable('DB_WWW', $table_name);

// 展示字段列表
$table->show_columns = array("log_id", "ctime", "staff_name", "sign_type", "sign_location", "user_ip", "is_void");
// 是否可添加记录
$table->add_able = false;

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
