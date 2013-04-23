<?php
/**
 * 系统设置中心
 * @author fotomxq <fotomxq.me>
 * @version 6
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
function message_config_false($msg) {
    global $message, $message_bool;
    if ($message_bool == true) {
        $message = '修改系统设置成功！';
    } else {
        $message = $msg;
    }
}

/**
 * 编辑系统设置
 * @since 6
 */
if (isset($_GET['edit']) == true) {
    //网站标题
    if (isset($_POST['config_web_title']) == true) {
        if ($_POST['config_web_title'] && strlen($_POST['config_web_title']) > 1 && strlen($_POST['config_web_title']) < 300) {
            $message_bool = $oaconfig->save('WEB_TITLE', $_POST['config_web_title']);
        }
    }
    message_config_false('无法修改网站标题。');
    //网站开关
    if ($message_bool == true && isset($_POST['config_web_on']) == true) {
        $config_web_on = $_POST['config_web_on'] ? '1' : '0';
        $message_bool = $oaconfig->save('WEB_ON', (int) $config_web_on);
        message_config_false('无法修改网站开关状态。');
    }
    //用户登录超时时效
    if ($message_bool == true && isset($_POST['config_user_timeout']) == true) {
        $message_bool = false;
        if ($_POST['config_user_timeout'] && $_POST['config_user_timeout'] >= 120 && $_POST['config_user_timeout'] <= 999999) {
            $message_bool = $oaconfig->save('USER_TIMEOUT', (int) $_POST['config_user_timeout']);
        }
        message_config_false('无法修改用户登录超时时间。');
    }
    //网站地址
    if (isset($_POST['config_web_url'])) {
        $message_bool = $oaconfig->save('WEB_URL', $_POST['config_web_url']);
        message_config_false('无法修改网站地址。');
    }
    //上传功能开关
    if (isset($_POST['config_uploadfile_on'])) {
        $config_uploadfile_on = $_POST['config_uploadfile_on'] ? '1' : '0';
        $message_bool = $oaconfig->save('UPLOADFILE_ON', (int) $config_uploadfile_on);
        message_config_false('无法修改上传功能开关。');
    }
    //上传禁用类型
    if (isset($_POST['config_uploadfile_inhibit_type'])) {
        $config_inhibit_type = '';
        if ($_POST['config_uploadfile_inhibit_type']) {
            $config_inhibit_type = $_POST['config_uploadfile_inhibit_type'];
        }
        $message_bool = $oaconfig->save('UPLOADFILE_INHIBIT_TYPE', $config_inhibit_type);
        message_config_false('无法修改上传文件禁止类型。');
    }
    //上传大小最小
    if (isset($_POST['config_uploadfile_size_min'])) {
        $message_bool = $oaconfig->save('UPLOADFILE_SIZE_MIN', (int) $_POST['config_uploadfile_size_min']);
        message_config_false('无法修改上传文件最小限制。');
    }
    //上传大小最大
    if (isset($_POST['config_uploadfile_size_max'])) {
        $message_bool = $oaconfig->save('UPLOADFILE_SIZE_MAX', (int) $_POST['config_uploadfile_size_max']);
        message_config_false('无法修改上传文件最大限制。');
    }
    //业绩加权(最终业绩将乘以该值)
    if (isset($_POST['config_performance_scale'])) {
        $message_bool = $oaconfig->save('PERFORMANCE_SCALE', (int) $_POST['config_performance_scale']);
        message_config_false('无法修改业绩加权。');
    }
    //自动备份开关
    if ($message_bool == true && isset($_POST['config_backup_auto_on']) == true) {
        $config_backup_auto_on = $_POST['config_backup_auto_on'] ? '1' : '0';
        $message_bool = $oaconfig->save('BACKUP_AUTO_ON', (int) $config_backup_auto_on);
        message_config_false('无法修改自动备份开关。');
    }
    //自动备份周期
    if ($message_bool == true && isset($_POST['config_backup_auto_cycle']) == true) {
        $message_bool = false;
        $config_backup_auto_cycle = (int) $_POST['config_backup_auto_cycle'];
        if ($config_backup_auto_cycle > 0) {
            $message_bool = $oaconfig->save('BACKUP_AUTO_CYCLE', $config_backup_auto_cycle);
        }
        message_config_false('无法修改自动备份周期。');
    }
    //备份文件存储目录
    if (isset($_POST['config_backup_dir'])) {
        $config_backup_dir = $oaconfig->save('BACKUP_DIR', $_POST['config_backup_dir']);
        message_config_false('无法修改备份目录。');
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
        <h4>系统综合</h4>
        <label class="control-label" for="config_web_title">网站标题，不能为空或大于150字</label>
        <div class="controls">
            <div class="input-prepend">
                <span class="add-on"><i class="icon-edit"></i></span>
                <input type="text" id="config_web_title" name="config_web_title" placeholder="网站标题" value="<?php echo $oaconfig->load('WEB_TITLE'); ?>">
            </div>
        </div>
        <label class="control-label" for="config_web_on">网站开关</label>
        <p>关闭后所有普通用户都无法登陆该系统。</p>
        <div class="controls">
            <div class="btn-group" data-toggle="buttons-radio">
                <button type="button" class="btn btn-success" value="1"><i class="icon-ok icon-white"></i> 开启</button>
                <button type="button" class="btn btn-danger" value="0"><i class="icon-off icon-white"></i> 关闭</button>
            </div>
            <div class="hidden">
                <input type="text" name="config_web_on" value="<?php echo $oaconfig->load('WEB_ON'); ?>">
            </div>
            <p>&nbsp;</p>
        </div>
        <label class="control-label" for="config_web_url">网站地址</label>
        <div class="controls">
            <div class="input-prepend">
                <span class="add-on"><i class="icon-globe"></i></span>
                <input type="text" id="config_web_url" name="config_web_url" placeholder="网站地址" value="<?php echo $oaconfig->load('WEB_URL'); ?>">
            </div>
        </div>
        <label class="control-label" for="config_user_timeout">用户登录超时时间(秒)，范围必须在120~999999秒之间</label>
        <div class="controls">
            <div class="input-prepend">
                <span class="add-on"><i class="icon-time"></i></span>
                <input type="text" id="config_user_timeout" name="config_user_timeout" placeholder="用户登录超时时间(秒)" value="<?php echo $oaconfig->load('USER_TIMEOUT'); ?>">
            </div>
        </div>
        <label class="control-label" for="config_performance_scale">业绩加权(业绩最终分值将乘以该数值)</label>
        <div class="controls">
            <div class="input-prepend">
                <span class="add-on"><i class="icon-random"></i></span>
                <input type="text" id="config_performance_scale" name="config_performance_scale" placeholder="数字" value="<?php echo $oaconfig->load('PERFORMANCE_SCALE'); ?>">
            </div>
        </div>
        <hr>
        <h4>文件上传设置</h4>
        <label class="control-label" for="config_uploadfile_on">上传功能</label>
        <div class="controls">
            <div class="btn-group" data-toggle="buttons-radio">
                <button type="button" class="btn btn-success" value="1"><i class="icon-ok icon-white"></i> 开启</button>
                <button type="button" class="btn btn-danger" value="0"><i class="icon-off icon-white"></i> 关闭</button>
            </div>
            <div class="hidden">
                <input type="text" name="config_uploadfile_on" value="<?php echo $oaconfig->load('UPLOADFILE_ON'); ?>">
            </div>
            <p>&nbsp;</p>
        </div>
        <label class="control-label" for="config_uploadfile_inhibit_type">禁止上传类型，小写逗号隔开</label>
        <div class="controls">
            <div class="input-prepend">
                <span class="add-on"><i class="icon-ban-circle"></i></span>
                <input type="text" id="config_uploadfile_inhibit_type" name="config_uploadfile_inhibit_type" placeholder="禁用列表" value="<?php echo $oaconfig->load('UPLOADFILE_INHIBIT_TYPE'); ?>">
            </div>
        </div>
        <label class="control-label" for="config_uploadfile_size_min">上传文件最小(KB)</label>
        <div class="controls">
            <div class="input-prepend">
                <span class="add-on"><i class="icon-circle-arrow-down"></i></span>
                <input type="text" id="config_uploadfile_size_min" name="config_uploadfile_size_min" placeholder="KB" value="<?php echo $oaconfig->load('UPLOADFILE_SIZE_MIN'); ?>">
            </div>
        </div>
        <label class="control-label" for="config_uploadfile_size_max">上传文件最大(KB)</label>
        <div class="controls">
            <div class="input-prepend">
                <span class="add-on"><i class="icon-circle-arrow-up"></i></span>
                <input type="text" id="config_uploadfile_size_max" name="config_uploadfile_size_max" placeholder="KB" value="<?php echo $oaconfig->load('UPLOADFILE_SIZE_MAX'); ?>">
            </div>
        </div>
        <hr>
        <h4>备份设置</h4>
        <label class="control-label" for="config_backup_auto_on">自动备份开关</label>
        <div class="controls">
            <div class="btn-group" data-toggle="buttons-radio">
                <button type="button" class="btn btn-success" value="1"><i class="icon-ok icon-white"></i> 开启</button>
                <button type="button" class="btn btn-danger" value="0"><i class="icon-off icon-white"></i> 关闭</button>
            </div>
            <div class="hidden">
                <input type="text" name="config_backup_auto_on" value="<?php echo $oaconfig->load('BACKUP_AUTO_ON'); ?>">
            </div>
            <p>&nbsp;</p>
        </div>
        <label class="control-label" for="config_backup_auto_cycle">自动备份周期(天)</label>
        <div class="controls">
            <div class="input-prepend">
                <span class="add-on"><i class="icon-time"></i></span>
                <input type="text" id="config_backup_auto_cycle" name="config_backup_auto_cycle" placeholder="天数" value="<?php echo $oaconfig->load('BACKUP_AUTO_CYCLE'); ?>">
            </div>
        </div>
        <label class="control-label" for="config_backup_dir">备份保存目录</label>
        <div class="controls">
            <div class="input-prepend">
                <span class="add-on"><i class="icon-folder-close"></i></span>
                <input type="text" id="config_backup_dir" name="config_backup_dir" placeholder="文件夹路径" value="<?php echo $oaconfig->load('BACKUP_DIR'); ?>">
            </div>
        </div>
        <div>
            <p>&nbsp;</p>
            <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> 修改设置</button>
            <a href="init.php?init=12&return=1" class="btn btn-warning"><i class="icon-repeat icon-white"></i> 还原系统设置</a>
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
        //单选按钮和input值关联
        $("div[data-toggle='buttons-radio'] > button").click(function(){
            $(this).parent().next().children().attr("value",$(this).attr("value"));
        });
        //遍历所有单选并设定值
        $("div[data-toggle='buttons-radio']").each(function(i,dom){
            var value = $(dom).next().children().attr("value");
            $(dom).children("button").each(function(j,dom_c){
                if($(dom_c).attr("value") == value){
                    $(dom_c).attr("class",$(dom_c).attr("class")+" active");
                    return;
                }
            });
        });
    });
</script>