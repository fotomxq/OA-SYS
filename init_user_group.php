<?php
/**
 * 用户组页面
 * @author fotomxq <fotomxq.me>
 * @version 3
 * @package oa
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 初始化变量
 */
$page = 1;
if(isset($_GET['page']) == true){
    $page = $_GET['page'];
}
$max = 10;

/**
 * 获取用户组列表记录数
 */
$group_list_row = $oauser->get_group_row();

/**
 * 计算页码
 */
if($page < 1){
    $page = 1;
}
$page_max = ceil($group_list_row/$max);
if($page > $page_max){
    $page = $page_max;
}
$page_prev = $page-1;
$page_next = $page+1;

/**
 * 获取用户组列表
 */
$group_list = $oauser->view_group_list($page);
?>
<h2>用户组管理</h2>
<table class="table table-hover table-bordered table-striped">
    <thead>
        <tr>
            <th><i class="icon-th-list"></i> ID</th>
            <th><i class="icon-th"></i> 用户组名称</th>
            <th><i class="icon-briefcase"></i> 权限</th>
            <th><i class="icon-info-sign"></i> 状态</th>
            <th><i class="icon-asterisk"></i> 操作</th>
        </tr>
    </thead>
    <tbody id="group_list">
        <?php if($group_list){ foreach($group_list as $k=>$v){ ?>
        <tr>
            <td><?php echo $v['id']; ?></td>
            <td><?php echo $v['group_name']; ?></td>
            <td><?php if($v['group_power'] == 'admin'){ echo '管理员'; }else{ echo '普通用户'; } ?></td>
            <td><?php echo $v['group_status'] ? '正常':'已禁用'; ?></td>
            <td><div class="btn-group"><button href="#group_edit" role="button" class="btn" data-toggle="modal"><i class="icon-pencil"></i> 编辑</button><button class="btn btn-danger"><i class="icon-trash icon-white"></i> 删除</button></div></td>
        </tr>
        <?php } } ?>
        <tr class="info">
            <td></td>
            <td><div class="input-prepend"><span class="add-on"><i class="icon-th"></i></span><input type="text" id="add_name" placeholder="组名称"></div></td>
            <td><div class="input-prepend"><span class="add-on"><i class="icon-briefcase"></i></span><select id="add_power"><option value="admin">管理员</option><option value="normal">普通用户</option></select></div></td>
            <td>启用</td>
            <td><button href="#add" type="submit" class="btn btn-success" type="button"><i class="icon-plus icon-white"></i> 添加</button></td>
        </tr>
    </tbody>
</table>

<!-- 页码 -->
<ul class="pager">
    <li class="previous<?php if($page<=1){ echo ' disabled'; } ?>">
        <a href="init.php?init=15&page=<?php echo $page_prev; ?>">&larr; 上一页</a>
    </li>
    <li class="next<?php if($page>=$page_max){ echo ' disabled'; } ?>">
        <a href="init.php?init=15&page=<?php echo $page_next; ?>">下一页 &rarr;</a>
    </li>
</ul>

<!-- 编辑框 -->
<div id="group_edit" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">编辑用户组</h3>
    </div>
    <div class="modal-body">
        <div class="control-group">
            <label class="control-label" for="edit_name">组名称</label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-th"></i></span>
                    <input type="text" id="edit_name" placeholder="组名称">
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="edit_power">权限</label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-briefcase"></i></span>
                    <select id="edit_power"><option value="admin">管理员</option><option value="normal">普通用户</option></select>
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="edit_status">状态</label>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on"><i class="icon-info-sign"></i></span>
                    <select id="edit_status"><option value="1">启用</option><option value="0">禁用</option></select>
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
        //添加按钮事件
    $("button[href='#add']").click(function(){
        $.post("ajax_user_group.php",{
            "add_name":$("#add_name").val(),
            "add_power":$("#add_power").val()
        },function(data){
            msg(data,"添加成功！","无法添加新的用户组，请检查您输入的用户组名称、权限是否正确！");
            tourl(1500,"init.php?init=15");
        });
    });
    
    //删除按钮事件
    $("button[class='btn btn-danger']").click(function(){
        var ev = $(this).parent().parent().parent().children();
        $("#group_list").data("del",$(ev).html());
       $.get("ajax_user_group.php?del="+$("#group_list").data("del"),function(data){
           msg(data,"删除成功！","无法删除该用户组，请确保系统至少存在一个用户组，同时您不能删除系统默认组！");
           if(data=="2"){
               $(ev).parent("tr").remove();
           }
       });
    });
    
    //编辑按钮事件
    $("button[href='#group_edit']").click(function(){
        var ev = $(this).parent().parent().parent();
        $("#group_edit").data("edit_x",ev);
        $("#group_edit").data("edit_id",$(ev).children().html());
        $("#group_edit").data("edit_name",$(ev).children("td:eq(1)").html());
        $("#edit_name").val($(ev).children("td:eq(1)").html());
        var power = $(ev).children("td:eq(2)").html();
        if(power=="管理员"){
            $("#edit_power").val("admin");
        }else{
            $("#edit_power").val("normal");
        }
        var status = $(ev).children("td:eq(3)").html();
        if(status=="正常"){
            $("#edit_status").val("1");
        }else{
            $("#edit_status").val("0");
        }
    });
    
    //编辑保存按钮事件
    $("button[href='#edit_save']").click(function(){
        $.post("ajax_user_group.php",{
            "edit_id":$("#group_edit").data("edit_id"),
            "edit_name":$("#edit_name").val(),
            "edit_power":$("#edit_power").val(),
            "edit_status":$("#edit_status").val()
        },function(data){
            msg(data,"修改成功！","无法修改用户组，请检查您输入的用户组名称或权限是否正确！");
            if(data=="2"){
                var ev = $("#group_edit").data("edit_x");
                $(ev).children("td:eq(1)").html($("#edit_name").val());
                var power = $("#edit_power").val();
                var power_str = "";
                if(power=="admin"){
                    power_str = "管理员";
                }else{
                    power_str = "普通用户";
                }
                $(ev).children("td:eq(2)").html(power_str);
                var status = $("#edit_status").val();
                var status_str = "";
                if(status=="1"){
                    status_str = "正常";
                }else{
                    status_str = "已禁用";
                }
                $(ev).children("td:eq(3)").html(status_str);
            }
        });
        $("#group_edit").modal('hide');
    });
    });
</script>
