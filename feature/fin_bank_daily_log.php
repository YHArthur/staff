<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'fin_bank_daily_log';
$table = new DBTable('DB_WWW', $table_name);

// 展示字段列表
$table->show_columns = array("pay_account_name", "rcpt_account_name", "amount_cn", "desc", "target", "bank_rec_date");

// 是否可添加记录
$table->add_able = false;

// 排序
$table->orderby = "bank_rec_date DESC";

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
