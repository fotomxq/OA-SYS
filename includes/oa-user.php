<?php

/**
 * 用户操作类
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package oa
 * @todo *继续完善
 */
class oauser {

    /**
     * 用户表名
     * @var string
     * @since 1 
     */
    public $table_name_user;

    /**
     * 用户组表名
     * @var string
     * @since 1 
     */
    public $table_name_group;

    /**
     * 数据库操作句柄
     * @var coredb
     * @since 1
     */
    private $db;

    /**
     * 初始化
     * @since 1
     * @param coredb $db 数据库操作句柄
     */
    public function __construct(&$db) {
        $this->db = $db;
        $this->table_name_user = $db->tables[2];
        $this->table_name_group = $db->tables[3];
    }

    public function view_user_list() {
        
    }

    public function view_group_list() {
        
    }

    public function view_user($id) {
        $sql_user = 'SELECT `user_username`,`user_password`,`user_email`,`user_name`,`user_group`,`user_create_date`,`user_create_ip`,`user_login_date`,`user_login_ip`,`user_status` FROM `' . $this->table_name_user . '` WHERE `id`=?';
        $sth = $this->db->prepare($sql_user);
    }

    public function view_group($id) {
        $sql_group = 'SELECT `user_username`,`user_password`,`user_email`,`user_name`,`user_group`,`user_create_date`,`user_create_ip`,`user_login_date`,`user_login_ip`,`user_status` FROM `' . $this->table_name_user . '` WHERE `id`=?';
    }

    /**
     * 登陆用户
     * <p>完成后注册$_SESSION['login']变量</p>
     * @since 1
     * @param string $user 客户端提交用户名
     * @param string $pass 客户端提交密码明文
     * @param int $ip_id 客户端IP ID
     * @param boolean $remember 是否记住登陆状态
     * @return boolean
     */
    public function login($user, $pass, $ip_id, $remember = false) {
        $return = false;
        $pass_sha1 = sha1($pass);
        $session_id = $this->get_session_id();
        //判断session是否存在
        if ($session_id) {
            //判断用户是否存在以及密码是否匹配
            $sql = 'SELECT `id`,`user_login_ip`,`user_login_session`,`user_status`,`user_remember` FROM `' . $this->table_name_user . '` WHERE `user_username` = ? and `user_password` = ?';
            $sth = $this->db->prepare($sql);
            $sth->bindParam(1, $user, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            $sth->bindParam(2, $pass_sha1, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            if ($sth->execute() == true) {
                $res = $sth->fetch(PDO::FETCH_ASSOC);
                if ($res['id'] > 0) {
                    $return = true;
                    //更新用户登陆IP、session、状态、remember
                    $sql_update = 'UPDATE `' . $this->table_name_user . '` SET ';
                    if ($res['user_remember']) {
                        $sql_update .= '`user_remember` = :remember';
                    }
                    if ($ip_id != $res['user_login_ip']) {
                        $sql_update .= '`user_login_ip` = :ip';
                    }
                    if ($session_id != $res['user_login_session']) {
                        $sql_update .= '`user_login_session` = :session';
                    }
                    $sql_update .= '`user_status` = 1';
                    $sql_update .= ' WHERE `user_id` = \'' . $res['id'] . '\'';
                    $sth_update = $this->db->prepare($sql_update);
                    $remember = $remember ? 1 : 0;
                    $sth_update->bindParam(':user', $remember, PDO::PARAM_INT);
                    $sth_update->bindParam(':ip', $ip_id, PDO::PARAM_INT);
                    $sth_update->bindParam(':session', $session_id, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
                    $sth_update->execute();
                    //注册session login变量
                    $_SESSION['login'] = $res['id'];
                }
            }
        }
        return $return;
    }

    public function status($ip_id) {
        $return = false;

        return $return;
    }

    public function logout() {
        $_SESSION['login'] = 0;
        $sql = '';
    }

    public function add_user() {
        
    }

    public function add_group() {
        
    }

    public function edit_user() {
        
    }

    public function edit_group() {
        
    }

    public function del_user() {
        
    }

    public function del_group() {
        $sql_del_user = '';
        $sql_del_group = '';
    }

    /**
     * 更新用户表内容
     * @since 1
     * @param int $id 用户ID
     * @param string $username 用户名
     * @param string $password 密码明文
     * @param string $email 邮箱
     * @param string $name 名字
     * @param int $group 用户组ID
     * @param int $login_ip IP ID
     * @param boolean $status 用户登陆状态
     * @param boolean $remember 是否记住用户
     * @return boolean
     */
    private function update_user($id, $username, $password, $email, $name, $group, $login_ip = false, $status = false, $remember = false) {
        $return = false;
        $sql = 'UPDATE `' . $this->table_name_user . '` SET `user_username` = :user,`user_password` = :pass,`user_email` = :email,`user_name` = :name,`user_group` = :group';
        if ($username) {
            $sql .= '`user_username` = :user';
        }
        if ($password) {
            $password = sha1($password);
            $sql .= '`user_password` = :pass';
        }
        if ($email) {
            $sql .= '`user_email` = :email';
        }
        if ($name) {
            $sql .= '`user_name` = :name';
        }
        if ($group) {
            $sql .= '`user_group` = :group';
        }
        //如果提供了IP参数，则更新登陆相关项
        if ($login_ip) {
            $status = $status ? 1 : 0;
            $remember = $remember ? 1 : 0;
            $session = $this->get_session_id();
            $sql .= ',`user_login_date` = NOW(),`user_login_ip` = :ip,`user_session` = :session,`user_status` = :status,`user_remember` = :remember';
        }
        $sql .= ' WHERE `id` = :id';
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':user', $username);
        $sth->bindParam(':pass', $password);
        $sth->bindParam(':email', $email);
        $sth->bindParam(':name', $name);
        $sth->bindParam(':group', $group);
        $sth->bindParam(':ip', $login_ip);
        $sth->bindParam(':session', $session);
        $sth->bindParam(':status', $status);
        $sth->bindParam(':remember', $remember);
        $return = $sth->execute();
        return $return;
    }

    /**
     * 获取客户端session id
     * @since 1
     * @return string
     */
    private function get_session_id() {
        return session_id();
    }

}

?>
