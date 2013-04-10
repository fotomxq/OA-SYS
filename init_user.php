<?php
/**
 * 用户页面
 * @author fotomxq <fotomxq.me>
 * @version 4
 * @package oa
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 初始化变量
 */
$group = isset($_GET['group']) ? $_GET['group'] : null;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$max = 10;
$sort = 0;
$desc = false;

/**
 * 获取用户列表记录数
 */
$userlist_row = $oauser->get_user_row($group);

/**
 * 计算页码
 */
if ($page < 1) {
    $page = 1;
}
$page_max = ceil($userlist_row / $max);
if ($page > $page_max) {
    $page = $page_max;
}
$page_prev = $page - 1;
$page_next = $page + 1;

/**
 * 获取用户列表
 */
$userlist = $oauser->view_user_list($group, $page, $max, $sort, $desc);

/**
 * 获取所有用户组
 */
$group_list = $oauser->view_group_list(1, 999, 0, true);
?>

<h2>用户管理</h2>
<table class="table table-hover table-bordered table-striped">
    <thead>
        <tr>
            <th><i class="icon-th-list"></i> ID</th>
            <th><i class="icon-user"></i> 用户名</th>
            <th><i class="icon-tags"></i> 昵称</th>
            <th><i class="icon-envelope"></i> 邮箱</th>
            <th><i class="icon-th"></i> 用户组</th>
            <th><i class="icon-time"></i> 最后登录时间</th>
            <th><i class="icon-globe"></i> 最后登录IP</th>
            <th><i class="icon-asterisk"></i> 操作</th>
        </tr>
    </thead>
    <tbody id="user_list">
        <?php
        if($userlist){
            foreach($userlist as $v){
                $v_group = $oauser->view_group($v['user_group']);
                $v_ip = $coreip->view($v['id']);
            ?>
        <tr>
            <td><?php echo $v['id']; ?></td>
            <td><?php echo $v['user_username']; ?></td>
            <td><?php echo $v['user_name']; ?></td>
            <td><?php echo $v['user_email']; ?></td>
            <td><?php echo $v_group['group_name']; ?></td>
            <td><?php echo $v['user_login_date']; ?></td>
            <td><?php  echo $v_ip['ip_addr']; ?></td>
            <td><div class="btn-group"><button href="#edit" role="button" class="btn" data-toggle="modal"><i class="icon-pencil"></i> 编辑</button><button href="#del" class="btn btn-danger"><i class="icon-trash icon-white"></i> 删除</button></div></td>
        </tr>
        <?php } } ?>
        <tr class="info">
            <td></td>
            <td><div class="input-prepend"><span class="add-on"><i class="icon-user"></i></span><input type="text" class="input-small" id="add_username" placeholder="用户名"></div></td>
            <td><div class="input-prepend"><span class="add-on"><i class="icon-star"></i></span><input type="password"class="input-small" id="add_password" placeholder="密码"></div></td>
            <td><div class="input-prepend"><span class="add-on"><i class="icon-envelope"></i></span><input type="text" id="add_email" placeholder="@邮箱.com"></div></td>
            <td><div class="input-prepend"><span class="add-on"><i class="icon-th"></i></span><select id="add_group"><?php if($group_list){ foreach($group_list as $v){ ?><option value="<?php echo $v['id']; ?>"><?php echo $v['group_name']; ?></option><?php } } ?></select></div></td>
            <td><div class="input-prepend"><span class="add-on">@</span><input type="text" id="add_name" class="input-small" placeholder="昵称"></div></td>
            <td><?php  echo $ip_arr['addr']; ?></td>
            <td><button href="#add" type="submit" class="btn btn-success" type="button"><i class="icon-plus icon-white"></i> 添加</button></td>
        </tr>
    </tbody>
</table>

<!-- 页码 -->
<ul class="pager">
    <li class="previous<?php if($page<=1){ echo ' disabled'; } ?>">
        <a href="init.php?init=14&page=<?php echo $page_prev; ?>">&larr; 上一页</a>
    </li>
    <li class="next<?php if($page>=$page_max){ echo ' disabled'; } ?>">
        <a href="init.php?init=14&page=<?php echo $page_next; ?>">下一页 &rarr;</a>
    </li>
