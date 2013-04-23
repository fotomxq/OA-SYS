<?php
/**
 * 错误响应页面
 * @author fotomxq <fotomxq.me>
 * @version 6
 * @package oa
 */
$error_arr = array(
    'login' => '登陆失败，可能是您的用户名或密码错误！<a href="index.php" target="_self">点击这里返回登陆页面。</a>',
    'logged' => '您还没有登陆，或者操作超时，请尝试重新登陆。<a href="index.php" target="_self">点击这里返回登陆页面。</a>',
    'noadmin' => '您不是管理员，无法访问该页面。',
    'selferror'=>'无法获取用户数据，请尝试重新登录。',
    'downloadfile-pw'=>'该文件被加密了，您必须输入密码才能访问。',
    'webclose'=>'网站已经关闭了。');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>出错了</title>
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
                    <a class="brand" href="#">错误页面</a>
                    <div class="nav-collapse collapse">
                        <p class="navbar-text pull-right">
                            <a href="index.php" class="navbar-link"><i class="icon-off icon-white"></i> 返回首页</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="alert alert-error">
                <span class="label label-warning">404!</span> 
                <?php
                    if (isset($_GET['e']) == true && is_string($_GET['e']) == true) {
                        if (isset($error_arr[$_GET['e']]) == true) {
                            echo $error_arr[$_GET['e']];
                        }
                    }
                    ?>
            </div>
        </div> <!-- /container -->
        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="includes/js/jquery.js"></script>
        <script src="includes/js/bootstrap.js"></script>

    </body>
</html>