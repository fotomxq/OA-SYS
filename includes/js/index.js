/**
 * 首页JS处理
 */

/**
 * 全局预处理器
 */
var ca = new ContentAdvance();
/**
 * 全局跳转对象
 */
var to_url = new ToURL();

/**
 * HTML ID
 */
var id_frame = 'frame';
var id_logo = 'logo';
var id_login = 'login';

/**
 * login框架操作动作封装
 */
var login = new Object;
//框架ID
login.id_user = 'input_user';
login.id_pass = 'input_pass';
//提交页面名称
login.page_post = 'post_login.php';
//提交成功跳转页面
login.page_login = 'init.php';
//框架初始化
login.start = function(){
    this.add(ca.tag_p('用户名 : '+ca.tag_input(login.id_user, '请输入您的用户名')));
    this.add(ca.tag_p('密码 : '+ca.tag_input(login.id_pass, '请输入您的密码')));
    this.add(ca.tag_p(ca.tag_a('javascript:login.login();', 1, '登录')));
    //设定CSS
    $('#'+id_login+' input').css({
        'color':'#999'
    });
    //焦点事件
    $('#'+login.id_user).one('focus',function(){
        login.set_one_focus($(this));
    });
    $('#'+login.id_pass).one('focus',function(){
        login.set_one_focus($(this));
    });
    //设定logo框体
    $('#'+id_login).dialog({
        autoOpen:true,
        title:$('title').html(),
        width:500,
        height:250
    });
    $('#'+id_login).css({
        left:25+'%',
        top:25+'%'
    });
    //设定按钮
    $('#'+id_login+' a').button();
    //设定logo
    $('#'+id_logo).css({
        left:20+'%',
        top:20+'%'
    });
}
//添加一个HTML
login.add = function(html){
    $('#'+id_login).append(html);
}
//登录事件
login.login = function(){
    if(login.check() == true){
        $.post(login.page_post,{
            user:login.get_value(login.id_user),
            pass:login.get_value(login.id_pass)
        },function(data){
            if(data != ''){
                if(data['status'] == true){
                    to_url.to(login.page_login);
                }else{
                //如果失败，提示
                }
            }
        },'json');
    }
}
//检测用户输入是否正确
login.check = function(){
    if(login.get_value(login.id_user) && login.get_value(login.id_pass)){
        return true;
    }
    return false;
}
//获取框架ID值
login.get_value = function(id_name){
    return $('#'+id_name).val();
}
//获取焦点处理
login.set_one_focus = function(e){
    e.val('');
    e.css({
        'color':'#000'
    });
}

/**
 * 加载完成后初始化
 */
$(function(){
    login.start();
});