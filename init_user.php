<?php
/**
 * 用户页面
 * @author fotomxq <fotomxq.me>
 * @version 2
 * @package oa
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 初始化变量
 */
$group = isset($_GET['group']) ? $_GET['group'] : null;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$max = 10;
$sort = 0;
$desc = false;

/**
 * 获取用户列表记录数
 */
$userlist_row = $oauser->get_user_row($group);

/**
 * 计算页码
 */
if ($page < 1) {
    $page = 1;
}
$page_max = ceil($userlist_row / $max);
if ($page > $page_max) {
    $page = $page_max;
}
$page_prev = $page - 1;
$page_next = $page + 1;

/**
 * 获取用户列表
 */
$userlist = $oauser->view_user_list($group, $page, $max, $sort, $desc);
?>

<h2>用户管理</h2>
<table class="table table-hover table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>用户名</th>
            <th>昵称</th>
            <th>用户组</th>
            <th>最后登录时间</th>
            <th>最后登录IP</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>ID</td>
            <td>用户名</td>
            <td>昵称</td>
            <td>用户组</td>
            <td>最后登录时间</td>
            <td>最后登录IP</td>
            <td>操作</td>
        </tr>
    </tbody>
</table>
<!-- 页码 -->
<ul class="pager">
    <li class="previous<?php if($page<=1){ echo ' disabled'; } ?>">
        <a href="init.php?init=14&page=<?php echo $page_prev; ?>">&larr; 上一页</a>
    </li>
    <li class="next<?php if($page>=$page_max){ echo ' disabled'; } ?>">
        <a href="init.php?init=14&page=<?php echo $page_next; ?>">下一页 &rarr;</a>
    </li>
</ul>