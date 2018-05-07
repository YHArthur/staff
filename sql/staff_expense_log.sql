/*
Navicat MySQL Data Transfer

Source Server         : www.fnying.com
Source Server Version : 50173
Source Host           : 
Source Database       : 

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2018-05-02 16:35:53
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for staff_expense_log
-- ----------------------------
DROP TABLE IF EXISTS `staff_expense_log`;
CREATE TABLE `staff_expense_log` (
  `hash_id` char(64) CHARACTER SET ascii NOT NULL COMMENT 'HASH值',
  `prvs_hash` char(64) CHARACTER SET ascii NOT NULL DEFAULT '' COMMENT '上一HASH值',
  `staff_id` char(36) CHARACTER SET ascii NOT NULL COMMENT '员工ID',
  `staff_name` varchar(50) NOT NULL COMMENT '员工姓名',
  `exp_id` char(36) CHARACTER SET ascii NOT NULL COMMENT '变动ID',
  `exp_amount` int(11) NOT NULL DEFAULT '0' COMMENT '变动金额',
  `exp_balance` int(11) NOT NULL DEFAULT '0' COMMENT '变动后余额',
  `exp_stamp` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '变动时间',
  `exp_memo` varchar(255) DEFAULT NULL COMMENT '变动原因',
  PRIMARY KEY (`hash_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='员工办公经费变动记录';
