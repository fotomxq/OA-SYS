<?php

/**
 * 数据库操作
 * 
 * @author fotomxq <fotomxq.me>
 * @package core
 * @version 4
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
     * 表数组
     * @var array
     * @since 4 
     */
    public $tables = array(
        'ip'=>'core_ip',
        'log'=>'core_log',
        'configs'=>'oa_configs',
        'posts'=>'oa_posts',
        'user'=>'oa_user',
        'ugroup'=>'oa_user_group');
    
    public $fields = array(
        'ip'=>array('id','ip_addr','ip_ban'),
        'log'=>array('id','log_date','log_ip','log_message'),
        'configs'=>array('id','config_name','config_value','config_default'),
        'posts'=>array('id','post_title','post_content','post_date','post_modified','post_ip','post_type','post_order','post_parent','post_user','post_password','post_name','post_url','post_status','post_meta'),
        'user'=>array('id','user_username','user_password','user_email','user_name',''),
        'ugroup'=>array());

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
    
    public function select($tables,$fields,$where,$page=1,$max=10,$order=0,$desc=false){
        //合成表部分
        if(is_array($tables) == true){
            $tables = implode(',', $tables);
        }else{
            $tables = '';
        }
        //合成字段部分
        if(is_array($fields) == true){
            $fields = implode(',', $fields);
        }else{
            if(is_int($fields) == true){
                $fields = 'id';
            }else{
                
            }
        }
        $sql = 'SELECT '.  $fields.' FROM '.  $tables.' WHERE '.$where.' ORDER BY '.$this->fields[$tables][$order].','.($desc ? 'DESC':'ASC').' LIMIT '.($page-1)*$max.','.$max.'';
    }
    
    public function insert(){
        
    }
    
    public function update(){
        
    }
    
    public function delete(){
        
    }
    
    /**
     * 合成表部分
     * @since 4
     * @param string $tables 表列
     * @return string
     */
    private function get_tables($tables){
        if(is_array($tables) == true){
            return implode(',', $this->tables[$tables]);
        }else{
            return $this->tables[$tables];
        }
    }

    private function get_fields($tables,$fields) {
        //主键
        if (is_int($fields) == true) {
            return $this->fields[$tables][$fields];
        }
        //所有字段
        if($fields == 'ALL'){
            if(is_array($tables) == true){
                $return = '';
                foreach($tables as $v){
                    $return .= $v.'.'.  implode($v.'.', $this->fields[$v]);
                }
                return $return;
            }else{
                
            }
        }
        //自定义字段
        if (is_array($fields) == true) {
            if(is_array($tables) == true){
                
            }else{
                
            }
        }
    }
    
    /**
     * 获取条件语句
     * @since 4
     * @param string $table 表键值
     * @param int $field 字段键值
     * @param string $e 算数符号
     * @param string $value 值
     * @return string
     */
    private function get_where($table,$field,$e,$value){
        return $this->fields[$table][$field].$e.'\''.$value.'\'';
    }

}

?>
