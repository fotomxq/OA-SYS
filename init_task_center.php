<?php
/**
 * 任务发布中心
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
$post_status = isset($_GET['status']) ? $_GET['status'] : 'public';
$post_parent = isset($_GET['parent']) ? $_GET['parent'] : '0';
if($post_parent > 0){
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
 * 添加新的记录
 * @since 1
 */
if (isset($_POST['add_title']) == true && isset($_POST['add_content']) == true && isset($_POST['add_date_start']) == true && isset($_POST['add_date_maturity']) == true) {
    if ($_POST['add_title'] && $_POST['add_content']) {
        $post_date_start = plugdate_check($_POST['add_date_start']);
        $post_date_maturity = plugdate_check($_POST['add_date_maturity']);
        if ((int) $post_date_start <= (int) $post_date_maturity) {
            if ($oapost->add($_POST['add_title'], $_POST['add_content'], $post_type, 0, $post_user, null, $post_date_start, $post_date_maturity, 'public', null)) {
                $message = '添加任务成功。';
                $message_bool = true;
            } else {
                $message = '无法添加新的任务。';
                $message_bool = false;
            }
        } else {
            $message = '无法添加新的任务，结束时间必须大于开始时间。';
            $message_bool = false;
        }
    } else {
        $message = '无法添加任务，请正确填写相关信息。';
        $message_bool = false;
    }
}

/**
 * 用户接受任务
 * @since 1
 */
if (isset($_GET['accept']) == true) {
    $accept_view = $oapost->view($_GET['accept']);
    if ($accept_view) {
        if ($accept_view['post_status'] === 'public' && $accept_view['post_parent'] == 0 && (int) $accept_view['post_url'] > (int) date('Ymd')) {
            $accept_parent = $oapost->view_list_row($post_user, null, null, null, $post_type, $accept_view['id']);
            if ($accept_parent < 1) {
                if ($oapost->add($accept_view['post_title'], '无', $post_type, $accept_view['id'], $post_user, null, null, null, 'public', null)) {
                    $message = '添加任务成功。';
                    $message_bool = true;
                } else {
                    $message = '无法添加新的任务。';
                    $message_bool = false;
                }
            } else {
                $message = '您已经接受过该任务了。';
                $message_bool = false;
            }
        } else {
            $message = '该任务已经结束了。';
            $message_bool = false;
        }
    } else {
        $message = '该任务不存在';
        $message_bool = false;
    }
}

/**
 * 修改记录信息
 * @since 1
 */
