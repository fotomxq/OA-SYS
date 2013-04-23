<?php
/**
 * 个人业绩考评
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
$max = 30;
$sort = 0;
$desc = true;
$post_type = 'performance';
$post_status = 'private';
$post_parent = '';

/**
 * 提示消息变量
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 获取列表记录数
 * @since 1
 */
$table_list_row = $oapost->view_list_row($post_user, null, null, $post_status, $post_type, $post_parent);

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
 * 获取列表
 * @since 1
 */
$table_list = $oapost->view_list($post_user, null, null, $post_status, $post_type, $page, $max, $sort, $desc, $post_parent);

/**
 * 计算业绩
 * @since 1
 */
$performance_count = $oapost->sum_fields('performance', $post_user, 'post_url');
$date_mouth_start = date('Y-m') . '-00 00:00:00';
$date_mouth_end = date('Y') . '-' . ((int) date('m') + 1) . '-00 00:00:00';
$performance_mouth_count = $oapost->sum_fields('performance', $post_user, 'post_url', $date_mouth_start, $date_mouth_end);
?>
<!-- 管理表格 -->
<h2>个人业绩考评</h2>
<p>总业绩：<?php echo $performance_count; ?>；本月：<?php echo $performance_mouth_count; ?></p>
<p><a href="#print_page" target="_self" class="btn"><i class="icon-print"></i> 打印该页</a></p>
<table class="table table-hover table-bordered table-striped">
    <thead>
        <tr>
            <th><i class="icon-tag"></i> 任务名称</th>
            <th><i class="icon-user"></i> 业绩</th>
            <th><i class="icon-asterisk"></i> 操作</th>
        </tr>
    </thead>
    <tbody id="message_list">
        <?php if ($table_list) {
            foreach ($table_list as $v) { ?>
                <tr>
                    <td><?php echo $v['post_title']; ?></td>
                    <td><?php echo $v['post_url']; ?></td>
                    <td>
                        <div class="btn-group">
                            <a href="init.php?init=3&view=<?php echo $v['post_parent']; ?>" class="btn"><i class="icon-search"></i> 查询该任务</a>
                        </div>
                    </td>
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

