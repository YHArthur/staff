/*
Navicat MySQL Data Transfer

Source Server         : www.fnying.com
Source Server Version : 50173
Source Host           : 
Source Database       : 

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2018-05-02 16:28:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for staff_expense
-- ----------------------------
DROP TABLE IF EXISTS `staff_expense`;
CREATE TABLE `staff_expense` (
  `exp_id` char(36) CHARACTER SET ascii NOT NULL COMMENT '经费ID',
  `staff_id` char(36) CHARACTER SET ascii NOT NULL COMMENT '员工ID',
  `staff_name` varchar(50) NOT NULL COMMENT '员工姓名',
  `exp_amount` int(11) NOT NULL DEFAULT '0' COMMENT '变动金额',
  `from_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间',
  `to_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间',
  `max_count` tinyint(4) DEFAULT '0' COMMENT '最大变动次数',
  `now_count` tinyint(4) DEFAULT '0' COMMENT '当前变动次数',
  `exp_memo` varchar(255) DEFAULT NULL COMMENT '变动原因',
  `is_void` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否无效',
  `cid` char(36) CHARACTER SET ascii NOT NULL COMMENT '办理员工ID',
  `cname` varchar(50) NOT NULL COMMENT '办理员工姓名',
  `utime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `ctime` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`exp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='员工办公经费';
