<?php

/**
 * Ajax编辑用户组
 * @author fotomxq <fotomxq.me>
 * @version 2
 * @package oa
 */
/**
 * 引入用户登陆检测模块(包含全局引用)
 * @since 2
 */
require('logged.php');

/**
 * 添加用户组
 */
if(isset($_POST['add_name']) == true && isset($_POST['add_power']) == true){
    if($oauser->add_group($_POST['add_name'],$_POST['add_power'])){
        die('2');
    }else{
        die('1');
    }
}

/**
 * 修改用户组
 */
if(isset($_POST['edit_id']) == true && isset($_POST['edit_name']) == true && isset($_POST['edit_power']) == true && isset($_POST['edit_status']) == true){
    if($oauser->edit_group($_POST['edit_id'],$_POST['edit_name'],$_POST['edit_power'],$_POST['edit_status'])){
        die('2');
    }else{
        die('1');
    }
}

/**
 * 删除用户组
 */
if (isset($_GET['del']) == true && $_GET['del'] > 1) {
    //如果记录数大于1则删除
    $group_list_row = $oauser->get_group_row();
    if ($group_list_row > 1) {
        if ($oauser->del_group($_GET['del'])) {
            die('2');
        } else {
            die('1');
        }
    }
}
?>
