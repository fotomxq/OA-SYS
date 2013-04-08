<?php

/**
 * 退出登陆操作
 * @author fotomxq <fotomxq.me>
 * @version 2
 * @package oa
 */
/**
 * 引入全局
 * @since 1
 */
require('glob.php');

/**
 * 引入用户类
 * @since 1
 */
require(DIR_LIB . DS . 'oa-user.php');

/**
 * 进行退出登陆操作
 */
$oauser = new oauser($db);
$oauser->logout($ip_arr['id']);
plugtourl('index.php');
?>
