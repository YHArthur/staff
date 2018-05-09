/*
Navicat MySQL Data Transfer

Source Server         : www.fnying.com
Source Server Version : 50173
Source Host           : 
Source Database       : 

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2018-05-09 19:02:52
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for obj
-- ----------------------------
DROP TABLE IF EXISTS `obj`;
CREATE TABLE `obj` (
  `obj_id` char(36) CHARACTER SET ascii NOT NULL COMMENT '目标ID',
  `obj_name` varchar(50) NOT NULL COMMENT '目标',
  `obj_intro` varchar(255) NOT NULL COMMENT '目标内容',
  `owner_id` char(36) CHARACTER SET ascii NOT NULL COMMENT '创建人ID',
  `owner_name` varchar(50) NOT NULL COMMENT '创建人',
  `check_id` char(36) CHARACTER SET ascii NOT NULL COMMENT '监督人ID',
  `check_name` varchar(50) NOT NULL COMMENT '监督人',
  `is_public` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否公开',
  `obj_level` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '目标等级',
  `obj_value` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '衡量指标',
  `obj_perc` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '目标进度',
  `obj_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '目标状态',
  `limit_time` datetime NOT NULL COMMENT '目标期限',
  `prvs_obj_id` char(36) CHARACTER SET ascii DEFAULT NULL COMMENT '上一目标ID',
  `is_void` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否无效',
  `utime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `ctime` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`obj_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='目标一览';
