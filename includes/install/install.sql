-- phpMyAdmin SQL Dump
-- version 3.5.7
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2013 年 03 月 20 日 03:16
-- 服务器版本: 5.1.44
-- PHP 版本: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- 数据库: `oasys`
--

-- --------------------------------------------------------

--
-- 表的结构 `core_ip`
--

CREATE TABLE IF NOT EXISTS `core_ip` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip_addr` varchar(39) COLLATE utf8_bin NOT NULL COMMENT 'IP地址',
  `ip_ban` tinyint(1) NOT NULL COMMENT 'IP封锁状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `ip_addr` (`ip_addr`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `core_ip`
--

INSERT INTO `core_ip` (`id`, `ip_addr`, `ip_ban`) VALUES
(1, '::1', 0),
(2, '127.0.0.1', 0);

-- --------------------------------------------------------

--
-- 表的结构 `core_log`
--

CREATE TABLE IF NOT EXISTS `core_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `log_date` datetime NOT NULL COMMENT '创建时间',
  `log_ip` bigint(20) unsigned NOT NULL COMMENT '宿主IP ID',
  `log_message` text COLLATE utf8_bin NOT NULL COMMENT '描述消息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `log_ip` (`log_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `oa_configs`
--

CREATE TABLE IF NOT EXISTS `oa_configs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_name` varchar(300) COLLATE utf8_bin NOT NULL,
  `config_value` text COLLATE utf8_bin NOT NULL,
  `config_default` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `config_name` (`config_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `oa_configs`
--

INSERT INTO `oa_configs` (`id`, `config_name`, `config_value`, `config_default`) VALUES
(1, 'WEB_TITLE', 'OA办公系统', 'OA办公系统'),
(2, 'USER_TIMEOUT', '900', '900'),
(3, 'UPLOADFILE_SIZE_MIN', '1','1'),
(4, 'UPLOADFILE_SIZE_MAX', '153600','153600'),
(5, 'UPLOADFILE_ON', '1','1'),
(6, 'UPLOADFILE_INHIBIT_TYPE', 'exe,bat,php,html,htm,shall','exe,bat,php,html,htm,shall'),
(7, 'WEB_URL', 'http://localhost/oasys', 'http://localhost'),
(8, 'PERFORMANCE_SCALE', '1', '1'),
(9, 'WEB_ON', '1', '1'),
(10, 'BACKUP_AUTO_ON', '1', '1'),
(11, 'BACKUP_LAST_DATE', '20130423', '20130423'),
(12, 'BACKUP_AUTO_CYCLE', '10', '10'),
(13, 'BACKUP_DIR', 'content/backup', 'content/backup');

-- --------------------------------------------------------

--
-- 表的结构 `oa_posts`
--

CREATE TABLE IF NOT EXISTS `oa_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `post_title` varchar(300) COLLATE utf8_bin DEFAULT NULL COMMENT '标题',
  `post_content` longtext COLLATE utf8_bin COMMENT '内容',
  `post_date` datetime NOT NULL COMMENT '创建时间',
  `post_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '修改时间',
  `post_ip` bigint(20) unsigned DEFAULT NULL COMMENT 'IP ID',
  `post_type` varchar(300) COLLATE utf8_bin NOT NULL COMMENT '内容类型标识',
  `post_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `post_parent` bigint(20) unsigned NOT NULL COMMENT '上一级ID',
  `post_user` bigint(20) unsigned DEFAULT NULL COMMENT '用户ID',
  `post_password` varchar(41) COLLATE utf8_bin DEFAULT NULL COMMENT '访问密码或内容匹配值',
  `post_name` varchar(300) COLLATE utf8_bin DEFAULT NULL COMMENT '媒体文件名称',
  `post_url` varchar(500) COLLATE utf8_bin DEFAULT NULL COMMENT '多媒体文件路径',
  `post_status` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT 'public' COMMENT '发布状态',
  `post_meta` varchar(300) COLLATE utf8_bin DEFAULT NULL COMMENT '头信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_2` (`id`),
  KEY `post_ip` (`post_ip`),
  KEY `post_parent` (`post_parent`),
  KEY `post_user` (`post_user`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `oa_user`
--

CREATE TABLE IF NOT EXISTS `oa_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_username` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '用户名',
  `user_password` char(41) COLLATE utf8_bin NOT NULL COMMENT '密码',
  `user_email` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '邮箱',
  `user_name` varchar(60) COLLATE utf8_bin NOT NULL COMMENT '昵称',
  `user_group` bigint(20) unsigned NOT NULL COMMENT '用户组',
  `user_date` datetime NOT NULL COMMENT '创建时间',
  `user_login_date` datetime NOT NULL COMMENT '上一次登录时间',
  `user_ip` bigint(20) unsigned NOT NULL COMMENT '登录IP ID',
  `user_session` char(32) COLLATE utf8_bin NOT NULL COMMENT '登录会话值',
  `user_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `user_remember` tinyint(1) NOT NULL DEFAULT '0' COMMENT '记住我',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `user_username` (`user_username`),
  KEY `user_group` (`user_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `oa_user`
--

INSERT INTO `oa_user` (`id`, `user_username`, `user_password`, `user_email`, `user_name`, `user_group`, `user_date`, `user_login_date`, `user_ip`, `user_session`, `user_status`, `user_remember`) VALUES
(1, 'oasysadmin', 'dd94709528bb1c83d08f3088d4043f4742891f4f', 'admin@admin.com', '管理员', 1, '2013-03-20 11:15:57', '2013-03-20 11:15:57', 2, 'cfcd208495d565ef66e7dff9f98764da', 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `oa_user_group`
--

CREATE TABLE IF NOT EXISTS `oa_user_group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `group_name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '名称',
  `group_power` text COLLATE utf8_bin NOT NULL COMMENT '权限',
  `group_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `oa_user_group`
--

INSERT INTO `oa_user_group` (`id`, `group_name`, `group_power`, `group_status`) VALUES
(1, '管理员组', 'admin', 1),
(2, '普通用户组', 'normal', 1);
