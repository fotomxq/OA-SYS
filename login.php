<?php

/**
 * 登陆处理
 * @author fotomxq <fotomxq.me>
 * @version 4
 * @package oa
 */
/**
 * 引入全局定义
 * @since 1
 */
require('glob.php');

/**
 * 引入用户操作封装
 * @since 1
 */
require(DIR_LIB . DS . 'oa-user.php');

/**
 * 检查变量存在并转移给user类
 * @since 3
 */
if (isset($_POST['user']) == true && isset($_POST['pass']) == true && isset($_POST['vcode']) == true) {
    if ($_POST['vcode'] == $_SESSION['vcode']) {
        $remember = false;
        if (isset($_POST['remeber']) == true) {
            $remember = true;
        }
        $user = new oauser($db);
        $login_bool = $user->login($_POST['user'], $_POST['pass'], $ip_arr['id'], $remember);
        if ($login_bool == true) {
            plugtourl('init.php');
        } else {
            plugtourl('error.php?e=login');
        }
    } else {
        plugtourl('error.php?e=login-vcode');
    }
}
?>
