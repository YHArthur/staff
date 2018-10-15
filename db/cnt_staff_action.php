<?php
//======================================
// 函数: 创建员工内部访问记录
// 参数: $data          信息数组
// 返回: id             新的记录ID
// 返回: 0              创建失败
//======================================
function ins_cnt_staff_action($data)
{
  // 提交时间
  $data['action_time'] = time();

  $db = new DB_SATFF();

  $sql = $db->sqlInsert("cnt_staff_action", $data);
  $q_id = $db->query($sql);

  if ($q_id == 0)
    return 0;
  return $db->insertID();
}
?>
