<?php
require_once "../inc/common.php";
require_once '../db/staff_expense.php';

header("Access-Control-Allow-Origin: *");
header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 经费设定 ==========================
GET参数
  exp_id          经费ID
  staff_id        员工ID
  staff_name      员工姓名
  exp_amount      变动金额
  from_date       开始时间
  to_date         结束时间
  max_count       最大变动次数
  now_count       当前变动次数
  exp_memo        变动原因
  is_void         是否无效

返回
  设定结果

说明
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('staff_id', 'staff_name', 'exp_amount', 'from_date', 'to_date', 'max_count', 'exp_memo');
chk_empty_args('GET', $args);

// 提交参数整理
$exp_id = get_arg_str('GET', 'exp_id');                   // 经费ID
$staff_id = get_arg_str('GET', 'staff_id');               // 员工ID
$staff_name = get_arg_str('GET', 'staff_name');           // 员工姓名
$exp_amount = get_arg_str('GET', 'exp_amount');           // 变动金额
$from_date = get_arg_str('GET', 'from_date');             // 开始时间
$to_date = get_arg_str('GET', 'to_date');                 // 结束时间
$max_count = get_arg_str('GET', 'max_count');             // 最大变动次数
$now_count = get_arg_str('GET', 'now_count');             // 当前变动次数
$exp_memo = get_arg_str('GET', 'exp_memo', 255);          // 变动原因
$is_void = get_arg_str('GET', 'is_void');                 // 是否无效

// 提交信息整理
$exp_amount = intval($exp_amount);
$max_count = intval($max_count);
$now_count = intval($now_count);
$is_void = intval($is_void);

$my_id = $_SESSION['staff_id'];
$my_name = $_SESSION['staff_name'];

// 员工姓名处理
if ($staff_name == '请选择员工') {
  $staff_name = '';
} else if ($my_id == $staff_id) {
  $staff_name = $my_name;
} else {
  list($staff_cd, $staff_name) = explode(" ", $staff_name);
}

$data = array();
$data['exp_id'] = $exp_id;                                // 经费ID
$data['staff_id'] = $staff_id;                            // 员工ID
$data['staff_name'] = $staff_name;                        // 员工姓名
$data['exp_amount'] = $exp_amount;                        // 变动金额
$data['from_date'] = $from_date;                          // 开始时间
$data['to_date'] = $to_date;                              // 结束时间
$data['max_count'] = $max_count;                          // 最大变动次数
$data['now_count'] = $now_count;                          // 当前变动次数
$data['exp_memo'] = $exp_memo;                            // 变动原因
$data['is_void'] = $is_void;                              // 是否无效
$data['cid'] = $my_id;                                    // 办理员工ID
$data['cname'] = $my_name;                                // 办理员工姓名

  
// 经费ID为空，表示创建经费
if ($exp_id == '') {
  // 取得唯一标示符GUID
  $data['exp_id'] = get_guid();                           // 经费ID

  // 经费创建
  $ret = ins_staff_expense($data);
  $msg = '【' . $staff_name . '】的经费条目已成功添加';
  // 经费信息创建失败
  if ($ret == '')
    exit_error('110', '经费条目信息创建失败');
} else {
  // 经费更新
  $ret = upd_staff_expense($data, $exp_id);
  $msg = '【' . $staff_name . '】的经费条目已成功更新';
  // 经费信息更新失败
  if (!$ret)
    exit_error('110', '经费信息更新失败');
}

// 输出结果
exit_ok($msg);
?>
