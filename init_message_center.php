<?php
/**
 * 消息中心页面
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package oa
 */
/**
 * 引入post类
 */
require(DIR_LIB . DS . 'oa-post.php');

/**
 * 创建post实例
 */
$oapost = new oapost($db, $ip_arr['id']);

/**
 * 操作消息内容
 */
$message = '';

/**
 * 添加新的消息
 */
if (isset($_POST['new_message']) == true) {
    $title = '';
    if (isset($_POST['new_title']) == true) {
        $title = $_POST['new_title'];
    } else {
        //引入截取字符串模块
        require(DIR_LIB . DS . 'plug-substrutf8.php');
        $title = plugsubstrutf8($_POST['new_message'], 100);
    }
    if($oapost->add($title, $_POST['new_message'], 'message', 0, $oauser->get_session_login(), null, null, null, 'public', null)){
        $message = '添加通知成功！';
    }else{
        $message = '无法添加新的通知。';
    }
}

/**
 * 编辑消息
 */
if (isset($_POST['edit_id']) == true && isset($_POST['edit_message']) == true) {
    $title = '';
    if (isset($_POST['edit_title']) == true) {
        $title = $_POST['edit_title'];
    } else {
        //引入截取字符串模块
        require(DIR_LIB . DS . 'plug-substrutf8.php');
        $title = plugsubstrutf8($_POST['edit_message'], 100);
    }
    if($oapost->edit($_POST['edit_id'], $title, $_POST['edit_message'], 'message', 0, $oauser->get_session_login(), null, null, null, 'public', null)){
        $message = '编辑通知成功！';
    }else{
        $message = '无法修改通知，请稍候重试。';
    }
}

/**
 * 删除消息
 */
if (isset($_GET['del']) == true) {
    if($oapost->del($_GET['del'])){
        $message = '删除通知成功！';
    }else{
        $message = '无法删除该通知。';
    }
}

/**
 * 初始化变量
 */
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$max = 10;
$sort = 0;
$desc = true;
$post_user = null;
if (isset($_GET['user']) == true) {
    $post_user = $_GET['user'];
}

/**
 * 获取消息列表记录数
 */
$message_list_row = $oapost->view_list_row($post_user, null, null, 'public', 'message');

/**
 * 计算页码
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
 */
$message_list = $oapost->view_list($post_user, null, null, 'public', 'message', $page, $max, $sort, $desc);
?>
<!-- 管理表格 -->
<h2>系统消息管理中心</h2>
<table class="table table-hover table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>作者</th>
            <th>发表时间</th>
            <th>消息内容</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody id="message_list">
        <?php if($message_list){ foreach($message_list as $v){ ?>
        <tr>
            <td><?php echo $v['id']; ?></td>
            <td><?php $message_user = $oauser->view_user($v['post_user']); if($message_user){ echo '<a href="init.php?init=11&user='.$message_user['id'].'" target="_self">'.$message_user['user_name'].'</a>'; unset($message_user); } ?></td>
            <td><?php echo $v['post_date']; ?></td>
            <td><?php echo $v['post_title']; ?></td>
            <td><div class="btn-group"><a href="init.php?init=11&edit=<?php echo $v['id']; ?>#edit" role="button" class="btn">编辑</a><a href="init.php?init=11&del=<?php echo $v['id']; ?>" class="btn btn-danger">删除</a></div></td>
        </tr>
        <?php } } ?>
    </tbody>
</table>

<!-- 页码 -->
<ul class="pager">
    <li class="previous<?php if($page<=1){ echo ' disabled'; } ?>">
        <a href="init.php?init=11&page=<?php echo $page_prev; ?>">&larr; 上一页</a>
    </li>
    <li class="next<?php if($page>=$page_max){ echo ' disabled'; } ?>">
        <a href="init.php?init=11&page=<?php echo $page_next; ?>">下一页 &rarr;</a>
    </li>
</ul>

<?php
if (isset($_GET['edit']) == false) {
    ?>
    <!-- 发布新通知 -->
    <h2>发布新的系统通知</h2>
    <p>所有用户均会收到该通知。</p>
    <form action="init.php?init=11" method="post" class="form-actions">
        <div class="control-group">
            <label class="control-label" for="new_message">系统通知内容</label>
            <div class="controls">
                <input type="text" id="add_title" name="add_title" placeholder="标题(可留空)">
            </div>
            <div class="controls">
                <textarea rows="5" id="new_message" name="new_message" placeholder="系统通知内容……"></textarea>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">发布</button>
            </div>
        </div>
    </form>

        <?php
}
if (isset($_GET['edit']) == true) {
    $edit_message = $oapost->view($_GET['edit']);
    if ($edit_message) {
        ?>
        <!-- 编辑通知 -->
        <div id="edit">
            <h2>编辑系统通知</h2>
            <p>编辑系统通知。</p>
            <form action="init.php?init=11" method="post" class="form-actions">
                <div class="control-group">
                    <div class="controls hide">
                        <input type="text" id="edit_id" name="edit_id" value="<?php echo $edit_message['id']; ?>">
                    </div>
                    <label class="control-label" for="edit_title">系统通知内容</label>
                    <div class="controls">
                        <input type="text" id="edit_title" name="edit_title" placeholder="标题(可留空)" value="<?php echo $edit_message['post_title']; ?>">
                    </div>
                    <div class="controls">
                        <textarea rows="5" id="edit_message" name="edit_message" placeholder="系统通知内容……"><?php echo $edit_message['post_content']; ?></textarea>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">修改</button>
                        <a href="init.php?init=11" role="button" class="btn" class="btn btn-primary">取消</a>
                    </div>
                </div>
            </form>
        </div>
    <?php
    }
}
?>