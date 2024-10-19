<?php
/**
 * 快捷登录
**/

include("confing/common.php");
include("confing/Oauth.config.php");
include("confing/Oauth.class.php");

if($_GET['code'] && $_GET['type']){
	if($_GET['state'] != $_SESSION['Oauth_state']){
		exit("<h2>The state does not match. You may be a victim of CSRF.</h2>");
	}
	$Oauth=new Oauth($Oauth_config);
	$arr = $Oauth->callback();
	if(isset($arr['code']) && $arr['code']==0){
		$openid=$arr['social_uid'];
		$access_token=$arr['access_token'];
		$nickname=$arr['nickname'];
		$faceimg=$arr['faceimg'];
	}elseif(isset($arr['code'])){
		exit('<h3>error:</h3>'.$arr['errcode'].'<h3>msg  :</h3>'.$arr['msg']);
	}else{
		exit('获取登录数据失败');
	}

	$row=$DB->get_row("SELECT * FROM `qingka_wangke_user` WHERE `qq_openid`='{$openid}' limit 1");
	if($row){
		$user=$row['user'];
		$pass=$row['pass'];
		if($islogin==1){
			@header('Content-Type: text/html; charset=UTF-8');
			exit("<script language='javascript'>alert('当前QQ已绑定用户:{$user}，请勿重复绑定！');window.location.href='./index.php';</script>");
		}
		$session=md5($user.$pass.$password_hash);
		$token=authcode("{$user}\t{$session}", 'ENCODE', SYS_KEY);
		setcookie("admin_tokens", $token, time() + 7200*3000);
		wlog($row['uid'],"QQ登录","使用QQ登录成功-{$conf['sitename']}",'0');
		//$DB->query("UPDATE qingka_wangke_user SET endtime='$date' WHERE uid='{$row['uid']}'");
		exit("<script language='javascript'>window.location.href='/index.php';</script>");
	}elseif($islogin==1){
		$sds=$DB->query("update `qingka_wangke_user` set `qq_openid` ='$openid',`nickname`='$nickname',`faceimg`='$faceimg' where `uid`='{$userrow['uid']}'");
		@header('Content-Type: text/html; charset=UTF-8');
		wlog($userrow['uid'],"绑定QQ","{$userrow['name']}绑定QQ成功，QQ昵称为{$nickname}",'0');
		exit("<script language='javascript'>alert('已成功绑定QQ！');window.location.href='./index.php';</script>");
	}else{
		$_SESSION['Oauth_qq_openid']=$openid;
		$_SESSION['Oauth_qq_token']=$access_token;
		$_SESSION['Oauth_qq_nickname']=$nickname;
		$_SESSION['Oauth_qq_faceimg']=$faceimg;
		@header('Content-Type: text/html; charset=UTF-8');
		if($_SESSION['Oauth_back'])$addstr = '?back='.$_SESSION['Oauth_back'];
		exit("<script language='javascript'>window.location.href='./connect.php{$addstr}';</script>");
	}
}elseif($islogin==1){
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('您已登陆！');window.location.href='./index.php';</script>");
}elseif(!$_SESSION['Oauth_qq_openid'] || !$_SESSION['Oauth_qq_token']){
	exit("<script language='javascript'>window.location.href='./index.php';</script>");
}else{
	exit("<script language='javascript'>alert('该QQ还未绑定账号哦！请先绑定之后在使用QQ登录');window.location.href='./index.php';</script>");
}

?>