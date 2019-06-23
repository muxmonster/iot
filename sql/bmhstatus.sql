/*
Navicat MySQL Data Transfer

Source Server         : VM-01
Source Server Version : 50561
Source Host           : 192.168.145.128:3306
Source Database       : bmhstatus

Target Server Type    : MYSQL
Target Server Version : 50561
File Encoding         : 65001

Date: 2018-10-04 17:00:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for notify_status
-- ----------------------------
DROP TABLE IF EXISTS `notify_status`;
CREATE TABLE `notify_status` (
  `noti_id` int(11) NOT NULL AUTO_INCREMENT,
  `noti_date_time` datetime DEFAULT NULL,
  `h_time` varchar(2) DEFAULT NULL,
  `notify` char(1) DEFAULT NULL,
  PRIMARY KEY (`noti_id`)
) ENGINE=MyISAM AUTO_INCREMENT=264 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for server_room
-- ----------------------------
DROP TABLE IF EXISTS `server_room`;
CREATE TABLE `server_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `temp_date_time` datetime DEFAULT NULL,
  `temp` int(11) DEFAULT NULL,
  `humidity` int(11) DEFAULT NULL,
  `temp_station` char(1) DEFAULT NULL,
  `smoke_value` int(11) DEFAULT NULL,
  `sm_station` char(1) DEFAULT NULL,
  `node_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2935919 DEFAULT CHARSET=tis620;
