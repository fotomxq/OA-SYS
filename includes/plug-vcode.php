<?php

/**
 * 验证码模块
 * <p>使用该函数后会自动输出图片，注意不要在前面输出任何内容，否则会造成乱码或图片错误等问题。</p>
 * <p>需要提前开启Session，完成后自动生成$_SESSION["vcode"]变量保存验证码内容。</p>
 * 
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package Plug
 */
function plugvcode($num = 4, $size = 20, $width = 0, $height = 0) {
    !$width && $width = $num * $size * 4 / 5 + 5;
    !$height && $height = $size + 10;
    // 去掉了 0 1 O l 等
    $str = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVW";
    $code = '';
    for ($i = 0; $i < $num; $i++) {
        $code .= $str[mt_rand(0, strlen($str) - 1)];
    }
    // 画图像
    $im = imagecreatetruecolor($width, $height);
    // 定义要用到的颜色
    $back_color = imagecolorallocate($im, 235, 236, 237);
    $boer_color = imagecolorallocate($im, 118, 151, 199);
    $text_color = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));
    // 画背景
    imagefilledrectangle($im, 0, 0, $width, $height, $back_color);
    // 画边框
    imagerectangle($im, 0, 0, $width - 1, $height - 1, $boer_color);
    // 画干扰线
    for ($i = 0; $i < 5; $i++) {
        $font_color = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
        imagearc($im, mt_rand(- $width, $width), mt_rand(- $height, $height), mt_rand(30, $width * 2), mt_rand(20, $height * 2), mt_rand(0, 360), mt_rand(0, 360), $font_color);
    }
    // 画干扰点
    for ($i = 0; $i < 50; $i++) {
        $font_color = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
        imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $font_color);
    }
    // 画验证码
    @imagefttext($im, $size, -5, 30, $size + 3, $text_color, DIR_DATA . DS . 'configs' . DS . 'font.ttf', $code);
    $_SESSION["vcode"] = $code;
    header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
    header("Content-type: image/png;charset=utf-8");
    imagepng($im);
    imagedestroy($im);
}

?>