<?php

/**
 * 用户操作类 (尚未完善)
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package core
 * @filesource check.class.php
 */
class core_user {

    /**
     * 数据库用户表名称
     * @var string 
     */
    private $table_name_user;

    /**
     * 数据库参数表名称
     * @var string 
     */
    private $table_name_param;

    /**
     * 数据库操作句柄
     * @var core_db 
     */
    private $db;

    /**
     * 初始化
     * @param core_db $db 数据库操作句柄引用
     * @param string $table_name 数据表名称
     */
    public function __construct(&$db, $table_name_user, $table_name_param) {
        $this->db = $db;
        $this->table_name_user = $table_name_user;
        $this->table_name_param = $table_name_param;
        if (session_status() == PHP_SESSION_DISABLED) {
            session_start();
        }
    }

    /**
     * 登录用户
     * @param string $username 用户名原文
     * @param string $password 密码原文
     * @return boolean 是否登录
     */
    public function login($username, $password) {
        return true;
        $re = false;
        if ($_SESSION['user_id'] > 0) {
            $re = true;
        } else {
            $password_sha1 = sha1($password);
            $sql = 'SELECT `id`,`username` FROM `' . $this->table_name_user . '` WHERE `user_username` = ? and `user_passwprd` = ?';
            $sth = $this->db->prepare($sql);
            $sth->bindParam(1, $username, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            $sth->bindParam(2, $password_sha1, PDO::PARAM_STR);
            if ($sth->execute() == true) {
                $res = $sth->fetch(PDO::FETCH_ASSOC);
                if ($res['id'] > 0) {
                    $_SESSION['user_id'] = $res['id'];
                    $re = true;
                }
            }
        }
        return $re;
    }

    /**
     * 退出用户登录状态
     */
    public function logout() {
        if ($_SESSION['user_id'] > 0) {
            $_SESSION['user_id'] = 0;
        }
    }

    public function view_list($page, $max, $sort, $desc, $search_username = '', $search_ip = '', $search_name = '') {
        
    }

    public function view($id) {
        
    }

    public function add($username, $password, $email, $name) {
        
    }

    public function edit($id, $username, $password, $email, $name) {
        
    }

    /**
     * 删除用户
     * @param int $id 用户ID
     * @return boolean
     */
    public function del($id) {
        $re = false;
        return $re;
    }

}

?>
