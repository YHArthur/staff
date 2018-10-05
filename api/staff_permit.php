<?php
require_once "../inc/common.php";
require_once '../db/permit.php';
require_once '../db/staff_permit.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 权限设定 ==========================
GET参数
  staff_id        员工ID
  staff_name      员工姓名
  pm_id           权限ID
  pm_name         权限名字
  from_date       开始时间
  to_date         结束时间
  is_void         是否无效

返回
  设定结果

说明
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('staff_id', 'staff_name', 'pm_id', 'pm_name', 'from_date', 'to_date');
chk_empty_args('GET', $args);

// 提交参数整理
$staff_id = get_arg_str('GET', 'staff_id');               // 员工ID
$staff_name = get_arg_str('GET', 'staff_name');           // 员工姓名
$pm_id = get_arg_str('GET', 'pm_id');                     // 权限ID
$pm_name = get_arg_str('GET', 'pm_name');                 // 权限名字
$from_date = get_arg_str('GET', 'from_date');             // 开始时间
$to_date = get_arg_str('GET', 'to_date');                 // 结束时间
$is_void = get_arg_str('GET', 'is_void');                 // 是否无效

// 提交信息整理
$is_void = intval($is_void);

$my_id = $_SESSION['staff_id'];
$my_name = $_SESSION['staff_name'];

// 员工姓名处理
if ($staff_name != '请选择')
  list($staff_cd, $staff_name) = explode(" ", $staff_name);

$data = array();
$data['staff_id'] = $staff_id;                            // 员工ID
$data['staff_name'] = $staff_name;                        // 员工姓名
$data['pm_id'] = $pm_id;                                  // 权限ID
$data['pm_name'] = $pm_name;                              // 权限名字
$data['from_date'] = $from_date;                          // 开始时间
$data['to_date'] = $to_date;                              // 结束时间
$data['is_void'] = $is_void;                              // 是否无效
$data['cid'] = $my_id;                                    // 办理员工ID
$data['cname'] = $my_name;                                // 办理员工姓名

// 取得指定员工ID权限ID的员工权限记录
$row = get_staff_permit($staff_id, $pm_id);
// 记录为空，表示创建员工权限
if (!$row) {
  // 员工权限创建
  $ret = ins_staff_permit($data);
  $msg = '【' . $staff_name . '】的【' . $pm_name . '】权限已成功添加';
  // 创建失败
  if ($ret == '')
    exit_error('110', '员工权限创建失败');
} else {
  // 员工权限更新
  $ret = upd_staff_permit($data, $staff_id, $pm_id);
  $msg = '【' . $staff_name . '】的【' . $pm_name . '】权限已成功更新';
  // 经费信息更新失败
  if (!$ret)
    exit_error('110', '员工权限更新失败');
}

// 输出结果
exit_ok($msg);
?>
