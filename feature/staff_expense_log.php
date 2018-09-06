<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'staff_expense_log';
$table = new DBTable('DB_WWW', $table_name);

// 变动时间
$table->format_columns[] = array('field'=>'exp_stamp', 'formatter'=>'dateTimeFormatter');

// 变动金额
$table->format_columns[] = array('field'=>'exp_amount', 'formatter'=>'currencyFormatter');

// 变动后余额
$table->format_columns[] = array('field'=>'exp_balance', 'formatter'=>'currencyFormatter');

// 展示字段列表
$table->show_columns = array("staff_name", "exp_count", "exp_stamp", "exp_memo", "exp_amount", "exp_balance");
// 排序
$table->orderby = "exp_stamp DESC, exp_count DESC";

// 默认不可添加
$table->add_able = false;

// 默认不可修改
$table->upd_able = false;

// 额外增加的工具栏代码
$table->add_toolbar = <<<EOF
      <button id="refresh_btn" class="btn btn-warning">
        <i class="glyphicon glyphicon-refresh"></i> 办公经费自动更新
      </button>
EOF;

// 额外增加的JS代码
$table->add_javascript =  <<<EOF
    $(function () {
      // 办公经费自动更新按钮点击事件
      $('#refresh_btn').click(function() {
          $.ajax({
            url:"api/refresh_staff_expense_log.php",
            success:function(msg) {
              if (msg.errcode == '0') {
                table.bootstrapTable('refresh');
                layer.msg(msg.errmsg);
              } else {
                layer.msg(msg.errmsg);
              }
            },
            error:function(XMLHttpRequest, textStatus, errorThrown) {
              layer.msg('数据更新失败' + textStatus + errorThrown);
            }
          });
      });
    });

    // 货币金额格式化
    function currencyFormatter(value, row, index) {

        var fmt = '¥'+parseInt(value/100)+'.'+value.substr(-2,2);
        return fmt;
    }

    // 日期格式化
    function dateTimeFormatter(value, row, index) {

        var date_time = new Date();
        date_time.setTime(value * 1000)
        var year = date_time.getFullYear();
        var month = date_time.getMonth() + 1;
        var day = date_time.getDate();
        var fmt = year+'年'+month+'月'+day+'日';
        return fmt;
    }

EOF;

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
