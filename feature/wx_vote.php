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

    <h1>微信投票项目一览</h1>

    <div id="toolbar">
        <button id="add_btn" class="btn btn-warning">
        <i class="glyphicon glyphicon-plus-sign"></i> 新的投票
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
      data-id-field="vote_id"
      data-page-list="[10, 25, 50, 100, 200]"
      data-show-footer="false"
      data-side-pagination="server"
      data-response-handler="responseHandler"
    </table>

    <script>
      getScript('js/wx_vote.js');
    </script>

EOF;

// 输出内容
php_end($rtn_str);
?>


