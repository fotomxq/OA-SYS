<?php

/**
 * 用户操作类
 * @author fotomxq <fotomxq.me>
 * @version 9
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
     * @since 6
     */
    private $powers = array(
        //管理员
        'admin' => 'ADMIN',
        //普通用户
        'normal' => 'NORMAL');

    /**
     * 初始化
     * @since 6
     * @param coredb $db 数据库操作句柄
     */
    public function __construct(&$db) {
        $this->db = $db;
        $this->table_name_user = $db->tables['user'];
        $this->table_name_group = $db->tables['ugroup'];
    }

    /**
     * 获取用户列表
     * @since 6
     * @param int $group 用户组ID|null
     * @param int $page 页数
     * @param int $max 页长
     * @param int $order 排序字段数组键值
     * @param boolean $desc 是否倒序
     * @return array|boolean
     */
    public function view_user_list($group = null, $page = 1, $max = 10, $order = 0, $desc = false) {
        $return = false;
        $fields = array('id', 'user_username', 'user_password', 'user_email', 'user_name', 'user_group', 'user_date', 'user_login_date', 'user_ip', 'user_login_status');
        if (isset($fields[$order]) == true) {
            $where = '';
            if ($group == null) {
                $where = '1';
            } else {
                $where = '`user_group` = :group';
            }
            $desc = $desc ? 'DESC' : 'ASC';
            $sql = 'SELECT `id`,`user_username`,`user_password`,`user_email`,`user_name`,`user_group`,`user_date`,`user_login_date`,`user_ip`,`user_status` FROM `' . $this->table_name_user . '` WHERE ' . $where . ' ORDER BY ' . $fields[$order] . ' ' . $desc . ' LIMIT ' . ($page - 1) * $max . ',' . $max;
            $sth = $this->db->prepare($sql);
            if ($group != null) {
                $sth->bindParam(':group', $group);
            }
            if ($sth->execute() == true) {
                $return = $sth->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        return $return;
    }

    /**
     * 获取用户列表记录数
     * @since 5
     * @param int $group 用户组ID
     * @return int
     */
    public function get_user_row($group = null) {
        $return = 0;
        $where = '';
        if ($group == null) {
            $where = '1';
        } else {
            $where = '`user_group` = :group';
        }
        $sql = 'SELECT COUNT(id) FROM `' . $this->table_name_user . '` WHERE ' . $where;
        $sth = $this->db->prepare($sql);
        if ($group != null) {
            $sth->bindParam(':group', $group, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        }
        if ($sth->execute() == true) {
            $return = $sth->fetchColumn();
        }
        return $return;
    }

    /**
     * 获取用户组列表
     * @since 5
     * @param int $page 页数
     * @param int $max 页长
     * @param int $order 排序字段数组键值
     * @param boolean $desc 是否倒序
     * @return array|boolean
     */
    public function view_group_list($page = 1, $max = 10, $order = 0, $desc = false) {
        $return = false;
        $fields = array('id', 'group_name', 'group_power', 'group_status');
        if (isset($fields[$order]) == true) {
            $desc = $desc ? 'DESC' : 'ASC';
            $sql = 'SELECT `id`,`group_name`,`group_power`,`group_status` FROM `' . $this->table_name_group . '` ORDER BY ' . $fields[$order] . ' ' . $desc . ' LIMIT ' . ($page - 1) * $max . ',' . $max;
            $sth = $this->db->prepare($sql);
            if ($sth->execute() == true) {
                $return = $sth->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        return $return;
    }

    /**
     * 获取用户组记录数
     * @since 5
     * @return int
     */
    public function get_group_row() {
        $return = 0;
        $sql = 'SELECT COUNT(id) FROM `' . $this->table_name_group . '`';
        $sth = $this->db->prepare($sql);
        if ($sth->execute() == true) {
            $return = $sth->fetchColumn();
        }
        return $return;
    }

    /**
     * 获取用户信息
     * @since 6
     * @param int $id 用户ID
     * @return array|boolean
     */
    public function view_user($id) {
        $return = false;
        $sql = 'SELECT `id`,`user_username`,`user_email`,`user_name`,`user_group`,`user_date`,`user_login_date`,`user_ip`,`user_status` FROM `' . $this->table_name_user . '` WHERE `id`=:id';
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':id', $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        if ($sth->execute() == true) {
            $return = $sth->fetch(PDO::FETCH_ASSOC);
        }
        return $return;
    }

    /**
     * 根据用户名查找用户
     * @since 9
     * @param string $username 用户名
     * @return array|boolean
     */
    public function view_user_name($username) {
        $return = false;
        $sql = 'SELECT `id`,`user_username`,`user_email`,`user_name`,`user_group`,`user_date`,`user_login_date`,`user_ip`,`user_status` FROM `' . $this->table_name_user . '` WHERE `user_username`=:name';
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':name', $username, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        if ($sth->execute() == true) {
            $return = $sth->fetch(PDO::FETCH_ASSOC);
        }
        return $return;
    }

    /**
     * 获取用户组信息
     * @since 5
     * @param int $id 用户组ID
     * @return array|boolean
     */
    public function view_group($id) {
        $return = false;
        $sql = 'SELECT `id`,`group_name`,`group_power`,`group_status` FROM `' . $this->table_name_group . '` WHERE `id` = :id';
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':id', $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        if ($sth->execute() == true) {
            $return = $sth->fetch(PDO::FETCH_ASSOC);
        }
        return $return;
    }

    /**
     * 登陆用户
     * <p>完成后注册$_SESSION['login']变量</p>
     * @since 7
     * @param string $user 客户端提交用户名
     * @param string $pass 客户端提交密码明文
     * @param int $ip_id 客户端IP ID
     * @param boolean $remember 是否记住登陆状态
     * @return boolean
     */
    public function login($user, $pass, $ip_id, $remember = false) {
        $return = false;
        if ($this->check_username($user) == true && $this->check_password($pass) == true) {
            $pass_sha1 = $this->get_password_sha1($pass);
            $session_id = $this->get_session_id();
            //判断session是否存在
            if ($session_id) {
                //判断用户是否存在以及密码是否匹配
                $sql = 'SELECT u.id FROM `' . $this->table_name_user . '` as u,`' . $this->table_name_group . '` as g WHERE u.user_username = :username and u.user_password = :password and u.user_group = g.id and g.group_status = 1';
                $sth = $this->db->prepare($sql);
                $sth->bindParam(':username', $user, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
                $sth->bindParam(':password', $pass_sha1, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
                if ($sth->execute() == true) {
                    $res = $sth->fetch(PDO::FETCH_ASSOC);
                    if ($res['id'] > 0) {
                        //更新用户登陆信息
                        if ($this->update_user($res['id'], $ip_id, true, $remember)) {
                            //注册session login变量
                            $this->set_session_login($res['id']);
                            //更新登陆时间
                            $this->user_time();
                        }
                        $return = true;
                    }
                }
            }
        }
        return $return;
    }

    /**
     * 获取用户登陆状态
     * @since 7
     * @param int $ip_id IP ID
     * @param int $config_user_timeout 用户超时时间（秒）
     * @return boolean
     */
    public function status($ip_id, $config_user_timeout = 900) {
        $return = false;
        if ($this->get_session_login() > 0) {
            $timeout = time() - $this->user_time();
            if ($timeout < $config_user_timeout) {
                $return = true;
            } else {
                $this->set_session_login(0);
            }
        } else {
            //$session = $this->get_session_id();
            $sql = 'SELECT u.id,u.user_remember as remember FROM `' . $this->table_name_user . '` as u,`' . $this->table_name_group . '` as g WHERE u.user_group = g.id and g.group_status = 1 and u.user_status = 1 and u.user_ip = :ip and u.user_remember = 1';
            $sth = $this->db->prepare($sql);
            $sth->bindParam(':ip', $ip_id, PDO::PARAM_INT);
            if ($sth->execute() == true) {
                $res = $sth->fetch(PDO::FETCH_ASSOC);
                //如果查询为记住登陆状态
                if ($res['id'] > 0) {
                    //更新用户登陆信息
                    if ($this->update_user($res['id'], $ip_id, true, $res['remember'])) {
                        //注册session login变量
                        $this->set_session_login($res['id']);
                        //修正登陆超时记录
                        $this->user_time();
                    }
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
        $this->update_user($user_id, $ip_id, false, false);
        $this->set_session_login(0);
        return true;
    }

    /**
     * 添加一个新用户
     * @since 8
     * @param string $username 用户名
     * @param string $password 密码明文
     * @param string $email 邮箱
     * @param string $name 名字
     * @param int $group 用户组ID
     * @param int $ip_id IP ID
     * @return int
     */
    public function add_user($username, $password, $email, $name, $group, $ip_id) {
        $return = 0;
        if ($this->check_username($username) == true && $this->check_password($password) == true && $this->check_email($email) == true && $this->check_name($name) == true && $this->check_int($group) == true) {
            //判断用户组是否存在
            $group_view = $this->view_group($group);
            if ($group_view == false) {
                return $return;
            }
            //判断用户名是否存在
            $sql_select = 'SELECT `id` FROM `' . $this->table_name_user . '` WHERE `user_username` = :username';
            $sth_select = $this->db->prepare($sql_select);
            $sth_select->bindParam(':username', $username, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            if ($sth_select->execute() != true) {
                return $return;
            }
            $res_select = $sth_select->fetchColumn();
            if ($res_select) {
                return $return;
            }
            //插入新的记录
            $sql = 'INSERT INTO `' . $this->table_name_user . '`(`user_username`,`user_password`,`user_email`,`user_name`,`user_group`,`user_date`,`user_login_date`,`user_ip`,`user_session`,`user_status`,`user_remember`) VALUES(:username,:password,:email,:name,:group,NOW(),NOW(),:ip,:session,0,0)';
            $sth = $this->db->prepare($sql);
            $sth->bindParam(':username', $username, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            $password = $this->get_password_sha1($password);
            $sth->bindParam(':password', $password, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            $sth->bindParam(':email', $email, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            $sth->bindParam(':name', $name, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            $sth->bindParam(':group', $group_view['id']);
            $sth->bindParam(':ip', $ip_id, PDO::PARAM_INT);
            $session = md5('0');
            $sth->bindParam(':session', $session);
            if ($sth->execute() == true) {
                $return = $this->db->lastInsertId();
            }
        }
        return $return;
    }

    /**
     * 添加新的用户组
     * @since 5
     * @param string $name 用户组名称
     * @param string $power 权限
     * @return boolean|int
     */
    public function add_group($name, $power) {
        $return = false;
        if ($this->check_name($name) == true && $this->check_is_power($power) == true) {
            $sql_select = 'SELECT `id` FROM `' . $this->table_name_group . '` WHERE `group_name` = :name';
            $sth_select = $this->db->prepare($sql_select);
            $sth_select->bindParam(':name', $name, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            if ($sth_select->execute() == true) {
                $res_select = $sth_select->fetchColumn();
                if ($res_select) {
                    return $return;
                }
                $sql = 'INSERT INTO `' . $this->table_name_group . '`(`group_name`,`group_power`,`group_status`) VALUES(:name,:power,\'1\')';
                $sth = $this->db->prepare($sql);
                $sth->bindParam(':name', $name, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
                $sth->bindParam(':power', $power, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
                if ($sth->execute() == true) {
                    $return = $this->db->lastInsertId();
                }
            }
        }
        return $return;
    }

    /**
     * 修改用户
     * @since 8
     * @param int $id 主键
     * @param string $username 用户名
     * @param string $password 密码明文或null，留空则不修改
     * @param string $email 邮箱
     * @param string $name 昵称
     * @param int $group 用户组ID
     * @return boolean
     */
    public function edit_user($id, $username, $password, $email, $name, $group) {
        $return = false;
        if ($this->check_int($id) == true && $this->check_username($username) == true && ($this->check_password($password) == true || $password == null) && $this->check_email($email) == true && $this->check_name($name) && $this->check_int($group) == true) {
            $group_view = $this->view_group($group);
            if ($group_view == false) {
                return $return;
            }
            $sql_password = '';
            if ($password != null) {
                $sql_password = ',`user_password` = \'' . $this->get_password_sha1($password) . '\'';
            }
            $sql = 'UPDATE `' . $this->table_name_user . '` SET `user_username` = :username,`user_email` = :email,`user_name` = :name,`user_group` = :group' . $sql_password . ' WHERE `id` = :id';
            $sth = $this->db->prepare($sql);
            $sth->bindParam(':id', $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
            $sth->bindParam(':username', $username, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            $sth->bindParam(':email', $email, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            $sth->bindParam(':name', $name, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            $sth->bindParam(':group', $group_view['id']);
            if ($sth->execute() == true) {
                $return = true;
            }
        }
        return $return;
    }

    /**
     * 编辑用户组
     * @since 5
     * @param int $id 用户组ID
     * @param string $name 用户组名称
     * @param string $power 权限
     * @param boolean $status 启用状态
     * @return boolean
     */
    public function edit_group($id, $name, $power, $status) {
        $return = false;
        if ($this->check_name($name) == true && $this->check_is_power($power) == true) {
            $sql = 'UPDATE `' . $this->table_name_group . '` SET `group_name` = :name,`group_power` = :power,`group_status` = :status WHERE `id` = :id';
            $sth = $this->db->prepare($sql);
            $sth->bindParam(':id', $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
            $sth->bindParam(':name', $name, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            $sth->bindParam(':power', $power, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            $status = $status ? 1 : 0;
            $sth->bindParam(':status', $status, PDO::PARAM_INT);
            if ($sth->execute() == true) {
                $return = true;
            }
        }
        return $return;
    }

    /**
     * 删除用户
     * @since 5
     * @param int $id 用户ID
     * @return boolean
     */
    public function del_user($id) {
        if ($this->check_int($id) == false) {
            return false;
        }
        $sql = 'DELETE FROM `' . $this->table_name_user . '` WHERE `id` = :id';
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':id', $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        return $sth->execute();
    }

    /**
     * 删除用户组
     * <p>注意，用户组下的所有用户将一并删除！</p>
     * @since 5
     * @param int $id 用户组ID
     * @return boolean
     */
    public function del_group($id) {
        if ($this->check_int($id) == false) {
            return false;
        }
        $sql_user = 'DELETE FROM `' . $this->table_name_user . '` WHERE `user_group` = :id';
        $sth_user = $this->db->prepare($sql_user);
        $sth_user->bindParam(':id', $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        if ($sth_user->execute() != true) {
            return false;
        }
        $sql_group = 'DELETE FROM `' . $this->table_name_group . '` WHERE `id` = :id';
        $sth_group = $this->db->prepare($sql_group);
        $sth_group->bindParam(':id', $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        return $sth_group->execute();
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
     * 退出登陆更新用户
     * @since 6
     * @param int $id 用户ID
     * @param int $ip IP ID
     * @param boolean $status 用户登陆状态
     * @param boolean $remember 是否记住用户
     * @return boolean
     */
    private function update_user($id, $ip = false, $status = false, $remember = false) {
        $return = false;
        $sql = 'UPDATE `' . $this->table_name_user . '` SET `user_login_date` = NOW(),`user_ip` = :ip,`user_session` = :session,`user_status` = :status,`user_remember` = :remember WHERE `id` = :id';
        //如果提供了IP参数，则更新登陆相关项
        $status = $status ? 1 : 0;
        $remember = $remember ? 1 : 0;
        $session = $this->get_session_id();
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':id', $id);
        $sth->bindParam(':ip', $login_ip, PDO::PARAM_INT);
        $sth->bindParam(':session', $session, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $sth->bindParam(':status', $status, PDO::PARAM_INT);
        $sth->bindParam(':remember', $remember, PDO::PARAM_INT);
        $return = $sth->execute();
        return $return;
    }

    /**
     * 获取session login变量
     * @since 8
     * @return int
     */
    public function get_session_login() {
        if (isset($_SESSION[$this->session_login_name]) == true) {
            return $_SESSION[$this->session_login_name];
        }
        return false;
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
        $now_time = time();
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
     * @since 6
     * @param string $name 名字
     * @return string|boolean
     */
    private function check_name($name) {
        if (filter_var($name, FILTER_SANITIZE_STRING) != false && strlen($name) < 300) {
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
     * @since 6
     * @param string $power 权限字符串 eg:USER|USER-GROUP|IP
     * @return boolean
     */
    private function check_is_power($power) {
        if ($power == 'admin' || $power == 'normal') {
            return true;
        }
        return false;
    }

    /**
     * 返回加密的密码
     * @since 6
     * @param string $password 密码明文
     * @return string
     */
    private function get_password_sha1($password) {
        return sha1($password);
    }

}

?>
