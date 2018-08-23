<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'fin_bank_daily_log';
$table = new DBTable('DB_WWW', $table_name);

// 展示字段列表
$table->show_columns = array("pay_account_name", "rcpt_account_name", "amount_cn", "abstract", "target", "bank_rec_date");

// 是否可添加记录
$table->add_able = false;

// 排序
$table->orderby = "bank_rec_date DESC";

// 额外增加的工具栏代码
$table->add_toolbar = <<<EOF
      <button id="add_btn" class="btn btn-warning">
        <i class="glyphicon glyphicon-plus-sign"></i> 银行日记账导入
      </button>
EOF;

// 额外增加的JS代码
$table->add_javascript =  <<<EOF
    $(function () {
      // 添加任务按钮点击事件
      $('#add_btn').click(function() {
          layer.open({
              type: 2,
              title: '银行日记账导入',
              shadeClose: true,
              shade: 0.8,
              area: ['600px', '480px'],
              content: 'dialog/fin_bank_daily_log.php'
          });
      });
    });

EOF;

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
