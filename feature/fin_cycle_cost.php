<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'fin_cycle_cost';
$table = new DBTable('DB_WWW', $table_name);

// 是否无效
$table->format_columns[] = array('field'=>'is_void', 'formatter'=>'isVoidFormatter');

// 支出金额
$table->format_columns[] = array('field'=>'cost_amount', 'formatter'=>'currencyFormatter');

// 开始时间
$table->format_columns[] = array('field'=>'from_date', 'formatter'=>'dateTimeFormatter');

// 结束时间
$table->format_columns[] = array('field'=>'to_date', 'formatter'=>'dateTimeFormatter');

// 展示字段设置
$table->show_columns = array("staff_cd", "staff_name", "cost_memo", "cost_amount", "from_date", "to_date", "month_gap", "term_day", "is_fix", "is_void", "cname");

// 排序
$table->orderby = "is_void, from_date DESC";

// 默认不可添加
$table->add_able = false;

// 默认不可修改
$table->upd_able = false;

// 修改经费
$table->add_columns[] = array('title'=>'修改经费', 'field'=>'upd_btn', 'align'=>'center', 'valign'=>'middle', 'events'=>'updBtnEvents', 'formatter'=>'updBtnFormatter');

// 额外增加的工具栏代码
$table->add_toolbar = <<<EOF
      <button id="add_btn" class="btn btn-warning">
        <i class="glyphicon glyphicon-plus-sign"></i> 添加支出条目
      </button>

      <button id="add_staff_btn" class="btn btn-warning">
        <i class="glyphicon glyphicon-user"></i> 添加入职员工
      </button>
EOF;

// 额外增加的JS代码
$table->add_javascript =  <<<EOF
    $(function () {
      // 添加支出条目按钮点击事件
      $('#add_btn').click(function() {
          layer.open({
              type: 2,
              title: '添加支出条目',
              shadeClose: true,
              shade: 0.8,
              area: ['800px', '450px'],
              content: 'dialog/fin_cycle_cost.php'
          });
      });

      // 添加入职员工按钮点击事件
      $('#add_staff_btn').click(function() {
          layer.open({
              type: 2,
              title: '添加入职员工',
              shadeClose: true,
              shade: 0.8,
              area: ['800px', '450px'],
              content: 'dialog/fin_cycle_cost_staff.php'
          });
      });
    });

    // 修改按钮
    function updBtnFormatter(value, row, index) {
        return '<button class="updbtn btn-warning" type="button" aria-label="修改"><i class="glyphicon glyphicon-edit"></i></button>';
    }

    // 货币金额格式化
    function currencyFormatter(value, row, index) {
        var fmt = '¥'+parseInt(value/100)+'.'+value.substr(-2,2);
        return fmt;
    }

    // 日期格式化
    function dateTimeFormatter(value, row, index) {
        var date_time = new Date(value.replace(/-/g, "/"));
        var year = date_time.getFullYear();
        var month = date_time.getMonth() + 1;
        var day = date_time.getDate();
        var fmt = year+'年'+month+'月'+day+'日';
        return fmt;
    }

    // 是否无效
    function isVoidFormatter(value, row, index) {
        var fmt = '?';
        switch (value) {
          case '0':
            fmt = '有效';
            break;
          case '1':
            fmt = '无效';
            break;
        }
        return fmt;
    }

    window.updBtnEvents = {
        'click .updbtn': function (e, value, row) {
          layer.open({
              type: 2,
              title: '修改支出条目',
              fix: false,
              maxmin: true,
              shadeClose: true,
              shade: 0.8,
              area: ['800px', '450px'],
              content: 'dialog/fin_cycle_cost_staff.php?id=' + row.cost_id
          });
        }
    };
EOF;

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
