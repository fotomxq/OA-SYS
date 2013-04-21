<?php
/**
 * 个人短消息中心
 * @author fotomxq <fotomxq.me>
 * @version 4
 * @package oa
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 初始化变量
 * @since 3
 */
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$max = 10;
$sort = 0;
$desc = true;

/**
 * 操作消息内容
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 添加新的消息
 * @since 1
 */
if (isset($_POST['new_message']) == true && isset($_POST['new_name']) == true) {
    $title = '';
    if (isset($_POST['new_title']) == true) {
        $title = $_POST['new_title'];
    } else {
        //引入截取字符串模块
        require(DIR_LIB . DS . 'plug-substrutf8.php');
        $title = plugsubstrutf8($_POST['new_message'], 100);
    }
    $new_user_view = $oauser->view_user_name($_POST['new_name']);
    if ($new_user_view) {
        if ($oapost->add($title, $_POST['new_message'], 'message', 0, $post_user, null, $new_user_view['id'], null, 'private', null)) {
            $message = '消息成功发送！';
            $message_bool = true;
        } else {
            $message = '无法发送消息。';
            $message_bool = false;
        }
    } else {
        $message = '该用户不存在！';
        $message_bool = false;
    }
}

/**
 * 删除消息
 * @since 3
 */
if (isset($_GET['del']) == true) {
    $del_view = $oapost->view($_GET['del']);
    if ($del_view) {
        if ($del_view['post_status'] == 'private' && ($del_view['post_user'] == $post_user || $del_view['post_name'] == $post_user)) {
            if ($oapost->del($_GET['del'])) {
                $message = '删除消息成功！';
                $message_bool = true;
            } else {
                $message = '无法删除该消息，删除失败。';
                $message_bool = false;
            }
        } else {
            $message = '无法删除该消息，该消息不存在。';
            $message_bool = false;
        }
    } else {
        $message = '无法删除该消息，该消息不存在。';
        $message_bool = false;
    }
}

/**
 * 获取消息列表记录数
 * @since 3
 */
$message_list_row = $oapost->view_list_row(null, null, null, 'private', 'message',null,$post_user);

/**
 * 计算页码
 * @since 1
 */
$page_max = ceil($message_list_row / $max);
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
 * @since 3
 */
$message_list = $oapost->view_list(null, null, null, 'private', 'message', $page, $max, $sort, $desc, null, $post_user);
?>
<!-- 管理表格 -->
<h2>短消息中心</h2>
<table class="table table-hover table-bordered table-striped">
    <thead>
        <tr>
            <th><i class="icon-calendar"></i> 时间</th>
            <th><i class="icon-user"></i> 作者</th>
            <th><i class="icon-comment"></i> 消息</th>
            <th><i class="icon-asterisk"></i> 操作</th>
        </tr>
    </thead>
    <tbody id="message_list">
        <?php if($message_list){ foreach($message_list as $v){ ?>
        <tr>
            <td><?php echo $v['post_date']; ?></td>
            <td><?php $message_user = $oauser->view_user($v['post_user']); if($message_user){ echo '<a href="'.$page_url.'&user='.$message_user['id'].'" target="_self">'.$message_user['user_name'].'</a>'; unset($message_user); } ?></td>
            <td><a href="<?php echo $page_url.'&view='.$v['id']; ?>" target="_self"><?php echo $v['post_title']; ?></a></td>
            <td><div class="btn-group"><a href="<?php echo $page_url.'&view='.$v['id']; ?>" class="btn" target="_self"><i class="icon-search"></i> 详情</a><a href="<?php echo $page_url.'&user='.$v['post_user']; ?>" class="btn" target="_self"><i class="icon-envelope"></i> 回复</a><a href="<?php echo $page_url.'&del='.$v['id']; ?>" class="btn btn-danger" target="_self"><i class="icon-trash icon-white"></i> 删除</a></div></td>
        </tr>
        <?php } } ?>
    </tbody>
</table>

<!-- 页码 -->
<ul class="pager">
    <li class="previous<?php if($page<=1){ echo ' disabled'; } ?>">
        <a href="<?php echo $page_url.'&page='.$page_prev; ?>">&larr; 上一页</a>
    </li>
    <li class="next<?php if($page>=$page_max){ echo ' disabled'; } ?>">
        <a href="<?php echo $page_url.'&page='.$page_next; ?>">下一页 &rarr;</a>
    </li>
</ul>

<?php
if (isset($_GET['view']) == false) {
    $send_user = '';
    if(isset($_GET['user']) == true){
        $send_user_view = $oauser->view_user((int)$_GET['user']);
        if($send_user_view){
            $send_user = $send_user_view['user_username'];
        }
    }
    ?>
    <!-- 发布消息 -->
    <h2 id="send">发送消息</h2>
    <form action="<?php echo $page_url; ?>" method="post" class="form-actions">
        <div class="control-group">
            <label class="control-label" for="new_name">接收人</label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-user"></i></span>
                    <input type="text" id="new_name" name="new_name" placeholder="接收人用户名" value="<?php echo $send_user; ?>">
                    <a class="btn" href="init.php?init=6"><i class="icon-user"></i> 通讯录</a>
                </div>
            </div>
            <label class="control-label" for="new_message">消息内容</label>
            <div class="controls">
                <textarea rows="5" id="new_message" name="new_message" placeholder="消息内容……"></textarea>
            </div>
            <div>
                <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> 发送</button>
            </div>
        </div>
    </form>

        <?php
}
        if (isset($_GET['view']) == true) {
            $view_message = $oapost->view($_GET['view']);
            if ($view_message) {
                if($view_message['post_name'] == $post_user){
                ?>
                <!-- 查看消息详情 -->
                <div id="view" class="form-actions">
                    <p><strong><?php echo $view_message['post_title']; ?></strong><em>&nbsp;<?php echo $view_message['post_date']; ?> - <?php $message_user = $oauser->view_user($view_message['post_user']); if($message_user){ echo '<a href="'.$page_url.'&user='.$message_user['id'].'" target="_self">'.$message_user['user_name'].'</a>'; unset($message_user); } ?></em></p>
                    <p>&nbsp;</p>
                    <p>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $view_message['post_content']; ?></p>
                    <p>&nbsp;</p>
                    <p><a href="<?php echo $page_url.'&user='.$view_message['id']; ?>" role="button" class="btn"><i class="icon-envelope"></i> 回复</a><a href="<?php echo $page_url; ?>" role="button" class="btn"><i class="icon-repeat"></i> 返回</a></p>
                </div>
                <?php
                }
            }
        }
        ?>

        <!-- Javascript -->
        <script>
            $(document).ready(function(){
                var message = "<?php echo $message; ?>";
                var message_bool = "<?php echo $message_bool ? '2' : '1'; ?>";
                if(message != ""){
                    msg(message_bool,message,message);
                }
            });
        </script>