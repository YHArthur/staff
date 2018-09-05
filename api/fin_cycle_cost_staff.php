<?php
require_once "../inc/common.php";
require_once '../db/fin_cycle_cost.php';

header("Access-Control-Allow-Origin: *");
header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 新员工周期支出添加 ==========================
GET参数
  staff_id        员工ID
  staff_name      员工姓名
  from_date       开始时间
  to_date         结束时间
  office_subsidy  办公经费
  base_salary     基本工资
  effic_salary    绩效工资

返回
  添加结果

说明
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('staff_id', 'staff_name', 'from_date', 'to_date', 'office_subsidy', 'base_salary', 'effic_salary');
chk_empty_args('GET', $args);

// 提交参数整理
$staff_id = get_arg_str('GET', 'staff_id');               // 员工ID
$staff_name = get_arg_str('GET', 'staff_name');           // 员工姓名
$from_date = get_arg_str('GET', 'from_date');             // 开始时间
$to_date = get_arg_str('GET', 'to_date');                 // 结束时间
$office_subsidy = get_arg_str('GET', 'office_subsidy');   // 办公经费
$base_salary = get_arg_str('GET', 'base_salary');         // 基本工资
$effic_salary = get_arg_str('GET', 'effic_salary');       // 绩效工资

// 提交信息整理
$office_subsidy = intval($office_subsidy * 100);
$base_salary = intval($base_salary);
$effic_salary = intval($effic_salary * 100);

$my_id = $_SESSION['staff_id'];
$my_name = $_SESSION['staff_name'];

// 员工工号姓名处理
$staff_cd = '000';
if ($staff_name != '请选择员工') {
  list($staff_cd, $staff_name) = explode(" ", $staff_name);
} else {
  $staff_name = '';
}
if ($my_id == $staff_id)
  $staff_name = $my_name;

$social_insurance_rate = 0.4166;         // 社会保险支付费率
$housing_fund_rate = 0.14;               // 公积金支付费率
$per_all_rate = 0.175;                  // 个人支付五险一金缴费费率

// 计算税前总额
$bef_tax_sum = round($base_salary * (1 - $per_all_rate), 2);
// 计算记税总额
$count_tax_sum = $bef_tax_sum - 3500.00;
// 个人所得税计算
$tax_sum = 0;
if ($count_tax_sum <= 0) {
  $tax_sum = 0;
} elseif ($count_tax_sum <= 1500) {
  $tax_sum = $count_tax_sum * 0.03;
} elseif ($count_tax_sum <= 4500) {
  $tax_sum = $count_tax_sum * 0.1 - 105;
} elseif ($count_tax_sum <= 9000) {
  $tax_sum = $count_tax_sum * 0.2 - 555;
} elseif ($count_tax_sum <= 35000) {
  $tax_sum = $count_tax_sum * 0.25 - 1005;
} elseif ($count_tax_sum <= 55000) {
  $tax_sum = $count_tax_sum * 0.3 - 2755;
} elseif ($count_tax_sum <= 80000) {
  $tax_sum = $count_tax_sum * 0.35 - 5505;
} elseif ($count_tax_sum > 80000) {
  $tax_sum = $count_tax_sum * 0.45 - 13505;
}

// 税后工资计算
$aft_tax_sum = $bef_tax_sum - $tax_sum;
// 员工社会保险支付总额
$social_insurance_sum = round($base_salary * $social_insurance_rate * 100);
// 员工公积金支付总额
$housing_fund_sum = round($base_salary * $housing_fund_rate * 100);

$msg = '【' . $staff_name . '】员工的所有周期支出费用条目已成功添加';

$data = array();
// 取得唯一标示符GUID
$data['staff_id'] = $staff_id;                            // 员工ID
$data['staff_cd'] = $staff_cd;                            // 员工工号
$data['staff_name'] = $staff_name;                        // 员工姓名
$data['from_date'] = $from_date;                          // 开始时间
$data['to_date'] = $to_date;                              // 结束时间
$data['month_gap'] = 1;                                   // 间隔月
$data['is_fix'] = 1;                                      // 是否固定
$data['cid'] = $my_id;                                    // 办理员工ID
$data['cname'] = $my_name;                                // 办理员工姓名
$data['is_void'] = 0;                                     // 是否无效

