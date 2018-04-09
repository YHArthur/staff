<?php
$code = intval($_GET["code"]);
switch($code)
{
  case '1':
    $info_str = "无法在微信浏览器中访问本页面，请在电脑上打开";
    break;
  case '2':
    $info_str = "无法在微信以外的浏览器中访问本页面，请在微信上打开";
    break;
  default:
    $info_str = "你没有访问该页面的权限";
    break;
}


?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>禁止访问-风赢科技</title>

  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/index.css">
</head>

<body>

  <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="index.php">员工管理平台</a>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <!--状态-->
    <div id="main_status">
    <h2><?php echo $info_str?></h2>
    </div>
  </div>
  
  <script type="text/javascript" src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script type="text/javascript" src="http://libs.baidu.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  
</body>
</html>
