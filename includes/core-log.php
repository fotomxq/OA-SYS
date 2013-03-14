<?php

/**
 * 日志操作类
 * @author fotomxq <fotomxq.me>
 * @version 4
 * @package 
 */
class corelog {

    /**
     * 日志记录开关
     * @since 1
     * @var boolean 
     */
    private $log_on;

    /**
     * 数据表名称
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
     * IP地址
     * @since 1
     * @var string 
     */
    private $ip_addr;

    /**
     * 初始化
     * @since 4
     * @param string $ip_addr IP地址
     * @param coredb $db 数据库操作句柄
     * @param boolean $log_on 日志记录开关
     */
    public function __construct($ip_addr, &$db, $log_on = true) {
        $this->db = $db;
        $this->table_name = $db->tables['log'];
        $this->log_on = $log_on;
        $this->ip_addr = $ip_addr;
    }

    /**
     * 记录新的日志
     * @since 1
     * @param string $message 日志内容
     * @return boolean
     */
    public function add($message) {
        $re_bool = false;
        $sql = 'INSERT INTO `' . $this->table_name . '`(`log_ip`,`log_message`) VALUES(?,?);';
        $sth = $this->db->prepare($sql);
        $sth->bindParam(1, $this->ip_addr, PDO::PARAM_STR);
        $sth->bindParam(2, $message, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        if ($sth->execute() == true) {
            $re_bool = true;
        }
        return $re_bool;
    }

    /**
     * 将当天之前的数据归档到logs文件夹
     * @since 2
     * @param string $log_dir 日志保存目录路径
     * @return boolean
     */
    public function files($log_dir) {
        $re = false;
        $sql = 'SELECT `id`,`log_date`,`log_ip`,`log_message` FROM `' . $this->table_name . '` ORDER BY `log_date` ASC LIMIT ?,30';
        $sth = $this->db->prepare($sql);
        $limit_page = 0;
        //遍历数据表，将记录按照时间顺序保存到文件
        do {
            $sth->bindParam(1, $limit_page, PDO::PARAM_INT);
            $sth->execute();
            $res = $sth->fetchAll(PDO::FETCH_ASSOC);
            if (is_array($res) && count($res) > 0) {
                foreach ($res as $k => $v) {
                    $date_ym = substr($v['log_date'], 0, 4) . substr($v['log_date'], 5, 2);
                    $date_d = substr($v['log_date'], 8, 2);
                    $log_file_dir = $log_dir . DS . $date_ym;
                    if (core_file::new_dir($log_file_dir) == false) {
                        return $re;
                    }
                    $log_file = $log_file_dir . DS . $date_d . '.log';
                    $log_message = $v['log_date'] . "\t" . $v['log_ip'] . "\t" . $v['log_message'] . "\r\n";
                    if (core_file::edit_file($log_file, $log_message, true) == false) {
                        return $re;
                    }
                }
            }
            $limit_page++;
        } while (is_array($res) && count($res) > 0);
        //清空日志数据表
        $sql_trash = 'TRUNCATE TABLE `' . $this->table_name . '`';
        if ($this->db->exec($sql_trash) == true) {
            $re = true;
        }
        return $re;
    }

}

?>
