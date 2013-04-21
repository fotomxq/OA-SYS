<?php

/**
 * 下载文件
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
 * 引入post类并创建实例
 * @since 1
 */
require(DIR_LIB . DS . 'oa-post.php');
$oapost = new oapost($db, $ip_arr['id']);

/**
 * 下载文件
 * @since 1
 */
if (isset($_GET['id']) == true) {
    $download_view = $oapost->view($_GET['id']);
    if ($download_view) {
        //判断密码是否匹配
        $download_password_boolean = false;
        if ($download_view['post_password']) {
            if ($_GET['pw'] === $download_view['post_password']) {
                $download_password_boolean = true;
            }
        } else {
            $download_password_boolean = true;
        }
        if ($download_password_boolean == true) {
            $download_parent_view = $oapost->view($download_view['post_parent']);
            $download_dir = substr($download_parent_view['post_date'], 0, 4) . substr($download_parent_view['post_date'], 5, 2) . '/' . substr($download_parent_view['post_date'], 8, 2);
            plugtourl($website_url . '/' . DIR_DATA . '/files/' . $download_dir . '/' . $download_parent_view['post_name']);
        } else {
            plugerror('downloadfile-pw');
        }
    }
}
?>