if (isset($_POST['edit_id']) == true && isset($_POST['edit_title']) == true && isset($_POST['edit_content']) == true && isset($_POST['edit_date_start']) == true && isset($_POST['edit_date_maturity']) == true) {
    if ($_POST['edit_title'] && $_POST['edit_content']) {
        $edit_view = $oapost->view($_POST['edit_id']);
        if ($edit_view) {
            $post_date_start = plugdate_check($_POST['edit_date_start']);
            $post_date_maturity = plugdate_check($_POST['edit_date_maturity']);
            if ((int) $post_date_start <= (int) $post_date_maturity) {
                if ($oapost->edit($_POST['edit_id'], $_POST['edit_title'], $_POST['edit_content'], $post_type, $edit_view['post_parent'], $edit_view['post_user'], $edit_view['post_password'], $post_date_start, $post_date_maturity, $edit_view['post_status'], $edit_view['post_meta']) == true) {
                    $message = '修改成功！';
                    $message_bool = true;
                } else {
                    $message = '无法修改任务。';
                    $message_bool = false;
                }
            } else {
                $message = '无法修改任务，结束时间必须大于开始时间';
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
 * 完结任务
 * @since 1
 */
if (isset($_GET['edit_finish']) == true) {
    $edit_finish_boolean = false;
    $edit_finish_parent_ready_view = $oapost->view_list_row(null, null, null, 'public-ready', $post_type, $_GET['edit_finish']);
    $edit_finish_parent_view = $oapost->view_list_row(null, null, null, 'public', $post_type, $_GET['edit_finish']);
    if ($edit_finish_parent_ready_view > 0 || $edit_finish_parent_view > 0) {
        $edit_finish_boolean = false;
    } else {
        $edit_finish_boolean = true;
    }
    $edit_view = $oapost->view($_GET['edit_finish']);
    if ($edit_view) {
        if ($edit_finish_boolean) {
            if ($oapost->edit($edit_view['id'], $edit_view['post_title'], $edit_view['post_content'], $post_type, $edit_view['post_parent'], $edit_view['post_user'], $edit_view['post_password'], $edit_view['post_name'], $edit_view['post_url'], 'public-finish', $edit_view['post_meta']) == true) {
                $message = '成功完结了该任务。';
                $message_bool = true;
            } else {
                $message = '无法设定为完结任务。';
                $message_bool = false;
            }
        } else {
            $message = '无法设定为完结任务，该任务下的职员完成状况尚未完全审批。';
            $message_bool = false;
        }
    }
}

/**
 * 审批任务
 * @since 1
 */
if ($post_parent > 0 && isset($_GET['parent_view']) == true && isset($_GET['edit_status']) == true) {
    $edit_view = $oapost->view($_GET['parent_view']);
    if ($edit_view) {
        if ($oapost->edit($_GET['parent_view'], $edit_view['post_title'], $edit_view['post_content'], $post_type, $edit_view['post_parent'], $edit_view['post_user'], $edit_view['post_password'], $edit_view['post_name'], $edit_view['post_url'], $_GET['edit_status'], $edit_view['post_meta']) == true) {
            $message = '审批成功。';
            $message_bool = true;
        } else {
            $message = '无法审批该任务。';
            $message_bool = false;
        }
    }
}

/**
 * 设定业绩
 * @since 1
 */
if ($post_parent > 0 && isset($_GET['parent_view']) == true && isset($_POST['set_results']) == true) {
    $results_task_view = $oapost->view($_GET['parent_view']);
    if ($results_task_view) {
        $results_view = $oapost->view_list(null, null, null, null, 'performance', 1, 10, 0, false, $_GET['parent_view']);
        if ($results_view) {
            if ($oapost->edit($results_view[0]['id'], $results_view[0]['post_title'], '', 'performance', $results_view[0]['post_parent'], $results_view[0]['post_user'], $results_view[0]['post_password'], $results_view[0]['post_name'], $_POST['set_results'], $results_view[0]['post_status'], $results_view[0]['post_meta']) == true) {
                $message = '设定业绩量成功。';
                $message_bool = true;
            } else {
                $message = '无法设定业绩量。';
                $message_bool = false;
            }
        } else {
            if ($oapost->add($results_task_view['post_title'], '无', 'performance', $results_task_view['id'], $results_task_view['post_user'], null, $post_user, $_POST['set_results'], 'private', null)) {
                $message = '设定业绩量成功。';
                $message_bool = true;
            } else {
                $message = '无法设定业绩量。';
                $message_bool = false;
            }
        }
    }
}

/**
 * 删除记录
 * @since 1
 */
if (isset($_GET['del']) == true) {
    $del_view = $oapost->view($_GET['del']);
    if ($del_view) {
        if ($del_view['post_status'] == 'public-trash') {
            //删除ID
            if ($oapost->del($del_view['id']) == true) {
                $message = '删除成功。';
                $message_bool = true;
            } else {
                $message = '无法彻底删除该任务。';
                $message_bool = false;
            }
        } else {
            if ($oapost->edit($del_view['id'], $del_view['post_title'], $del_view['post_content'], $post_type, $del_view['post_parent'], $del_view['post_user'], $del_view['post_password'], $del_view['post_name'], $del_view['post_url'], 'public-trash', $del_view['post_meta']) == true) {
                $message = '删除成功。';
                $message_bool = true;
            } else {
                $message = '无法删除该任务。';
                $message_bool = false;
            }
        }
    } else {
        $message = '无法删除该任务。';
        $message_bool = false;
    }
}

/**
 * 获取列表记录数
 * @since 1
 */
$table_list_row = $oapost->view_list_row(null, null, null, $post_status, $post_type, $post_parent);

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
$table_list = $oapost->view_list(null, null, null, $post_status, $post_type, $page, $max, $sort, $desc, $post_parent);

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
<h2>生产任务中心</h2>
<p>
    <a href="<?php echo $page_url; ?>&status=public" role="button" class="btn<?php if($post_status=='public'){ echo ' disabled'; } ?>"><i class="icon-list"></i> 查看任务列表</a>
    <a href="<?php echo $page_url; ?>&status=public-finish" role="button" class="btn btn-success<?php if($post_status=='public-finish'){ echo ' disabled'; } ?>"><i class="icon-thumbs-up icon-white"></i> 查看已完结的任务</a>
    <a href="<?php echo $page_url; ?>&status=public-trash" role="button" class="btn btn-danger<?php if($post_status=='public-trash'){ echo ' disabled'; } ?>"><i class="icon-trash icon-white"></i> 查看已删除的任务</a>
    <a href="#print_page" target="_self" class="btn"><i class="icon-print"></i> 打印该页</a>
</p>
<table class="table table-hover table-bordered table-striped">
    <thead>
        <tr>
            <th><i class="icon-tag"></i> 任务名称</th>
            <?php if($post_parent == 0){ ?>
            <th><i class="icon-calendar"></i> 开始时间</th>
            <th><i class="icon-calendar"></i> 到期时间</th>
            <?php }else{ ?>
            <th><i class="icon-user"></i> 用户</th>
            <th><i class="icon-adjust"></i> 状态</th>
            <?php } ?>
            <th><i class="icon-asterisk"></i> 操作</th>
        </tr>
    </thead>
    <tbody id="message_list">
        <?php if ($table_list) {
            foreach ($table_list as $v) { ?>
                <tr class="<?php
                    if((int)$v['post_url'] < (int)date('Ymd') && (int)$v['post_url'] > 0 && $post_status=='public' && $post_parent == 0){
                        echo 'warning';
                    }
                    if($post_parent > 0){
                        if($v['post_status'] === 'public-ready'){
                            echo 'warning';
                        }elseif($v['post_status'] === 'public-fail'){
                            echo 'error';
                        }elseif($v['post_status'] === 'public-finish'){
                            echo 'success';
                        }
                    }
                    ?>">
                    <td><a href="<?php if($post_parent == 0){ echo $page_url.'&view='.$v['id'].'#view'; }else{ echo $page_url.'&parent='.$post_parent.'&parent_view='.$v['id'].'#parent'; } ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
                    <?php if($post_parent == 0){ ?>
                    <td><?php echo plugdate_get($v['post_name']); ?></td>
                    <td><?php echo plugdate_get($v['post_url']); ?></td>
                    <?php }else{ ?>
                    <td><?php $v_user = $oauser->view_user($v['post_user']); if($v_user){ echo $v_user['user_name']; unset($v_user); } ?></td>
                    <td><?php echo get_tag_status($v['post_status']); ?></td>
                    <?php } ?>
                    <td>
                        <div class="btn-group">
                            <a href="<?php if($post_parent == 0){ echo $page_url.'&view='.$v['id'].'#view'; }else{ echo $page_url.'&parent='.$post_parent.'&parent_view='.$v['id'].'#parent'; } ?>" class="btn"><i class="icon-search"></i> 查看详情</a>
                            <?php if($post_status==='public' && $post_parent == 0){ ?>
                            <a href="<?php echo $page_url.'&accept='.$v['id']; ?>" role="button" class="btn btn-inverse"><i class="icon-plane icon-white"></i> 接受该任务</a>
                            <?php } ?>
                                <?php if($logged_admin){ ?><?php if($post_status==='public' && $post_parent == 0){ ?>
                            <a href="<?php echo $page_url.'&edit_finish='.$v['id']; ?>" class="btn btn-success"><i class="icon-thumbs-up icon-white"></i> 完结该任务</a>
                            <a href="<?php echo $page_url.'&parent='.$v['id']; ?>" class="btn btn-info"><i class="icon-check icon-white"></i> 审批该任务</a>
                                <?php } if($post_parent == 0){ ?>
                            <a href="<?php echo $page_url.'&edit='.$v['id']; ?>#edit" role="button" class="btn"><i class="icon-pencil"></i> 编辑</a>
                            <a href="<?php echo $page_url.'&del='.$v['id']; ?>" class="btn btn-danger"><i class="icon-trash icon-white"></i> 删除</a>
                                <?php } } ?>
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

<?php if (isset($_GET['view']) == false && isset($_GET['edit']) == false && $post_parent == 0) { ?>
<!-- 添加 -->
<h2>添加新的生产任务</h2>
<p>添加新的生产任务指标，提供给职工接收。职工完成任务后需要管理员审批以确定是否合格。</p>
<form class="form-horizontal form-actions" action="<?php echo $page_url; ?>" method="post">
    <div class="control-group">
        <label class="control-label" for="add_title">任务名称</label>
        <div class="controls">
            <input type="text" id="add_title" name="add_title" placeholder="标题">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="add_content">任务描述</label>
        <div class="controls">
            <textarea name="add_content" class="input-xxlarge" rows="10" placeholder="任务描述..."></textarea>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="add_date_start">开始日期</label>
        <div class="controls">
            <input type="text" id="add_date_start" name="add_date_start" placeholder="YYYY-MM-DD">
            <a href="#date_now_button" role="button" class="btn"><i class="icon-time"></i> 选择今天</a>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="add_date_maturity">结束日期</label>
        <div class="controls">
            <input type="text" id="add_date_maturity" name="add_date_maturity" placeholder="YYYY-MM-DD">
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> 添加</button>
            <a href="<?php echo $page_url; ?>" role="button" class="btn"><i class="icon-arrow-left"></i> 返回</a>
        </div>
    </div>
</form>
<?php } ?>

<?php if (isset($_GET['view']) == true && $post_parent==0) { $view_res = $oapost->view($_GET['view']); if($view_res){ ?>
<!-- 查看信息 -->
<h2>查看生产任务详情</h2>
<div id="view" class="form-actions">
    <p><?php echo $view_res['post_title']; ?><?php if((int)$view_res['post_url'] < (int)date('Ymd') && (int)$view_res['post_url'] > 0){ echo ' - 已过期'; }?></p>
    <p>&nbsp;</p>
    <p>开始日期：<?php echo plugdate_get($view_res['post_name']); ?></p>
    <p>&nbsp;</p>
    <p>结束日期：<?php echo plugdate_get($view_res['post_url']); ?></p>
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

<?php if (isset($_GET['edit']) == true && isset($_GET['view']) == false && $post_parent==0) {  $view_res = $oapost->view($_GET['edit']); if($view_res){ ?>
<!-- 编辑 -->
<h2>修改生产任务</h2>
<form class="form-horizontal form-actions" action="<?php echo $page_url.'&view='.$view_res['id']; ?>" method="post">
    <div class="control-group">
        <label class="control-label" for="edit_title">任务标题</label>
        <div class="controls">
            <input type="text" id="edit_title" name="edit_title" placeholder="任务标题" value="<?php echo $view_res['post_title']; ?>">
        </div>
        <div class="hidden">
            <input type="text" name="edit_id" value="<?php echo $view_res['id']; ?>">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="edit_content">任务描述</label>
        <div class="controls">
            <textarea name="edit_content" class="input-xxlarge" rows="10" placeholder="任务描述..."><?php echo $view_res['post_content']; ?></textarea>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="edit_date_start">开始日期</label>
        <div class="controls">
            <input type="text" id="edit_date_start" name="edit_date_start" placeholder="YYYY-MM-DD" value="<?php echo plugdate_get($view_res['post_name']); ?>">
            <a href="#date_now_button" role="button" class="btn"><i class="icon-time"></i> 选择今天</a>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="edit_date_maturity">结束日期</label>
        <div class="controls">
            <input type="text" id="edit_date_maturity" name="edit_date_maturity" placeholder="YYYY-MM-DD" value="<?php echo plugdate_get($view_res['post_url']); ?>">
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> 修改</button>
            <a href="<?php echo $page_url.'&view='.$view_res['id']; ?>" role="button" class="btn"><i class="icon-remove"></i> 取消</a>
        </div>
</form>
<?php } } ?>

<?php
if ($post_parent != 0 && isset($_GET['parent_view']) == true) {
    $view_res = $oapost->view($_GET['parent_view']);
    if ($view_res) {
        $view_results_view = $oapost->view_list(null, null, null, 'private', 'performance', 1, 5, 0, false, $view_res['id']);
        ?>
<!-- 审批任务 -->
<h2>审批生产任务完成状况</h2>
<div id="parent" class="form-actions">
    <p>
                <?php
                echo $view_res['post_title'];
                if ((int) $view_res['post_url'] < (int) date('Ymd') && (int) $view_res['post_url'] > 0) {
                    echo ' - 已过期';
                }
                if ($view_res['post_status'] === 'public-finish') {
                    echo '&nbsp;&nbsp;<span class="label label-success">审批合格</span>';
                } else if ($view_res['post_status'] === 'public-ready') {
                    echo '&nbsp;&nbsp;<span class="label label-info">等待审批</span>';
                } else if ($view_res['post_status'] === 'public-trash') {
                    echo '&nbsp;&nbsp;<span class="label label-inverse">放弃</span>';
                } else if ($view_res['post_status'] === 'public-fail') {
                    echo '&nbsp;&nbsp;<span class="label label-important">没有完成</span>';
                } else {
                    echo '&nbsp;&nbsp;<span class="label">正在进行中</span>';
                }
                ?>
    </p>
    <p>&nbsp;</p>
    <p>所属用户：
        <?php
        $view_user = $oauser->view_user($view_res['post_user']);
        if($view_user){
            echo $view_user['user_name'];
        } 
        ?>
    </p>
    <p>&nbsp;</p>
    <p>个人说明：<?php echo $view_res['post_content']; ?></p>
    <p>&nbsp;</p>
    <form class="form-horizontal form-actions" action="<?php echo $page_url.'&parent='.$post_parent.'&parent_view='.$view_res['id']; ?>" method="post">
        <div class="control-group">
            <label class="control-label" for="set_results">给予业绩量</label>
            <div class="controls">
                <input type="text" class="input-small" id="set_results" name="set_results" placeholder="0" value="<?php if(isset($view_results_view[0]['post_url'])){ echo $view_results_view[0]['post_url']; } ?>">
                <button type="submit" class="btn btn-primary"><i class="icon-signal icon-white"></i> 设定</button>
            </div>
            <div class="controls">
                <p>负值可以减少该职员业绩量</p>
            </div>
        </div>
    </form>
    <div class="control-group">
        <div class="controls">
            <a href="<?php echo $page_url.'&parent='.$post_parent.'&parent_view='.$view_res['id']; ?>&edit_status=public-finish#parent" role="button" class="btn"><i class="icon-thumbs-up"></i> 批准完成</a>
            <a href="<?php echo $page_url.'&parent='.$post_parent.'&parent_view='.$view_res['id']; ?>&edit_status=public-fail#parent" role="button" class="btn"><i class="icon-thumbs-down"></i> 设定为不合格</a>
            <a href="init.php?init=1&user=<?php echo $view_res['post_user']; ?>#send" role="button" class="btn"><i class="icon-envelope"></i> 发送消息</a>
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
        /** 日历插件
        $( "#add_date_start" ).datepicker({
            altFormat:"yy-mm-dd",
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
                $( "#add_date_maturity" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        $( "#add_date_maturity" ).datepicker({
            altFormat:"yy-mm-dd",
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
                $( "#add_date_start" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
        */
       //时间选择今天按钮
       $("a[href='#date_now_button']").click(function(){
           $(this).prev().attr("value","<?php echo date('Y-m-d'); ?>");
       });
    });
</script>

