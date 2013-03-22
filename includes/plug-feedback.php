<?php

/**
 * 反馈操作模块
 * <p>主要用于Ajax-JSON服务端反馈信息操作</p>
 * @author fotomxq <fotomxq.me>
 * @version 3
 * @package PlugFeedBack
 */

/**
 * 设定头信息为JSON
 * @since 1
 */
function plugfeedbackheaderjson() {
    header('Content-Type: text/plain, charset=utf-8');
}

/**
 * 输出JSON内容
 * @since 3
 * @param string $status 反馈内容
 * @param string $error 错误代码
 */
function plugfeedbackjson($status, $error = null) {
    $re = array('status' => $status, 'error' => $error);
    print(json_encode($re));
    die();
}

?>
