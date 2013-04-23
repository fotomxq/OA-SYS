<?php
/**
 * 通讯录页面
 * @author fotomxq <fotomxq.me>
 * @version 5
 * @package oa
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 初始化变量
 * @since 5
 */
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$max = 20;
$sort = 0;
$desc = true;

/**
 * 操作消息内容
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 添加新的联系人
 * @since 1
 */
if (isset($_POST['edit_id']) == false && isset($_POST['new_title']) == true) {
    $post_name = null;
    if (isset($_POST['new_name']) == true) {
        $post_user_view = $oauser->view_user_name($_POST['new_name']);
        if ($post_user_view) {
            if ($post_user_view['id'] != $post_user) {
                $post_name = $post_user_view['id'];
            } else {
                $message = '不能添加当前用户自身！';
                $message_bool = false;
            }
        } else {
            $message = '该用户不存在！';
            $message_bool = false;
        }
    }
    if (!$message) {
        if ($oapost->add($_POST['new_title'], '', 'addressbook', 0, $post_user, null, $post_name, null, 'public', null)) {
            $message = '添加联系人成功！';
            $message_bool = true;
        } else {
            $message = '无法添加新的联系人。';
            $message_bool = false;
        }
    }
}

/**
 * 添加新的联系人信息
 * @since 1
 */
if (isset($_POST['edit_id']) == true && isset($_POST['new_title']) == true && isset($_POST['new_content']) == true) {
    if ($_POST['new_title'] && $_POST['new_content']) {
        if ($oapost->add($_POST['new_title'], $_POST['new_content'], 'addressbook', $_POST['edit_id'], $post_user, null, null, null, 'public', null)) {
            $message = '';
        } else {
            $message = '无法添加新的联系信息。';
            $message_bool = false;
        }
    }
}

/**
 * 编辑联系人或子信息
 * @since 1
 */
if (isset($_POST['edit_id']) == true && isset($_POST['edit_title']) == true) {
    $post_content = '';
    if (isset($_POST['edit_content']) == true) {
        $_POST['edit_content'] = $post_content;
    }
    $post_parent = 0;
    if (isset($_POST['edit_parent']) == true) {
        $_POST['edit_parent'] = $post_parent;
    }
    $post_name = null;
    if (isset($_POST['edit_name']) == true) {
        $post_user_view = $oauser->view_user_name($_POST['edit_name']);
        if ($post_user_view) {
            if ($post_user_view['id'] != $post_user) {
                $post_name = $post_user_view['id'];
            } else {
                $message = '不能改为当前用户！';
                $message_bool = false;
            }
        } else {
            $message = '该用户不存在！';
            $message_bool = false;
        }
    }
    if (!$message) {
        if ($oapost->edit($_POST['edit_id'], $_POST['edit_title'], $post_content, 'addressbook', $post_parent, $post_user, null, $post_name, null, 'public', null)) {
            $message = '编辑联系人信息成功！';
            $message_bool = true;
        } else {
            $message = '无法编辑联系人信息，请稍候重试。';
            $message_bool = false;
        }
    }
}

/**
 * 删除联系人或子信息
 * @since 1
 */
if (isset($_GET['del']) == true) {
    $del_view = $oapost->view($_GET['del']);
    if ($del_view) {
        if ($del_view['post_user'] == $post_user) {
            if ($oapost->del_parent($_GET['del'])) {
                $message = '删除联系人成功！';
                $message_bool = true;
            } else {
                $message = '无法删除该联系人。';
                $message_bool = false;
            }
        }
    }
}

/**
 * 获取消息列表记录数
 * @since 1
 */
$table_list_row = $oapost->view_list_row($post_user, null, null, 'public', 'addressbook', 0);

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
$table_list = $oapost->view_list($post_user, null, null, 'public', 'addressbook', $page, $max, $sort, $desc, 0);
?>
<!-- 管理表格 -->
<h2>通讯录</h2>
<p><a href="#print_page" target="_self" class="btn"><i class="icon-print"></i> 打印该页</a></p>
<table class="table table-hover table-bordered table-striped">
    <thead>
        <tr>
            <th><i class="icon-user"></i> 联系人</th>
            <th><i class="icon-asterisk"></i> 操作</th>
        </tr>
    </thead>
    <tbody id="message_list">
        <?php if ($table_list) {
            foreach ($table_list as $v) { ?>
                <tr>
                    <td><a href="<?php echo $page_url.'&view='.$v['id']; ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
                    <td><div class="btn-group"><a href="<?php echo $page_url.'&view='.$v['id']; ?>#view" class="btn"><i class="icon-search"></i> 详情</a><?php if($v['post_name']){ ?><a href="init.php?init=1&user=<?php echo $v['post_name']; ?>#send" role="button" class="btn"><i class="icon-envelope"></i> 发送消息</a><?php } ?><a href="<?php echo $page_url.'&edit='.$v['id']; ?>#edit" role="button" class="btn"><i class="icon-pencil"></i> 编辑</a><a href="<?php echo $page_url.'&del='.$v['id']; ?>" class="btn btn-danger"><i class="icon-trash icon-white"></i> 删除</a></div></td>
                </tr>
    <?php }
} ?>
    </tbody>
</table>

<!-- 页码 -->
<ul class="pager">
    <li class="previous<?php if ($page <= 1) {
    echo ' disabled';
} ?>">
        <a href="<?php echo $page_url . '&page=' . $page_prev; ?>">&larr; 上一页</a>
    </li>
    <li class="next<?php if ($page >= $page_max) {
    echo ' disabled';
} ?>">
        <a href="<?php echo $page_url.'&page='.$page_next; ?>">下一页 &rarr;</a>
    </li>
