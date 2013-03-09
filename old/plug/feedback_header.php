<?php

/**
 * 设置页面编码
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package feedback
 */
function feedback_header($name) {
    switch ($name) {
        case 'json':
            header('Content-Type: text/plain, charset=utf-8');
            break;
        default:
            header("Content-type: text/html; charset=utf-8");
            break;
    }
}

?>
