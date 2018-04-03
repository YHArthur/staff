/*
Navicat MySQL Data Transfer

Source Server         : www.fnying.com
Source Server Version : 50173
Source Host           : 
Source Database       : 

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2018-03-30 10:06:24
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for staff_main
-- ----------------------------
DROP TABLE IF EXISTS `staff_main`;
CREATE TABLE `staff_main` (
  `staff_id` char(36) CHARACTER SET ascii NOT NULL COMMENT '员工ID',
  `staff_cd` char(3) CHARACTER SET ascii NOT NULL DEFAULT '000' COMMENT '员工工号',
  `staff_name` varchar(50) NOT NULL COMMENT '员工姓名',
  `staff_sex` tinyint(1) DEFAULT '0' COMMENT '员工性别',
  `identity` varchar(50) CHARACTER SET ascii DEFAULT '' COMMENT '身份证件号',
  `birth_year` int(4) DEFAULT '0' COMMENT '出生年份',
  `birth_day` char(5) CHARACTER SET ascii DEFAULT '' COMMENT '生日',
  `join_date` datetime DEFAULT NULL COMMENT '加入时间',
  `utime` int(11) DEFAULT '0' COMMENT '更新时间',
  `ctime` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`staff_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='员工主表';
