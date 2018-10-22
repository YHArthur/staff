<?php
require_once "../inc/common.php";
require_once '../db/fin_staff_salary.php';
require_once '../db/fin_staff_salary_log.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 修改员工工资 ==========================
POST参数
  staff_id        员工ID
  salary_ym       工资年月
  salary_date     支付工资日期
  pre_tax_salary  税前工资
  office_subsidy  办公经费
  base_salary     基本工资
  effic_salary    绩效工资
  pension_base    社保基数
  fund_base       公积金基数
  pension_fee     养老保险
  medical_fee     医疗保险
  jobless_fee     失业保险
  fund_fee        住房公积金
  bef_tax_sum     税前总额
  tax_sum         个人所得税

返回
  设定结果

说明
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('staff_id', 'salary_ym', 'salary_date', "pre_tax_salary");
chk_empty_args('POST', $args);

// 提交参数整理
$staff_id = get_arg_str('POST', 'staff_id');              // 员工ID
$salary_ym = get_arg_str('POST', 'salary_ym');            // 工资年月
$salary_date = get_arg_str('POST', 'salary_date');        // 支付工资日期
$pre_tax_salary = get_arg_str('POST', 'pre_tax_salary');  // 税前工资
$office_subsidy = get_arg_str('POST', 'office_subsidy');  // 办公经费
$base_salary = get_arg_str('POST', 'base_salary');        // 基本工资
$effic_salary = get_arg_str('POST', 'effic_salary');      // 绩效工资
$pension_base = get_arg_str('POST', 'pension_base');      // 社保基数
$fund_base = get_arg_str('POST', 'fund_base');            // 公积金基数
$pension_fee = get_arg_str('POST', 'pension_fee');        // 养老保险
$medical_fee = get_arg_str('POST', 'medical_fee');        // 医疗保险
$jobless_fee = get_arg_str('POST', 'jobless_fee');        // 失业保险
$fund_fee = get_arg_str('POST', 'fund_fee');              // 住房公积金
$bef_tax_sum = get_arg_str('POST', 'bef_tax_sum');        // 税前总额
$tax_sum = get_arg_str('POST', 'tax_sum');                // 个人所得税

$pre_tax_salary = intval($pre_tax_salary * 100);
$office_subsidy = intval($office_subsidy * 100);
$base_salary = intval($base_salary * 100);
$effic_salary = intval($effic_salary * 100);
$pension_base = intval($pension_base * 100);
$fund_base = intval($fund_base * 100);
$pension_fee = intval($pension_fee * 100);
$medical_fee = intval($medical_fee * 100);
$jobless_fee = intval($jobless_fee * 100);
$fund_fee = intval($fund_fee * 100);
$bef_tax_sum = intval($bef_tax_sum * 100);
$tax_sum = intval($tax_sum * 100);

$my_id = $_SESSION['staff_id'];                           // 当前用户ID
$my_name = $_SESSION['staff_name'];                       // 当前用户昵称

// 员工工资发放记录存在检查
$ret = chk_fin_staff_salary_log_exist($salary_ym, $staff_id);
if (!$ret)
  exit_error('120', '工资发放记录不存在');
  
$data = array();
$data['salary_ym'] = $salary_ym;                          // 工资年月
$data['staff_id'] = $staff_id;                            // 员工ID
$data['pre_tax_salary'] = $pre_tax_salary;                // 税前工资
$data['office_subsidy'] = $office_subsidy;                // 办公经费
$data['base_salary'] = $base_salary;                      // 基本工资
$data['effic_salary'] = $effic_salary;                    // 绩效工资
$data['pension_base'] = $pension_base;                    // 社保基数
$data['fund_base'] = $fund_base;                          // 公积金基数
$data['pension_fee'] = $pension_fee;                      // 养老保险
$data['medical_fee'] = $medical_fee;                      // 医疗保险
$data['jobless_fee'] = $jobless_fee;                      // 失业保险
$data['fund_fee'] = $fund_fee;                            // 住房公积金
$data['bef_tax_sum'] = $bef_tax_sum;                      // 税前总额
$data['tax_sum'] = $tax_sum;                              // 个人所得税

$aft_tax_sum = $bef_tax_sum - $tax_sum;
$data['aft_tax_sum'] = $aft_tax_sum;                      // 税后工资
$data['salary_date'] = $salary_date;                      // 支付工资日期
$data['cid'] = $my_id;                                    // 创建员工ID
$data['cname'] = $my_name;                                // 创建员工姓名

// 员工工资发放记录更新
$ret = upd_fin_staff_salary_log($data, $salary_ym, $staff_id);
$msg = '【' . $staff_name . '】' . $salary_ym . '的工资发放记录已更新';
// 任务信息创建失败
if ($ret == '')
  exit_error('110', '工资发放记录更新失败');

// 输出结果
exit_ok($msg);
?>
