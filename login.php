<?php

/**
 * 登陆处理
 * @author fotomxq <fotomxq.me>
 * @version 2
 * @package oa
 */
//临时跳转
header('Location:init.php');

/**
 * 引入全局定义
 */
require('glob.php');
/**
 * 引入用户操作封装
 */
require(DIR_LIB . DS . 'oa-user.php');

/**
 * 检查变量存在并转移给user类
 */
if (isset($_POST['user']) == true && isset($_POST['pass']) == true && isset($_POST['vcode'])) {
    $remember = 0;
    $remember = $_POST['remeber'] ? true : false;
    $user = new core_user($db, $tablename_user, $tablename_user_param);
    $login_bool = $user->login($_POST['user'], $_POST['pass']);
    feedback_simple($login_bool, '', SYS_JSON, true);
}
?>
