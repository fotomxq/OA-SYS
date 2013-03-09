<?php

/**
 * 文件操作类
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package Core
 */
class corefile {
    /**
     * 路径分隔符
     * @var string 
     */
    static public $ds = '/';

    /**
     * 判断是否为文件
     * @param string $src 路径
     * @return boolean 
     */
    static public function is_file($src) {
        return is_file($src);
    }

    /**
     * 以文件形式读取路径
     * @param string $src 路径
     * @return string/boolean 文件内容 / 失败
     */
    static public function read_file($src) {
        return file_get_contents($src);
    }

    /**
     * 以文件形式写入路径
     * @param string $src 路径
     * @param string $data 写入数据
     * @param boolean $append 是否为插入信息
     * @return boolean 执行是否成功
     */
    static public function edit_file($src, $data, $append = false) {
        $res = null;
        if ($append) {
            $res = file_put_contents($src, $data, FILE_APPEND);
        } else {
            $res = file_put_contents($src, $data);
        }
        return $res;
    }

    /**
     * 剪切或修改名称
     * 剪切或者修改文件或文件夹的路径或名称
     * @param string $src 源路径
     * @param string $dest 目标路径
     * @return boolean 
     */
    static public function cut_fx($src, $dest) {
        return rename($src, $dest);
    }

    /**
     * 复制文件
     * @param string $src 源文件路径
     * @param string $dest 目标路径
     * @return boolean 
     */
    static public function copy_file($src, $dest) {
        return copy($src, $dest);
    }

    /**
     * 移动上传文件
     * @param string $src 上传文件地址
     * @param string $dest 目标路径
     * @return boolean 
     */
    static public function move_upload($src, $dest) {
        return move_uploaded_file($src, $dest);
    }

    /**
     * 删除文件
     * @param string $src 路径
     * @return boolean 
     */
    static public function delete_file($src) {
        $re = true;
        if (is_file($src)) {
            $re = unlink($src);
        }
        return $re;
    }

    /**
     * 判断是否为目录
     * @param string $src 路径
     * @return boolean 
     */
    static public function is_dir($src) {
        return is_dir($src);
    }

    /**
     * 搜索目录
     * @param string $src 搜索的路径 eg: C:\a
     * @param string $search 搜索的内容 eg: *
     * @param int $flags 参数 eg: GLOB_ONLYDIR
     * @return array/boolean 数据数组 / 失败 
     */
    static public function list_dir($src, $search = '', $flags = null) {
        $res = null;
        $src = $src . core_file::$ds . $search;
        if ($flags) {
            $res = glob($src, $flags);
        } else {
            $res = glob($src);
        }
        return $res;
    }

    /**
     * 创建目录
     * @param string $src 路径
     * @return boolean 
     */
    static public function new_dir($src) {
        $re = false;
        if (core_file::is_dir($src)) {
            $re = true;
        } else {
            $re = mkdir($src, 0777, true);
        }
        return $re;
    }

    /**
     * 复制目录
     * @param string $src 源目录路径
     * @param string $dest 目标路径
     * @return boolean
     */
    static public function copy_dir($src, $dest) {
        $re = true;
        if (core_file::new_dir($dest) == true) {
            $dir_list = core_file::list_dir($src, '*');
            if ($dir_list != false) {
                foreach ($dir_list as $v) {
                    $v_src = $src . core_file::$ds . $v;
                    $v_dest = $dest . core_file::$ds . $v;
                    if (core_file::is_dir($v_src) == true) {
                        if (core_file::copy_dir($v_src, $v_dest) == false) {
                            $re = false;
                            break;
                        }
                    }
                    if (core_file::is_file($v_src) == true) {
                        if (core_file::copy_file($v_src, $v_dest) == false) {
                            $re = false;
                            break;
                        }
                    }
                }
            }
        } else {
            $re = false;
        }
        return $re;
    }

    /**
     * 删除目录
     * @param string $src 路径
     * @return boolean 
     */
    static public function delete_dir($src) {
        $re = true;
        if (core_file::is_dir($src) == true) {
            $dir_list = core_file::list_dir($src, '*');
            if ($dir_list != false) {
                foreach ($dir_list as $v) {
                    $vSrc = $src . core_file::$ds . $v;
                    if (core_file::is_dir($vSrc) == true) {
                        if (core_file::delete_dir($vSrc) == false) {
                            $re = false;
                            break;
                        }
                    }
                    if (core_file::is_file($vSrc) == true) {
                        if (core_file::delete_file($vSrc) == false) {
                            $re = false;
                            break;
                        }
                    }
                }
            } else {
                if (rmdir($src) == false) {
                    $re = false;
                }
            }
        } else {
            $re = false;
        }
        return $re;
    }

}

?>
