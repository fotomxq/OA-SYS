OA-SYS
======
<p>OA办公系统开源项目</p>
<p>这是一套主要用于演示、学习为目的OA办公自动化系统。该系统具备基本的自动化办公功能。</p>
<p>在使用前请确保您已阅读本系统协议部分内容。</p>


主要功能列表
======
* 用户和用户组、权限管理
* 个人网盘和分享文件
* 生产计划任务和业绩考评
* 通讯录和内部短消息
* 公告系统
* 个人工作日记本
* 全自动IP记录和黑名单
* 备份和恢复功能、自动备份功能
* 列表打印功能


安装和使用
======

<p>1、在系统中构建PHP和Mysql或其他数据库的运行环境。例如，Apache > 2.4.3、PHP > 5.4.7、Mysql > 5.5。</p>
<p>2、配置完成后，请将相关文件拷贝到网站根目录下，并修改content目录权限。</p>
<p>3、在数据库中创建oasys数据库，并运行/includes/install/install.sql内的SQL代码。</p>
<p>4、建立数据库访问用户和密码，然后根据注释修改位于/content/configs/db.inc.php文件内有关配置信息。</p>
<p>5、打开浏览器访问网站，默认的用户名是"oasysadmin"，密码是"adminadmin"。由于该平台设计初衷是演示用途，所以这里的用户名和密码、验证码会自动填写。</p>

FAQ
======
* 该项目必须使用Apache和Mysql构建吗？
<p> 不需要，但你必须构建PHP和数据库运行环境。</p>
* 登录失败，总是提示验证码错误？
<p> 请注意区分验证码大小写。</p>
* 如何修改数据库名称？
<p> 使用第三方工具修改好数据库名称后，打开/content/configs/db.inc.php，修改15行的“mysql:host=localhost;dbname=oasys;charset=utf8”代码，其中oasys就是数据库的名称。</p>
* 如何取消登录自动输入用户名和密码？
<p>打开/index.php，修改第78、79、80的“value="..."”参数为“value=""”即可。</p>
* 不想使用Mysql作为数据库？
<p>打开/content/configs/db.inc.php文件，修改15行PDO连接协议。你需要一定的PHP PDO知识。</p>
* 打开持久化连接？
<p>持久化连接默认是打开的，你可以在/content/configs/db.inc.php中32行关闭它。修改true为false即可。</p>
* 设定数据库连接用户名和密码？
<p>打开/content/configs/db.inc.php根据注释修改即可。</p>
* 无法显示验证码？
<p>请安装php GD和GD2模块，并开启Session模块。</p>
* 让验证码更随机化？
<p>有两种方法使验证码更随机化。1、直接修改\includes\plug-vcode.php文件plugvcode(a,b,c,d)函数，注意参数格式必须存在，以及最后必须构建$_SESSION["vcode"]变量。2、更换\content\configs\font.ttf字体库文件。</p>
* 修改平台图标？
<p>修改\includes\images目录下的logo.png和logo-57.png、logo-72.png、logo-114.png、logo-144.png文件。</p>
* 可以在移动设备上使用吗？
<p>网站采用了Bootstrap响应式设计，理论上是可以在任意平台、浏览器使用的。IE6支持性较差。</p>

协议
======
<p>本项目使用并遵守MIT许可协议。</p>
<p>Copyright (C) 2013 liuzilu</p>
<p>Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:</p>
<p>The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.</p>
<p>THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.</p>
<p>本项目引用并使用了Bootstrap界面库和Jquery相关库内容。</p>
<p>Bootstrap声明引用</p>
<p>Designed and built with all the love in the world by <a href="http://twitter.com/mdo" target="_blank">@mdo</a> and <a href="http://twitter.com/fat" target="_blank">@fat</a>.</p>
<p>Code licensed under <a href="http://www.apache.org/licenses/LICENSE-2.0" target="_blank">Apache License v2.0</a>, documentation under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>.</p>
<p><a href="http://glyphicons.com">Glyphicons Free</a> licensed under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>.</p>
<p>Jquery声明引用</p>
<p>jQuery is provided under the <a href="http://jquery.org/license/">MIT license</a>.</p>
