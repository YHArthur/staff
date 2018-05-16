<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'www_email';
$table = new DBTable('DB_WWW', $table_name);

// 展示字段列表
$table->show_columns = array("email", "uuid", "user_ip", "is_void", "utime", "ctime");

// 默认不可添加
$table->add_able = false;

// 额外增加的工具栏代码
$table->add_toolbar = <<<EOF
      <button id="add_btn" class="btn btn-warning">
        <i class="glyphicon glyphicon-envelope"></i> 批量发送邮件
      </button>
EOF;

// 额外增加的JS代码
$table->add_javascript =  <<<EOF
    $(function () {
      // 批量发送邮件按钮点击事件
      $('#add_btn').click(function() {
          layer.open({
              type: 2,
              title: '批量发送邮件',
              shadeClose: true,
              shade: 0.8,
              area: ['800px', '550px'],
              content: 'dialog/send_email.php'
          });
      });
    });

EOF;

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
