<?php
require_once "../inc/common.php";
require_once '../db/fin_staff_salary.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

/*
========================== 员工工资基数设定 ==========================
GET参数
  staff_id            员工ID
  staff_name          员工姓名
  pre_tax_salary      税前月薪
  base_salary         基本工资
  effic_salary        绩效工资
  pension_base        社保基数
  fund_base           公积金基数
  office_subsidy      办公经费
  from_date           开始时间
  to_date             结束时间
  is_void             是否无效

返回
  设定结果

说明
*/

// 禁止游客访问
exit_guest();

// 参数检查
$args = array('staff_id', 'staff_name', 'from_date', 'to_date');
chk_empty_args('GET', $args);

// 提交参数整理
$staff_id = get_arg_str('GET', 'staff_id');                       // 员工ID
$staff_name = get_arg_str('GET', 'staff_name');                   // 员工姓名
$pre_tax_salary = get_arg_str('GET', 'pre_tax_salary');           // 税前月薪
$base_salary = get_arg_str('GET', 'base_salary');                 // 基本工资
$effic_salary = get_arg_str('GET', 'effic_salary');               // 绩效工资
$pension_base = get_arg_str('GET', 'pension_base');               // 社保基数
$fund_base = get_arg_str('GET', 'fund_base');                     // 公积金基数
$office_subsidy = get_arg_str('GET', 'office_subsidy');           // 办公经费
$from_date = get_arg_str('GET', 'from_date');                     // 开始时间
$to_date = get_arg_str('GET', 'to_date');                         // 结束时间
$is_void = get_arg_str('GET', 'is_void');                         // 是否无效

// 提交信息整理
$pre_tax_salary = intval($pre_tax_salary * 100);
$base_salary = intval($base_salary * 100);
$effic_salary = intval($effic_salary * 100);
$pension_base = intval($pension_base * 100);
$fund_base = intval($fund_base * 100);
$office_subsidy = intval($office_subsidy * 100);
$is_void = intval($is_void);

$my_id = $_SESSION['staff_id'];
$my_name = $_SESSION['staff_name'];

if ($staff_name == '请选择员工')
  exit_error('120', '请选择员工');

if ($my_id == $staff_id)
  exit_error('110', '员工无法修改自己的工资基数');

// 员工工号姓名处理
list($staff_cd, $staff_name) = explode(" ", $staff_name);

$data = array();
$data['staff_id'] = $staff_id;                                    // 员工ID
$data['staff_cd'] = $staff_cd;                                    // 员工工号
$data['staff_name'] = $staff_name;                                // 员工姓名
$data['pre_tax_salary'] = $pre_tax_salary;                        // 税前月薪
$data['base_salary'] = $base_salary;                              // 基本工资
$data['effic_salary'] = $effic_salary;                            // 绩效工资
$data['pension_base'] = $pension_base;                            // 社保基数
$data['fund_base'] = $fund_base;                                  // 公积金基数
$data['office_subsidy'] = $office_subsidy;                        // 办公经费
$data['from_date'] = $from_date;                                  // 开始时间
$data['to_date'] = $to_date;                                      // 结束时间
$data['is_void'] = $is_void;                                      // 是否无效
$data['cid'] = $my_id;                                            // 办理员工ID
$data['cname'] = $my_name;                                        // 办理员工姓名

// 取得指定员工ID的员工工资基数
$row = get_fin_staff_salary($staff_id);
// 取得记录为空，表示创建员工工资基数
if (empty($row)) {

  // 员工工资基数创建
  $ret = ins_fin_staff_salary($data);
  $msg = '【' . $staff_name . '】的工资基数已成功添加';
  // 创建失败
  if ($ret == '')
    exit_error('110', '员工工资基数信息创建失败');

} else {
  // 员工工资基数更新
  $ret = upd_fin_staff_salary($data, $staff_id);
  $msg = '【' . $staff_name . '】的工资基数已成功更新';
  // 更新失败
  if (!$ret)
    exit_error('110', '员工工资基数更新失败');

}

// 输出结果
exit_ok($msg);
?>
