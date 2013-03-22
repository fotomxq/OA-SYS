<?php

/**
 * 页数列表生成模块
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package oa
 * @param int $page 页数
 * @param int $max 页长
 * @param string $str 替换字符串
 * @param string $strtr 替换标识
 * @return string 合成字符串
 */
function plugpagex($page, $max, $str, $strtr = ':page') {
    $re = str_ireplace($strtr, '1', $str);
    $fin = false;
    for ($i = 1; $i < 4; $i++) {
        $p = $i + $page;
        if ($p < $max) {
            $re .= str_ireplace($strtr, $p, $str);
        } else {
            $fin = true;
            break;
        }
    }
    if ($fin == false) {
        $re .= str_ireplace($strtr, '...', $str);
    }
    return $re;
}

?>
