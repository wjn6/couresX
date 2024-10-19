<?php

$filename = "install/index.php";
if (file_exists($filename)) {
	echo "准备安装程序，请稍等...";
	echo "<meta http-equiv='refresh' content='1;url=/install'>";
	exit();
}
?>

 <?php 
    include 'confing/common.php';
    if($conf["onlineStore_open"] == '1'){
        include_once($root.'/index/'.$conf['storePath']);
    }else{
        include_once($root.'/index/'.$conf['f_homePath']);
    }
 ?>