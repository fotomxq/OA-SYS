<?php

/**
 * 缓冲类
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package core
 * @global string DIR_DATA
 * @global string DS
 * @filesource file.class.php
 */
class core_cache {

    /**
     * 缓冲全局目录
     * @var string
     */
    protected $cache_dir;

    /**
     * 缓冲文件详细路径
     * @var string 
     */
    protected $cache_file_dir;

    /**
     * 缓冲文件SHA1摘要值
     * @var string 
     */
    protected $cache_file_sha1;

    /**
     * 源文件
     * @var string 
     */
    protected $file_src;

    /**
     * 已经创建的缓冲文件路径
     * @var string 
     */
    protected $cache_file_ready;

    /**
     * 设定缓冲
     * @param string $src 资源路径<br/>
     * 原始资源路径，如果没有则需要手动创建，然后赋予该参数。
     * @param string $sha1 信息摘要<br/>
     * 资源的信息摘要，如果是文件则是文件信息摘要，如果是文本内容则是文本信息摘要。<br/>
     * 该内容不限制，只要保证内容是唯一的即可。
     * @param string $create_time 内容创建时间标记(可选)<br/>
     * 如果没有提供日期，则直接以SHA1摘要值为文件名，创建到缓冲目录下。<br/>
     * eg.20121228105023
     * @param string $cache_dir 缓冲保存目录<br/>
     * 缓冲文件保存的目录，如果不修改则为空，将使用初始化时给定的值。
     * @return string 尚未缓存的文件路径
     */
    protected function set($src, $sha1, $create_time = '', $cache_dir = '') {
        $re = '';
        if ($cache_dir != '') {
            $this->cache_dir = DIR_DATA . DS . 'cache' . DS . $cache_dir;
        }
        $this->file_src = $src;
        $this->cache_file_sha1 = $sha1;
        $this->cache_file_ready = '';
        if ($create_time == '') {
            $this->cache_file_dir = $this->cache_dir;
        } else {
            $data_year_month = strstr($create_time, 0, 6);
            $data_day = strstr($create_time, 6, 2);
            $this->cache_file_dir = $this->cache_dir . DS . $data_year_month . DS . $data_day;
        }
        return $this->cache_dir . DS . $this->cache_file_sha1;
    }

    /**
     * 创建缓冲文件
     * @return string 缓冲文件路径
     */
    protected function create_cache() {
        $re = '';
        if (core_file::is_file($this->cache_file) == true) {
            $re = $this->cache_file;
        } else {
            if (core_file::new_dir($this->cache_file_dir) == true) {
                $cache_dest = $this->cache_file_dir . DS . $this->cache_file_sha1;
                $re = core_file::copy_file($this->file_src, $cache_dest) == true;
            }
        }
        $this->cache_file_ready = $re;
        return $re;
    }

    /**
     * 删除缓冲文件
     * @return boolean
     */
    protected function delete_cache() {
        return core_file::delete_file($this->cache_file);
    }

    /**
     * 删除缓冲目录下的所有缓冲文件
     * @return boolean
     */
    protected function delete_all_cache() {
        $re = true;
        $file_list = core_file::list_dir($this->cache_dir, '*');
        if ($file_list != false && is_array($file_list) == true) {
            foreach ($file_list as $v_src) {
                if (core_file::is_dir($v_src) == true) {
                    if (core_file::delete_dir($v_src) == false) {
                        $re = false;
                    }
                } else {
                    if (core_file::delete_file($v_src) == false) {
                        $re = false;
                    }
                }
            }
        } else {
            $re = false;
        }
        return $re;
    }

}

?>
