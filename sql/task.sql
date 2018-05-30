/*
Navicat MySQL Data Transfer

Source Server         : www.fnying.com
Source Server Version : 50173
Source Host           : 
Source Database       : 

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2018-05-30 15:37:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for task
-- ----------------------------
DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `task_id` char(36) CHARACTER SET ascii NOT NULL COMMENT '任务ID',
  `task_name` varchar(50) NOT NULL COMMENT '任务',
  `task_intro` text NOT NULL COMMENT '任务内容',
  `owner_id` char(36) CHARACTER SET ascii NOT NULL COMMENT '创建人ID',
  `owner_name` varchar(50) NOT NULL COMMENT '创建人',
  `respo_id` char(36) CHARACTER SET ascii DEFAULT NULL COMMENT '责任人ID',
  `respo_name` varchar(50) DEFAULT NULL COMMENT '责任人',
  `check_id` char(36) CHARACTER SET ascii DEFAULT NULL COMMENT '监督人ID',
  `check_name` varchar(50) DEFAULT NULL COMMENT '监督人',
  `public_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '公开等级',
  `task_level` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '任务等级',
  `task_value` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '任务价值',
  `task_perc` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '任务进度',
  `task_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '任务状态',
  `limit_time` datetime NOT NULL COMMENT '任务期限',
  `prvs_task_id` char(36) CHARACTER SET ascii DEFAULT NULL COMMENT '上一任务ID',
  `is_void` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否无效',
  `utime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `ctime` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`task_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='任务一览';
