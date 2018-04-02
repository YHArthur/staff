<?php
//======================================
// 函数: need_wx_login
// 功能: 需要用户微信登录
// 参数: 无
// 返回: 无
// 说明: 如果设置了 $_SESSION['unionid'] 直接返回
//       如果同时设置了 $_GET['id'] 和 $_GET['token']
//         判断 $_GET['token'] 与 $_SESSION['token'] 是否一致
//         如果一致登录有效，设置 $_SESSION['unionid'] = $_GET['id'] 直接返回
//       如果未设置 $_GET['id'] 或 $_GET['token']
//       生成并设置 $_SESSION['token']，并调用微信登录页面（用get参数传递token和当前url）
//======================================
function need_wx_login()
{
  if (!session_id())
    session_start();

  // 如果设置了 $_SESSION['unionid']，表示已经登录，直接返回
  if (isset($_SESSION['unionid']))
    return;
  
  // 如果同时设置了 $_GET['id'] 和 $_GET['token']，表示登录成功，回调回来
  if (isset($_GET['id']) && isset($_GET['token']) && isset($_SESSION['token'])) {
    // 判断 $_GET['token'] 与 $_SESSION['token'] 是否一致
    if ($_SESSION['token'] == $_GET['token']) {
      // 登录有效，清除token
      unset($_SESSION['token']);
      // 设置 $_SESSION['unionid'] = $_GET['id']
      $unionid = $_GET['id'];
      $_SESSION['unionid'] = $unionid;
      // 为了保持浏览器地址栏的整洁，读取原来的url进行一次跳转
      if (isset($_SESSION['url'])) {
        $url = $_SESSION['url'];
        unset($_SESSION['url']);
      } else {
        // 预防万一读取原来的url失败
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol . $_SERVER['HTTP_HOST'];
      }
      Header("Location: $url");
      exit(0);
    } else {
      exit('error token');
    }
  }

  // 以上都不满足，表示需要登录
  // 生成并设置 $_SESSION['token']，$_SESSION['url']
  $token = md5(uniqid(mt_rand(), true));
  $_SESSION['token'] = $token;
  $_SESSION['url'] = get_url();
  $url = urlencode($_SESSION['url']);
  // 并调用微信登录页面（用get参数传递token和当前url）
  $login_url = Config::WX_LOGIN_URL  . "?token={$token}&url={$url}";
  Header("Location: $login_url");
  exit(0);
}
?>