</ul>

<!-- 编辑框 -->
<div id="edit" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">编辑用户</h3>
    </div>
    <div class="modal-body">
        <div class="control-group">
            <label class="control-label" for="edit_username">用户名</label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-user"></i></span>
                    <input type="text" id="edit_username" placeholder="用户名">
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="edit_password">密码</label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-star"></i></span>
                    <input type="password" id="edit_password" placeholder="密码">
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="edit_name">昵称</label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on">@</span>
                    <input type="text" id="edit_name" placeholder="昵称">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="edit_email">邮箱</label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-envelope"></i></span>
                    <input type="text" id="edit_email" placeholder="@邮箱.com">
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="edit_group">用户组</label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-th"></i></span>
                    <select id="edit_group"><?php if($group_list){ foreach($group_list as $v){ ?><option value="<?php echo $v['id']; ?>"><?php echo $v['group_name']; ?></option><?php } } ?></select>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i> 关闭</button>
        <button href="#edit_save" class="btn btn-primary"><i class="icon-ok icon-white"></i> 保存修改</button>
    </div>
</div>

<!-- Javascript -->
<script>
    $(document).ready(function(){
        var ajax_page = "ajax_user.php";
        //添加用户
        $("button[href='#add']").click(function(){
            $.post(ajax_page,{
                "add_username":$("#add_username").val(),
                "add_password":$("#add_password").val(),
                "add_email":$("#add_email").val(),
                "add_name":$("#add_name").val(),
                "add_group":$("#add_group").val()
            },function(data){
                msg(data,"添加用户成功！","无法添加用户，请检查您输入的用户名、密码、邮箱等信息是否正确。");
                tourl(1500,"init.php?init=14");
            });
        });
        //编辑用户
        $("button[href='#edit']").click(function(){
            var ev = $(this).parent().parent().parent();
            $("#edit").data("edit_x",ev);
            $("#edit").data("id",$(ev).children("td:eq(0)").html());
            $("#edit").data("name",$(ev).children("td:eq(1)").html());
            $("#edit_username").val($(ev).children("td:eq(1)").html());
            $("#edit_password").val("");
            $("#edit_name").val($(ev).children("td:eq(2)").html());
            $("#edit_email").val($(ev).children("td:eq(3)").html());
            var group_name = $(ev).children("td:eq(4)").html();
            var group_id = 0;
            $("#edit_group > option").each(function(i){
                if(group_name == $(this).html()){
                    group_id = $(this).attr("value");
                }
            });
            $("#edit_group").val(group_id);
        });
        //编辑用户确认
        $("button[href='#edit_save']").click(function(){
            $.post(ajax_page,{
                "edit_id":$("#edit").data("id"),
                "edit_username":$("#edit_username").val(),
                "edit_password":$("#edit_password").val(),
                "edit_name":$("#edit_name").val(),
                "edit_email":$("#edit_email").val(),
                "edit_group":$("#edit_group").val()
            },function(data){
                msg(data,"修改用户成功！","无法修改该用户，请检查您输入的内容是否正确。");
                if(data == "2"){
                    $("#edit").data("edit_x").children("td:eq(1)").html($("#edit_username").val());
                    $("#edit").data("edit_x").children("td:eq(2)").html($("#edit_name").val());
                    $("#edit").data("edit_x").children("td:eq(3)").html($("#edit_email").val());
                    $("#edit").data("edit_x").children("td:eq(4)").html($("#edit_group > option[value='"+$("#edit_group").val()+"']").html());
                }
            });
            $("#edit").modal('hide');
        });
        //删除用户
        $("button[href='#del']").click(function(){
            var ev = $(this).parent().parent().parent().children();
            $("#user_list").data("del",$(ev).html());
            $.get("ajax_user.php?del="+$("#user_list").data("del"),function(data){
                msg(data,"删除成功！","无法删除该用户，系统必须至少存在一个用户！");
                if(data=="2"){
                    $(ev).parent("tr").remove();
                }
            });
        });
    });
</script>