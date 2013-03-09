<?php

/**
 * 图像处理类
 * @author fotomxq <fotomxq.me>
 * @version 4
 * @package core
 * @filesource cache.class.php
 * @global string DIR_TEMP
 */
class core_img extends core_cache {

    /**
     * 缓冲目录
     * @var string 
     */
    private $cache_dir;

    /**
     * 初始化
     */
    public function __construct() {
        $this->cache_dir = 'pictures';
    }

    /**
     * 将图片文件资源输出到浏览器<br/>
     * 必须是jpg格式的图片，缓存文件默认为jpg。
     * @param string $src 图片文件路径
     */
    public function view_header($src) {
        header("Content-Type: image/jpeg");
        $img = @imagecreatefromjpeg($src);
        imagejpeg($img, NULL, $quality);
        imagedestroy($img);
    }

    /**
     * 获取缓存文件路径
     * @param string $src 图片源文件路径
     * @param string $type 图片文件格式
     * @param int $create_time 图片创建时间
     * @param int $max_width 最大宽度
     * @param int $max_height 最大高度
     * @return string 缓存文件路径
     */
    public function get_src($src, $type, $create_time, $max_width, $max_height) {
        $re = '';
        if (parent::cache_file_ready != '') {
            $re = parent::cache_file_ready;
        } else {
            if (core_file::is_file($src) == true) {
                $img = null;
                //获取图片资源
                switch ($type) {
                    case 'jpg':
                        $img = @imagecreatefromjpeg($src);
                        break;
                    case 'jpeg':
                        $img = @imagecreatefromjpeg($src);
                        break;
                    case 'png':
                        $img = @imagecreatefrompng($src);
                        break;
                    case 'gif':
                        $img = @imagecreatefromgif($src);
                        break;
                    case 'wbmp':
                        $img = @imagecreatefromwbmp($src);
                        break;
                }
                $temp = DIR_TEMP . DS . $src . $max_width . $max_height;
                //压缩图片并输出到临时目录
                $src_w = imagesx($img);
                $src_h = imagesy($img);
                if ($src_w > 0 && $src_h > 0) {
                    $size_w = $size_w ? $size_w : $src_w;
                    $size_h = $size_h ? $size_h : $src_h;
                    $new_size = $this->get_new_size($src_w, $src_h, $size_w, $size_h);
                    if ($new_size[0] > 0 && $new_size[1] > 0) {
                        $img_new = imagecreatetruecolor($new_size[0], $new_size[1]);
                        if (imagecopyresampled($img_new, $img, 0, 0, 0, 0, $new_size[0], $new_size[1], $src_w, $src_h)) {
                            if (imagejpeg($img_new, $temp, 100) == true) {
                                $file_sha1 = sha1_file($temp);
                                //转移文件资源给缓冲目录
                                parent::set($temp, $file_sha1, $create_time, $this->cache_dir);
                                $re = parent::create_cache();
                                //释放临时文件
                                core_file::delete_file($temp);
                            }
                        }
                        imagedestroy($img_new);
                    }
                }
                imagedestroy($img);
            }
        }
        return $re;
    }

    /**
     * 获取压缩尺寸
     * @param int $src_w 源宽度
     * @param int $src_h 源高度
     * @param int $max_w 最大宽度
     * @param int $max_h 最大高度
     * @return array
     */
    private function get_new_size($src_w, $src_h, $max_w, $max_h) {
        $re = array();
        $cut_w = $src_w - $max_w;
        $cut_h = $src_h - $max_h;
        if ($cut_w > 0 || $cut_h > 0) {
            $p = 1;
            if ($cut_w > $cut_h) {
                $p = $cut_w / $src_w;
            } else {
                $p = $cut_h / $src_h;
            }
            $p = 1 - $p;
            $new_w = floor($src_w * $p);
            $new_h = floor($src_h * $p);
            if ($new_w == 0) {
                $new_w = 1;
            }
            if ($new_h == 0) {
                $new_h = 1;
            }
            $re = array($new_w, $new_h);
        } else {
            $re = array($src_w, $src_h);
        }
        return $re;
    }

}

?>
