<?php

/**
 * 报错模块
 * <p>需要plug-tourl模块支持</p>
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package plugerror
 * @param string $message 消息标识
 */
function plugerror($message_id) {
    //判断plug-tourl模块是否引用
    if (function_exists('plugtourl') == false) {
        return false;
    }
    //获取错误页面地址
    $error_page = '';
    if (class_exists('coreerror') == true) {
        $error_page = coreerror::$error_page;
    } else {
        $error_page = 'error.php';
    }
    //生成URL
    $error_page = $error_page . '?e=' . $message_id;
    //执行跳转
    plugtourl($error_page);
}

?>