</ul>

<?php
if (isset($_GET['edit']) == false && isset($_GET['view']) == false) {
    ?>
    <!-- 添加新的联系人 -->
    <h2>添加新的联系人</h2>
    <form action="<?php echo $page_url; ?>" method="post" class="form-actions">
        <div class="control-group">
            <label class="control-label" for="new_title">联系人的姓名</label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-tag"></i></span>
                    <input type="text" id="new_title" name="new_title" placeholder="联系人姓名">
                </div>
            </div>
            <label class="control-label" for="new_name">联系人的用户名(可选)</label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-user"></i></span>
                    <input type="text" id="new_name" name="new_name" placeholder="对方用户名">
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> 添加</button>
            </div>
        </div>
    </form>

    <?php
}
if (isset($_GET['edit']) == true && isset($_GET['view']) == false) {
    $edit_res = $oapost->view($_GET['edit']);
    if ($edit_res) {
        $edit_user = null;
        if($edit_res['post_name']){
            $edit_user = $oauser->view_user($edit_res['post_name']);
        }
        $edit_childrens = $oapost->view_list($post_user, null, null, 'public', 'addressbook', 1, 9999, 0, false, $edit_res['id']);
        ?>
        <!-- 编辑联系人 -->
        <div id="edit">
            <h2>编辑联系人</h2>
            <form action="<?php echo $page_url.'&view='.$edit_res['id']; ?>" method="post" class="form-actions">
                <div class="control-group">
                    <div class="controls hide">
                        <input type="text" id="edit_id" name="edit_id" value="<?php echo $edit_res['id']; ?>">
                    </div>
                    <label class="control-label" for="edit_title">联系人姓名</label>
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-tag"></i></span>
                            <input type="text" id="edit_title" name="edit_title" placeholder="联系人姓名" value="<?php echo htmlentities($edit_res['post_title']); ?>">
                        </div>
                    </div>
                    <label class="control-label" for="edit_name">联系人用户名(可选)</label>
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-user"></i></span>
                            <input type="text" id="edit_name" name="edit_name" placeholder="联系人用户名" value="<?php if($edit_user){ echo $edit_user['user_username']; } ?>">
                        </div>
                    </div>
                    <?php if($edit_childrens){ foreach($edit_childrens as $v){ ?>
                    <label class="control-label" for="edit_x_<?php echo $v['id']; ?>"><?php echo htmlentities($v['post_title']); ?></label>
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-tag"></i></span>
                            <input type="text" id="edit_x_<?php echo $v['id']; ?>" name="edit_x_<?php echo $v['id']; ?>" placeholder="<?php echo $v['post_title']; ?>" value="<?php $v_c = $oapost->view($v['id']); if($v_c){ echo htmlentities($v_c['post_content']); unset($v_c); } ?>">
                            <a class="btn btn-danger" href="<?php echo $page_url.'&edit='.$edit_res['id'].'&del='.$v['id'].'#edit'; ?>"><i class="icon-remove icon-white"></i>删除</a>
                        </div>
                    </div>
                    <?php } } ?>
                    <label class="control-label" for="new_title">添加一条联系信息(可选)</label>
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-flag"></i></span>
                            <input type="text" id="new_title" name="new_title" placeholder="名称" value="">
                        </div>
                    </div>
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-tag"></i></span>
                            <input type="text" id="new_content" name="new_content" placeholder="联系方式" value="">
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> 修改</button>
                        <a href="<?php echo $page_url; ?>" role="button" class="btn"><i class="icon-remove"></i> 取消</a>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}
if (isset($_GET['view']) == true) {
    $view_res = $oapost->view($_GET['view']);
    if ($view_res) {
        $view_user = null;
        if($view_res['post_name']){
            $view_user = $oauser->view_user($view_res['post_name']);
        }
        $view_childrens = $oapost->view_list($post_user, null, null, 'public', 'addressbook', 1, 9999, 0, false, $view_res['id']);
        ?>
        <!-- 查看联系人信息 -->
        <h2>查看联系人详情</h2>
        <div id="view" class="form-actions">
            <p><b>姓名</b>&nbsp;&nbsp;<?php echo $view_res['post_title']; ?></p>
            <p>&nbsp;</p>
            <p><b>所属用户</b>&nbsp;&nbsp;<?php if($view_user){ echo $view_user['user_username']; }else{ echo '无'; } ?></p>
            <p>&nbsp;</p>
            <?php if($view_childrens){ foreach($view_childrens as $k=>$v){ ?>
            <p><b><?php echo $v['post_title']; ?></b>&nbsp;&nbsp;<?php $v_c = $oapost->view($v['id']); if($v_c){ echo $v_c['post_content']; unset($v_c); } ?></p>
            <p>&nbsp;</p>
            <?php } }else{ ?>
            <p>没有找到任何信息，请尝试编辑该联系人。</p>
            <p>&nbsp;</p>
            <?php } ?>
            <p><a href="<?php echo $page_url; ?>&edit=<?php echo $view_res['id']; ?>#edit" role="button" class="btn"><i class="icon-pencil"></i> 编辑</a><a href="<?php echo $page_url; ?>" role="button" class="btn"><i class="icon-arrow-left"></i> 返回</a></p>
        </div>
        <?php
    }
}
?>

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