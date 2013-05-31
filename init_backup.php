<?php
/**
 * 备份和恢复中心
 * @author fotomxq <fotomxq.me>
 * @version 2
 * @package oa
 */
/**
 * 页面引用判断
 * @since 1
 */
if (isset($init_page) == false) {
    die();
}

/**
 * 初始化基础变量
 * @since 1
 */
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$max = 30;
$sort = 0;
$desc = true;
$post_type = 'backup';
$post_status = 'public';
$backup_dir = $oaconfig->load('BACKUP_DIR');
if (!$backup_dir) {
    $backup_dir = DIR_DATA . DS . 'backup';
}

/**
 * 提示消息变量
 * @since 1
 */
$message = '';
$message_bool = false;

/**
 * 引用文件处理类
 * @since 1
 */
require(DIR_LIB . DS . 'core-file.php');

/**
 * 引用备份插件
 * @since 1
 */
require(DIR_LIB . DS . 'plug-backup.php');

/**
 * 添加新的备份
 * @since 2
 */
if (isset($_GET['backup']) == true || isset($_GET['auto']) == true) {
    //进入维护模式，关闭平台
    $oaconfig->save('WEB_ON', '0');
    if (isset($_GET['backup']) == true) {
        if ($_GET['backup'] == '1') {
            if (plugbackup($db, $backup_dir, DIR_DATA) == true) {
                $message = '备份成功！';
                $message_bool = true;
            }
        }
    }
    if (!$message) {
        $message = '无法生成备份文件，请确保您有足够的操作权限！';
        $message_bool = false;
    }
    if ($message_bool == true && isset($_GET['auto']) == true) {
        $message = '系统自动' . $message;
    }
    //开启平台
    $oaconfig->save('WEB_ON', '1');
}

/**
 * 下载备份文件
 * @since 1
 */
if (isset($_GET['down']) == true) {
    $backup_filename = $backup_dir . DS . (int) substr($_GET['down'], 0, -4) . '.zip';
    //plugtourl($backup_filename);
}

/**
 * 恢复备份
 * @since 1
 */
if (isset($_GET['return']) == true) {
    $backup_filename = $backup_dir . DS . (int) substr($_GET['return'], 0, -4) . '.zip';
    //进入维护模式，关闭平台
    $oaconfig->save('WEB_ON', '0');
    if (plugbackup_return($db, $backup_filename, $backup_dir . DS . 'return', DIR_DATA) == true) {
        $message = '系统还原成功！';
        $message_bool = true;
    } else {
        $message = '无法还原系统，可能是该备份文件损坏了！';
        $message_bool = false;
    }
    //开启平台
    $oaconfig->save('WEB_ON', '1');
}

/**
 * 删除备份
 * @since 1
 */
if (isset($_GET['del']) == true) {
    $backup_filename = $backup_dir . DS . (int) substr($_GET['del'], 0, -4) . '.zip';
    if (corefile::is_file($backup_filename) == true) {
        if (corefile::delete_file($backup_filename) == true) {
            $message = '删除成功！';
            $message_bool = true;
        }
    }
    if(!$message){
        $message = '无法删除该备份文件！';
        $message_bool = false;
    }
}

/**
 * 获取备份文件列表
 * @since 1
 */
$table_list = corefile::list_dir($backup_dir, '*.zip');

/**
 * 获取上一次自动备份时间
 * @since 1
 */

?>
<!-- 管理表格 -->
<h2>备份文件</h2>
<p><a href="<?php echo $page_url; ?>&backup=1" class="btn btn-large btn-warning"><i class="icon-hdd icon-white"></i> 开始备份</a></p>
<p><?php if($config_backup_date){ echo '上一次自动备份时间：'.$config_backup_date; } ?></p>
<table class="table table-hover table-bordered table-striped">
    <thead>
        <tr>
            <th><i class="icon-file"></i> 文件名称</th>
            <th><i class="icon-calendar"></i> 备份时间</th>
            <th><i class="icon-info-sign"></i> 大小(KB)</th>
            <th><i class="icon-asterisk"></i> 操作</th>
        </tr>
    </thead>
    <tbody id="message_list">
        <?php if ($table_list) {
            foreach ($table_list as $v) { $v_stat = stat($v); ?>
                <tr>
                    <td><?php echo basename($v); ?></td>
                    <td><?php echo date('Y-m-d H:i:s',$v_stat['ctime']); ?></td>
                    <td><?php echo floor($v_stat['size']/1024); ?></td>
                    <td><div class="btn-group"><a href="<?php echo $v; //echo $page_url.'&down='.basename($v); ?>" class="btn"><i class="icon-file"></i> 下载</a><a href="<?php echo $page_url.'&return='.basename($v); ?>" class="btn btn-warning"><i class="icon-retweet icon-white"></i> 还原</a><a href="<?php echo $page_url.'&del='.basename($v); ?>" class="btn btn-danger"><i class="icon-trash icon-white"></i> 删除</a></div></td>
                </tr>
        <?php } } ?>
    </tbody>
</table>

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