// 税后工资数据设定
if ($aft_tax_sum > 0) {
  $data['cost_id'] = get_guid();                            // 经费ID
  $data['cost_amount'] = $aft_tax_sum * 100;                // 支出金额
  // 应付职工薪酬-应付工资
  $data['sub_id'] = '2211';                                 // 会计科目ID
  $data['cost_memo'] = '税后工资';                          // 支出摘要
  $data['term_day'] = 6;                                    // 支付日

  // 周期支出费用创建
  $ret = ins_fin_cycle_cost($data);
  // 经费信息创建失败
  if ($ret == '')
    exit_error('110', '税后工资的周期支出费用信息创建失败');
}

// 个人所得税数据设定
if ($tax_sum > 0) {
  $data['cost_id'] = get_guid();                            // 经费ID
  $data['cost_amount'] = $tax_sum * 100;                    // 支出金额
  // 应付职工薪酬-辞退福利
  $data['sub_id'] = '2221';                                 // 会计科目ID
  $data['cost_memo'] = '个人所得税';                        // 支出摘要
  $data['term_day'] = 0;                                    // 支付日（不定日）

  // 周期支出费用创建
  $ret = ins_fin_cycle_cost($data);
  // 经费信息创建失败
  if ($ret == '')
    exit_error('110', '办公经费的周期支出费用信息创建失败');
}

// 绩效工资数据设定
if ($effic_salary > 0) {
  $data['cost_id'] = get_guid();                            // 经费ID
  $data['cost_amount'] = $effic_salary;                     // 支出金额
  // 应付职工薪酬-绩效奖金
  $data['sub_id'] = '2211';                                 // 会计科目ID
  $data['cost_memo'] = '绩效奖金';                          // 支出摘要
  $data['term_day'] = 6;                                    // 支付日

  // 周期支出费用创建
  $ret = ins_fin_cycle_cost($data);
  // 经费信息创建失败
  if ($ret == '')
    exit_error('110', '绩效工资的周期支出费用信息创建失败');
}

// 社会保险数据设定
if ($social_insurance_sum > 0) {
  $data['cost_id'] = get_guid();                            // 经费ID
  $data['cost_amount'] = $social_insurance_sum;             // 支出金额
  // 应付职工薪酬-辞退福利
  $data['sub_id'] = '2211';                                 // 会计科目ID
  $data['cost_memo'] = '社会保险';                          // 支出摘要
  $data['term_day'] = 0;                                    // 支付日（不定日）

  // 周期支出费用创建
  $ret = ins_fin_cycle_cost($data);
  // 经费信息创建失败
  if ($ret == '')
    exit_error('110', '社会保险的周期支出费用信息创建失败');
}

// 公积金数据设定
if ($housing_fund_sum > 0) {
  $data['cost_id'] = get_guid();                            // 经费ID
  $data['cost_amount'] = $housing_fund_sum;                 // 支出金额
  // 应付职工薪酬-辞退福利
  $data['sub_id'] = '2211';                                 // 会计科目ID
  $data['cost_memo'] = '住房公积金';                        // 支出摘要
  $data['term_day'] = 0;                                    // 支付日（不定日）

  // 周期支出费用创建
  $ret = ins_fin_cycle_cost($data);
  // 经费信息创建失败
  if ($ret == '')
    exit_error('110', '办公经费的周期支出费用信息创建失败');
}

// 办公经费数据设定
if ($office_subsidy > 0) {
  $data['cost_id'] = get_guid();                            // 经费ID
  $data['cost_amount'] = $office_subsidy;                   // 支出金额
  // 应付职工薪酬-辞退福利
  $data['sub_id'] = '2211';                                 // 会计科目ID
  $data['cost_memo'] = '办公经费';                          // 支出摘要
  $data['term_day'] = 0;                                    // 支付日（不定日）

  // 周期支出费用创建
  $ret = ins_fin_cycle_cost($data);
  // 经费信息创建失败
  if ($ret == '')
    exit_error('110', '办公经费的周期支出费用信息创建失败');
}

// 输出结果
exit_ok($msg);
?>
