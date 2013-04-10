<?php

/**
 * 配置信息操作类
 * @author fotomxq <fotomxq.me>
 * @version 5
 * @package OA
 */
class oaconfigs {

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
     * 初始化
     * @since 4
     * @param coredb $db 数据库操作句柄
     */
    public function __construct(&$db) {
        $this->db = $db;
        $this->table_name = $db->tables['configs'];
    }

    /**
     * 获取配置值
     * @since 2
     * @param string $config_name 配置名称
     * @param boolean $only_on 是否仅输出当前值
     * @return array|null
     */
    public function load($config_name,$only_on = true) {
        $sql = 'SELECT `config_value` as `value`,`config_default` as `default` FROM `' . $this->table_name . '` WHERE `config_name` = ?';
        $st = $this->db->prepare($sql);
        $st->bindParam(1, $config_name, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $res = null;
        if ($st->execute() === true) {
            $res = $st->fetch(PDO::FETCH_ASSOC);
            if($only_on == true && $res){
                $res = $res['value'];
            }
        }
        return $res;
    }

    /**
     * 保存配置值
     * @since 5
     * @param string $config_name 配置名称
     * @param string $config_value 配置值
     * @return boolean
     */
    public function save($config_name, $config_value) {
        $sql = 'UPDATE `' . $this->table_name . '` SET `config_value` = ? WHERE `config_name` = ?';
        $st = $this->db->prepare($sql);
        $st->bindParam(1, $config_value, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        $st->bindParam(2, $config_name, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        return $st->execute();
    }

    /**
     * 将指定配置恢复到默认
     * @since 1
     * @param string $config_name 配置名称
     * @return boolean
     */
    public function return_default($config_name) {
        $sql = 'UPDATE `' . $this->table_name . '` SET `config_value` = `config_default` WHERE `config_name` = ?';
        $st = $this->db->prepare($sql);
        $st->bindParam(1, $config_name, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
        return $st->execute();
    }

    /**
     * 将所有配置恢复到默认
     * @since 1
     * @return boolean
     */
    public function return_default_all() {
        $sql = 'UPDATE `' . $this->table_name . '` SET `config_value` = `config_default`';
        return $this->db->exec($sql);
    }

}

?>
