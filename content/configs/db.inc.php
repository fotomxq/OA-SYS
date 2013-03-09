<?php

/**
 * 数据库定义
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package core
 */
/**
 * ===数据库基础定义===
 */
/**
 * 数据库连接字符串，eg: mysql:host=127.1.1.0;dbname=personal;charset=utf8
 */
$db_dns = 'mysql:host=localhost;dbname=oasys;charset=utf8';
/**
 * 数据库连接用户名
 */
$db_username = 'root';
/**
 * 数据库连接密码
 */
$db_password = 'root';
/**
 * 编码<br/>
 * PHP某些版本不支持PDO DNS直接设定编码，所以需要单独再设定一次。
 */
$db_encoding = 'utf8';
/**
 * 是否使用持久化连接
 */
$db_persistent = true;
?>
