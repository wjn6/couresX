<?php
/**
 * 快捷登录
**/

include("confing/common.php");
include("confing/Oauth.config.php");
include("confing/Oauth.class.php");
$type = $_POST['type'];
$Oauth=new Oauth($Oauth_config);
$arr = $Oauth->login($type);
if(isset($arr['code']) && $arr['code']==0){
	exit('{"code":1,"url":"'.$arr['url'].'"}');
}elseif(isset($arr['code'])){
    exit('{"code":0,"msg":"'.$arr['msg'].'"}');
}else{
    exit('{"code":0,"msg":"获取登录地址失败"}');
}
