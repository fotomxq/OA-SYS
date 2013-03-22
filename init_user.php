<?php
/**
 * 用户页面
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package oa
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 引入页数插件
 */
require(DIR_LIB . DS . 'plug-pagex.php');

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