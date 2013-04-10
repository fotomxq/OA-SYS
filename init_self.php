<?php
/**
 * 修改个人信息页面
 * @author fotomxq <fotomxq.me>
 * @version 4
 * @package oa
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 获取用户信息
 */
$self_user = $oauser->view_user($oauser->get_session_login());

/**
 * 编辑用户信息
 */
//编辑是否成功标记
$self_edit_bool = false;
if (isset($_POST['edit_email']) == true && isset($_POST['edit_name']) == true) {
    $password = null;
    //如果提交了密码
    if (isset($_POST['edit_password']) == true && isset($_POST['edit_new_password']) == true && isset($_POST['edit_new_password2']) == true) {
        if ($_POST['edit_new_password'] === $_POST['edit_new_password2']) {
            $password = $_POST['edit_new_password'];
        }
    }
    $self_edit_bool = $oauser->edit_user($self_user['id'], $self_user['user_username'], $password, $_POST['edit_email'], $_POST['edit_name'], $self_user['user_group']);
}

//如果编辑成功则重新获取用户信息
if ($self_edit_bool == true) {
    $self_user = $oauser->view_user($oauser->get_session_login());
}

//如果用户信息获取失败
if (!$self_user) {
    plugerror('selferror');
}
?>
<!-- 管理表格 -->
<h2>修改个人信息</h2>
<form action="init.php?init=7" method="post" class="form-actions">
    <div class="control-group">
        <label class="control-label" for="edit_email">邮箱</label>
        <div class="controls">
            <div class="input-prepend">
                <span class="add-on"><i class="icon-envelope"></i></span>
                <input type="text" id="edit_email" name="edit_email" placeholder="@邮箱.com" value="<?php echo $self_user['user_email']; ?>">
            </div>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="edit_name">昵称</label>
        <div class="controls">
            <div class="input-prepend">
                <span class="add-on">@</span>
                <input type="text" id="edit_name" name="edit_name" placeholder="昵称" value="<?php echo $self_user['user_name']; ?>">
            </div>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="edit_password">修改登录密码（不修改则留空）</label>
        <div class="controls">
            <p>
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-star"></i></span>
                    <input type="password" id="edit_password" name="edit_password" placeholder="当前密码">
                </div>
            </p>
            <p>
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-star"></i></span>
                    <input type="password" id="edit_new_password" name="edit_new_password" placeholder="新密码">
                </div>
            </p>
            <p>
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-star"></i></span>
                    <input type="password" id="edit_new_password2" name="edit_new_password2" placeholder="新密码确认">
                </div>
            </p>
        </div>
    </div>
    <div>
        <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> 修改</button>
        <button href="#return" type="button" class="btn"><i class="icon-refresh"></i> 重置</button>
    </div>
</form>

<!-- script -->
<script>
    $(document).ready(function() {
        //默认值
        var default_email = "<?php echo $self_user['user_email']; ?>";
        var default_name = "<?php echo $self_user['user_name']; ?>";
        var is_edit = <?php if(isset($_POST['edit_email']) == true){ echo 'true'; }else{ echo 'false'; } ?>;
        var is_edit_r = "<?php if($self_edit_bool == true){ echo '2'; }else{ echo '1'; } ?>";
        if(is_edit){
            msg(is_edit_r,"修改用户信息成功！","无法修改用户信息，请稍候重试。");
        }
        //复原按钮事件
        $("button[href='#return']").click(function() {
            $("#edit_email").val(default_email);
            $("#edit_name").val(default_name);
            $("#edit_password").val("");
            $("#edit_new_password").val("");
            $("#edit_new_password2").val("");
        });
    });
</script>