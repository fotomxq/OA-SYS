<?php

/**
 * 反馈状态封装模块
 * <p>适用于Ajax反馈提示，尽量不要用于封装大量数据包。</p>
 * @author fotomxq <fotomxq.me>
 * @version 2
 * @package feedback
 * @param boolean|string $ststus 状态或反馈字符串
 * @param string $error 失败原因描述
 * @param string $type 反馈内容类型
 * @param boolean $print_die 是否输出并中断页面
 * @return string|null JSON字符串|普通状态字符串|无返回值
 */
function feedback_simple($ststus, $error, $type = SYS_JSON, $print_die = false) {
    $re = '';
    if ($type == SYS_JSON) {
        $re_arr = array('status' => $ststus, 'error' => $error);
        $re = json_encode($re_arr);
    } else {
        $re = $ststus ? $ststus : $error;
    }
    if ($print_die == true) {
        print($re);
        die();
    } else {
        return $re;
    }
}

?>
