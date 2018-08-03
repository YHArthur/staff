<?php
require_once '../inc/common.php';

php_begin();

if (!session_id())
  session_start();

if (!isset($_SESSION['staff_id'])) {
  php_end('登录失效，请重新登录');
}

$cur_id = $_SESSION['staff_id'];

$rtn_str  = <<<EOF

    <div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-2">
       <h1 style="text-align: center;"><small class="text-muted" id="bef_name"></small></h1>
      </div>
      <div class="col-md-4">
        <h1 style="text-align: center;"><span id="cur_name" class="text-primary">我</span> 的任务一览</h1>
      </div>
      <div class="col-md-2">
        <h1 style="text-align: center;"><small class="text-muted" id="aft_name"></small></h1>
      </div>
      <div class="col-md-2"></div>
    </div>

    <input type="hidden" id="cur_id" value="{$cur_id}">
    <input type="hidden" id="my_id" value="{$cur_id}">

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
      data-detail-view="false"
      data-detail-formatter="detailFormatter"
      data-minimum-count-columns="2"
      data-pagination="true"
      data-classes="table table-hover table-no-bordered"
      data-striped="true"
      data-id-field="task_id"
      data-page-list="[10, 25, 50, 100, 200]"
      data-show-footer="false"
      data-side-pagination="server"
      data-url="/staff/api/task_list.php?task_status=9&staff_id={$cur_id}"
      data-response-handler="responseHandler"
    </table>

    <script>
      getScript('js/task_list.js');
    </script>

EOF;

// 输出内容
php_end($rtn_str);
?>


