/* 
 * 库封装
 * <p>封装的内容依次为 : </p>
 * <p> * ToURL 跳转页面封装</p>
 * <p> * Dialog 弹出框体封装</p>
 * <p> * ContantList 内容列队封装</p>
 * <p> * ContentAdvance 内容预处理器 </p>
 * <p> * Layout 布局器 </p>
 * <p> * TipList 列队式提示框架 </p>
 * @author fotomxq
 * @version 1
 */

/**
 * 封装跳转方法
 */
function ToURL(){
    this.to = function(url){
        location.href = url;
    }
}

/**
 * 弹出框体封装
 */
function Dialog(){
    //窗体ID
    this.id = '#dialog';
    //显示窗体
    this.show = function(title,html,buttons){
        $(this.id).attr('title',title);
        $(this.id).html(html);
        $(this.id).dialog({
            autoOpen:false,
            resizable:false,
            draggable:false,
            modal:true,
            minWidth:500,
            minHeight:600,
            buttons:buttons,
            close:function(){
                $(this.id).attr('title','');
                $(this.id).html('');
                $(this.id).dialog('destroy');
            }
        });
        $(this.id).dialog('open');
    }
    //建立按钮
    this.button = function(title,func){
        var b = {
            text:title,
            click:func
        }
        return b;
    }
    //关闭窗体
    this.close = function(){
        $(this.id).dialog('close');
    }
}

/**
 * 内容列队封装
 */
function ContantList(){
    //列队内容
    this.list = new Array();
    //添加内容
    this.add = function(html){
        this.list.push(html);
    }
    //清空列队
    this.clean = function(){
        this.list = new Array();
    }
    //获取组合字符串
    this.string = function(str){
        return this.list.join(str);
    }
}

/**
 * 预定义内容处理
 * <p>注意！内部引号只能使用\'，而不是"或其他！</p>
 */
function ContentAdvance(){
    //标签段落 p
    this.tag_p = function(value){
        return '<p>'+value+'</p>';
    }
    //标签文本 input
    this.tag_input = function(id,value){
        return '<input type="text" class="text ui-widget-content ui-corner-all" id="'+id+'" value="'+value+'">';
    }
    //标签文本域 textarea
    this.tag_textarea = function(id,text){
        return '<textarea id="'+id+'">'+text+'</textarea>';
    }
    //标签链接 a
    this.tag_a = function(link,target_key,text){
        var target_list = new Array(2);
        target_list[0] = '_blank';
        target_list[1] = '_self';
        return '<a href="'+link+'" target="'+target_list[target_key]+'">'+text+'</a>';
    }
}

/**
 * 布局器
 * wh 框体名称
 * w 宽度
 * h 高度
 */
function Layout(wh,w,h){
    //操作内容ID
    this.id = wh;
    //元素宽度
    this.width = w;
    //元素高度
    this.height = h;
    //元素中心点x坐标
    this.center_point_x = Layout.CENTER;
    //元素中心点y坐标
    this.center_point_y = Layout.CENTER;
    //布局定位方式
    this.locate_type = Layout.LOCATE_ABSOLUTE;
    //刷新定位和大小
    this.refresh = function(x,y){
        var center_x = this.get_locate_axis(this.width, this.center_point_x);
        var center_y = this.get_locate_axis(this.height, this.center_point_y);
        var point_x = this.get_locate_axis(Layout.WINDOW_WIDTH, x);
        var point_y = this.get_locate_axis(Layout.WINDOW_HEIGHT, y);
        point_x += center_x;
        point_y += center_y;
        $(id).css({
            position:this.locate_type,
            left:point_x+'px',
            top:point_y+'px',
            width:this.width+'px',
            height:this.height+'px'
        });
    }
    /**
     * 获取元素相对父级原点位移值
     * parent 父级轴尺寸
     * point 定位坐标，可使用常量
     */
    this.get_locate_axis = function(parent,point){
        switch(point){
            case Layout.MIN:
                return 0;
                break;
            case Layout.MAX:
                return parent;
                break;
            case Layout.CENTER:
                return parent/2;
                break;
            default:
                return point;
                break;
        }
    }
    //初始化
    this.refresh();
}
//位置布局常量，最小|最大|中心
Layout.MIN = 'MIN';
Layout.MAX = 'MAX';
Layout.CENTER = 'CENTER';
//布局定位常量
//全局绝对定位
Layout.LOCATE_ABSOLUTE = 'absolute';
//相对相邻元素定位
Layout.LOCATE_RELATIVELY = 'relative';
//页面全局常量
//页面可视宽度
Layout.WINDOW_WIDTH = $(window).width();
//页面可视高度
Layout.WINDOW_HEIGHT = $(window).height();

/**
 * 列队式提示框架
 * <p>需要Layout布局器配合</p>
 * <p>尺度单位为px</p>
 * html_id 框体ID
 * w 宽度
 * h 高度
 */
function TipList(html_id,w,h){
    //框体ID
    this.id = html_id;
    //框体横坐标
    this.x = 0;
    //框体纵坐标
    this.y = 0;
    //消息列队
    this.list = new Array();
    //消息宽度
    this.message_width = 100;
    //消息高度
    this.message_height = 25;
    //消息存在时间 毫秒
    this.message_time = 3000;
    //消息列队保留个数
    this.message_max = 5;
    //添加一条消息
    this.add = function(msg){
        var length = this.list.length;
        var key = length+1;
        this.list[key] = new Array();
        this.list[key]['msg'] = msg;
        this.list[key]['time'] = setTimeout(function(){
            this.del();
        },this.message_time);
        this.list[key]['id'] = this.id+'_'+key;
        var message_id = this.id+'_'+key;
        $('#'+this.id).append('<p id="'+this.list[key]['id']+'">'+this.list[key]['msg']+'</p>');
    }
    //删除最后一条消息
    this.del = function(){
        var key = this.list.length-1;
        clearTimeout(this.list[key]['time']);
    }
    //初始化
    //初始化框体布局
    var it = new Layout('#'+this.id,w,h);
    it.refresh(this.x, this.y);
    //初始化消息布局
    var it_msg = new Layout('#'+this.id+' p',this.message_width,this.message_height);
    it_msg.locate_type = Layout.LOCATE_RELATIVELY;
    it_msg.refresh(0, this.message_height+5);
}