<?php
require_once "../inc/common.php";
require_once '../db/staff_expense.php';
require_once '../db/staff_expense_log.php';

header("Access-Control-Allow-Origin: *");
header("cache-control:no-cache,must-revalidate");
header("Content-Type:application/json;charset=utf-8");

php_begin(Config::INFO_LEVEL);

// 当前时间戳
$current_time = time();
// 默认间隔30天
$interval = 60*60*24*30;
// 处理记录条数
$counts = 0;

// 取得待处理的所有员工办公经费记录
$rows = get_pending_staff_expense();
foreach($rows as $row) {
  $exp_id = $row['exp_id'];                         // 经费ID
  $staff_id = $row['staff_id'];                     // 员工ID
  $staff_name = $row['staff_name'];                 // 员工姓名
  $exp_amount = $row['exp_amount'];                 // 变动金额
  $from_stamp = strtotime($row['from_date']);       // 开始时间
  $max_count = $row['max_count'];                   // 最大变动次数
  $now_count = $row['now_count'];                   // 当前变动次数
  $exp_memo = $row['exp_memo'];                     // 变动原因
  
  // 待处理时间戳计算
  $pending_stamp = $from_stamp + ($now_count + 1) * $interval;
  
  // 判断该数据是否需要添加
  if ($pending_stamp < $current_time) {
    // 该数据未生成
    if (!chk_staff_expense_log_exist($exp_id, $pending_stamp)) {
      $data = array();
      $data['prvs_hash'] = $staff_id;
      $data['staff_id'] = $staff_id;
      $data['staff_name'] = $staff_name;
      $data['exp_id'] = $exp_id;
      $data['exp_amount'] = $exp_amount;
      $data['exp_balance'] = $exp_amount;
      $data['exp_stamp'] = $pending_stamp;
      $data['exp_memo'] = $exp_memo;
      
      // 取得指定员工最后一次经费变动记录
      $last_log = get_staff_last_expense_log($staff_id);
      // 指定员工有经费变动记录
      if ($last_log) {
        // 上一HASH值
        $data['prvs_hash'] = $last_log['hash_id'];
        // 变动后余额
        $data['exp_balance'] = $last_log['exp_balance'] + $exp_amount;
      }
      // 创建员工办公经费变动记录
      $ret = ins_staff_expense_log($data);
      if (!$ret)
        exit_error('110', '办公经费变动记录创建失败');
      $counts ++;
    }
    // 更新员工办公经费当前变动次数
    $data = array();
    $data['now_count'] = $now_count + 1;
    // 最后一次变动
    if (($now_count + 1) == $max_count) {
      $data['to_date'] = date('Y-m-d H:i:s', $pending_stamp);
      $data['is_void'] = 1;
    }
    // 办公经费更新
    $ret = upd_staff_expense($data, $exp_id);
    if (!$ret)
      exit_error('110', '办公经费更新失败');
    
  }
}


// 输出结果
if ($counts > 0) {
  exit_ok("已添加 {$counts} 条办公经费变动记录");
} else {
  exit_ok("没有办公经费需要更新");
}
?>

