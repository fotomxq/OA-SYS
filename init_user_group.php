<?php
/**
 * 用户组页面
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package oa
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 初始化变量
 */
$user_group = isset($_POST['user_group']) ? $_POST['user_group'] : null;

/**
 * 获取用户列表记录数
 */
$userlist_row = $oauser->get_user_row($user_group);
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
        <button class="btn" type="button">首页</button>
        <button class="btn" type="button">尾页</button>
    </div>
</div>
<h2>用户组列表</h2>
