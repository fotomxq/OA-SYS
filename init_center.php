<?php
/**
 * 个人首页
 * @author fotomxq <fotomxq.me>
 * @version 3
 * @package oa
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 操作消息内容
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 获取消息列表
 * @since 1
 */
$message_list = $oapost->view_list(null, null, null, 'private', 'message', 1, 6, 0, true, null, $post_user);

/**
 * 获取系统消息
 * @since 3
 */
$system_message_list = $oapost->view_list(null, null, null, 'public', 'message', 1, 1, 0, true, null, null);
$system_message_view = null;
if ($system_message_list) {
    $system_message_view = $oapost->view($system_message_list[0]['id']);
}
unset($system_message_list);

/**
 * 计算任务信息
 * @since 1
 */
$task_user_count = $oapost->view_list_row($post_user, null, null, 'public-finish', 'task', '');
if(!$task_user_count){
    $task_user_count = 0;
}
$task_count = $oapost->view_list_row(null, null, null, 'public', 'task', 0);
if(!$task_count){
    $task_count = 0;
}

/**
 * 计算业绩
 * @since 1
 */
$performance_count = $oapost->sum_fields('performance', $post_user, 'post_url');
$date_mouth_start = date('Y-m') . '-00 00:00:00';
$date_mouth_end = date('Y') . '-' . ((int) date('m') + 1) . '-00 00:00:00';
$performance_mouth_count = $oapost->sum_fields('performance', $post_user, 'post_url', $date_mouth_start, $date_mouth_end);
?>
<!-- 欢迎界面 -->
<?php if($system_message_view){ ?>
<div class="hero-unit">
    <h1><?php echo $system_message_view['post_title']; ?></h1>
    <p><?php echo $system_message_view['post_content']; ?></p>
    <p><a class="btn btn-primary btn-large">了解详情</a></p>
</div>
<?php } ?>

<!-- 消息 -->
<ul class="thumbnails">
    <li class="span4">
        <div class="caption well well-large">
            <h4 class="text-info">业绩报告</h4>
            <p>本月业绩：<?php echo $performance_mouth_count; ?></p>
            <p>累计总业绩：<?php echo $performance_count; ?></p>
        </div>
    </li>
    <li class="span4">
        <div class="caption well well-large">
            <h4 class="text-info">任务报告</h4>
            <p>您还有<?php echo $tip_task_user_row; ?>个任务没有完成<?php if($tip_task_user_row>0){ ?>，请尽快完成哦！<?php }else{ echo '；'; } ?></p>
            <p>您累计完成了<?php echo $task_user_count; ?>个任务；</p>
            <p>生产任务中心有<?php echo $task_count; ?>个任务等待完成。</p>
        </div>
    </li>
    <?php if($message_list){ foreach($message_list as $v){ $v_view = $oapost->view($v['id']); $v_user = $oauser->view_user($v['post_user']); ?>
    <li class="span4">
        <div class="caption well well-large">
            <h4 class="text-info"><?php echo $v['post_title']; ?></h4>
            <p><?php if($v_view){ echo $v_view['post_content']; unset($v_view); } ?></p>
            <p class="text-right"><em>--- <?php if($v_user){ echo $v_user['user_name']; } ?></em></p>
        </div>
    </li>
    <?php } }else{ ?>
    <li class="span4">
        <div class="caption well well-large">
            <h4 class="text-info">没有任何短消息</h4>
        </div>
    </li>
    <?php } ?>
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