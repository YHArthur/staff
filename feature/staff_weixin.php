<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'staff_weixin';
$table = new DBTable('DB_WWW', $table_name);

// 展示字段列表
$table->show_columns = array("staff_cd", "staff_name", "staff_avata", "wx_name", "is_void", "ctime");
// 字段转换样式列表
$table->format_columns[] = array('field'=>'staff_avata', 'formatter'=>'urlFormatter');
// 是否可添加记录
$table->add_able = false;
// 排序
$table->orderby = "CTIME DESC";

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
