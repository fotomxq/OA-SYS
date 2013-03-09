<?php

/**
 * 过滤器封装
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package core
 */
class core_filter {

    /**
     * 过滤是否卡死
     * <p>如果不满足条件则不予通过</p>
     * @var string 
     */
    private $strict;

    /**
     * 初始化
     */
    public function __construct() {
        $this->set_strict(false);
    }

    /**
     * 检查过滤严格性设定
     * @param boolean $bool
     */
    public function set_strict($bool) {
        $this->strict = $bool;
    }

    /**
     * 是否为整数
     * @param object $value
     */
    public function is_int($value) {
        
    }

    /**
     * 过滤字符串
     * @param string $value
     */
    public function is_string($value) {
        
    }

}

?>
