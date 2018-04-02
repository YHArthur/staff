/*
Navicat MySQL Data Transfer

Source Server         : www.fnying.com
Source Server Version : 50173
Source Host           : bdm25986977.my3w.com:3306
Source Database       : bdm25986977_db

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2018-04-02 17:17:06
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for staff_permit
-- ----------------------------
DROP TABLE IF EXISTS `staff_permit`;
CREATE TABLE `staff_permit` (
  `staff_id` char(36) CHARACTER SET ascii NOT NULL COMMENT '员工ID',
  `staff_name` varchar(50) NOT NULL COMMENT '员工姓名',
  `pm_id` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '权限ID',
  `pm_name` varchar(10) CHARACTER SET ascii NOT NULL DEFAULT '0' COMMENT '权限名字',
  `is_void` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否无效',
  `utime` int(11) DEFAULT '0' COMMENT '更新时间戳',
  `ctime` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`staff_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='员工权限表';
