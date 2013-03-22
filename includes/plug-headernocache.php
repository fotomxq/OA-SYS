<?php

/**
 * 头信息无缓冲模块
 * @author fotomxq <fotomxq.me>
 * @version 1
 * @package plugheadernocache
 */
function plugheadernocache() {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
}

?>
