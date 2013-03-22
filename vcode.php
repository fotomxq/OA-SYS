<?php

/**
 * 验证码
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package oa
 */
sleep(1);
require('glob.php');
require('includes/plug-vcode.php');
require('includes/plug-headernocache.php');
plugheadernocache();
plugvcode(4, 20, 150, 35);
?>
