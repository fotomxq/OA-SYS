<?php

/**
 * 上传文件处理模块
 * @author fotomxq <fotomxq.me>
 * @version 2
 * @package oa
 */
/**
 * 引入用户登陆检测模块(包含全局引用)
 * @since 1
 */
require('logged.php');

/**
 * 引入文件处理类
 * @since 1
 */
require(DIR_LIB . DS . 'core-file.php');

/**
 * 处理上传
 * @since 1
 */
if (isset($_FILES['file']) == true) {
    if ($_FILES["file"]["error"] == 0) {
        $config_uploadfile_on = $oaconfig->load('UPLOADFILE_ON');
        if ($config_uploadfile_on > 0) {
            $config_uploadfile_min = $oaconfig->load('UPLOADFILE_SIZE_MIN');
            $config_uploadfile_max = $oaconfig->load('UPLOADFILE_SIZE_MAX');
            $file_size = $_FILES["file"]["size"] / 1024;
            if ($file_size > $config_uploadfile_min && $file_size < $config_uploadfile_max) {
                //判断文件类型是否正确
                $config_uploadfile_hibit_type = $oaconfig->load('UPLOADFILE_INHIBIT_TYPE');
                $config_uploadfile_hibit_type_arr = null;
                if ($config_uploadfile_hibit_type) {
                    $config_uploadfile_hibit_type_arr = explode(',', $config_uploadfile_hibit_type);
                }
                unset($config_uploadfile_hibit_type);
                $file_type = substr($_FILES["file"]["type"], stripos($_FILES["file"]["type"], '.', -1) + 1);
                if (in_array($file_type, $config_uploadfile_hibit_type_arr) == false || $config_uploadfile_hibit_type_arr == null) {
                    //开始转移文件
                    $file_dest_dir = UPLOADFILE_DIR . DS . date('Ym') . DS . date('d');
                    if (corefile::new_dir($file_dest_dir) == true) {
                        $file_dest = $file_dest_dir . DS . $_FILES["file"]["name"];
                        if (corefile::move_upload($_FILES["file"]["tmp_name"], $file_dest) == true) {
                            $post_res = $oapost->add($_FILES["file"]["name"], '', 'uploadfile', 0, $post_user, null, $_FILES["file"]["name"], $file_dest, 'private', $_FILES["file"]["type"]);
                            if ($post_res > 0) {
                                //上传成功
                            } else {
                                corefile::delete_file($file_dest);
                                plugerror('uploadfile');
                            }
                        } else {
                            plugerror('uploadfile');
                        }
                    } else {
                        plugerror('uploadfile');
                    }
                } else {
                    plugerror('uploadfile-type');
                }
            } else {
                plugerror('uploadfile-size');
            }
        } else {
            plugerror('uploadfile-off');
        }
    } else {
        plugerror('uploadfile');
    }
}
?>
