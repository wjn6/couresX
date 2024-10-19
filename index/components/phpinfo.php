<?php
$mod = 'blank';
$title = 'phpinfo';

include_once('../../confing/common.php');
include_once('jscss.php');
if ($userrow['uid'] != 1) {
    alert("您的账号无权限！", "/index.php");
    exit();
}
?>


<?=  phpinfo() ?>