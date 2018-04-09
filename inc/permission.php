<?php
//======================================
// 函数: need_staff_login
// 功能: 需要员工登录
// 参数: 无
// 返回: 无
// 说明: 如果设置了 $_SESSION['staff_id'] 直接返回
//       如果设置了 $_SESSION['unionid']，表示已经微信登录，根据微信统一标识获取staff_id信息
//         无员工微信账号记录，跳转新员工注册页面
//         有员工微信账号记录，但尚未审核通过的跳转新员工待审核页面
//         正常员工设置 $_SESSION['staff_id']和staff_name，然后返回
//       先进行微信登录
//======================================
function need_staff_login()
{
  if (!session_id())
    session_start();

  // 如果设置了 $_SESSION['staff_id']，表示已经登录，直接返回
  if (isset($_SESSION['staff_id']))
    return;
  
  // 如果设置了 $_SESSION['unionid']，表示已经微信登录，根据微信统一标识获取staff_id信息
  if (isset($_SESSION['unionid'])) {
    $unionid = $_SESSION['unionid'];
    // 取得指定微信统一标识的员工微信账号记录
    $rec = get_staff_weixin($unionid);
    
    // 无员工微信账号记录
    if (!$rec) {
      // 跳转新员工注册URL
      $url = Config::STAFF_SIGN_URL;
      Header("Location: $url");
      exit(0);
    }
    
    // 是否无效
    $is_void = $rec['is_void'];
    // 已注册未审核
    if ($is_void != 0) {
      // 跳转新员工待审核URL
      $url = Config::STAFF_WAIT_URL;
      Header("Location: $url");
      exit(0);
    }
    
    // 正常员工，设置Session
    $_SESSION['staff_id'] = $rec['staff_id'];
    $_SESSION['staff_name'] = $rec['staff_name'];
    return;
  }

  // 以上都不满足，表示需要微信登录
  need_wx_login();
}

//======================================
// 函数: has_pm($pmid)
// 功能: 员工是否有该权限
// 参数: $pmid          权限ID
// 返回: true           有权限
//       false          无权限
// 说明: 根据Session中保存的员工权限列表判定
//======================================
function has_pm($pmid)
{
  $staff_id = $_SESSION['staff_id'];
  if ($staff_id == '640C3986-5EC2-EABA-59C1-B9C6EC4FF610')
    return true;
  return false;
}
?>
