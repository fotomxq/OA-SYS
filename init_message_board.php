<?php
/**
 * 留言薄页面
 * @author fotomxq <fotomxq.me>
 * @version 2
 * @package oa
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 初始化变量
 * @since 2
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
 * 初始化post_parent
 * @since 1
 */
$post_parent = 0;
if (isset($_GET['parent']) == true) {
    if ($_GET['parent'] > 0) {
        $new_parent_view = $oapost->view($_GET['parent']);
        if ($new_parent_view) {
            if ($new_parent_view['post_type'] == 'messageboard') {
                $post_parent = $new_parent_view['id'];
            }
        } else {
            $message = '您回复的留言不存在！';
            $message_bool = false;
        }
    }
}

/**
 * 添加新的留言
 * @since 1
 */
if (isset($_POST['new_content']) == true) {
    $str_len = strlen($_POST['new_content']);
    if ($str_len > 0 && $str_len < 500) {
        if ($oapost->add('', $_POST['new_content'], 'messageboard', $post_parent, $post_user, null, null, null, 'public', null)) {
            $message = '留言发布成功！';
            $message_bool = true;
        } else {
            $message = '无法发表该留言。';
            $message_bool = false;
        }
    } else {
        $message = '留言不能为空，或不能超过500字。';
        $message_bool = false;
    }
}

/**
 * 删除留言
 * @since 1
 */
if (isset($_GET['del']) == true && $logged_admin == true) {
    if ($oapost->del($_GET['del'])) {
        $message = '删除留言成功！';
        $message_bool = true;
    } else {
        $message = '无法删除该留言。';
        $message_bool = false;
    }
}

/**
 * 获取列表记录数
 * @since 1
 */
$table_list_row = $oapost->view_list_row(null, null, null, 'public', 'messageboard', 0);

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
$message_list = $oapost->view_list(null, null, null, 'public', 'messageboard', $page, $max, $sort, $desc, 0);

/**
 * 留言回复递归
 * @since 1
 */
$echo_parent = '';
function view_parent($id){
    global $logged_admin,$page_url,$echo_parent,$oapost,$oauser;
    $view_p = $oapost->view_list(null, null, null, 'public', 'messageboard', 1, 9999, 0, true, $id);
    if($view_p){
        foreach($view_p as $vr){
            $echo_parent .= '<div class="media"><a class="pull-left" href="'.$page_url.'&parent='.$vr['id'].'"><img class="media-object" data-src="holder.js/64x64" src="includes/images/logo.png"></a><div class="media-body"><h4 class="media-heading">';
            $v_user = $oauser->view_user($vr['post_user']);
            $v_user_name = '';
            if($v_user){
                $v_user_name = $v_user['user_username'];
            }
            $echo_parent .= $vr['id'].'&nbsp;'.$v_user_name.'</h4>';
            unset($v_user,$v_user_name);
            $v_view = $oapost->view($vr['id']);
            $v_content = '';
            if($v_view){
                $v_content = $v_view['post_content'];
            }
            $echo_parent .= $v_content.'&nbsp;<span>-&nbsp;<a href="'.$page_url.'&parent='.$vr['id'].'#nmd" target="_self">回复</a>';
            if($logged_admin == true){
                $echo_parent .= '&nbsp;<a href="'.$page_url.'&del='.$vr['id'].'" target="_self">删除</a>';
            }
            unset($v_view,$v_content);
            $echo_parent .= '</span>';
            view_parent($vr['id']);
            $echo_parent .= '</div></div>';
        }
    }
    unset($view_p);
}
?>
<!-- 管理表格 -->
<h2>留言薄</h2>
<ul class="media-list">
    <?php if($message_list){ foreach($message_list as $v){ ?>
    <li class="media well">
        <a class="pull-left" href="<?php echo $page_url.'&parent='.$v['id']; ?>">
            <img class="media-object" data-src="holder.js/64x64" src="includes/images/logo.png">
        </a>
        <div class="media-body">
            <h4 class="media-heading"><?php echo $v['id'].'&nbsp;'; $v_user = $oauser->view_user($v['post_user']); if($v_user){ echo $v_user['user_username']; unset($v_user); } ?></h4>
            <?php $v_view = $oapost->view($v['id']); if($v_view){ echo $v_view['post_content']; unset($v_view); } ?>&nbsp;<span>-&nbsp;<a href="<?php echo $page_url.'&parent='.$v['id']; ?>#nmd" target="_self"> 回复</a>
            <?php
            if($logged_admin == true){
                echo '&nbsp;<a href="'.$page_url.'&del='.$v['id'].'" target="_self">删除</a></span>';
            }
            view_parent($v['id']);
            echo $echo_parent;
            unset($echo_parent);
            ?>
        </div>
    </li>
    <?php } } ?>
</ul>

<!-- 页码 -->
<ul class="pager">
    <li class="previous<?php if ($page <= 1) {
    echo ' disabled';
} ?>">
        <a href="<?php echo $page_url.'&page='.$page_prev; ?>">&larr; 上一页</a>
    </li>
    <li class="next<?php if ($page >= $page_max) {
    echo ' disabled';
} ?>">
        <a href="<?php echo $page_url.'&page='.$page_next; ?>">下一页 &rarr;</a>
    </li>
</ul>

<!-- 发布留言 -->
<h2 id="nmd">发布留言</h2>
<form action="<?php echo $page_url . '&parent=' . $post_parent; ?>" method="post" class="form-actions">
    <div class="control-group">
        <label class="control-label" for="new_content">留言内容(不能超过500字)</label>
        <?php if($post_parent>0){ echo '<p>回复ID:'.$post_parent.'</p>'; } ?>
        <div class="controls">
            <textarea rows="5" id="new_content" name="new_content" placeholder="<?php if($post_parent>0){ echo '回复内容...'; }else{ echo '留言内容...'; }?>"></textarea>
        </div>
        <div>
            <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> 发表</button>
            <?php if($post_parent>0){ ?><a class="btn" href="<?php echo $page_url; ?>&parent=0#nmd" target="_self"><i class="icon-refresh"></i> 取消回复</a><?php } ?>
        </div>
    </div>
</form>

<!-- Javascript -->
<script>
    $(document).ready(function() {
        var message = "<?php echo $message; ?>";
        var message_bool = "<?php echo $message_bool ? '2' : '1'; ?>";
        if (message != "") {
            msg(message_bool, message, message);
        }
        $("div[class='media'] > div[class='media-body'] > span,li[class='media well'] > div[class='media-body'] > span").hide();
        $("div[class='media'],li[class='media well']").hover(function(){
            $(this).children("div[class='media-body']").children('span').show();
        },function(){
            $(this).children("div[class='media-body']").children('span').hide();
        });
    });
</script>