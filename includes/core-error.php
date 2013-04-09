<?php

/**
 * 错误处理模块
 * @author fotomxq <fotomxq.me>
 * @version 4
 * @package Core
 */
class coreerror {

    /**
     * 错误响应页面
     * @since 4
     * @var string 
     */
    public static $error_page = 'error.php';

    /**
     * 错误日志记录开关
     * @since 2
     * @var type 
     */
    public $log_on = true;

    /**
     * 初始化
     * @since 3
     * @param string $msg 消息内容
     * @param string $id 问题标识 eg: core-error::__construct()::return true
     * @param integer $level 级别 eg: 0 - 普通错误 1- 数据库和IO故障 2 - 无法预料的系统故障
     * @param corelog $log 日志操作句柄
     */
    public function __construct($message, $id, $level = 0, $log = null) {
        if ($this->log_on == true && $level == 0 && $log != null) {
            $log->add($id . ' : ' . $message);
        }
        if (SYS_DEBUG === true) {
            die('<p>Location : ' . $id . '</p>' . '<p>Message : ' . $message . '</p>' . '<p>Level : ' . $level . '</p>');
        } else {
            $this->load_page();
        }
    }

    /**
     * 跳转到错误页面
     * @since 3
     */
    private function load_page() {
        try {
            header('Location:' . $this->error_page);
        } catch (Exception $e) {
            die();
        }
    }

}

/**
 * 错误接收函数
 * @since 3
 * @param string $errno 错误级别
 * @param string $errstr 错误描述
 * @param string $errfile 错误文件名
 * @param integer $errline 错误发生行
 */
function core_error_handle($errno, $errstr, $errfile, $errline) {
    $message = $errno . ' : ' . $errstr;
    $id = $errfile . '::' . $errline;
    new coreerror($message, $id, 2);
}

/**
 * 设定错误输出函数
 * @since 2
 */
set_error_handler('core_error_handle');
?>
