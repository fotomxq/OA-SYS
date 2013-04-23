<?php
/**
 * 备份和恢复中心
 * @author fotomxq <fotomxq.me>
 * @version 1
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
$max = 30;
$sort = 0;
$desc = true;
$post_type = 'backup';
$post_status = 'public';

/**
 * 提示消息变量
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 添加新的备份
 * @since 1
 */

/**
 * 恢复备份
 * @since 1
 */

/**
 * 删除备份
 * @since 1
 */


/**
 * 获取消息列表记录数
 * @since 1
 */
$table_list_row = $oapost->view_list_row(null, null, null, $post_status, $post_type);

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
$table_list = $oapost->view_list(null, null, null, $post_status, $post_type, $page, $max, $sort, $desc);

/**
 * 获取上一次自动备份时间
 * @since 1
 */

?>
<!-- 管理表格 -->
<h2>备份文件</h2>
<p><a href="<?php echo $page_url; ?>&backup=1" class="btn btn-large btn-warning"><i class="icon-hdd icon-white"></i> 开始备份</a></p>
<p><?php if($config_backup_date){ echo '上一次自动备份时间：'.$config_backup_date; } ?></p>
<table class="table table-hover table-bordered table-striped">
    <thead>
        <tr>
            <th><i class="icon-file"></i> 文件名称</th>
            <th><i class="icon-calendar"></i> 备份时间</th>
            <th><i class="icon-info-sign"></i> 大小(KB)</th>
            <th><i class="icon-asterisk"></i> 操作</th>
        </tr>
    </thead>
    <tbody id="message_list">
        <?php if ($table_list) {
            foreach ($table_list as $v) { ?>
                <tr>
                    <td><?php echo $v['post_name']; ?></td>
                    <td><?php echo $v['post_date']; ?></td>
                    <td><?php if($v['post_url'] > 0){ echo $v['post_url']/1024; }else{ echo '0'; } ?></td>
                    <td><div class="btn-group"><a href="<?php echo $page_url.'&return='.$v['id']; ?>" class="btn btn-warning"><i class="icon-retweet icon-white"></i> 还原</a><a href="<?php echo $page_url.'&del='.$v['id']; ?>" class="btn btn-danger"><i class="icon-trash icon-white"></i> 删除</a></div></td>
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

