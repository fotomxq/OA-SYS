<?php
/**
 * 个人任务中心
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
$post_type = 'task';
$post_status = 'public';
$post_parent = '';
if(isset($_GET['status'])){
    $post_status = null;
}

/**
 * 提示消息变量
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 引入时间处理插件
 * @since 1
 */
require(DIR_LIB . DS . 'plug-date.php');

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
                $message = '无法修改任务。';
                $message_bool = false;
            }
        } else {
            $message = '无法修改任务，找不到该任务。';
            $message_bool = false;
        }
    } else {
        $message = '无法修改任务，您必须输入任务标题和描述。';
        $message_bool = false;
    }
}

/**
 * 改变任务状态
 * @since 1
 */
if (isset($_GET['edit_status']) == true && (isset($_GET['finish']) == true || isset($_GET['trash']) == true)) {
    $edit_status = 'public-ready';
    if(isset($_GET['finish']) == false){
        $edit_status = 'public-trash';
    }
    $edit_view = $oapost->view($_GET['edit_status']);
    if ($edit_view) {
        if ($oapost->edit($edit_view['id'], $edit_view['post_title'], $edit_view['post_content'], $post_type, $edit_view['post_parent'], $edit_view['post_user'], $edit_view['post_password'], $edit_view['post_name'], $edit_view['post_url'], $edit_status, $edit_view['post_meta']) == true) {
            $message = '修改状态成功。';
            $message_bool = true;
        } else {
            $message = '无法修改该任务状态。';
            $message_bool = false;
        }
    }
}

/**
 * 获取任务列表记录数
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
 * 获取任务列表
 * @since 1
 */
$table_list = $oapost->view_list($post_user, null, null, $post_status, $post_type, $page, $max, $sort, $desc, $post_parent);

/**
 * 获取状态标记
 * @since 1
 * @param string $status 状态
 * @return string
 */
function get_tag_status($status) {
    $return = '';
    if ($status === 'public-finish') {
        $return = '&nbsp;&nbsp;<span class="label label-success">审批合格</span>';
    } else if ($status === 'public-ready') {
        $return = '&nbsp;&nbsp;<span class="label label-info">等待审批</span>';
    } else if ($status === 'public-trash') {
        $return = '&nbsp;&nbsp;<span class="label label-inverse">放弃</span>';
    } else if ($status === 'public-fail') {
        $return = '&nbsp;&nbsp;<span class="label label-important">没有完成</span>';
    } else {
        $return = '&nbsp;&nbsp;<span class="label">正在进行中</span>';
    }
    return $return;
}
?>
<!-- 管理表格 -->
<h2>个人计划任务</h2>
<p>
    <a href="<?php echo $page_url; ?>" role="button" class="btn<?php if($post_status=='public'){ echo ' disabled'; } ?>"><i class="icon-list"></i> 查看正在进行</a>
    <a href="<?php echo $page_url; ?>&status=all" role="button" class="btn btn-inverse<?php if($post_status===null){ echo ' disabled'; } ?>"><i class="icon-list-alt icon-white"></i> 查看所有</a>
    <a href="#print_page" target="_self" class="btn"><i class="icon-print"></i> 打印该页</a>
</p>
<table class="table table-hover table-bordered table-striped">
    <thead>
        <tr>
            <th><i class="icon-plane"></i> 任务名称</th>
            <?php if($post_status !== null){ ?>
            <th><i class="icon-calendar"></i> 期限</th>
            <?php }else{ ?>
            <th><i class="icon-adjust"></i> 状态</th>
            <?php } ?>
            <th><i class="icon-asterisk"></i> 操作</th>
        </tr>
    </thead>
    <tbody id="message_list">
        <?php if ($table_list) {
            foreach ($table_list as $v) {
                $v_parent_view = $oapost->view($v['post_parent']);
                if($v_parent_view){
                ?>
                <tr>
                    <td><a href="<?php echo $page_url.'&view='.$v['id'].'#view'; ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
                    <?php if($post_status !== null){ ?>
                    <td><?php echo plugdate_get($v_parent_view['post_name']).' - '.plugdate_get($v_parent_view['post_url']); ?></td>
                    <?php }else{ ?>
                    <td><?php echo get_tag_status($v['post_status']); ?></td>
                    <?php } ?>
                    <td>
                        <div class="btn-group">
                            <a href="<?php echo $page_url.'&view='.$v['id']; ?>" class="btn"><i class="icon-search"></i> 查看详情</a>
                            <?php if($post_status !== null){ ?>
                            <a href="<?php echo $page_url.'&edit_status='.$v['id']; ?>&finish=1" class="btn btn-primary"><i class="icon-ok icon-white"></i> 标记完成</a>
                            <a href="<?php echo $page_url.'&edit_status='.$v['id']; ?>&trash=1" class="btn btn-danger"><i class="icon-remove icon-white"></i> 标记放弃</a>
                            <a href="<?php echo $page_url.'&edit='.$v['id']; ?>" class="btn"><i class="icon-edit"></i> 修改任务描述</a>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
            <?php } } } ?>
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

<?php if (isset($_GET['view']) == true) { $view_res = $oapost->view($_GET['view']); if($view_res){ $view_parent_res = $oapost->view($view_res['post_parent']); if($view_parent_res) { ?>
<!-- 查看信息 -->
<h2>查看任务详情</h2>
<div id="view" class="form-actions">
    <p><?php echo $view_parent_res['post_title'].'&nbsp;&nbsp;'.get_tag_status($view_res['post_status']); ?></p>
    <p>&nbsp;</p>
    <p>任务期限：<?php echo plugdate_get($view_parent_res['post_name']).' - '.plugdate_get($view_parent_res['post_url']); ?></p>
    <p>&nbsp;</p>
    <p>任务描述：<?php echo $view_parent_res['post_content']; ?></p>
    <p>&nbsp;</p>
    <p>个人描述：<?php echo $view_res['post_content']; ?></p>
    <p>&nbsp;</p>
    <div class="control-group">
        <div class="controls">
            <a href="<?php echo $page_url.'&edit='.$view_res['id']; ?>#edit" role="button" class="btn"><i class="icon-pencil"></i> 编辑</a>
            <a href="<?php echo $page_url; ?>" role="button" class="btn"><i class="icon-arrow-left"></i> 返回</a>
        </div>
    </div>
</div>
<?php } } } ?>

<?php if (isset($_GET['edit']) == true && isset($_GET['view']) == false) {  $view_res = $oapost->view($_GET['edit']); if($view_res){ ?>
<!-- 编辑 -->
<h2>修改完成描述</h2>
<form class="form-horizontal form-actions" action="<?php echo $page_url.'&view='.$view_res['id']; ?>" method="post">
    <div class="control-group">
        <label class="control-label" for="edit_title">个人描述标题</label>
        <div class="controls">
            <input type="text" id="edit_title" name="edit_title" placeholder="任务标题" value="<?php echo $view_res['post_title']; ?>">
        </div>
        <div class="hidden">
            <input type="text" name="edit_id" value="<?php echo $view_res['id']; ?>">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="edit_content">个人描述</label>
        <div class="controls">
            <textarea name="edit_content" class="input-xxlarge" rows="10" placeholder="任务描述..."><?php echo $view_res['post_content']; ?></textarea>
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

