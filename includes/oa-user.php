<?php

/**
 * 用户操作类
 * @author fotomxq <fotomxq.me>
 * @version 4
 * @package oa
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
     * session login变量名称
     * @var string
     * @since 2 
     */
    private $session_login_name = 'login';

    /**
     * session login user变量名称
     * @var string
     * @since 2
     */
    private $session_login_time_name = 'login_time';

    /**
     * 操作权限标识符
     * @var array
     * @since 2
     */
    private $powers = array(
        //全局操作权限
        'admin' => 'ADMIN',
        //日志查看和归档权限
        'log' => 'LOG',
        //日志查看权限
        'log_view' => 'LOG-VIEW',
        //日志归档权限
        'log_files' => 'LOG-FILES',
        //IP查看和操作权限
        'ip' => 'IP',
        //IP查看权限
        'ip_view' => 'IP-VIEW',
        //IP操作权限
        'ip_operate' => 'IP-OP',
        //配置信息操作权限
        'config' => 'CONFIG',
        //用户全局操作权限
        'user' => 'USER',
        //用户列表查看权限
        'user_view_list' => 'USER-VIEW-LIST',
        //用户自身操作权限
        'user_self' => 'USER-SELF',
        //用户操作权限
        'user_operate' => 'USER-OP',
        //用户组全局权限
        'user_group' => 'USER-GROUP',
        //用户组自身查看权限
        'user_group_view_self' => 'USER-GROUP-VIEW-SELF',
        //用户组列表查看权限
        'user_group_view_list' => 'USER-GROUP-VIEW-LIST',
        //用户组操作权限
        'user_group_operate' => 'USER-GROUP-OP');

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
     * @since 4
     * @param string $user 客户端提交用户名
     * @param string $pass 客户端提交密码明文
     * @param int $ip_id 客户端IP ID
     * @param boolean $remember 是否记住登陆状态
     * @return boolean
     */
    public function login($user, $pass, $ip_id, $remember = false) {
        $return = false;
        if ($this->check_username($user) == true && $this->check_password($pass) == true) {
            $pass_sha1 = sha1($pass);
            $session_id = $this->get_session_id();
            //判断session是否存在
            if ($session_id) {
                //判断用户是否存在以及密码是否匹配
                $sql = 'SELECT tuser.id,tuser.user_login_ip,tuser.user_login_session,tuser.user_status,tuser.user_remember FROM `' . $this->table_name_user . '` as tuser,`' . $this->table_name_group . '` as tgroup WHERE tuser.user_username = ? and tuser.user_password = ? and tuser.user_group = tgroup.id and tgroup.group_status = 1';
                $sth = $this->db->prepare($sql);
                $sth->bindParam(1, $user, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
                $sth->bindParam(2, $pass_sha1, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
                if ($sth->execute() == true) {
                    $res = $sth->fetch(PDO::FETCH_ASSOC);
                    if ($res['id'] > 0) {
                        $return = true;
                        //更新用户登陆信息
                        if ($this->update_user($res['id'], NULL, NULL, NULL, NULL, NULL, $ip_id, true, $remember)) {
                            //注册session login变量
                            $this->set_session_login($res['id']);
                        }
                    }
                }
            }
        }
        return $return;
    }

    /**
     * 获取用户登陆状态
     * @since 3
     * @param int $ip_id IP ID
     * @param int $config_user_timeout 用户超时时间（秒）
     * @return boolean
     */
    public function status($ip_id, $config_user_timeout = 900) {
        $return = false;
        if ($this->get_session_login() > 0) {
            $timeout = $this->user_time() - NOW();
            if ($timeout > $config_user_timeout) {
                $return = true;
            } else {
                $this->set_session_login(0);
            }
        } else {
            $session = $this->get_session_id();
            $sql = 'SELECT tuser.id FROM `' . $this->table_name_user . '` as tuser,`' . $this->table_name_group . '` as tgroup WHERE tuser.user_group = tgroup.id and tgroup.group_status = 1 and tuser.user_login_ip = :ip and tuser.user_remember = 1';
            $sth = $this->db->prepare($sql);
            $sth->bindParam(':ip', $ip_id, PDO::PARAM_INT);
            if ($sth->execute() == true) {
                $res = $sth->fetch(PDO::FETCH_ASSOC);
                //更新用户登陆信息
                if ($this->update_user($res['id'], NULL, NULL, NULL, NULL, NULL, $ip_id, true, true)) {
                    //注册session login变量
                    $this->set_session_login($res['id']);
                    //修正登陆超时记录
                    $this->user_time();
                }
            }
        }
        return $return;
    }

    /**
     * 退出用户登陆
     * @since 2
     * @param int $ip_id IP ID
     * @return boolean
     */
    public function logout($ip_id) {
        $user_id = $this->get_session_login();
        $this->update_user($user_id, NULL, NULL, NULL, NULL, NULL, $ip_id, false, false);
        $this->set_session_login(0);
        return true;
    }

    public function add_user($username, $password, $email, $name, $group, $ip_id) {
        
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
        $sql = '';
    }

    /**
     * 判断用户是否具备该权限
     * @since 4
     * @param string $power 提交的匹配权限
     * @param int $user_id 用户ID
     * @return boolean
     */
    public function is_powers($power, $user_id) {
        $return = false;
        $sql = 'SELECT tgroup.group_power FROM `' . $this->table_name_user . '` as tuser,`' . $this->table_name_group . '` as tgroup WHERE tuser.id = ? and tuser.user_group = tgroup.id';
        $sth = $this->db->prepare($sql);
        $sth->bindParam(1, $user_id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        if ($sth->execute() == true) {
            $res = $sth->fetchColumn();
            $power_list = str_split($res);
            if (array_search($power, $power_list) != false) {
                $return = true;
            }
        }
        return $return;
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
     * 获取session login变量
     * @since 2
     * @return int
     */
    private function get_session_login() {
        return $_SESSION[$this->session_login_name];
    }

    /**
     * 设置session login变量
     * @since 2
     * @param int $id 用户ID
     */
    private function set_session_login($id) {
        $_SESSION[$this->session_login_name] = $id;
    }

    /**
     * 获取当前用户登陆操作时间并重新校对
     * @since 2
     * @return int
     */
    private function user_time() {
        $time = 0;
        $now_time = NOW();
        if (isset($_SESSION[$this->session_login_time_name]) == true) {
            $time = $_SESSION[$this->session_login_time_name];
        } else {
            $time = $now_time;
        }
        $_SESSION[$this->session_login_time_name] = $now_time;
        return $time;
    }

    /**
     * 获取客户端session id
     * @since 1
     * @return string
     */
    private function get_session_id() {
        return session_id();
    }

    /**
     * 过滤用户名
     * @since 3
     * @param string $username 用户名
     * @return string|boolean
     */
    private function check_username($username) {
        if (filter_var($username, FILTER_SANITIZE_STRING) != false && strlen($username) <= 30) {
            return $username;
        }
        return false;
    }

    /**
     * 过滤密码
     * @since 3
     * @param string $password 密码
     * @return string|boolean
     */
    private function check_password($password) {
        if (filter_var($password, FILTER_SANITIZE_STRING) != false && strlen($password) <= 30) {
            return $password;
        }
        return false;
    }

    /**
     * 过滤email
     * @since 3
     * @param stirng $email EMail
     * @return string|boolean
     */
    private function check_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * 过滤名字
     * @since 3
     * @param string $name 名字
     * @return string|boolean
     */
    private function check_name($name) {
        if (filter_var($email, FILTER_SANITIZE_STRING) != false && strlen($name) < 300) {
            return $name;
        }
        return false;
    }

    /**
     * 过滤数字
     * @since 4
     * @param int $int
     * @return int|boolean
     */
    private function check_int($int) {
        return filter_var($int, FILTER_VALIDATE_INT);
    }

    /**
     * 检查权限是否存在
     * @since 4
     * @param string $power 权限字符串 eg:USER|USER-GROUP|IP
     * @return boolean
     */
    private function check_is_power($power) {
        $power_list = str_split($power, '|');
        if (count(array_diff($power_list, $this->powers)) > 0) {
            return false;
        }
        return true;
    }

}

?>
