<?php

/**
 * 已登陆检测
 * <p>如果发现尚未登陆，则直接中断页面</p>
 * @author fotomxq <fotomxq.me>
 * @version 3
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
 * 进行登陆检测
 * @since 2
 */
//读取用户超时配置
$config_user_timeout = (int) $oaconfig->load('USER_TIMEOUT');
$oauser = new oauser($db);
$logged_admin = false;
if ($oauser->status($ip_arr['id'], $config_user_timeout) == true) {
    $logged_user = $oauser->view_user($oauser->get_session_login());
    if ($logged_user) {
        $logged_group = $oauser->view_group($logged_user['user_group']);
        if ($logged_group) {
            if ($logged_group['group_power'] == 'admin') {
                $logged_admin = true;
            }
        }
    }
} else {
    //如果尚未登陆处理
    plugerror('logged');
}
unset($config_user_timeout);

/**
 * 判断网站开关且是否为管理员
 * @since 3
 */
$website_on = $oaconfig->load('WEB_ON');
if (!$website_on && !$logged_admin) {
    plugerror('webclose');
}
?>
