<?php
/**
 * 工作日记
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
$max = 10;
$sort = 0;
$desc = true;
$post_type = 'text';
$post_status = 'private';

/**
 * 提示消息变量
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 添加新的记录
 * @since 1
 */
if (isset($_POST['add_title']) == true && isset($_POST['add_content']) == true) {
    if ($_POST['add_title'] && $_POST['add_content']) {
        if ($oapost->add($_POST['add_title'], $_POST['add_content'], $post_type, 0, $post_user, null, null, null, $post_status, null)) {
            $message = '添加成功。';
            $message_bool = true;
        } else {
            $message = '无法添加日记。';
            $message_bool = false;
        }
    } else {
        $message = '无法添加日记，必须输入标题和内容。';
        $message_bool = false;
    }
}

/**
 * 修改记录信息
 * @since 1
 */
if (isset($_POST['edit_id']) == true && isset($_POST['edit_title']) == true && isset($_POST['edit_content']) == true) {
    if ($_POST['edit_title'] && $_POST['edit_content']) {
        $edit_view = $oapost->view($_POST['edit_id']);
        if ($edit_view) {
            if ($oapost->edit($_POST['edit_id'], $_POST['edit_title'], $_POST['edit_content'], $post_type, $edit_view['post_parent'], $edit_view['post_user'], $edit_view['post_password'], $edit_view['post_name'], $edit_view['post_url'], $edit_view['post_status'], $edit_view['post_meta']) == true) {
                $message = '修改成功！';
                $message_bool = true;
            } else {
                $message = '无法修改日记。';
                $message_bool = false;
            }
        } else {
            $message = '无法修改日记。';
            $message_bool = false;
        }
    } else {
        $message = '无法修改日记，必须输入标题和内容。';
        $message_bool = false;
    }
}

/**
 * 删除记录
 * @since 1
 */
if (isset($_GET['del']) == true) {
    $del_view = $oapost->view($_GET['del']);
    if ($del_view) {
        //删除ID
        if ($oapost->del($del_view['id']) == true) {
            $message = '删除成功。';
            $message_bool = true;
        } else {
            $message = '无法删除该日记。';
            $message_bool = false;
        }
    } else {
        $message = '无法删除该日记。';
        $message_bool = false;
    }
}

/**
 * 获取消息列表记录数
 * @since 1
 */
$table_list_row = $oapost->view_list_row($post_user, null, null, $post_status, $post_type);

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
$table_list = $oapost->view_list($post_user, null, null, $post_status, $post_type, $page, $max, $sort, $desc);
?>
<!-- 管理表格 -->
<h2>工作日记本</h2>
<table class="table table-hover table-bordered table-striped">
    <thead>
        <tr>
            <th><i class="icon-tag"></i> 日记标题</th>
            <th><i class="icon-calendar"></i> 创建时间</th>
            <th><i class="icon-asterisk"></i> 操作</th>
        </tr>
    </thead>
    <tbody id="message_list">
        <?php if ($table_list) {
            foreach ($table_list as $v) { ?>
                <tr>
                    <td><a href="<?php echo $page_url.'&view='.$v['id']; ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
                    <td><?php echo $v['post_date']; ?></td>
                    <td><div class="btn-group"><a href="<?php echo $page_url.'&view='.$v['id']; ?>#view" class="btn"><i class="icon-search"></i> 查看</a><a href="<?php echo $page_url.'&edit='.$v['id']; ?>#edit" role="button" class="btn"><i class="icon-pencil"></i> 编辑</a><a href="<?php echo $page_url.'&del='.$v['id']; ?>" class="btn btn-danger"><i class="icon-trash icon-white"></i> 删除</a></div></td>
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

<?php if (isset($_GET['view']) == false && isset($_GET['edit']) == false) { ?>
<!-- 添加 -->
<h2>添加日记</h2>
<form class="form-horizontal form-actions" action="<?php echo $page_url; ?>" method="post">
    <div class="control-group">
        <label class="control-label" for="add_title">标题</label>
        <div class="controls">
            <input type="text" id="add_title" name="add_title" placeholder="标题">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="add_content">日记内容</label>
        <div class="controls">
            <textarea name="add_content" class="input-xxlarge" rows="10" placeholder="日记内容..."></textarea>
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> 添加</button>
        </div>
    </div>
</form>
<?php } ?>

<?php if (isset($_GET['view']) == true) { $view_res = $oapost->view($_GET['view']); if($view_res){ ?>
<!-- 查看信息 -->
<h2>查看日记</h2>
<div id="view" class="form-actions">
    <p><?php echo $view_res['post_title']; ?> - <?php echo $view_res['post_date']; ?></p>
    <p>&nbsp;</p>
    <p><?php echo $view_res['post_content']; ?></p>
    <p>&nbsp;</p>
    <div class="control-group">
        <div class="controls">
            <a href="<?php echo $page_url.'&edit='.$view_res['id']; ?>#edit" role="button" class="btn"><i class="icon-pencil"></i> 编辑</a>
            <a href="<?php echo $page_url; ?>" role="button" class="btn"><i class="icon-arrow-left"></i> 返回</a>
        </div>
    </div>
</div>
<?php } } ?>

<?php if (isset($_GET['edit']) == true && isset($_GET['view']) == false) {  $view_res = $oapost->view($_GET['edit']); if($view_res){ ?>
<!-- 编辑 -->
<h2>修改日记</h2>
<form class="form-horizontal form-actions" action="<?php echo $page_url.'&view='.$view_res['id']; ?>" method="post">
    <div class="control-group">
        <label class="control-label" for="edit_title">标题</label>
        <div class="controls">
            <input type="text" id="edit_title" name="edit_title" placeholder="标题" value="<?php echo $view_res['post_title']; ?>">
        </div>
        <div class="hidden">
            <input type="text" name="edit_id" value="<?php echo $view_res['id']; ?>">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="edit_content">日记内容</label>
        <div class="controls">
            <textarea name="add_content" class="input-xxlarge" rows="10" placeholder="日记内容..."><?php echo $view_res['post_content']; ?></textarea>
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> 修改</button>
            <a href="<?php echo $page_url.'&view='.$view_res['id']; ?>" role="button" class="btn"><i class="icon-remove"></i> 取消</a>
        </div>
</form>
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

