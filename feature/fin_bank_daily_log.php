<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'fin_bank_daily_log';
$table = new DBTable('DB_WWW', $table_name);

// 货币金额
$table->format_columns[] = array('field'=>'amount', 'formatter'=>'currencyFormatter');

// 收支区分
$table->format_columns[] = array('field'=>'is_pay', 'formatter'=>'isPayFormatter');

// 展示字段列表
$table->show_columns = array("is_pay", "pay_account_name", "rcpt_account_name", "amount", "abstract", "bank_rec_date");

// 是否可添加记录
$table->add_able = false;

// 是否可修改记录
$table->upd_able = false;

// 排序
$table->orderby = "bank_rec_date DESC";

// 额外增加的工具栏代码
$table->add_toolbar = <<<EOF
      <button id="add_btn" class="btn btn-warning">
        <i class="glyphicon glyphicon-plus-sign"></i> 银行账导入
      </button>
      <button id="sum_btn" class="btn btn-primary">
        <i class="glyphicon glyphicon-stats"></i> 余额集计表
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

      // 集计按钮点击事件
      $('#sum_btn').click(function() {
          layer.open({
              type: 2,
              title: '银行余额集计表',
              shadeClose: true,
              shade: 0.8,
              area: ['780px', '500px'],
              content: 'dialog/fin_bank_daily_sum.php'
          });
      });      
    });

    // 货币金额格式化
    function currencyFormatter(value, row, index) {
        var fmt;
        if (value.length > 1) {
          fmt = '¥'+parseInt(value/100)+'.'+value.substr(-2,2);
        } else {
          fmt = '¥0.0'+value;
        }
        return fmt;
    }

    // 收支区分格式化
    function isPayFormatter(value, row, index) {
        var fmt = '?';
        switch (value) {
          case '0':
            fmt = '<span class="label label-success">收</span>';
            break;
          case '1':
            fmt = '<span class="label label-danger">支</span>';
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
