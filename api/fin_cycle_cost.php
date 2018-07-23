<?php
require_once "../inc/common.php";
require_once '../db/fin_cycle_cost.php';

header("Access-Control-Allow-Origin: *");
header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 周期支出费用设定 ==========================
GET参数
  cost_id         周期支出费用ID
  staff_id        员工ID
  staff_name      员工姓名
  cost_amount     支出金额
  from_date       开始时间
  to_date         结束时间
  sub_id          会计科目ID
  cost_memo       支出摘要
  month_gap       间隔月
  term_day        支付日
  is_fix          是否固定
  is_void         是否无效

返回
  设定结果

说明
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('staff_id', 'staff_name', 'cost_amount', 'from_date', 'to_date', 'cost_memo', 'month_gap');
chk_empty_args('GET', $args);

// 提交参数整理
$cost_id = get_arg_str('GET', 'cost_id');                 // 周期支出费用ID
$staff_id = get_arg_str('GET', 'staff_id');               // 员工ID
$staff_name = get_arg_str('GET', 'staff_name');           // 员工姓名
$cost_amount = get_arg_str('GET', 'cost_amount');         // 支出金额
$from_date = get_arg_str('GET', 'from_date');             // 开始时间
$to_date = get_arg_str('GET', 'to_date');                 // 结束时间
$sub_id = get_arg_str('GET', 'sub_id');                   // 会计科目ID
$cost_memo = get_arg_str('GET', 'cost_memo', 255);        // 支出摘要
$month_gap = get_arg_str('GET', 'month_gap');             // 间隔月
$term_day = get_arg_str('GET', 'term_day');               // 支付日
$is_fix = get_arg_str('GET', 'is_fix');                   // 是否固定
$is_void = get_arg_str('GET', 'is_void');                 // 是否无效

// 提交信息整理
$cost_amount = intval($cost_amount * 100);
$month_gap = intval($month_gap);
$term_day = intval($term_day);
$is_fix = intval($is_fix);
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
$data['cost_id'] = $cost_id;                              // 周期支出费用ID
$data['staff_id'] = $staff_id;                            // 员工ID
$data['staff_cd'] = $staff_cd;                            // 员工工号
$data['staff_name'] = $staff_name;                        // 员工姓名
$data['cost_amount'] = $cost_amount;                      // 支出金额
$data['from_date'] = $from_date;                          // 开始时间
$data['to_date'] = $to_date;                              // 结束时间
$data['sub_id'] = $sub_id;                                // 会计科目ID
$data['cost_memo'] = $cost_memo;                          // 支出摘要
$data['month_gap'] = $month_gap;                          // 间隔月
$data['term_day'] = $term_day;                            // 支付日
$data['is_fix'] = $is_fix;                                // 是否固定
$data['is_void'] = $is_void;                              // 是否无效
$data['cid'] = $my_id;                                    // 办理员工ID
$data['cname'] = $my_name;                                // 办理员工姓名

  
// 周期支出费用ID为空，表示创建周期支出费用
if ($cost_id == '') {
  // 取得唯一标示符GUID
  $data['cost_id'] = get_guid();                           // 周期支出费用ID

  // 周期支出费用创建
  $ret = ins_fin_cycle_cost($data);
  $msg = '【' . $staff_name . '】的周期支出费用条目已成功添加';
  // 周期支出费用信息创建失败
  if ($ret == '')
    exit_error('110', '周期支出费用条目信息创建失败');
} else {
  // 周期支出费用更新
  $ret = upd_fin_cycle_cost($data, $cost_id);
  $msg = '【' . $staff_name . '】的周期支出费用条目已成功更新';
  // 周期支出费用信息更新失败
  if (!$ret)
    exit_error('110', '周期支出费用信息更新失败');
}

// 输出结果
exit_ok($msg);
?>
