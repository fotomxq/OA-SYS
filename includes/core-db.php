<?php

/**
 * 数据库操作
 * 
 * @author fotomxq <fotomxq.me>
 * @package core
 * @version 3
 */
class coredb extends PDO {

    /**
     * 数据表数组
     * @var array 
     * @since 1
     */
    public $tables = array('core_ip','core_log','oa_user','oa_user_group','oa_configs');

    /**
     * 数据库连接DNS字符串
     * @var string 
     * @since 1
     */
    public $dns;

    /**
     * 数据库连接用户名
     * @var string
     * @since 1
     */
    public $username;

    /**
     * 数据库连接密码
     * @var string
     * @since 1
     */
    public $passwd;

    /**
     * 是否持久化连接
     * @var boolean
     * @since 1 
     */
    public $persistent;

    /**
     * 数据库连接句柄
     * @var PDO 
     * @since 1
     */
    public $link_handle;

    /**
     * 数据库连接状态
     * @var boolean
     * @since 1 
     */
    public $status;

    /**
     * 初始化
     * @since 1
     * @param string $dns
     * @param string $username
     * @param string $passwd
     * @param boolean $persistent
     */
    public function __construct($dns, $username, $passwd, $persistent = true) {
        $this->dns = $dns;
        $this->username = $username;
        $this->passwd = $passwd;
        $this->persistent = $persistent;
        $this->connect();
    }

    /**
     * 初始化连接
     * @throws PDOException
     * @return boolean 连接数据库是否成功
     * @since 3
     */
    private function connect() {
        $this->status = false;
        try {
            $this->link_handle = parent::__construct($this->dns, $this->username, $this->passwd, array(PDO::ATTR_PERSISTENT => $this->persistent));
            //$this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            //$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->status = true;
        } catch (PDOException $exc) {
            new coreerror('Connect Database error.', 'core-db::connect()::PDOException', 1);
        }
    }

    /**
     * 设定编码
     * @param string $encoding_name 编码名称
     * @return boolean
     * @since 2
     */
    public function set_encoding($encoding_name) {
        $set_bool = false;
        if ($this->is_link() == true) {
            $sql_msg = 'SET NAMES ' . $encoding_name;
            $set_bool = $this->exec($sql_msg);
        }
        return $set_bool;
    }

    /**
     * 检查是否连接成功
     * @return boolean
     * @since 2
     */
    public function is_link() {
        return $this->status;
    }

}

?>
