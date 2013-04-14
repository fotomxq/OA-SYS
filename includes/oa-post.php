<?php

/**
 * POST类
 * <p>* 消息：post_type必须为message标识；post_user表示发布用户；post_name表示接收用户，0表示系统消息；post_content表示消息内容，消息内容不能超过500字。</p>
 * <p>  消息示例(用户间)：ID=1,post_type=message,post_user=1,post_name=2,post_content='你好，张三'</p>
 * <p>* 通讯录：post_type必须为addressbook；post_user表示通讯录所属用户；post_parent表示所属子信息，0表示某人，非0表示该记录为子信息并指向post id；post_title表示联系人姓名或子信息名称，如“电话号码”；post_content表示子信息内容，如电话号码“150xxx”；post_name表示该人存在用户，并指向该用户ID，如果不存在则为null。</p>
 * <p>   通讯录记录示例(联系人)：ID=1,post_type='addressbook',post_user=1,post_parent=0,post_title='张三',post_content=null,post_name=1</p>
 * <p>   (联系人信息)：ID=2,post_type='addressbook',post_user=1,post_parent=1,post_title='联系电话',post_content='15003540000',post_name=null</p>
 * @author fotomxq <fotomxq.me>
 * @version 3
 * @package oa
 */
class oapost {

    /**
     * Type标识组
     * @since 3
     * @var array 
     */
    private $type_values = array('message' => 'message', 'text' => 'text', 'addressbook' => 'addressbook');

    /**
     * 表名称
     * @since 1
     * @var string 
     */
    private $table_name;

    /**
     * 数据库操作句柄
     * @since 1
     * @var coredb 
     */
    private $db;

    /**
     * 操作IP ID
     * @since 1
     * @var int
     */
    private $ip_id;

    /**
     * 字段列表
     * @since 1
     * @var array 
     */
    private $fields;

    /**
     * 初始化
     * @since 1
     * @param coredb $db 数据库操作句柄
     * @param int $ip_id IP ID
     */
    public function __construct(&$db, $ip_id) {
        $this->db = $db;
        $this->table_name = $db->tables['posts'];
        $this->ip_id = $ip_id;
        $this->fields = array('id', 'post_title', 'post_content', 'post_date', 'post_modified', 'post_ip', 'post_type', 'post_order', 'post_parent', 'post_user', 'post_password', 'post_name', 'post_url', 'post_status', 'post_meta');
    }

