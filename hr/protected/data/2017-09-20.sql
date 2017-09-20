/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : hrdev

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2017-09-20 15:28:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hr_company
-- ----------------------------
DROP TABLE IF EXISTS `hr_company`;
CREATE TABLE `hr_company` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '公司名字',
  `agent` varchar(30) NOT NULL COMMENT '代理人',
  `head` varchar(30) NOT NULL COMMENT '負責人',
  `address` varchar(255) NOT NULL COMMENT '公司地址',
  `city` varchar(30) NOT NULL COMMENT '公司歸屬地區',
  `phone` varchar(255) DEFAULT NULL COMMENT '公司電話',
  `tacitly` varchar(11) DEFAULT '0' COMMENT '默認公司：0（否）1（是）',
  `organization_code` varchar(30) DEFAULT NULL COMMENT '組織機構代碼',
  `organization_time` date DEFAULT NULL COMMENT '組織機構代碼發出時間',
  `security_code` varchar(30) DEFAULT NULL COMMENT '勞動保障代碼',
  `license_code` varchar(30) DEFAULT NULL COMMENT '證照編號',
  `license_time` date DEFAULT NULL COMMENT '證照發出日期',
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='公司資料表';

-- ----------------------------
-- Table structure for hr_dept
-- ----------------------------
DROP TABLE IF EXISTS `hr_dept`;
CREATE TABLE `hr_dept` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `z_index` varchar(11) DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '0:部門  1:職位',
  `city` varchar(255) DEFAULT NULL,
  `dept_id` int(11) DEFAULT '1' COMMENT '部門id',
  `lcu` varchar(50) DEFAULT NULL,
  `luu` varchar(50) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='工作部門';
