/*
Navicat MySQL Data Transfer

Source Server         : www.fnying.com
Source Server Version : 50173
Source Host           : 
Source Database       : 

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2018-04-02 13:53:32
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for staff_office_sign
-- ----------------------------
DROP TABLE IF EXISTS `staff_office_sign`;
CREATE TABLE `staff_office_sign` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `staff_id` char(36) CHARACTER SET ascii NOT NULL DEFAULT '' COMMENT '员工ID',
  `staff_name` varchar(50) NOT NULL DEFAULT '' COMMENT '员工姓名',
  `sign_type` varchar(50) NOT NULL DEFAULT '' COMMENT '签到类型',
  `sign_location` varchar(512) CHARACTER SET ascii DEFAULT '' COMMENT '签到地理位置',
  `user_ip` int(11) DEFAULT '0' COMMENT '用户IP地址',
  `utime` int(11) DEFAULT '0' COMMENT '更新时间',
  `ctime` datetime DEFAULT NULL COMMENT '签到时间',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='员工办公室签到记录表';
