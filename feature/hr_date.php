<?php
require_once '../inc/common.php';

php_begin();

if (!session_id())
  session_start();

if (!isset($_SESSION['staff_id'])) {
  php_end('登录失效，请重新登录');
}

$cur_month = date('Ym');
$cur_month_str = date('Y年n月');

$rtn_str  = <<<EOF

    <div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-2">
       <h1 style="text-align: center;"><small class="text-muted" id="bef_month"></small></h1>
      </div>
      <div class="col-md-4">
        <h1 style="text-align: center;"><span id="cur_month_str" class="text-primary">{$cur_month_str}</span> 节假日设定</h1>
      </div>
      <div class="col-md-2">
        <h1 style="text-align: center;"><small class="text-muted" id="aft_month"></small></h1>
      </div>
      <div class="col-md-2"></div>
    </div>

    <input type="hidden" id="cur_month" value="{$cur_month}">
    
    <div class="fixed-table-toolbar">
      <div class="bars pull-left">
        <div id="toolbar">
          <button id="add_btn" class="btn btn-danger"><i class="glyphicon glyphicon-plus-sign"></i> 初始化数据</button>
        </div>
      </div>
      <div class="columns columns-right btn-group pull-right">
        <div class="export btn-group">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button">
        <i class="glyphicon glyphicon-calendar icon-share"></i>
        <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu" style="min-width: 60px;">
          <li data-type="01"><a href="javascript:void(0)">一月</a></li>
          <li data-type="02"><a href="javascript:void(0)">二月</a></li>
          <li data-type="03"><a href="javascript:void(0)">三月</a></li>
          <li data-type="04"><a href="javascript:void(0)">四月</a></li>
          <li data-type="05"><a href="javascript:void(0)">五月</a></li>
          <li data-type="06"><a href="javascript:void(0)">六月</a></li>
          <li data-type="07"><a href="javascript:void(0)">七月</a></li>
          <li data-type="08"><a href="javascript:void(0)">八月</a></li>
          <li data-type="09"><a href="javascript:void(0)">九月</a></li>
          <li data-type="10"><a href="javascript:void(0)">十月</a></li>
          <li data-type="11"><a href="javascript:void(0)">十一月</a></li>
          <li data-type="12"><a href="javascript:void(0)">十二月</a></li>
        </ul>
        </div>
      </div>
    </div>
    
    <div class="fixed-table-container table-no-bordered" style="height: 691px; padding-bottom: 41px;">

      <table class="table table-hover table-no-bordered table-striped">
        <thead>
          <tr>
            <th>周一</th>
            <th>周二</th>
            <th>周三</th>
            <th>周四</th>
            <th>周五</th>
            <th style="background-color: #CC6666;">周六</th>
            <th style="background-color: #CC6666;">周日</th>
          </tr>
        </thead>
        <tbody id="tag_list">
          <tr>
            <td colspan="7">暂时没有日期数据</td>
          </tr>
        </tbody>
      </table>
    
    </div>

    <script>
      getScript('js/hr_date.js');
    </script>

EOF;

// 输出内容
php_end($rtn_str);
?>
