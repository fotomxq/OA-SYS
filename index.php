<?php
/**
 * OA登录首页
 * @author fotomxq <fotomxq.me>
 * @version 7
 * @package oa
 */
require('glob.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo $website_title; ?> - 登录首页</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->
        <link href="includes/css/bootstrap.css" rel="stylesheet">
        <style type="text/css">
            body {
                padding-top: 40px;
                padding-bottom: 40px;
                background-image: url(includes/images/login-bg.png);
                background-repeat: no-repeat;
                background-size: 100% auto;
            }

            .form-signin {
                max-width: 300px;
                padding: 19px 29px 29px;
                margin: 0 auto 20px;
                margin-right: 205px;
                background-color: #fff;
                border: 1px solid #e5e5e5;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
            }
            .form-signin .form-signin-heading,
            .form-signin .checkbox {
                margin-bottom: 10px;
            }
            .form-signin input[type="text"],
            .form-signin input[type="password"] {
                font-size: 16px;
                height: auto;
                margin-bottom: 15px;
                padding: 7px 9px;
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

        <div class="container">

            <form class="form-signin" action="login.php" method="post">
                <h2 class="form-signin-heading"><?php echo $website_title; ?></h2>
                <input name="user" type="text" class="input-block-level" placeholder="用户名" value="">
                <input name="pass" type="password" class="input-block-level" placeholder="密码" value="">
                <input name="vcode" type="text" class="input-block-level" placeholder="验证码" value="">
                <a href="#"><img onclick="javascript:$('img').attr('src', 'vcode.php?r=' + Math.random());" src="vcode.php" style="width:150px;height:35px;"></a>
                <label class="checkbox">
                    <input name="remember" type="checkbox" value="remember-me"> 记住我
                </label>
                <button class="btn btn-large btn-primary" type="submit">登陆</button>
            </form>

        </div> <!-- /container -->

        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="includes/js/jquery.js"></script>
        <script src="includes/js/bootstrap.js"></script>

    </body>
</html>