    /**
     * 查询列表
     * @since 3
     * @param string $user 用户ID
     * @param string $title 搜索标题
     * @param string $content 搜索内容
     * @param string $status 状态 public|private|trush
     * @param string $type 识别类型 text|picture|file
     * @param int $page 页数
     * @param int $max 页长
     * @param int $sort 排序字段键值
     * @param boolean $desc 是否倒序
     * @param int $parent 上一级ID
     * @return boolean
     */
    public function view_list($user = null, $title = null, $content = null, $status = 'public', $type = 'text', $page = 1, $max = 10, $sort = 7, $desc = true, $parent = null) {
        $return = false;
        $sql_where = '';
        if ($title) {
            $title = '%' . $title . '%';
            $sql_where .= ' OR `post_title`=:title';
        }
        if ($content) {
            $content = '%' . $content . '%';
            $sql_where .= ' OR `post_content`=:content';
        }
        if ($sql_where) {
            $sql_where = '(' . substr($sql_where, 4) . ') AND';
        }
        if ($user) {
            $sql_where = $sql_where . ' `post_user`=:user AND';
        }
        if ($parent !== null) {
            $sql_where = $sql_where . ' `post_parent`=:parent AND';
        }
        $sql_desc = $desc ? 'DESC' : 'ASC';
        $sql = 'SELECT `id`,`post_title`,`post_date`,`post_modified`,`post_ip`,`post_type`,`post_order`,`post_parent`,`post_user`,`post_password`,`post_name`,`post_url`,`post_status`,`post_meta` FROM `' . $this->table_name . '` WHERE ' . $sql_where . ' `post_status`=:status AND `post_type`=:type ORDER BY ' . $this->fields[$sort] . ' ' . $sql_desc . ' LIMIT ' . ($page - 1) * $max . ',' . $max;
        $sth = $this->db->prepare($sql);
        if ($title) {
            $sth->bindParam(':title', $title, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        }
        if ($content) {
            $sth->bindParam(':content', $content, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        }
        if ($user) {
            $sth->bindParam(':user', $user, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        }
        if ($parent !== null) {
            $sth->bindParam(':parent', $parent, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        }
        $sth->bindParam(':status', $status, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $type = $this->get_type($type);
        $sth->bindParam(':type', $type, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        if ($sth->execute() == true) {
            $return = $sth->fetchAll(PDO::FETCH_ASSOC);
        }
        return $return;
    }

    /**
     * 获取条件下的记录数
     * @since 3
     * @param string $user 用户ID
     * @param string $title 搜索标题
     * @param string $content 搜索内容
     * @param string $status 状态 public|private|trush
     * @param string $type 识别类型 text|picture|file
     * @return boolean
     */
    public function view_list_row($user = null, $title = null, $content = null, $status = 'public', $type = 'text',$parent=null) {
        $return = false;
        $sql_where = '';
        if ($title) {
            $title = '%' . $title . '%';
            $sql_where .= ' OR `post_title`=:title';
        }
        if ($content) {
            $content = '%' . $content . '%';
            $sql_where .= ' OR `post_content`=:content';
        }
        if ($sql_where) {
            $sql_where = '('.substr($sql_where, 4).') AND';
        }
        if($user){
            $sql_where = $sql_where.' `post_user`=:user AND';
        }
        if ($parent != null) {
            $sql_where = $sql_where . ' `post_parent`=:parent AND';
        }
        $sql = 'SELECT COUNT(id) FROM `' . $this->table_name . '` WHERE ' . $sql_where . ' `post_status`=:status AND `post_type`=:type';
        $sth = $this->db->prepare($sql);
        if ($title) {
            $sth->bindParam(':title', $title, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        }
        if ($content) {
            $sth->bindParam(':content', $content, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        }
        if($user){
            $sth->bindParam(':user', $user, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        }
        if ($parent != null) {
            $sth->bindParam(':parent', $parent, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        }
        $sth->bindParam(':status', $status, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $type = $this->get_type($type);
        $sth->bindParam(':type', $type, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        if ($sth->execute() == true) {
            $return = $sth->fetchColumn();
        }
        return $return;
    }

    /**
     * 查询ID
     * @since 1
     * @param int $id 主键
     * @return boolean|array
     */
    public function view($id) {
        $return = false;
        if ($this->check_int($id) == false) {
            return $return;
        }
        $sql = 'SELECT `' . implode('`,`', $this->fields) . '` FROM `' . $this->table_name . '` WHERE `id` = :id';
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':id', $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        if ($sth->execute() == true) {
            $return = $sth->fetch(PDO::FETCH_ASSOC);
        }
        return $return;
    }

    /**
     * 添加新的记录
     * @since 1
     * @param string $title 标题
     * @param string $content 内容
     * @param string $type 类型
     * @param int $parent 上一级ID
     * @param int $user 用户ID
     * @param string $pw 密码明文
     * @param string $name 媒体文件原名称
     * @param string $url 媒体路径或内容访问路径
     * @param string $status 状态 public|private|trash
     * @param string $meta 媒体文件访问头信息
     * @return int 0或记录ID
     */
    public function add($title, $content, $type, $parent, $user, $pw, $name, $url, $status, $meta) {
        $return = 0;
        $sql = 'INSERT INTO `' . $this->table_name . '`(`post_title`,`post_content`,`post_date`,`post_ip`,`post_type`,`post_order`,`post_parent`,`post_user`,`post_password`,`post_name`,`post_url`,`post_meta`) VALUES(:title,:content,NOW(),:ip,:type,0,:parent,:user,:pw,:name,:url,:meta)';
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':title', $title, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $sth->bindParam(':content', $content, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $sth->bindParam(':ip', $this->ip_id, PDO::PARAM_INT);
        $type = $this->get_type($type);
        $sth->bindParam(':type', $type, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $sth->bindParam(':parent', $parent, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        $sth->bindParam(':user', $user, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        if ($pw != null) {
            $pw = sha1($pw);
        }
        $sth->bindParam(':pw', $pw, PDO::PARAM_STR);
        $sth->bindParam(':name', $name, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $sth->bindParam(':url', $url, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $sth->bindParam(':meta', $meta, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        if ($sth->execute() == true) {
            $return = $this->db->lastInsertId();
        }
        return $return;
    }

    /**
     * 编辑记录
     * @since 1
     * @param int $id 主键
     * @param string $title 标题
     * @param string $content 内容
     * @param string $type 类型
     * @param int $parent 上一级ID
     * @param int $user 用户ID
     * @param string $pw 密码明文
     * @param string $name 媒体文件原名称
     * @param string $url 媒体路径或内容访问路径
     * @param string $status 状态 public|private|trash
     * @param string $meta 媒体文件访问头信息
     * @return boolean
     */
    public function edit($id, $title, $content, $type, $parent, $user, $pw, $name, $url, $status, $meta) {
        $return = false;
        $sql = 'UPDATE `' . $this->table_name . '` SET `post_title`=:title,`post_content`=:content,`post_type`=:type,`post_parent`=:parent,`post_user`=:user,`post_password`=:pw,`post_name`=:name,`post_url`=:url,`post_status`=:status,`post_meta`=:meta WHERE `id`=:id';
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':title', $title, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $sth->bindParam(':content', $content, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $type = $this->get_type($type);
        $sth->bindParam(':type', $type, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $sth->bindParam(':parent', $parent, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        $sth->bindParam(':user', $user, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        if ($pw != null) {
            $pw = sha1($pw);
        }
        $sth->bindParam(':pw', $pw, PDO::PARAM_STR);
        $sth->bindParam(':name', $name, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $sth->bindParam(':url', $url, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $sth->bindParam(':status', $status, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $sth->bindParam(':meta', $meta, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $sth->bindParam(':id', $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        if ($sth->execute() == true) {
            $return = true;
        }
        return $return;
    }

    /**
     * 删除post
     * @since 2
     * @param int $id 主键
     * @return boolean
     */
    public function del($id) {
        if ($this->check_int($id) == false) {
            return false;
        }
        $sql = 'DELETE FROM `' . $this->table_name . '` WHERE `id` = :id';
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':id', $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        return $sth->execute();
    }

    /**
     * 删除上一级ID的所有子ID
     * @since 3
     * @param int $id ID
     * @return boolean
     */
    public function del_parent($id) {
        $return = false;
        $sql = 'SELECT `id` FROM `' . $this->table_name . '` WHERE `post_parent` = :id';
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':id', $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        if ($sth->execute() == true) {
            $res = $sth->fetchAll(PDO::FETCH_ASSOC);
            if ($res) {
                foreach ($res as $v) {
                    $this->del_parent($v['id']);
                }
            }
            $sql_delete = 'DELETE FROM `' . $this->table_name . '` WHERE `id` = :id';
            $sth_delete = $this->db->prepare($sql_delete);
            $sth_delete->bindParam(':id', $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
            $return = $sth_delete->execute();
        }
        return $return;
    }

    /**
     * 过滤数字
     * @since 1
     * @param int $int
     * @return int|boolean
     */
    private function check_int($int) {
        return filter_var($int, FILTER_VALIDATE_INT);
    }

    /**
     * 获取类型标识
     * @since 2
     * @param string $type
     * @return string
     */
    private function get_type($type) {
        return $this->type_values[$type];
    }

}

?>
