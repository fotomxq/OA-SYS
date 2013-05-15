OA-SYS
======
<p>OA办公系统开源项目</p>
<p>这是一套主要用于演示、学习为目的OA办公自动化系统。该系统具备基本的自动化办公功能。</p>
<p>在使用前请确保您已阅读本系统协议部分内容。</p>


主要功能列表
======
* 用户和用户组、权限管理
* 个人网盘和共享文件
* 生产计划任务和业绩考评
* 通讯录和短消息
* 公告系统
* 工作日记本
* 全自动IP记录和黑名单
* 备份和恢复功能、自动备份功能
* 列表快速打印功能


安装和使用
======

<p>1、在您的操作系统上搭建Apache > 2.4.3、PHP > 5.4.7 (Perl)、Mysql > 5.5运行环境。其中Mysql非必须，但您必须至少搭建一个数据库环境。</p>
<p>2、配置完成后，请将相关文件拷贝到网站根目录下，注意修改相关(特别是content)目录权限。</p>
<p>3、在数据库中创建oasys数据库，并运行includes/install/install.sql内的SQL代码。数据库可以为其他名称，但请在稍后步骤中修改数据库名称。</p>
<p>4、建立数据库访问用户和密码，然后根据注释修改位于content/configs/db.inc.php文件内有关配置信息。</p>
<p>5、打开浏览器访问网站，默认的用户名是"oasysadmin"，密码是"adminadmin"。由于该平台设计初衷是演示用途，所以这里的用户名和密码、验证码会自动填写。</p>


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
