<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'staff_permit';
$table = new DBTable('DB_WWW', $table_name);

// 权限
$table->format_columns[] = array('field'=>'pm_name', 'formatter'=>'pmNameFormatter');

// 是否无效
$table->format_columns[] = array('field'=>'is_void', 'formatter'=>'isVoidFormatter');

// 展示字段列表
$table->show_columns = array("staff_name", "pm_name", "from_date", "to_date", "is_void", "ctime", "cname");

// 排序
$table->orderby = "CTIME DESC";

// 默认不可添加
$table->add_able = false;

// 默认不可修改
$table->upd_able = false;

// 修改权限
$table->add_columns[] = array('title'=>'修改权限', 'field'=>'upd_btn', 'align'=>'center', 'valign'=>'middle', 'events'=>'updBtnEvents', 'formatter'=>'updBtnFormatter');

// 额外增加的工具栏代码
$table->add_toolbar = <<<EOF
      <button id="add_btn" class="btn btn-warning">
        <i class="glyphicon glyphicon-plus-sign"></i> 添加权限
      </button>
EOF;

// 额外增加的JS代码
$table->add_javascript =  <<<EOF
    $(function () {
      // 添加权限按钮点击事件
      $('#add_btn').click(function() {
          layer.open({
              type: 2,
              title: '添加权限',
              shadeClose: true,
              shade: 0.8,
              area: ['500px', '500px'],
              content: 'dialog/staff_permit.php'
          });
      });
    });

    // 权限
    function pmNameFormatter(value, row, index) {
        var fmt = value.replace('　', "菜单：");
        return fmt;
    }

    // 是否无效
    function isVoidFormatter(value, row, index) {
        var fmt = '?';
        switch (value) {
          case '0':
            fmt = '<span class="label label-success">有效</span>';
            break;
          case '1':
            fmt = '<span class="label label-danger">无效</span>';
            break;
        }
        return fmt;
    }
    
    // 修改按钮
    function updBtnFormatter(value, row, index) {
        return '<button class="updbtn btn-warning" type="button" aria-label="修改"><i class="glyphicon glyphicon-edit"></i></button>';
    }
    
    window.updBtnEvents = {
        'click .updbtn': function (e, value, row) {
          layer.open({
              type: 2,
              title: '修改权限',
              fix: false,
              maxmin: true,
              shadeClose: true,
              shade: 0.8,
              area: ['500px', '500px'],
              content: 'dialog/staff_permit.php?staff_id=' + row.staff_id + '&pm_id=' + row.pm_id
          });
        }
    };
EOF;

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
