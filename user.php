<?php

/**
 * 用户页面
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package oa
 */
/**
 * 引入用户登陆检测模块(包含全局引用)
 * @since 1
 */
require('logged.php');

/**
 * 引入页数插件
 */
require(DIR_LIB.DS.'plug-pagex.php');

/**
 * 初始化变量
 */
$user_group = isset($_GET['group']) ? $_GET['group'] : null;
$user_page = isset($_GET['page']) ? $_GET['page'] : 1;
if ($user_page < 1) {
    $user_page = 1;
}
$user_max = 10;
$user_sort = 0;
$user_desc = false;

/**
 * 获取用户列表记录数
 */
//计算页数
$userlist_row = $oauser->get_user_row($user_group);
$user_page_max = ceil($user_page / $userlist_row);
$userlist = $oauser->view_user_list($user_group, $user_page, $user_max, $user_sort, $user_desc);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $website_title; ?> - 用户</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->
        <link href="includes/css/bootstrap.css" rel="stylesheet">
        <style type="text/css">
            body {
                padding-top: 60px;
                padding-bottom: 40px;
            }
            .sidebar-nav {
                padding: 9px 0;
            }

            @media (max-width: 980px) {
                /* Enable use of floated navbar text */
                .navbar-text.pull-right {
                    float: none;
                    padding-left: 5px;
                    padding-right: 5px;
                }
            }
        </style>
        <link href="includes/css/bootstrap-responsive.css" rel="stylesheet">

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="includes/js/html5shiv.js"></script>
        <![endif]-->

        <!-- Fav and touch icons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="includes/images/logo-144.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="includes/images/logo-114.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="includes/images/logo-72.png">
        <link rel="apple-touch-icon-precomposed" href="includes/images/logo-57.png">
        <link rel="shortcut icon" href="includes/images/logo.png">
    </head>

    <body>

<?php require('init_menu.php'); ?>

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span3">
                    <div class="well sidebar-nav">
                        <ul class="nav nav-list">
                            <li class="nav-header">个人中心</li>
                            <li><a href="#">个人消息</a></li>
                            <li><a href="#">网络硬盘</a></li>
                            <li><a href="#">生产任务计划</a></li>
                            <li><a href="#">个人业绩考评</a></li>
                            <li><a href="#">工作日记本</a></li>
                            <li><a href="#">通讯录</a></li>
                            <li><a href="#">修改个人信息</a></li>
                            <li class="nav-header">共享协作</li>
                            <li><a href="#">文件共享中心</a></li>
                            <li><a href="#">生产任务中心</a></li>
                            <li><a href="#">会议室</a></li>
                            <li><a href="#">公共留言薄</a></li>
                            <li class="nav-header">系统</li>
                            <li><a href="#">消息中心</a></li>
                            <li><a href="#">系统设置</a></li>
                            <li><a href="#">备份和恢复</a></li>
                            <li class="active"><a href="user.php" target="_self">用户和用户组管理</a></li>
                        </ul>
                    </div><!--/.well -->
                </div><!--/span-->
                <div class="span9">
                    <h2>用户列表</h2>
                    <table class="table table-hover table-bordered">
                        <thead>
                            <td>ID</td>
                            <td>用户名</td>
                            <td>昵称</td>
                            <td>所属用户组</td>
                            <td>最后登陆时间</td>
                            <td>最后登陆IP</td>
                            <td>操作</td>
                        </thead>
                        <tbody>
                            <td></td>
                        </tbody>
                    </table>
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <?php echo plugpagex($user_page, $user_page_max, '<button class="btn" type="button">:page</button>') ?>
                        </div>
                    </div>
                    <h2>用户组列表</h2>
                </div><!--/span-->
            </div><!--/row-->

            <hr>

            <footer>
                <p><?php echo $website_footer; ?></p>
            </footer>

        </div><!--/.fluid-container-->

        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="includes/js/jquery.js"></script>
        <script src="includes/js/bootstrap.js"></script>

    </body>
</html>