<?php
require_once "../inc/common.php";
require_once '../db/fin_staff_salary.php';
require_once '../db/fin_staff_salary_log.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 设定员工工资 ==========================
POST参数
  ym              工资年月
  dt              支付工资日期
  id              员工ID
  pts             税前工资
  es              绩效奖金

返回
  设定结果

说明
*/

// 禁止游客访问
api_exit_guest();

// 参数检查
$args = array('ym', 'dt', 'id');
chk_empty_args('POST', $args);

// 提交参数整理
$salary_ym = get_arg_str('POST', 'ym');                   // 工资年月
$salary_date = get_arg_str('POST', 'dt');                 // 支付工资日期
$staff_id = get_arg_str('POST', 'id');                    // 员工ID
$pre_tax_salary = get_arg_str('POST', 'pts');             // 税前工资
$effic_salary = get_arg_str('POST', 'es');                // 绩效奖金

$pre_tax_salary = intval($pre_tax_salary * 100);
$effic_salary = intval($effic_salary * 100);

// 取得指定员工ID和有效年月的员工工资基数
$salary =  get_fin_staff_salary_valid($staff_id, $salary_ym);
$staff_cd = $salary['staff_cd'];
$staff_name = $salary['staff_name'];
// $pre_tax_salary = $salary['pre_tax_salary'];
// $effic_salary = $salary['effic_salary'];
$base_salary = $salary['base_salary'];
$pension_base = $salary['pension_base'];
$fund_base = $salary['fund_base'];
$office_subsidy = $salary['office_subsidy'];

$my_id = $_SESSION['staff_id'];                           // 当前用户ID
$my_name = $_SESSION['staff_name'];                       // 当前用户昵称

$data = array();
$data['salary_ym'] = $salary_ym;                          // 工资年月
$data['staff_id'] = $staff_id;                            // 员工ID
$data['staff_cd'] = $staff_cd;                            // 员工工号
$data['staff_name'] = $staff_name;                        // 员工姓名
$data['pre_tax_salary'] = $pre_tax_salary;                // 税前工资
$data['base_salary'] = $base_salary;                      // 基本工资
$data['effic_salary'] = $effic_salary;                    // 绩效工资
$data['pension_base'] = $pension_base;                    // 社保基数
$data['fund_base'] = $fund_base;                          // 公积金基数
$data['office_subsidy'] = $office_subsidy;                // 办公经费

$pension_fee = $pension_base * 0.08;                      // 个人养老保险比例
$data['pension_fee'] = $pension_fee;                      // 养老保险

$medical_fee = $pension_base * 0.02;                      // 个人医疗保险比例
$data['medical_fee'] = $medical_fee;                      // 医疗保险

$jobless_fee = $pension_base * 0.005;                     // 个人失业保险比例
$data['jobless_fee'] = $jobless_fee;                      // 失业保险

$fund_fee = $fund_base * 0.07;                            // 个人公积金比例
$data['fund_fee'] = $fund_fee;                            // 住房公积金

// 税前总额（出现考情扣减情况）
if ($pre_tax_salary >= $base_salary) {
  $bef_tax_sum = $base_salary - $pension_fee - $medical_fee - $jobless_fee - $fund_fee;
} else {
  $bef_tax_sum = $pre_tax_salary - $pension_fee - $medical_fee - $jobless_fee - $fund_fee;
}
$data['bef_tax_sum'] = $bef_tax_sum;

// 个人所得税计算
$tax_sum = 0;

if ($salary_date >= '2018-10-01') {
  // 计算记税总额
  $count_tax_sum = $bef_tax_sum - 500000;
  if ($count_tax_sum <= 0) {
    $tax_sum = 0;
  } elseif ($count_tax_sum <= 300000) {
    $tax_sum = $count_tax_sum * 0.03;
  } elseif ($count_tax_sum <= 1200000) {
    $tax_sum = $count_tax_sum * 0.1 - 21000;
  } elseif ($count_tax_sum <= 2500000) {
    $tax_sum = $count_tax_sum * 0.2 - 141000;
  } elseif ($count_tax_sum <= 3500000) {
    $tax_sum = $count_tax_sum * 0.25 - 266000;
  } elseif ($count_tax_sum <= 5500000) {
    $tax_sum = $count_tax_sum * 0.3 - 441000;
  } elseif ($count_tax_sum <= 8000000) {
    $tax_sum = $count_tax_sum * 0.35 - 716000;
  } elseif ($count_tax_sum > 8000000) {
    $tax_sum = $count_tax_sum * 0.45 - 1516000;
  }
} else {
  // 计算记税总额
  $count_tax_sum = $bef_tax_sum - 350000;
  if ($count_tax_sum <= 0) {
    $tax_sum = 0;
  } elseif ($count_tax_sum <= 150000) {
    $tax_sum = $count_tax_sum * 0.03;
  } elseif ($count_tax_sum <= 450000) {
    $tax_sum = $count_tax_sum * 0.1 - 10500;
  } elseif ($count_tax_sum <= 900000) {
    $tax_sum = $count_tax_sum * 0.2 - 55500;
  } elseif ($count_tax_sum <= 3500000) {
    $tax_sum = $count_tax_sum * 0.25 - 100500;
  } elseif ($count_tax_sum <= 5500000) {
    $tax_sum = $count_tax_sum * 0.3 - 275500;
  } elseif ($count_tax_sum <= 8000000) {
    $tax_sum = $count_tax_sum * 0.35 - 550500;
  } elseif ($count_tax_sum > 8000000) {
    $tax_sum = $count_tax_sum * 0.45 - 1350500;
  }
}

$data['tax_sum'] = $tax_sum;                              // 个人所得税

$aft_tax_sum = $bef_tax_sum - $tax_sum;
$data['aft_tax_sum'] = $aft_tax_sum;                      // 税后工资

$data['salary_date'] = $salary_date;                      // 支付工资日期
$data['cid'] = $my_id;                                    // 创建员工ID
$data['cname'] = $my_name;                                // 创建员工姓名

// 员工工资发放记录存在检查
$ret = chk_fin_staff_salary_log_exist($salary_ym, $staff_id);
if (!$ret) {
  // 员工工资发放记录创建
  $ret = ins_fin_staff_salary_log($data);
  $msg = '【' . $staff_name . '】' . $salary_ym . '的工资发放记录已创建';
  // 任务信息创建失败
  if ($ret == '')
    exit_error('110', '工资发放记录创建失败');
} else {
  // 员工工资发放记录更新
  $ret = upd_fin_staff_salary_log($data, $salary_ym, $staff_id);
  $msg = '【' . $staff_name . '】' . $salary_ym . '的工资发放记录已更新';
  // 任务信息创建失败
  if ($ret == '')
    exit_error('110', '工资发放记录更新失败');
}

// 输出结果
exit_ok($msg);
?>
