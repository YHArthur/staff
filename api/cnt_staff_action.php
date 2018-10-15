<?php
require_once '../inc/common.php';
require_once '../db/cnt_staff_action.php';

header("Access-Control-Allow-Origin:*");
header("cache-control:no-cache,must-revalidate");

/*
========================== 网页访问统计 ==========================
GET参数
  referrer       来源URL
  url            访问URL
  uuid           访问用户id

返回
  不返回任何参数,仅在数据库记录

说明
  获取到的访问url和来源url进行参数分割，然后统计到数据库表中，访问ip函数获取。
*/

// 参数检查
$args = array('url');
chk_empty_args('GET', $args);

// 提交参数整理
$referrer = get_arg_str('GET','referrer', 512);       // 来源URL
$url = get_arg_str('GET','url', 512);                 // 访问URL
$uuid = get_arg_str('GET' , 'uuid');                  // 访问ID

if (!session_id())
  session_start();

if (!isset($_SESSION['staff_id'])) {
  $staff_id = $uuid;
  $staff_name = '游客';
} else {
  $staff_id = $_SESSION['staff_id'];
  $staff_name = $_SESSION['staff_name'];
}

// 解析网址
$referrer_parse = parse_url($referrer);
$url_parse = parse_url($url);

// 来源URL
$from_url = isset($referrer_parse['host']) ? $referrer_parse['host'] : '';
$from_url .= isset($referrer_parse['path']) ? $referrer_parse['path'] : '';
// 来源URL参数
$from_prm = isset($referrer_parse['query']) ? $referrer_parse['query'] : '';
// 访问URL
$action_url = isset($url_parse['host']) ? $url_parse['host'] : '';
$action_url .= isset($url_parse['path']) ? $url_parse['path'] : '';
// 访问URL参数
$action_prm = isset($url_parse['query']) ? $url_parse['query'] : '';
// 访问IP
$action_ip = get_int_ip();

// 本地文件判断
if (substr($action_url, 1, 1) == ':' || substr($action_url, 0, 5) == 'local' || substr($action_url, 0, 3) == '127')
  exit();

// 字段设定
$data = array();
$data['from_url'] = $from_url;
$data['from_prm'] = $from_prm;
$data['staff_id'] = $staff_id;
$data['staff_name'] = $staff_name;
$data['action_url'] = $action_url;
$data['action_prm'] = $action_prm;
$data['action_ip'] = $action_ip;

// 创建员工内部访问记录
$ret = ins_cnt_staff_action($data);
// 返回
exit();
?>
