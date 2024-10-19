<?php
include_once('../confing/common.php');
if ($islogin != 1) {
    exit("<script language='javascript'>window.location.href='login';</script>");
}

if ($userrow['active'] == "0") {
    alert('您的账号已被封禁！', 'login');
    setcookie("admin_tokens", "", time() - 216000);
}
if ((float)$userrow['zcz'] < 0) {
    alert('账号已限制登陆！', 'login');
    setcookie("admin_tokens", "", time() - 216000);
}

$sj = $DB->get_row("select uid,user,yqm,money,notice,qq,wx from qingka_wangke_user WHERE uid='{$userrow['uuid']}' limit 1");

$spsm = $DB->get_row("select content from qingka_wangke_class where cid='$cid' limit 1");

?>
<!DOCTYPE html>

<html lang="zh-cn">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title><?= $conf['sitename'] ?> <?php if(!empty($conf['subsitename'])){ echo ' | '.$conf['subsitename']; } ?></title>
    <meta name="keywords" content="<?= $conf['keywords']; ?>" />
    <meta name="description" content="<?= $conf['description']; ?>" />
    <link rel="icon" href="../favicon.ico" type="image/ico">
    <meta name="author" content=" ">

    <link rel="stylesheet" href="assets/css/apps.css?v=1.0.4" type="text/css" />
    <link rel="stylesheet" href="assets/css/app.css" type="text/css" />

    <?php include_once('components/jscss.php'); ?>

    <style>
        .vditor-toolbar {
            padding-left: 0 !important;
        }

        .vditor-reset {
            padding: 5px 10px !important;
        }

        .layui-table-fixed-r .layui-table-col-special .layui-table-sort {
            display: none !important;
            /* 隐藏固定在右侧的列的展开按钮 */
        }
        
        
        hr{
            margin: 10px 0;
        }
        
        i{
            font-size: inherit;
        }
        
        .layui-layer-loading-icon{
            font-size:38px !important;
        }
        
        .layui-fixbar .layui-fixbar-top{
            background: transparent;
            color: #2F363C;
        }
        
    </style>

</head>

<body>
    
<style>
    .COVERID{
        
        position: fixed;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        z-index: 9999999999999999;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
    }
    /* HTML: <div class="loader"></div> */
    .COVERID .COVERID_BOX .COVERID_LOAD {
      width: 20px;
      aspect-ratio: 1;
      border-radius: 50%;
      background: #695757;
      box-shadow: 0 0 0 0 #0004;
      animation: COVERID_LOAD 1.5s infinite linear;
      position: relative;
    }
    .COVERID .COVERID_BOX .COVERID_LOAD:before,
    .COVERID .COVERID_BOX .COVERID_LOAD:after {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: inherit;
      box-shadow: 0 0 0 0 #0004;
      animation: inherit;
      animation-delay: -0.5s;
    }
    .COVERID .COVERID_BOX .COVERID_LOAD:after {
      animation-delay: -1s;
    }
    @keyframes COVERID_LOAD {
        100% {box-shadow: 0 0 0 40px #0000}
    }
    
    .COVERID .COVERID_BOX{
        width: 100px;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
    
    .COVERID .COVERID_BOX .COVERID_TEXT{
        margin-top: 40px;
        font-weight: bold;
    }
</style>

<div id="PAGECOVERID" class="COVERID">
    <div class="COVERID_BOX ">
        <div class="COVERID_LOAD">
        </div>
        <div class="COVERID_TEXT">
            加载中...
        </div>
    </div>
</div>

<script>
    $(window).on('load', () => {
        setTimeout(()=>{
            document.getElementById("PAGECOVERID").style.display = 'none';
        },1000)
    });
    setTimeout(()=>{
        document.getElementById("PAGECOVERID").style.display = 'none';
    },1500)
</script>