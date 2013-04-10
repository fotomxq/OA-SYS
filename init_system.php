<?php
/**
 * 系统设置中心
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package oa
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 操作消息内容
 */
$message = '';
$message_bool = false;

/**
 * 编辑失败消息
 * @since 1
 * @global string $message
 * @global string $message_bool
 * @param string $msg 失败的消息
 */
function message_config_false($msg){
    global $message,$message_bool;
    if($message_bool == true){
        $message = '修改系统设置成功！';
    }else{
        $message = $msg;
    }
}

/**
 * 编辑系统设置
 * @since 1
 */
if (isset($_GET['edit']) == true) {
    //网站标题
    if (isset($_POST['config_web_title']) == true) {
        if ($_POST['config_web_title'] && strlen($_POST['config_web_title']) > 1 && strlen($_POST['config_web_title']) < 300) {
            $message_bool = $oaconfig->save('WEB_TITLE', $_POST['config_web_title']);
        }
    }
    message_config_false('无法修改网站标题。');
    //用户登录超时时效
    if ($message_bool == true && isset($_POST['config_user_timeout']) == true) {
        $message_bool = false;
        if ($_POST['config_user_timeout'] && $_POST['config_user_timeout'] >= 120 && $_POST['config_user_timeout'] <= 999999) {
            $message_bool = $oaconfig->save('USER_TIMEOUT', (int) $_POST['config_user_timeout']);
        }
        message_config_false('无法修改用户登录超时时间。');
    }
}

/**
 * 还原系统设置
 * @since 1
 */
if(isset($_GET['return']) == true){
    $message_bool = $oaconfig->return_default_all();
    if($message_bool){
        $message = '还原设置成功，现在系统所有设置均恢复到最初状态。';
    }else{
        $message = '无法还原系统设置，可能是某些参数正在被修改。';
    }
}
?>
<!-- 系统设置 -->
<h2>系统设置</h2>
<form action="init.php?init=12&edit=1" method="post" class="form-actions">
    <div class="control-group">
        <label class="control-label" for="config_web_title">网站标题，不能为空或大于150字</label>
        <div class="controls">
            <input type="text" id="config_web_title" name="config_web_title" placeholder="网站标题" value="<?php echo $oaconfig->load('WEB_TITLE'); ?>">
        </div>
        <label class="control-label" for="config_user_timeout">用户登录超时时间(秒)，范围必须在120~999999秒之间</label>
        <div class="controls">
            <input type="text" id="config_user_timeout" name="config_user_timeout" placeholder="用户登录超时时间(秒)" value="<?php echo $oaconfig->load('USER_TIMEOUT'); ?>">
        </div>
        <div>
            <button type="submit" class="btn btn-primary">修改设置</button>
            <a href="init.php?init=12&return=1" class="btn btn-warning">还原系统设置</a>
        </div>
    </div>
</form>

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