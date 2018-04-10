/*
Navicat MySQL Data Transfer

Source Server         : www.fnying.com
Source Server Version : 50173
Source Host           : 
Source Database       : 

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2018-04-10 20:24:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for staff_weixin
-- ----------------------------
DROP TABLE IF EXISTS `staff_weixin`;
CREATE TABLE `staff_weixin` (
  `unionid` varchar(50) CHARACTER SET ascii NOT NULL COMMENT '微信统一标识',
  `staff_id` char(36) CHARACTER SET ascii NOT NULL COMMENT '员工ID',
  `staff_name` varchar(50) NOT NULL COMMENT '员工姓名',
  `staff_phone` varchar(20) NOT NULL COMMENT '员工电话',
  `staff_avata` varchar(255) CHARACTER SET ascii DEFAULT '' COMMENT '员工头像',
  `wx_name` varchar(50) NOT NULL COMMENT '微信昵称',
  `is_void` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否无效',
  `last_ip` int(11) DEFAULT NULL COMMENT '登录IP',
  `utime` int(11) DEFAULT '0' COMMENT '更新时间戳',
  `ctime` datetime DEFAULT NULL COMMENT '申请时间',
  PRIMARY KEY (`unionid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='员工微信账号';
