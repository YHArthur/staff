<?php
//======================================
// 函数: get_test_arg($arg)
// 功能: 取得自动测试数据
// 参数: $arg           字段名
// 返回: 测试数据
// 说明: config.php AUTO_TEST_FLAG = true 时有效
//======================================
function get_test_arg($arg)
{
  
  $args = array(
    'uuid'=>'93F601C6-562C-CE60-23C3-1084464C3F2D',
    'email'=>'364177653@qq.com'
  );
    
  if (array_key_exists($arg, $args))
    return $args[$arg];
  
  return 'test_' . $arg;
  
}
?>