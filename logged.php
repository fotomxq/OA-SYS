<?php

/**
 * 已登陆检测
 * <p>如果发现尚未登陆，则直接中断页面</p>
 * @author fotomxq <fotomxq.me>
 * @version 1
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
 * 引入跳转URL模块
 * @since 1
 */
require(DIR_LIB . DS . 'plug-tourl.php');

/**
 * 进行登陆检测
 */
//读取用户超时配置
$config_user_timeout = (int) $oaconfig->load('USER_TIMEOUT');
$oauser = new oauser($db);
if ($oauser->status($ip_arr['id'], $config_user_timeout) == false) {
    //如果尚未登陆，则跳转到错误页面
    plugtourl('error.php?e=logged');
}
?>
