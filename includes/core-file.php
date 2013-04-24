<?php

/**
 * 文件操作类
 * @author fotomxq <fotomxq.me>
 * @version 2
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
        $src = $src . corefile::$ds . $search;
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
        if (corefile::is_dir($src)) {
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
        $return = false;
        if (corefile::is_dir($src) == true) {
            if (corefile::new_dir($dest) == true) {
                $dir_list = corefile::list_dir($src, '*');
                if ($dir_list) {
                    foreach ($dir_list as $v) {
                        $v_name = basename($v);
                        $v_dest = $dest . DS . $v_name;
                        $return = corefile::copy_dir($v, $v_dest);
                        if ($return == false) {
                            break;
                        }
                    }
                } else {
                    $return = true;
                }
            }
        } else {
            $return = corefile::copy_file($src, $dest);
        }
        return $return;
    }

    /**
     * 删除目录
     * @since 2
     * @param string $src 路径
     * @return boolean 
     */
    static public function delete_dir($src) {
        $return = true;
        if (corefile::is_dir($src) == true) {
            $dir_list = corefile::list_dir($src, '*');
            if ($dir_list) {
                foreach ($dir_list as $v) {
                    $return = corefile::delete_dir($v);
                    if ($return == false) {
                        break;
                    }
                }
            }
        }
        if (corefile::is_file($src) == true) {
            $return = unlink($src);
        } else {
            $return = rmdir($src);
        }
        return $return;
    }

    /**
     * 创建ZIP文件并压缩目录或文件
     * @since 2
     * @param string $zip_filename 压缩包路径
     * @param string $src 压缩对象
     * @return boolean
     */
    static public function create_zip($zip_filename, $src) {
        $return = false;
        if (class_exists('ZipArchive') == true) {
            $zip = new ZipArchive();
            if (corefile::is_file($zip_filename)) {
                $zip->open($zip_filename);
            } else {
                $zip->open($zip_filename, ZIPARCHIVE::CREATE);
            }
            $return = corefile::create_zip_add($zip, $src);
            $zip->close();
        }
        return $return;
    }

    /**
     * 添加文件到压缩包
     * @since 2
     * @param ZipArchive $zip 压缩操作句柄
     * @param string $src 源文件路径
     * @param string $dest 压缩包内的路径
     * @return boolean
     */
    static public function create_zip_add(&$zip, $src, $dest = null) {
        $return = false;
        if ($dest == null) {
            $dest = basename($src);
        }
        if (corefile::is_dir($src)) {
            if ($zip->addEmptyDir($dest) == true) {
                $dir_list = corefile::list_dir($src, '*');
                foreach ($dir_list as $v) {
                    $v_src = basename($v);
                    $v_dest = $dest . DS . $v_src;
                    $return = corefile::create_zip_add($zip, $v, $v_dest);
                }
            }
        } else {
            $return = $zip->addFile($src, $dest);
        }
        return $return;
    }

    /**
     * 解压文件
     * @since 2
     * @param string $zip_filename 压缩包路径
     * @param string $dest 解压到路径
     * @return boolean
     */
    static public function extract_zip($zip_filename, $dest) {
        $return = false;
        if (class_exists('ZipArchive') == true) {
            $zip = new ZipArchive();
            if ($zip->open($zip_filename) === true) {
                $return = $zip->extractTo($dest);
            }
            $zip->close();
        }
        return $return;
    }

}

?>
