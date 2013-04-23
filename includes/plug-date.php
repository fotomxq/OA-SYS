<?php

/**
 * 时间处理插件
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package plugdate
 */

/**
 * 检查用户提交的时间是否规范
 * @since 1
 * @param string $date 时间 eg:2013-04-22
 * @return string 格式化后的时间 eg:20130422
 */
function plugdate_check($date) {
    if (strlen($date) == 10) {
        $y = substr($date, 0, 4);
        $m = substr($date, 5, 2);
        $d = substr($date, 8, 2);
        if (is_int((int)$y) == true && is_int((int)$m) == true && is_int((int)$d) == true) {
            return $y . $m . $d;
        }
    }
    return '0';
}

/**
 * 获取格式化时间
 * @param string $date 时间 eg:20130422
 * @return string
 */
function plugdate_get($date) {
    if (strlen($date) == 8) {
        return substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2);
    }
    return '';
}

?>
