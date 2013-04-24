<?php

/**
 * 备份和恢复模块
 * <p>需要：core-file、core-db支持</p>
 * @author fotomxq <fotomxq.me>
 * @version 2
 * @package plugbackup
 */

/**
 * 生成新的备份文件
 * @since 2
 * @param coredb $db 数据库操作句柄
 * @param string $backup_dir 备份到目录
 * @param string $content_dir 文件数据目录
 * @return string 备份文件路径
 */
function plugbackup(&$db, $backup_dir, $content_dir) {
    $return = '';
    $bool = false;
    $file_type = 'zip';
    $ls_dir = $backup_dir . DS . substr(sha1(rand(1, 99999)), 0, 8);
    if (!corefile::is_dir($ls_dir)) {
        $ls_sql_dir = $ls_dir . DS . 'sql';
        //创建临时目录
        $bool = corefile::new_dir($ls_dir);
        //创建临时SQL目录
        if ($bool == true) {
            $bool = corefile::new_dir($ls_sql_dir);
        }
        //拷贝文件数据
        if ($bool == true) {
            $bool = corefile::copy_dir($content_dir . DS . 'files', $ls_dir . DS . 'content' . DS . 'files');
        }
        if ($bool == true) {
            $bool = corefile::copy_dir($content_dir . DS . 'logs', $ls_dir . DS . 'content' . DS . 'logs');
        }
        //依次遍历所有数据并拷贝到文件内
        foreach ($db->tables as $k => $v) {
            //创建表目录
            $v_table_dir = $ls_sql_dir . DS . $v;
            if ($bool == true) {
                $bool = corefile::new_dir($v_table_dir);
            } else {
                break;
            }
            //计算表内所有字段数据平均长度，得出最终步长
            $max = 50;
            if ($bool == true) {
                $sql = 'SELECT AVG( LENGTH(`' . implode('`))+AVG(LENGTH(`', $db->fields[$k]) . '`)) as al FROM `' . $v . '`';
                $sth = $db->prepare($sql);
                if ($sth->execute() == true) {
                    $res = (int) $sth->fetchColumn();
                    if ($res > 500) {
                        $max = 20;
                    } elseif ($res > 1000) {
                        $max = 10;
                    } elseif ($res > 5000) {
                        $max = 1;
                    }
                } else {
                    $bool = false;
                    break;
                }
            } else {
                break;
            }
            //遍历数据写入文件
            $p = 0;
            $p_bool = true;
            while ($p_bool) {
                $sql = 'SELECT * FROM `' . $v . '` ORDER BY ' . $db->fields[$k][0] . ' ASC LIMIT ' . ($p * $max) . ',' . $max;
                $sth = $db->prepare($sql);
                if ($sth->execute() == true) {
                    $res = $sth->fetchAll(PDO::FETCH_ASSOC);
                    if ($res) {
                        $file_content = 'INSERT INTO `' . $v . '`(`' . implode('`,`', array_keys($res[0])) . '`) VALUES';
                        foreach ($res as $v_res) {
                            $file_content .= '(';
                            foreach ($v_res as $v_res_v) {
                                if ($v_res_v == null) {
                                    $file_content .= 'NULL,';
                                } elseif (is_int($v_res_v) == true) {
                                    $file_content .= $v_res_v . ',';
                                } else {
                                    $file_content .= '\'' . $v_res_v . '\',';
                                }
                            }
                            $file_content = substr($file_content, 0, -1);
                            $file_content .= '),';
                        }
                        $file_content = substr($file_content, 0, -1) . ';';
                        $file_table_row = $v_table_dir . DS . $v . '_' . $p . '.sql';
                        $p_bool = corefile::edit_file($file_table_row, $file_content);
                        $bool = $p_bool;
                        $file_content = null;
                    } else {
                        $p_bool = false;
                        $bool = true;
                    }
                } else {
                    $p_bool = false;
                    $bool = false;
                }
                $p += 1;
            }
        }
    }
    //将临时文件压缩为压缩包
    $backup_file = $backup_dir . DS . time() . '.' . $file_type;
    if (corefile::is_file($backup_file)) {
        $bool = corefile::delete_file($backup_file);
    }
    if ($bool == true) {
        $bool = corefile::create_zip($backup_file, $ls_dir);
    }
    if ($bool == true) {
        $return = $backup_file;
    } else {
        //失败删除所有临时文件
        corefile::delete_file($backup_file);
    }
    //删除临时文件
    corefile::delete_dir($ls_dir);
    return $return;
}

/**
 * 还原备份
 * @since 2
 * @param coredb $db
 * @param string $backup_file 备份的文件路径
 * @return boolean
 */
function plugbackup_return(&$db, $backup_file, $return_dir, $content_dir) {
    $return = false;
    //清空return目录所有文件夹
    $dir_list = corefile::list_dir($return_dir, '*', GLOB_ONLYDIR);
    if ($dir_list) {
        foreach ($dir_list as $v) {
            $v_src = basename($v);
            $return = corefile::delete_dir($v);
            if ($return == false) {
                return $return;
            }
        }
    } else {
        $return = true;
    }
    //解压备份文件到return目录
    if ($return == true) {
        $return = corefile::extract_zip($backup_file, $return_dir);
    }
    //获取临时文件夹路径
    $ls_dir = '';
    if ($return == true) {
        $dir_list = corefile::list_dir($return_dir, '*', GLOB_ONLYDIR);
        if ($dir_list) {
            $ls_dir = $dir_list[0];
        }
    }
    //检查数据是否正确
    if ($return == true) {
        $v_dirs = array($ls_dir . DS . 'content', $ls_dir . DS . 'sql', $ls_dir . DS . 'content' . DS . 'files', $ls_dir . DS . 'content' . DS . 'logs');
        foreach ($v_dirs as $v) {
            if (corefile::is_dir($v) == false) {
                $return = false;
                break;
            }
        }
        foreach ($db->tables as $v) {
            if (corefile::is_dir($v_dirs[1] . DS . $v) == false) {
                $return = false;
                break;
            }
        }
    }
    //删除现有数据
    if ($return == true) {
        if (corefile::delete_dir($content_dir . DS . 'files') == true && corefile::delete_dir($content_dir . DS . 'logs') == true) {
            $return = true;
        } else {
            $return = false;
        }
    }
    //拷贝备份数据
    if ($return == true) {
        if (corefile::copy_dir($ls_dir . DS . 'content' . DS . 'files', $content_dir . DS . 'files') == true && corefile::copy_dir($ls_dir . DS . 'content' . DS . 'logs', $content_dir . DS . 'logs') == true) {
            $return = true;
        } else {
            $return = false;
        }
    }
    //清空所有表
    if ($return == true) {
        $sql = 'TRUNCATE ';
        foreach ($db->tables as $v) {
            if ($db->exec($sql . $v) === false) {
                $return = false;
                break;
            }
        }
    }
    //根据文件次序执行sql
    if ($return == true) {
        foreach ($db->tables as $v) {
            $v_table_dir = $ls_dir . DS . 'sql' . DS . $v;
            $dir_list = corefile::list_dir($v_table_dir, '*.sql');
            if ($dir_list) {
                foreach ($dir_list as $v_i) {
                    $i_content = corefile::read_file($v_i);
                    if ($db->exec($i_content) === false) {
                        $return = false;
                        break;
                    }
                    $i_content = null;
                }
            }
        }
    }
    //删除临时文件夹
    if ($return == true) {
        $return = corefile::delete_dir($ls_dir);
    }
    return $return;
}

?>
