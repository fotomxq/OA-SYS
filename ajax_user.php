<?php

/**
 * ajax用户操作
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package oa
 */
/**
 * 引入用户登陆检测模块(包含全局引用)
 * @since 1
 */
require('logged.php');

/**
 * 添加新的用户
 * @since 1
 */
if (isset($_POST['add_username']) == true && isset($_POST['add_password']) == true && isset($_POST['add_name']) == true && isset($_POST['add_email']) == true && isset($_POST['add_group']) == true) {
    if ($oauser->add_user($_POST['add_username'], $_POST['add_password'], $_POST['add_email'], $_POST['add_name'], $_POST['add_group'], $ip_arr['id'])) {
        die('2');
    }
}

/**
 * 编辑用户
 * @since 1
 */
if (isset($_POST['edit_id']) == true && isset($_POST['edit_username']) == true && isset($_POST['edit_password']) == true && isset($_POST['edit_name']) == true && isset($_POST['edit_email']) == true && isset($_POST['edit_group']) == true) {
    if ($oauser->edit_user($_POST['edit_id'], $_POST['edit_username'], $_POST['edit_password'], $_POST['edit_email'], $_POST['edit_name'], $_POST['edit_group']) == true) {
        die('2');
    }
}

/**
 * 删除用户
 * @since 1
 */
if (isset($_GET['del']) == true) {
    if ($_GET['del'] != $oauser->get_session_login()) {
        $user_list_row = $oauser->get_user_row();
        if ($user_list_row > 1) {
            if ($oauser->del_user($_GET['del']) == true) {
                die('2');
            }
        }
    }
}

die('1');
?>
