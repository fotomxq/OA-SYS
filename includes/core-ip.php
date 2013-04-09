<?php

/**
 * IP操作类
 * @author fotomxq <fotomxq.me>
 * @version 4
 * @package core
 */
class coreip {

    /**
     * IP定位数据库文件
     * @since 1
     * @var string 
     */
    private $locate_data_file;

    /**
     * 数据库表名称
     * @since 1
     * @var string
     */
    private $table_name;

    /**
     * 外部数据库操作句柄引用
     * @since 1
     * @var coredb 
     */
    private $db;

    /**
     * 初始化
     * @since 3
     * @param string $locate_data_file IP定位数据库文件
     * @param coredb $db 数据库操作句柄
     */
    public function __construct($locate_data_file, &$db) {
        $this->locate_data_file = $locate_data_file;
        $this->db = $db;
        $this->table_name = $db->tables['ip'];
    }

    /**
     * 查询IP ID
     * @since 4
     * @param int $id ID
     * @return boolean|array
     */
    public function view($id) {
        $sql = 'SELECT `id`,`ip_addr`,`ip_ban` FROM `' . $this->table_name . '` WHERE `id` = ?';
        $sth = $this->db->prepare($sql);
        $sth->bindParam(1, $id, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT);
        if ($sth->execute() == true) {
            return $sth->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * 获取或创建IP信息
     * <p>返回数组array('id','addr','ban')</p>
     * @since 1
     * @return array
     */
    public function get_ip() {
        $ip_addr = $this->get_addr();
        $re = array('id' => 0, 'ban' => '1', 'addr' => '');
        if ($ip_addr != '') {
            $sql = 'SELECT `id`,`ip_addr`,`ip_ban` FROM `' . $this->table_name . '` WHERE `ip_addr` = ?';
            $sth = $this->db->prepare($sql);
            $sth->bindParam(1, $ip_addr, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
            if ($sth->execute() == true) {
                $res = $sth->fetch(PDO::FETCH_ASSOC);
                if ($res['id'] > 0) {
                    $re = array('id' => $res['id'], 'addr' => $res['ip_addr'], 'ban' => $res['ip_ban']);
                } else {
                    $sql_insert = 'INSERT INTO `' . $this->table_name . '`(`id`,`ip_addr`,`ip_ban`) VALUES(NULL,?,0)';
                    $sth_insert = $this->db->prepare($sql_insert);
                    $sth_insert->bindParam(1, $ip_addr, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT);
                    if ($sth_insert->execute() == true) {
                        $last_id = $this->db->lastInsertId();
                        $re = array('id' => $last_id, 'addr' => $ip_addr, 'ban' => '0');
                    }
                }
            }
        }
        return $re;
    }

    /**
     * 获取IP真实地址
     * @since 1
     * @param string $ip_addr IP地址
     * @return string
     */
    public function get_locate($ip_addr) {
        $dat_path = $this->locate_data_file;
        if (!$fd = @fopen($dat_path, 'rb')) {
            return 'IP date file not exists or access denied';
        }
        $ip_addr = explode('.', $ip_addr);
        $ipNum = $ip_addr[0] * 16777216 + $ip_addr[1] * 65536 + $ip_addr[2] * 256 + $ip_addr[3];
        $DataBegin = fread($fd, 4);
        $DataEnd = fread($fd, 4);
        $ipbegin = implode('', unpack('L', $DataBegin));
        if ($ipbegin < 0)
            $ipbegin += pow(2, 32);
        $ipend = implode('', unpack('L', $DataEnd));
        if ($ipend < 0)
            $ipend += pow(2, 32);
        $ipAllNum = ($ipend - $ipbegin) / 7 + 1;
        $BeginNum = 0;
        $EndNum = $ipAllNum;
        while ($ip1num > $ipNum || $ip2num < $ipNum) {
            $Middle = intval(($EndNum + $BeginNum) / 2);
            fseek($fd, $ipbegin + 7 * $Middle);
            $ipData1 = fread($fd, 4);
            if (strlen($ipData1) < 4) {
                fclose($fd);
                return 'System Error';
            }
            $ip1num = implode('', unpack('L', $ipData1));
            if ($ip1num < 0)
                $ip1num += pow(2, 32);
            if ($ip1num > $ipNum) {
                $EndNum = $Middle;
                continue;
            }
            $DataSeek = fread($fd, 3);
            if (strlen($DataSeek) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $DataSeek = implode('', unpack('L', $DataSeek . chr(0)));
            fseek($fd, $DataSeek);
            $ipData2 = fread($fd, 4);
            if (strlen($ipData2) < 4) {
                fclose($fd);
                return 'System Error';
            }
            $ip2num = implode('', unpack('L', $ipData2));
            if ($ip2num < 0)
                $ip2num += pow(2, 32);
            if ($ip2num < $ipNum) {
                if ($Middle == $BeginNum) {
                    fclose($fd);
                    return 'Unknown';
                }
                $BeginNum = $Middle;
            }
        }
        $ipFlag = fread($fd, 1);
        if ($ipFlag == chr(1)) {
            $ipSeek = fread($fd, 3);
            if (strlen($ipSeek) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $ipSeek = implode('', unpack('L', $ipSeek . chr(0)));
            fseek($fd, $ipSeek);
            $ipFlag = fread($fd, 1);
        }
        if ($ipFlag == chr(2)) {
            $AddrSeek = fread($fd, 3);
            if (strlen($AddrSeek) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $ipFlag = fread($fd, 1);
            if ($ipFlag == chr(2)) {
                $AddrSeek2 = fread($fd, 3);
                if (strlen($AddrSeek2) < 3) {
                    fclose($fd);
                    return 'System Error';
                }
                $AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
                fseek($fd, $AddrSeek2);
            } else {
                fseek($fd, -1, SEEK_CUR);
            }
            while (($char = fread($fd, 1)) != chr(0))
                $ipAddr2 .= $char;
            $AddrSeek = implode('', unpack('L', $AddrSeek . chr(0)));
            fseek($fd, $AddrSeek);
            while (($char = fread($fd, 1)) != chr(0))
                $ipAddr1 .= $char;
        } else {
            fseek($fd, -1, SEEK_CUR);
            while (($char = fread($fd, 1)) != chr(0))
                $ipAddr1 .= $char;

            $ipFlag = fread($fd, 1);
            if ($ipFlag == chr(2)) {
                $AddrSeek2 = fread($fd, 3);
                if (strlen($AddrSeek2) < 3) {
                    fclose($fd);
                    return 'System Error';
                }
                $AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
                fseek($fd, $AddrSeek2);
            } else {
                fseek($fd, -1, SEEK_CUR);
            }
            while (($char = fread($fd, 1)) != chr(0)) {
                $ipAddr2 .= $char;
            }
        }
        fclose($fd);
        if (preg_match('/http/i', $ipAddr2)) {
            $ipAddr2 = '';
        }
        $ipaddr = "$ipAddr1 $ipAddr2";
        $ipaddr = preg_replace('/CZ88.Net/is', '', $ipaddr);
        $ipaddr = preg_replace('/^s*/is', '', $ipaddr);
        $ipaddr = preg_replace('/s*$/is', '', $ipaddr);
        if (preg_match('/http/i', $ipaddr) || $ipaddr == '') {
            $ipaddr = 'Unknown';
        }
        $ipaddr = iconv('gbk', 'utf-8//IGNORE', $ipaddr);
        if ($ipaddr != '  ')
            return $ipaddr;
        else
            $ipaddr = '未知区域';
        return $ipaddr;
    }

    /**
     * 获取IP地址
     * @since 1
     * @return string
     */
    private function get_addr() {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        elseif (isset($_SERVER["HTTP_CLIENT_IP"]))
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        elseif (isset($_SERVER["REMOTE_ADDR"]))
            $ip = $_SERVER["REMOTE_ADDR"];
        elseif (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        elseif (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        elseif (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "0.0.0.0";
        return $ip;
    }

}

?>
