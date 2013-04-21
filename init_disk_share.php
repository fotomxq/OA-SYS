<?php

/**
 * 文件分享中心
 * @author fotomxq <fotomxq.me>
 * @version 2
 * @package oa
 */
/**
 * 页面引用判断
 * @since 1
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 初始化基础变量
 * @since 1
 */
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$max = 10;
$sort = 0;
$desc = true;
$post_type = 'file';
$post_status = 'public';

/**
 * 提示消息变量
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 分享文件
 * @since 1
 */
if (isset($_GET['share']) == true) {
    $edit_view = $oapost->view($_GET['share']);
    if ($edit_view) {
        $edit_post_status = '';
        if ($edit_view['post_status'] == 'public') {
            $edit_post_status = 'private';
        } else {
            $edit_post_status = 'public';
        }
        if ($oapost->edit($edit_view['id'], $edit_view['post_title'], $edit_view['post_content'], $post_type, $edit_view['post_parent'], $edit_view['post_user'], $edit_view['post_password'], $edit_view['post_name'], $edit_view['post_url'], $edit_post_status, $edit_view['post_meta']) == true) {
            $message = '修改成功！';
            $message_bool = true;
        } else {
            $message = '无法修改文件信息。';
            $message_bool = false;
        }
    } else {
        $message = '无法修改文件信息';
        $message_bool = false;
    }
}

/**
 * 获取消息列表记录数
 * @since 1
 */
$table_list_row = $oapost->view_list_row(null, null, null, $post_status, $post_type, '');

/**
 * 计算页码
 * @since 1
 */
$page_max = ceil($table_list_row / $max);
if ($page < 1) {
    $page = 1;
} else {
    if ($page > $page_max) {
        $page = $page_max;
    }
}
$page_prev = $page - 1;
$page_next = $page + 1;

/**
 * 获取消息列表
 * @since 1
 */
$table_list = $oapost->view_list(null, null, null, $post_status, $post_type, $page, $max, $sort, $desc, '');
?>
<!-- 管理表格 -->
<h2>文件分享中心</h2>
<table class="table table-hover table-bordered table-striped">
    <thead>
        <tr>
            <th><i class="icon-file"></i> 文件名称</th>
            <th><i class="icon-calendar"></i> 上传时间</th>
            <th><i class="icon-user"></i> 上传用户</th>
            <th><i class="icon-asterisk"></i> 操作</th>
        </tr>
    </thead>
    <tbody id="message_list">
        <?php if ($table_list) {
            foreach ($table_list as $v) { ?>
                <tr>
                    <td><a href="<?php echo $page_url.'&view='.$v['id']; ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
                    <td><?php echo $v['post_date']; ?></td>
                    <td><?php $v_user = $oauser->view_user($v['post_user']); if($v_user){ echo $v_user['user_name']; } ?></td>
                    <td><div class="btn-group"><a href="file_download.php?id=<?php echo $v['id']; ?>" class="btn"><i class="icon-file"></i> 下载</a><a href="<?php echo $page_url.'&view='.$v['id']; ?>#view" class="btn"><i class="icon-search"></i> 查看</a><?php if($logged_admin){ ?><a href="<?php echo $page_url.'&share='.$v['id']; ?>" class="btn btn-inverse"><i class="icon-lock icon-white"></i> 取消分享</a><?php } ?></div></td>
                </tr>
        <?php } } ?>
    </tbody>
</table>

<!-- 页码 -->
<ul class="pager">
    <li class="previous<?php if ($page <= 1) { echo ' disabled'; } ?>">
        <a href="<?php echo $page_url . '&page=' . $page_prev; ?>">&larr; 上一页</a>
    </li>
    <li class="next<?php if ($page >= $page_max) { echo ' disabled'; } ?>">
        <a href="<?php echo $page_url.'&page='.$page_next; ?>">下一页 &rarr;</a>
    </li>
</ul>

<?php if (isset($_GET['view']) == true) { $view_res = $oapost->view($_GET['view']); if($view_res){ ?>
<!-- 查看信息 -->
<h2>查看信息</h2>
<div id="view" class="form-actions form-horizontal">
    <dl class="dl-horizontal">
        <dt>文件名称</dt>
        <dd><?php echo $view_res['post_title']; ?></dd>
        <dt>上传时间</dt>
        <dd><?php echo $view_res['post_date']; ?></dd>
        <dt>上传用户</dt>
        <dd><?php $view_user = $oauser->view_user($view_res['post_user']); if($view_user){ echo $view_user['user_name']; } ?></dd>
        <dt>文件描述</dt>
        <dd><?php echo $view_res['post_content']; ?></dd>
    </dl>
    <div class="control-group">
        <div class="controls">
            <a href="<?php echo $page_url; ?>" role="button" class="btn"><i class="icon-arrow-left"></i> 返回</a>
        </div>
    </div>
</div>
<?php } } ?>

<!-- Javascript -->
<script>
    $(document).ready(function() {
        var message = "<?php echo $message; ?>";
        var message_bool = "<?php echo $message_bool ? '2' : '1'; ?>";
        if (message != "") {
            msg(message_bool, message, message);
        }
    });
</script>

