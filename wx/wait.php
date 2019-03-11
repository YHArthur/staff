<?php
require_once '../inc/common.php';
require_once '../inc/wx_login.php';
require_once '../db/staff_weixin.php';

// 需要微信登录
need_wx_login();

if (!session_id())
  session_start();

if (!isset($_SESSION['unionid']))
  exit_error('119', '该网页已失效，请重新刷新页面再试');

$unionid = $_SESSION['unionid'];
$is_void = 1;
$page_title = '账号审核中';
$page_desc = '你的微信账号信息已经成功提交，审核通过后即可使用相关功能。';

// 取得审核结果
$rec = get_staff_weixin($unionid);
if ($rec) {
  $is_void = $rec['is_void'];
  if ($is_void == 0) {
    $page_title = '账号审核已通过';
    $page_desc = '你的员工微信账号已经审核通过，可以使用相关功能。';
  }
  $staff_name = $rec['staff_name'];
  $staff_avata = $rec['staff_avata'];
  $page_desc = "亲爱的<b>{$staff_name}</b>，{$page_desc}";
}


?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
  <title>员工微信注册-风赢科技</title>
  <link rel="stylesheet" href="css/weui.css">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>

  <div class="page__hd">
    <h1 class="page__title"><?php echo $page_title ?></h1>
    <p class="page__desc"><?php echo $page_desc ?></p>
  </div>

  <div class="weui-msg__extra-area"><a href="../h5_menu.php">风赢科技</a></div>

</body>
</html>

