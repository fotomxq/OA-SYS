
<?php

/**
 * 
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package 
 */

/**
 * 引入并初始化数据库类<br/>
 * 保留$db变量用于后面的数据库操作引用
 */
require(DIR_LIB . DS . 'core' . DS . 'db.class.php');
$db = null;
try {
    $db = new core_db($db_dns, $db_username, $db_password, $db_persistent);
} catch (PDOException $exc) {
    die('Mysql cannot link.');
}
if ($db->is_link() == false) {
    die('Mysql cannot link.');
}
$db->set_encoding($db_encoding);
unset($db_dns, $db_username, $db_password, $db_encoding, $db_persistent);

/**
 * 引入SQL操作类库
 */
require(DIR_LIB . DS . 'core' . DS . 'sql.class.php');

/**
 * 引入并初始化IP类<br/>
 * 保留数组变量$ip_arr用于后面的IP相关信息操作<br/>
 * $ip_arr = array('id'=>Integer ID,'addr'=>String 地址,'ban'=>Integer 是否拉黑);
 */
require(DIR_LIB . DS . 'core' . DS . 'ip.class.php');
$ip = new core_ip($tablename_ip, $ip_location_data, $db);
$ip_arr = $ip->get_ip();
if ($ip_arr['id'] == 0) {
    die('Your ip cannot in database of record.');
}
if ($ip_ban_on == true && $ip_arr['ban'] != '0') {
    die('Your IP is ban.');
}
unset($tablename_ip, $ip_location_data, $ip);

/**
 * 引入文件操作类库
 */
require(DIR_LIB . DS . 'core' . DS . 'file.class.php');

/**
 * 引入并初始化日志类<br/>
 * 保留变量$log用于后面日志相关的操作。
 */
require(DIR_LIB . DS . 'core' . DS . 'log.class.php');
$log = new core_log($ip_arr['addr'], $db, $tablename_log, $log_on);
unset($tablename_log, $log_on);

/**
 * 引入语言包操作类库<br/>
 * 全局引用该类库，但不进行初始化，仅当需要时进行建立。
 */
require(DIR_LIB . DS . 'core' . DS . 'language.class.php');

/**
 * ===引入插件===
 */
/**
 * 反馈头信息编码设定包
 */
require(DIR_LIB . DS . 'plug' . DS . 'feedback_header.php');
?>
