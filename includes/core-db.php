<?php

/**
 * 数据库操作
 * 
 * @author fotomxq <fotomxq.me>
 * @package core
 * @version 6
 */
class coredb extends PDO {

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
     * 表数组
     * @var array
     * @since 4 
     */
    public $tables = array(
        'ip' => 'core_ip',
        'log' => 'core_log',
        'configs' => 'oa_configs',
        'posts' => 'oa_posts',
        'user' => 'oa_user',
        'ugroup' => 'oa_user_group');

    /**
     * 字段数组
     * @var array
     * @since 6
     */
    public $fields = array(
        'ip' => array('id', 'ip_addr', 'ip_ban'),
        'log' => array('id', 'log_date', 'log_ip', 'log_message'),
        'configs' => array('id', 'config_name', 'config_value', 'config_default'),
        'posts' => array('id', 'post_title', 'post_content', 'post_date', 'post_modified', 'post_ip', 'post_type', 'post_order', 'post_parent', 'post_user', 'post_password', 'post_name', 'post_url', 'post_status', 'post_meta'),
        'user' => array('id', 'user_username', 'user_password', 'user_email', 'user_name', 'user_group', 'user_date', 'user_login_date', 'user_ip', 'user_session', 'user_status', 'user_remember'),
        'ugroup' => array('id', 'group_name', 'group_power', 'group_status'));

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

    /**
     * 查询列表
     * @since 5
     * @param array $tables 表数组 eg:array('ip','log')
     * @param array $fields 字段数组 eg:array('ip'=>array(0,1,2))
     * @param string $where 条件语句
     * @param int $page 页数
     * @param int $max 页长
     * @param int $order 排序字段键值
     * @param boolean $desc 是否倒序
     * @return boolean|array 查询结果
     */
    public function select($tables, $fields, $where, $page = 1, $max = 1, $order = 0, $desc = false) {
        //合成表部分
        $sql_table = $this->get_tables($tables);
        //合成字段部分
        $sql_field = '';
        $sql_order = '';
        foreach ($fields as $t => $f) {
            foreach ($f as $fv) {
                $sql_field .= ',' . $this->get_fields($t, $fv);
            }
            $sql_order .= ',' . $this->fields[$t][$order];
        }
        $sql_field = substr($sql_field, 1);
        $sql_order = substr($sql_order, 1);
        $sql_desc = $desc ? 'DESC' : 'ASC';
        $sql = 'SELECT ' . $sql_field . ' FROM ' . $sql_table . ' WHERE ' . $where . ' ORDER BY ' . $sql_order . ',' . $sql_desc . ' LIMIT ' . ($page - 1) * $max . ',' . $max;
        $sth = $this->prepare($sql);
        if ($sth->execute() == true) {
            if ($max > 1) {
                return $sth->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return $sth->fetch(PDO::FETCH_ASSOC);
            }
        }
        return false;
    }

    /**
     * 创建新的记录
     * @since 5
     * @param string $table 表名
     * @param array $values 数据数组
     * @return int|boolean
     */
    public function insert($table, $values) {
        if (isset($this->tables[$table]) == true) {
            if (count($values) == count($this->fields[$table])) {
                $sql = 'INSERT INTO `' . $this->tables[$table] . '`(`' . implode('`,`', $this->fields[$table]) . '`) VALUES(:' . implode(',:', $this->fields) . ')';
                $sth = $this->prepare($sql);
                if ($sth->execute($values) == true) {
                    return $this->lastInsertId();
                }
            }
        }
        return false;
    }

    /**
     * 更新记录
     * @since 5
     * @param string $table 表名
     * @param array $sets 设置值数组
     * @param string $where 条件语句
     * @return boolean
     */
    public function update($table, $sets, $where) {
        $sql = 'UPDATE `' . $this->tables[$table] . '` SET ';
        $set = '';
        foreach ($sets as $k => $v) {
            $set .= $k . ' = :' . $k;
        }
        $sql .= $set . ' WHERE ' . $where;
        $sth = $this->prepare($sql);
        if ($sth->execute($sets) == true) {
            return true;
        }
        return false;
    }

    /**
     * 删除记录
     * @since 5
     * @param string $table
     * @param string $where
     * @return boolean
     */
    public function delete($table, $where) {
        $sql = 'DELETE FROM `' . $this->tables[$table] . '` WHERE ' . $where;
        $sql .= $set . ' WHERE ' . $where;
        $sth = $this->prepare($sql);
        return $sth->execute();
    }

    /**
     * 获取条件语句
     * @since 5
     * @param int $field 字段键值
     * @param string $e 算数符号
     * @param string $value 值
     * @param string $table 表键值
     * @return string
     */
    public function get_where($field, $e, $value, $table) {
        return $this->fields[$table][$field] . $e . '\'' . $value . '\'';
    }

    /**
     * 合成表部分
     * @since 4
     * @param string|array $tables 表列
     * @return string
     */
    private function get_tables($tables) {
        if (is_array($tables) == true) {
            return implode(',', $this->tables[$tables]);
        } else {
            return $this->tables[$tables];
        }
    }

    /**
     * 获取字段
     * @since 5
     * @param string $table 表键值
     * @param string $field 字段键值
     * @return string 语句
     */
    private function get_fields($table, $field) {
        return $this->tables[$table] . '.' . $this->fields[$table][$field];
    }

}

?>
