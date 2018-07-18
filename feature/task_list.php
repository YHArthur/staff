<?php
require_once '../inc/common.php';
require_once '../db/staff_main.php';

php_begin();

if (!session_id())
  session_start();

if (!isset($_SESSION['staff_id'])) {
  php_end('登录失效，请重新登录');
}

$cur_id = $_SESSION['staff_id'];
$cur_name = $_SESSION['staff_name'];

$rtn_str  = <<<EOF

    <div class="row">
      <div class="col-md-4">
       <h1 style="text-align: center;"><small class="text-muted" id="bef_name">123</small></h1>
      </div>
      <div class="col-md-4">
        <h1 style="text-align: center;"><span id="cur_name">{$cur_name}</span>的任务一览</h1>
      </div>
      <div class="col-md-4">
        <h1 style="text-align: center;"><small class="text-muted" id="aft_name">456</small></h1>
      </div>
    </div>

    <input type="hidden" id="cur_id" value="{$cur_id}">

    <div id="toolbar">
        <button id="add_btn" class="btn btn-warning">
        <i class="glyphicon glyphicon-plus-sign"></i> 添加任务
      </button>
    </div>

    <table id="table"
      data-locale="zh-CN"
      data-toolbar="#toolbar"
      data-search="true"
      data-show-refresh="true"
      data-show-toggle="true"
      data-show-columns="true"
      data-show-export="true"
      data-detail-view="true"
      data-detail-formatter="detailFormatter"
      data-minimum-count-columns="2"
      data-pagination="true"
      data-classes="table table-hover table-no-bordered"
      data-striped="true"
      data-id-field="task_id"
      data-page-list="[10, 25, 50, 100, 200]"
      data-show-footer="false"
      data-side-pagination="server"
      data-url="/staff/feature/task.php?m=data"
      data-response-handler="responseHandler"
    </table>

    <script src='js/task_list.js'></script>

EOF;

// 输出内容
php_end($rtn_str);
?>


