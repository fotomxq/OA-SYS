<?php
/**
 * 登录后首页
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package oa
 */
require('glob.php');
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $website_title; ?> - 欢迎登陆</title>
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
              <a href="#" class="navbar-link">退出登陆</a>
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

    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span3">
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">个人</li>
              <li class="active"><a href="#">个人消息</a></li>
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
              <li><a href="#">用户和用户组管理</a></li>
            </ul>
          </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
          <div class="hero-unit">
            <h1>欢迎您</h1>
            <p>中心最新通知：</p>
            <p>中心最新通知内容中心最新通知中心最新通知内容中心最新通知中心最新通知内容中心最新通知...</p>
            <p><a href="#" class="btn btn-primary btn-large">查看详细 &raquo;</a></p>
          </div>
          <div class="row-fluid">
            <div class="span4">
              <h2>通知1</h2>
              <p>通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容...</p>
              <p><a class="btn" href="#">查看详细内容 &raquo;</a></p>
            </div><!--/span-->
            <div class="span4">
              <h2>短消息1</h2>
              <p>短消息1内容短消息1内容短消息1内容短消息1内容短消息1内容短消息1内容短消息1内容短消息1内容。</p>
              <p><a class="btn" href="#">查看详细内容 &raquo;</a></p>
            </div><!--/span-->
            <div class="span4">
              <h2>通知2</h2>
              <p>通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容通知内容...</p>
              <p><a class="btn" href="#">查看详细内容 &raquo;</a></p>
            </div><!--/span-->
          </div><!--/row-->
        </div><!--/span-->
      </div><!--/row-->

      <hr>

      <footer>
        <p>&copy; <?php echo $website_title; ?> 2013</p>
      </footer>

    </div><!--/.fluid-container-->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="includes/js/jquery.js"></script>
    <script src="includes/js/bootstrap.js"></script>

  </body>
</html>
