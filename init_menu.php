<?php

/**
 * 页面顶部菜单部分
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package oa
 */
?>
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="brand" href="#"><?php echo $website_title; ?></a>
                    <div class="nav-collapse collapse">
                        <p class="navbar-text pull-right">
                            <a href="logout.php" class="navbar-link">退出登陆</a>
                        </p>
                        <ul class="nav">
                            <li class="active"><a href="init.php">主页</a></li>
                            <li><a href="#about">设置</a></li>
                            <li><a href="#about">消息</a></li>
                            <li><a href="#about">速记</a></li>
                            <li><a href="#about">网盘</a></li>
                            <li><a href="#about">任务</a></li>
                            <li><a href="#about">通讯录</a></li>
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>
