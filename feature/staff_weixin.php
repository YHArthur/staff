<?php
require_once '../inc/common.php';

php_begin();

$table_name = 'staff_weixin';
$table = new DBTable('DB_WWW', $table_name);

// 展示字段列表
$table->show_columns = array("staff_name", "staff_phone", "staff_avata", "wx_name", "ctime", "is_void");
// 字段转换样式列表
$table->format_columns[] = array('field'=>'staff_avata', 'formatter'=>'imageFormatter');
// 是否无效
$table->format_columns[] = array('field'=>'is_void', 'formatter'=>'isVoidFormatter');
// 是否可添加记录
$table->add_able = false;
// 排序
$table->orderby = "CTIME DESC";

// 额外增加的JS代码
$table->add_javascript =  <<<EOF


    // 正式员工审核通过处理
    function getChkBtn(value, row, index) {
        return '<button class="updfeedback btn-warning" type="button" aria-label="审核"><i class="glyphicon glyphicon-ok"></i> 审核</button>';
    }

    // 是否无效
    function isVoidFormatter(value, row, index) {
        var fmt = '';
        switch (value) {
          case '0':
            fmt = '正式员工';
            break;
          case '1':
            fmt = getChkBtn(value, row, index);
            break;
        }
        return fmt;
    }
    

    window.updfeedbackEvents = {
        'click .updfeedback': function (e, value, row) {
          layer.open({
              type: 2,
              title: row.uname + ' 的故障申述处理',
              fix: false,
              maxmin: true,
              shadeClose: true,
              shade: 0.8,
              area: ['800px', '850px'],
              content: 'dialog/dat_feedback.php?logid=' + row.logid
          });
        }
    };    
    
EOF;

// 根据参数分析表格处理
$rtn_str = $table->analysis();

// 输出内容
php_end($rtn_str);
?>
