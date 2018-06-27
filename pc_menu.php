<?php
require_once 'inc/common.php';
require_once 'db/permit.php';
require_once 'db/staff_weixin.php';
require_once 'db/staff_permit.php';

// 需要员工登录
need_staff_login();

$staff_id = $_SESSION['staff_id'];
$staff_name = $_SESSION['staff_name'];

// 模块图标设定
$icon = array();
$icon[1000] = 'king';
$icon[1010] = 'star';
$icon[1020] = 'bullhorn';
$icon[1030] = 'yen';
$icon[1040] = 'list-alt';
$icon[1050] = 'list-alt';
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="format-detection" content="telephone=no" />
  <meta name="description" content="">
  <meta name="author" content="">

  <title>风赢科技员工管理平台</title>

  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/bootstrap-table.min.css">
  <link rel="stylesheet" href="css/bootstrap-editable.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/index.css">
</head>

<body>

  <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menubar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="h5_menu.php">风赢科技员工管理平台</a>
      </div>
      <div id="h-navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
          <li><a href="#"><?php echo $staff_name?></a></li>
          <li><a href="logout.php">退出</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <!--左侧导航-->
      <div class="col-sm-3 col-md-2 sidebar panel-group" id="menubar">
<?php
// 取得当前员工所有有效权限
$staff_pm_list = get_staff_permit_list($staff_id);
// 是否有系统权限
$sys_pm = has_sys_pm($staff_pm_list);
// 取得系统所有权限
$rows = get_permit_all();
// 当前模块
$tmp_sub = '';

// 循环系统所有权限
foreach ($rows AS $row) {
  $pm_id =  $row['pm_id'];        // 权限ID
  $pm_cd =  $row['pm_cd'];        // 权限代号
  $pm_nm =  $row['pm_nm'];        // 权限名字
  // 系统管理员默认模块名称
  if (substr($pm_id, 1) == '000')
    $pm_nm = '系统管理员';
  
  // 模块
  if (substr($pm_id, 3) == '0') {
    // 前一模块菜单收尾输出
    if ($tmp_sub != '') {
      echo "\n              </ul>";
      echo "\n            </div>";
      echo "\n          </div>";
      echo "\n        </div>";
      echo "\n";
    }

    // 计算模块权限
    if ($sys_pm) {
      $sub_pm = 2;
    } else {
      $sub_pm = has_sub_pm($pm_id, $staff_pm_list);
    }
    
    // 员工至少有部分模块权限
    if ($sub_pm >= 0) {
      echo "\n".'        <div class="panel panel-default">';
      echo "\n".'          <div class="panel-heading">';
      echo "\n".'            <h4 class="panel-title"><a data-toggle="collapse" data-parent="#menubar" href="#' . $pm_cd . '_menu">';
      echo "\n".'              <i class="glyphicon glyphicon-' . $icon[$pm_id] . '"></i> ' . $pm_nm;
      echo "\n".'            </a></h4>';
      echo "\n".'          </div>';
      echo "\n".'          <div id="' . $pm_cd . '_menu" class="panel-collapse collapse">';
      echo "\n".'            <div class="panel-body">';
      echo "\n".'              <ul class="nav nav-sidebar">';
      $tmp_sub = $pm_id;
    }
  } else {
    // 计算菜单权限
    if ($sys_pm || $sub_pm == 1) {
      $menu_pm = true;
    } else {
      $menu_pm = in_array($pm_id, $staff_pm_list);
    }

    if ($menu_pm)
      echo "\n".'                <li><a href="javascript:;" onclick="menu_click(' . "'feature','" . $pm_cd . "'" . ')">' . $pm_nm . '</a></li>';
  }
}

if ($tmp_sub != '') {
  // 前一模块菜单收尾输出
  echo "\n              </ul>";
  echo "\n            </div>";
  echo "\n          </div>";
  echo "\n        </div>";
  echo "\n";
}
?>

      </div>

      <!--右侧内容-->
      <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

        <!--状态-->
        <div id="main_status">
        </div>

      </div>

    </div>

  </div>

  <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
  <script src="http://libs.baidu.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="http://cdn.bootcss.com/Chart.js/2.1.6/Chart.bundle.min.js"></script>
  <script src="js/layer/layer.js"></script>
  <script src="js/pc_menu.js"></script>

</body>
</html>
