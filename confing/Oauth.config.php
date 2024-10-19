<?php
/**
 * 彩虹聚合登录SDK
 * 1.0
**/

// API地址
$Oauth_config['apiurl'] = $conf['login_apiurl'];

// APPID
$Oauth_config['appid'] = $conf['login_appid'];

// APPKEY
$Oauth_config['appkey'] = $conf['login_appkey'];

// 登录成功返回地址
$Oauth_config['callback'] = $siteurl.'connect.php';
