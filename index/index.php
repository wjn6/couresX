<?php

include_once('../confing/common.php');
if ($islogin != 1) {
    exit("<script language='javascript'>window.location.href='login.php';</script>");
}
if ($userrow['active'] == "0") {
    alert('您的账号已被封禁！', 'login');
    exit();
}
$real_ip = real_ip();

?>

<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">
    <title><?= $conf['sitename'] ?> <?php if (!empty($conf['subsitename'])) {
           echo ' | ' . $conf['subsitename'];
       } ?></title>
    <meta name="keywords" content="<?= $conf['keywords']; ?>" />
    <meta name="description" content="<?= $conf['description']; ?>" />
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <?php include_once($root . '/index/components/jscss.php'); ?>
    <link rel="stylesheet" href="assets/css/new.css?v=1.0.1" type="text/css" />

    <script src="/assets/toc/ua-parser.js?v=1.0.38"></script>

    <!--避免被代理用浏览器控制台调试-->
    <?php if ($userrow['uid'] != "1" && $conf['useF12_d'] == 1) { ?>
        <script disable-devtool-auto src="/assets/js/security.js" url="https://baidu.com/"></script>
    <?php } ?>

    <!--nprogress-->
    <link href="/assets/toc/nprogress.css?v=0.2.0" rel="stylesheet">
    <script src="/assets/toc/nprogress.js?v=0.2.0"></script>
    <style>
        #nprogress {
            z-index: 99999999999999999999;
            position: fixed;
        }

        .layui-anim-loop-1s {
            animation-duration: 1s;
        }

        .messageBox_dropdown .layui-dropdown-menu li .layui-menu-body-title {
            font-size: 12px;
        }

        .messageBox_dropdown .layui-dropdown-menu li:nth-child(1) {
            line-height: 15px;
        }

        .messageBox_dropdown .layui-dropdown-menu li:nth-child(1) .title {
            color: #333333 !important;
        }
    </style>
    <script>
        $(document).ready(function () {
            NProgress.start();
            $(window).on('load', function () {
                NProgress.done();
            });
        });
    </script>

    <script>
        layui.config({
            base: '../layuiadmin/' //静态资源所在路径
        }).extend({
            index: 'lib/index' //主入口模块
        }).use('index');
    </script>


</head>

<style>
    .layui-side-menu .layui-side-scroll {
        width: fit-content;
    }

    #LAY_app {
        min-height: 100vh;
    }

    .layui-body {
        background: #fff8f8;
    }

    .layadmin-side-shrink .layui-side-menu .layui-nav {}

    .layadmin-side-shrink .layui-side-menu .layui-nav-item a span {
        visibility: hidden;
    }

    #INDEXmenuID .layadmin-side-shrink .layui-side-menu .layui-nav .layui-nav-child {
        padding: 0 5px 0 10px !important;
    }

    .layadmin-side-shrink .layui-layout-admin .layui-logo {
        background: url('<?= $conf['cb_logo'] ?>') center center no-repeat;
        background-size: 90% 90%;
    }

    .layadmin-side-shrink #LAY-system-side-menu-set .bt {
        display: none;
    }

    .dropdown-menu li {
        cursor: pointer;
    }

    .lockscreenDemoBox {
        background: url(https://t.alcy.cc/ai) #16b777 !important;
        background-size: cover !important;
        background-repeat: no-repeat !important;
        color: rgba(255, 255, 255, 1);
    }

    @media all and (orientation : portrait) {
        .lockscreenDemoBox {
            background: url(https://t.alcy.cc/ai) #16b777 !important;
            background-size: cover !important;
            background-repeat: no-repeat !important;
            color: rgba(255, 255, 255, 1);
        }
    }

    .lockscreenDemoBox .layui-form {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 300px;
        transform: translate(-50%, -50%);
    }

    .lockscreenDemoBox .layui-form>div {
        margin-bottom: 8px;
    }

    .class-demo-layer-pin {
        width: 100%;
        height: 38px;
        padding: 0 8px;
        background-color: rgba(255, 255, 255, .94);
        border: none;
        border-radius: 3px;
        box-sizing: border-box;
    }

    .lockscreenDemoBox .layui-input-suffix {
        pointer-events: auto;
        background-color: rgba(0, 0, 0, .5);
        border-radius: 0 3px 3px 0;
    }

    .lockscreenDemoBox .layui-input-suffix .layui-icon-right {
        cursor: pointer;
        color: #fff;
    }

    #openThemeColorC .themeLiInput {
        display: flex;
        gap: 8px;
        align-items: center;
        padding: 10px;
        border-radius: 5px;
        position: relative;
    }

    #openThemeColorC .layui-form-radio .themeLiInput .lay-skin-color-picker {
        border-radius: 50%;
        /*border-width: 1px;*/
        /*border-style: solid;*/
        width: 20px;
        height: 20px;
        box-shadow: 0 2px 7px #98989894, 0 0 6px rgba(0, 0, 0, .04);
    }

    /* 选中 */
    #openThemeColorC .layui-form-radioed .themeLiInput {
        box-shadow: 3px 3px 6px 1px #16b77763;

    }

    #openThemeColorC .layui-form-radioed .themeLiInput:after {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        color: #ffffff;
        border-radius: 0 5px 0 0;
        border: 13px solid;
        border-color: #16b777 #16b777 transparent transparent;
    }

    #openThemeColorC .layui-form-radioed .themeLiInput:before {
        position: absolute;
        font-family: "layui-icon";
        content: "\e605";
        color: #fff;
        right: 2px;
        top: 2px;
        font-size: 12px;
        z-index: 1;
    }

    #openThemeColorC .layui-form-radioed .themeLiInput .themeLiInputText {
        color: #16b777;
        font-weight: bold;
    }

    #openThemeColorC .themesBox .themeLi {
        margin-bottom: 5px;
    }
</style>

<style>
    .COVERID {

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
        100% {
            box-shadow: 0 0 0 40px #0000
        }
    }

    .COVERID .COVERID_BOX {
        width: 100px;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .COVERID .COVERID_BOX .COVERID_TEXT {
        margin-top: 40px;
        font-weight: bold;
    }
</style>

<script>
    $(window).on('load', () => {
        setTimeout(() => {
            document.getElementById("INDEXCOVERID").style.display = 'none';
        }, 1000)
    });
    setTimeout(() => {
        document.getElementById("INDEXCOVERID").style.display = 'none';
    }, 1500)
</script>

<body class="layui-layout-body" style="height:100%;">

    <div id="INDEXCOVERID" class="COVERID">
        <div class="COVERID_BOX">
            <div class="COVERID_LOAD">
            </div>
            <div class="COVERID_TEXT">
                加载中...
            </div>
        </div>
    </div>

    <div id="LAY_app" style="display:none;">

        <div class="layui-layout layui-layout-admin">

            <div class="layui-header">
                <!-- 头部区域 -->
                <ul class="layui-nav layui-layout-left">
                    <li class="layui-nav-item layadmin-flexible" lay-unselect>
                        <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
                            <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
                        </a>
                    </li>
                    <li class="layui-nav-item" lay-unselect>
                        <a href="javascript:;" layadmin-event="refresh" title="刷新">
                            <i class="layui-icon layui-icon-refresh"></i>
                        </a>
                    </li>
                </ul>
                <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">

                    <li class="layui-nav-item layui-hide-xs " lay-unselect>
                        <div id="tp-weather-widget" class="layui-font-green layui-font-12" style="scale: .8;">
                            <span>天气加载中</span>
                        </div>
                    </li>
                    <li class="layui-nav-item" lay-unselect lay-href="hots" title="销量热榜" lay-text="销量热榜">
                        <a href="javascript:void(0)" title='销量热榜'>
                            <img style="height: 25px; " src="/assets/images/bd1.svg">
                            </span>
                        </a>
                    </li>
                    <li class="layui-nav-item layui-hide-xs" lay-unselect title="锁屏" @click="lockscreen">
                        <a href="javascript:void(0)" title='锁屏'>
                            <i class="layui-icon layui-icon-password"></i>
                        </a>
                    </li>
                    <li class="layui-nav-item layui-hide-xs" lay-unselect title="主题切换" @click="openThemeColor">
                        <a href="javascript:void(0)" title='主题切换'>
                            <el-icon>
                                <Brush />
                            </el-icon>
                        </a>
                    </li>
                    <?php if ($userrow['uid'] == 1) { ?>
                    
                        <li class="layui-nav-item " lay-unselect>
                            <a href="javascript:void(0)" data-toggle="dropdown" title="应用">
                                <i class="layui-icon layui-icon-app"></i>
                            </a>
                            <dl class="layui-nav-child" style="color: initial;padding:0;">
                                <table class="layui-table appLayTable">
                                    <tbody>

                                        <tr class="layui-row layui-col-space1">
                                            <td class="layui-col-xs4 layui-padding-2"
                                                @click="openNewHref('https://github.com/time-demon/couresX')">
                                                <span class="icon"><i class="layui-icon layui-icon-github"></i></span>
                                                <span class="text layui-font-12 layui-font-green" style="scale: .8;">
                                                    Github
                                                </span>
                                            </td>
                                            
                                            <td class="layui-col-xs4 layui-padding-2"
                                                @click="openApp('EailsQueue',`邮件队列 <span class='layui-font-12 layui-font-blue'>监控中</span>`,`components/EailsQueue.php`)">
                                                <span class="icon"><el-icon>
                                                        <Message />
                                                    </el-icon></span>
                                                <span class="text layui-font-12 layui-font-green" style="scale: .8;">
                                                    邮件队列
                                                </span>
                                            </td>
                                        </tr>

                                        <tr class="layui-row layui-col-space1">
                                            <td class="layui-col-xs4 layui-padding-2" lay-unselect lay-href="XCharts"
                                                title="XCharts" lay-text="XCharts">
                                                <span class="icon"><el-icon><pie-chart /></el-icon></span>
                                                <span class="text layui-font-12 layui-font-green" style="scale: .8;">
                                                    XCharts
                                                </span>
                                            </td>
                                            <td class="layui-col-xs4 layui-padding-2" lay-href="jkzx">
                                                <span class="icon"><i class="layui-icon layui-icon-chart"></i></span>
                                                <span class="text layui-font-12 layui-font-green" style="scale: .8;">
                                                    Redis监控
                                                </span>
                                            </td>
                                            <td class="layui-col-xs4 layui-padding-2"
                                                @click="openApp('oatool',`优化加速工具`,`components/oatool.php`,window_WH.height+'px','r')">
                                                <span class="icon"><i class="fa fa-rocket"></i></span>
                                                <span class="text layui-font-12 layui-font-green" style="scale: .8;">
                                                    优化加速
                                                </span>
                                            </td>
                                        </tr>

                                        <tr class="layui-row layui-col-space1">
                                            <td class="layui-col-xs4 layui-padding-2" @click="openThemeColor">
                                                <span class="icon"><el-icon>
                                                        <Brush />
                                                    </el-icon></span>
                                                <span class="text layui-font-12 layui-font-green" style="scale: .8;">
                                                    主题切换
                                                </span>
                                            </td>
                                            <td class="layui-col-xs4 layui-padding-2"
                                                @click="openApp('POSTGET',`Post/Get接口测试工具`,`components/POSTGET.php`,window_WH.height+'px','r')">
                                                <span class="icon"><i class="layui-icon layui-icon-fonts-code"></i></span>
                                                <span class="text layui-font-12 layui-font-green" style="scale: .8;">
                                                    接口测试
                                                </span>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </dl>
                        </li>
                    <?php } ?>

                    <li class="layui-nav-item " lay-unselect>
                        <a href="javascript:void(0)" id="messageBox_dropdown" data-toggle="dropdown"
                            :title="autoGet_data_need_reduce?`您有${autoGet_data_need_reduce}条消息需要处理`:'无需要处理的消息'">
                            <el-icon :size="13">
                                <Message-Box style="font-size: 13px;" />{{autoGet_data_need_reduce}}
                            </el-icon>
                            <span v-show="autoGet_data_need_reduce"
                                class="layui-badge-dot layui-anim layui-anim-fadeout layui-anim-loop layui-anim-loop-1s"
                                style="right: -1px; top: 16px; scale: .8;">
                            </span>
                        </a>
                    </li>

                    <li class="layui-nav-item layui-hide-xs" lay-unselect>
                        <a href="javascript:;" layadmin-event="fullscreen">
                            <i class="layui-icon layui-icon-screen-full"></i>
                        </a>
                    </li>
                    <li class="layui-nav-item " lay-unselect style="margin-right: 15px;">
                        <a href="javascript:void(0)" data-toggle="dropdown">

                            <!--/assets/images/user.gif-->
                            <img class="img-avatar img-avatar-48 m-r-10"
                                style="border-radius: 50%;width:30px;height:30px;border:0px;"
                                :src="'//q1.qlogo.cn/g?b=qq&nk=<?= empty($userrow['qq']) ? $userrow['name'] : $userrow['qq']; ?>&s=100'"
                                onerror="this.src='/assets/images/user.gif';return false;"
                                alt="<?= $userrow['name']; ?>" />
                            <!--<img class="img-avatar img-avatar-48 m-r-10"-->
                            <!--    style="border-radius: 50%;width:30px;height:30px;border:0px;"-->
                            <!--    src="/assets/images/user.gif" alt="<?= $userrow['name']; ?>" />-->
                            <!--<img class="img-avatar img-avatar-48 m-r-10" style="border-radius: 50%;width:30px;height:30px;border:0px;" src="//q2.qlogo.cn/headimg_dl?dst_uin=<?= $userrow['user']; ?>&spec=100" alt="<?= $userrow['name']; ?>" />-->
                            <span><?= $userrow['name']; ?> <span class="caret"></span></span>
                        </a>
                        <dl class="layui-nav-child">
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li> <a id="sjqy" @click="sjqyOpen">上级迁移</a> </li>
                                <!--<li> <a lay-href="passwd" id="passwd">修改密码</a> </li>-->
                                <li> <a id="szyqprice" @click="szyqpriceOpen">设置邀请费率</a> </li>
                                <li> <a href="../apiadmin.php?act=logout">
                                        <el-icon><Switch-Button /></el-icon>&nbsp;
                                        退出登录</a> </li>
                                <?php if ($userrow['uid'] == 1) { ?>
                                    <li class="layui-font-12" style="border-top:1px solid  rgba(255,255,255,.1)">
                                        <a href="javascript:(0)" style="font-size: inherit;color:#aaaaaa">
                                            版本：<?php echo $conf['version'] ? $conf['version'] : '无法获取' ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </dl>

                    </li>

                    <!--<li class="layui-nav-item layui-hide-xs" lay-unselect>-->
                    <!--    <a href="javascript:;" layadmin-event="about"><i class="layui-icon layui-icon-more-vertical"></i></a>-->
                    <!--</li>-->

                    <!--<li class="layui-nav-item  layui-hide-xs " lay-unselect>-->
                    <!--    <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>-->
                    <!--</li>-->

                    <!--<li class="layui-nav-item  layui-hide-xs " lay-unselect>-->
                    <!--    <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>-->
                    <!--</li>-->

                </ul>
            </div>

            <!-- 侧边菜单 -->
            <div class="layui-side layui-side-menu" style="">
                <div class="layui-side-scroll" id="INDEXmenuID">
                    <div class="layui-logo"
                        style="font-size: 1rem; display: flex; justify-content: center; align-items: center;">
                        <!--<a href="home"><img src="<?= $conf['logo'] ?>" title="LightYear" height="50" width="190" alt="LightYear" /></a>-->
                        <span><?= $conf['sitename'] ?></span>
                    </div>

                    <!--三级菜单-->
                    <!-- 请到【网站设置】->【页面路径】里配置侧边菜单 -->
                    <ul v-if="menuList" class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu"
                        lay-filter="layadmin-system-side-menu">
                        <!--一级-->
                        <template v-for="(item1,index1) in menuList" :key="index1">
                            <template v-if="(item1.admin?admin:true) && (item1.hidden?false:true)">
                                <template v-if="(!item1.children)?true:(!item1.children.length)">
                                    <li :data-name="item1.title" class="layui-nav-item"
                                        :class="item1.type==='shou3ye4_home'?'layui-this':''"
                                        style="position: relative;">
                                        <a
                                            :lay-href="item1.type==='shou3ye4_home'?'<?= $conf['homePath'] ?>':item1.href">
                                            <i :class="item1.icon"></i><cite><span>{{item1.title}}</span></cite>
                                        </a>
                                    </li>
                                </template>
                                <template v-else>
                                    <li :data-name="item1.title" class="layui-nav-item" style="position: relative;">
                                        <a href="javascript:;" :lay-tips="item1.title" lay-direction="2">
                                            <i :class="item1.icon"></i>
                                            <cite><span>{{item1.title}}</span></cite>
                                        </a>
                                        <dl class="layui-nav-child">
                                            <!--二级-->
                                            <template v-for="(item2,index2) in item1.children" :key="index2">
                                                <template v-if="(item2.admin?admin:true) && (item2.hidden?false:true)">
                                                    <template v-if="(!item2.children)?true:(!item2.children.length)">
                                                        <dd>
                                                            <a :lay-href="item2.href">{{item2.title}}</a>
                                                        </dd>
                                                    </template>
                                                    <template v-else>
                                                        <dd>
                                                            <a href="javascript:;">{{item2.title}}</a>
                                                            <dl class="layui-nav-child">
                                                                <!--三级-->
                                                                <template v-for="(item3,index3) in item2.children"
                                                                    :key="index3">
                                                                    <template
                                                                        v-if="(item3.admin?admin:true) && (item3.hidden?false:true)">
                                                                        <dd><a
                                                                                :lay-href="item3.href">{{item3.title}}</a>
                                                                        </dd>
                                                                    </template>
                                                                </template>
                                                            </dl>
                                                        </dd>
                                                    </template>
                                                </template>
                                            </template>
                                        </dl>
                                    </li>
                                </template>
                            </template>
                        </template>

                    </ul>

                    <?php if ($userrow['uid'] == 1) { ?>
                        <div id="LAY-system-side-menu-set" class="center" style="margin-top: 30px;">
                            <div class="layui-font-12">
                                <button lay-href="wzsz#tabid=pagePath"
                                    class="layui-btn layui-btn-primary layui-border layui-btn-sm" title="配置侧边菜单">
                                    <i class="layui-icon layui-icon-set"></i> <span class="bt">配置侧边菜单</span>
                                </button>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>

            <!-- 页面标签 -->
            <div class="layadmin-pagetabs" id="LAY_app_tabs" style="padding: 0 40px 0 0">

                <div class="layui-icon layadmin-tabs-control layui-icon-down">
                    <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
                        <li class="layui-nav-item" lay-unselect>
                            <a href="javascript:;"></a>
                            <dl class="layui-nav-child layui-anim-fadein">
                                <dd layadmin-event="closeThisTabs"><a href="javascript:;">关闭当前</a></dd>
                                <dd layadmin-event="closeOtherTabs"><a href="javascript:;">关闭其它</a></dd>
                                <dd layadmin-event="closeAllTabs"><a href="javascript:;">关闭全部</a></dd>
                            </dl>
                        </li>
                    </ul>
                </div>


                <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs"
                    style="overflow-x: auto;">
                    <ul class="layui-tab-title" id="LAY_app_tabsheader" style="left:0 !important;">
                        <li lay-id="home.php" lay-attr="home.php" class="layui-this">
                            <i class="layui-icon layui-icon-home"></i>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- 主体内容 -->
            <div class="layui-body" id="LAY_app_body" style="">
                <div class="layadmin-tabsbody-item layui-show">
                    <iframe src="<?= $conf['homePath'] ?>" frameborder="0" class="layadmin-iframe"></iframe>
                </div>
            </div>

            <!-- 辅助元素，一般用于移动设备下遮罩 -->
            <div class="layadmin-body-shade" layadmin-event="shade"></div>

        </div>

        <!--全局弹窗公告-->
        <div id="globalNotice" class="layui-padding-2" style="display:none">
            <div style="margin-bottom: 5px;">

                <el-alert class="alert_width100" title="" type="info" show-icon :closable="false">
                    <slot name="title">
                        <div>
                            <span>
                                欢迎使用 <?= $conf["sitename"] ?> ！
                            </span>
                            &nbsp;&nbsp;&nbsp;&nbsp;<el-link
                                @click="window.localStorage.setItem('globalNotice',Date.now());layer.close(globalNoticeIndex)"
                                type="info" style="text-decoration: underline;float: right;" class="layui-font-12"
                                :underline="false">
                                今日不再显示
                            </el-link>
                        </div>
                    </slot>
                </el-alert>
            </div>
            <?= $conf['tcgonggao']; ?>
        </div>

        <!--主题配置-->
        <div id="openThemeColorC" class="layui-padding-2" style="display:none">
            <div class="layui-form themesBox">
                <el-row :gutter="5">

                    <el-col :xs="8" :sm="8" v-for="(themeitem,themeindex) in themesData" :key="themeindex">
                        <div class="themeLi">
                            <input lay-filter="theme-radio-filter" type="radio" name="color" v-model="theme_now"
                                :value="themeindex" :title="themeitem.name" lay-skin="none">
                            <div lay-radio>
                                <div class="themeLiInput">
                                    <div class="lay-skin-color-picker" style="color: red; "
                                        :style=" `background: linear-gradient(135deg, ${themeitem.c1} 60%, ${themeitem.c2} 40%)` ">
                                    </div>
                                    <div class="themeLiInputText">
                                        {{ themeitem.name }}
                                    </div>
                                </div>
                                <hr style="margin: 0px 0 3px;" />
                                <div class="layui-font-12 layui-font-green center" style="scale: .8;">
                                    来源：{{ themeitem.author?themeitem.author:'未知' }}
                                </div>
                            </div>
                        </div>
                    </el-col>

                </el-row>

                <?php if ($userrow['uid'] == 1) { ?>
                    <div class="layui-font-12 center" style="margin-top: 30px;">
                        <button lay-href="wzsz#tabid=theme" class="layui-btn layui-btn-primary layui-border layui-btn-sm"
                            title="配置侧边菜单">
                            <i class="layui-icon layui-icon-set"></i> <span class="bt">配置主题</span>
                        </button>
                    </div>
                <?php } ?>

            </div>
        </div>

    </div>

    <div class="ban"
        style="position: fixed; right: 15px; bottom: 5px; z-index: 999999999999; font-size: 12px; transform-origin: right;scale: .8;text-align: right;color: #b4b4b4;">
        <div id="DEBUGID"></div>
    </div>

</body>



<script>
    if (localStorage.getItem('v') != '<?= $conf["version"] ?>') {
        localStorage.clear();
        sessionStorage.clear();
        const cookies = document.cookie.split(";");
        for (let i = 0; i < cookies.length; i++) {
            const cookie = cookies[i];
            const eqPos = cookie.indexOf("=");
            const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
            document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
        }
        let themesData_default = <?= $conf['themesData'] ?>.filter(i => i.id == '<?= $conf['themesData_default'] ?>')[0];
        localStorage.setItem('theme', JSON.stringify({
            id: themesData_default.id,
            url: themesData_default.url
        }));
        localStorage.setItem('v', '<?= $conf["version"] ?>')
        location.href = "/index/login";
    }

    const uap = new UAParser();
    document.getElementById('DEBUGID').innerHTML = `
        <li>
            <?= $real_ip ?>
        </li>
        <li>
            ${uap.getOS().name} ${uap.getBrowser().name} <?= $userrow["endaddress"] ?>
        </li>
    `;
</script>

<script type="text/javascript">
    if ("<?= $userrow['pass']; ?>" === "<?= $conf['user_pass']; ?>") {
        layui.use(function () {
            layer.open({
                type: 1,
                title: '<p style="display: flex; flex-direction: row;"><i class="layui-icon layui-icon-auz"></i>&nbsp;您的密码不安全</p>',
                offset: 'rb', // 详细可参考 offset 属性
                id: 'ID-demo-layer-offset-', // 防止重复弹出
                content: '<div style="padding: 10px 16px;color:red;font-weight:bold;">请及时更改密码！<br /><br /><button type="button" class="layui-btn layui-btn-primary layui-btn-sm" lay-href="userinfo" lay-text="个人信息" id="passwd"><i class="layui-icon layui-icon-password"></i> 点我修改密码</button></div>',
                area: '150px',
                btn: '',
                closeBtn: 0,
                btnAlign: 'c', // 按钮居中
                shade: 0, // 不显示遮罩
                yes: function () {
                    layer.closeAll();
                }
            });
        })
    }
</script>

<!--主题-->
<script>
    if (localStorage.getItem('theme') === null) {
        let themesData_default = <?= $conf['themesData'] ?>.filter(i => i.id == '<?= $conf['themesData_default'] ?>')[0];
        localStorage.setItem('theme', JSON.stringify({
            id: themesData_default.id,
            url: themesData_default.url
        }));
    }
    let themeLinkClassEl0 = document.querySelectorAll('link.themeLink');
    if (!themeLinkClassEl0.length) {
        let new_themeLink0 = document.createElement('link');
        new_themeLink0.classList.add('themeLink');
        new_themeLink0.rel = 'stylesheet';
        new_themeLink0.media = 'all';
        new_themeLink0.href = JSON.parse(localStorage.getItem('theme')).url;
        document.head.appendChild(new_themeLink0);
    }
</script>

<script>
    const app = Vue.createApp({
        data() {
            return {
                globalNoticeIndex: null,
                admin: '<?= $userrow['uid'] ?>' === '1' ? true : false,
                // 请到【网站设置】->【页面路径】里配置侧边菜单
                menuList: <?= $conf["menuList"] ?>,
                // menuList1: [{
                //     title: '首页',
                //     icon: 'layui-icon layui-icon-home',
                //     href: 'home',
                //     on: true,
                // },
                //     <?php if ($conf["gpt"] == '1') { ?> {
                    //         title: 'TocAI(免费GPT)',
                    //         icon: 'layui-icon layui-icon-senior',
                    //         href: <?= json_encode($conf['gpt_url']) ?>,
                    //     },
                    //     <?php } ?> {
                //     title: '后台管理',
                //     icon: 'layui-icon layui-icon-set',
                //     href: '',
                //     hide: '<?= $userrow['uid'] !== '1' ?>',
                //     children: [{
                //         title: '网站管理',
                //         icon: '',
                //         href: '',
                //         children: [{
                //             title: '网站设置',
                //             icon: '',
                //             href: 'wzsz',
                //         },
                //         {
                //             title: '帮助设置',
                //             icon: '',
                //             href: 'helpsz',
                //         },
                //         ],
                //     },
                //     {
                //         title: '商品管理',
                //         icon: '',
                //         href: '',
                //         children: [{
                //             title: '对接设置',
                //             icon: '',
                //             href: 'djsz',
                //         },
                //         {
                //             title: '分类设置',
                //             icon: '',
                //             href: 'flsz',
                //         },
                //         {
                //             title: '商品设置',
                //             icon: '',
                //             href: 'spsz',
                //         },
                //         {
                //             title: '密价设置',
                //             icon: '',
                //             href: 'mjsz',
                //         },
                //         ],
                //     },
                //     {
                //         title: '代理等级',
                //         icon: '',
                //         href: 'dldj',
                //     },
                //     {
                //         title: '卡密管理',
                //         icon: '',
                //         href: 'kamisz',
                //     },
                //     ],
                // },
                // {
                //     title: '学习中心',
                //     icon: 'layui-icon layui-icon-component',
                //     href: '',
                //     children: [{
                //         title: '提交订单',
                //         icon: '',
                //         href: 'add_pl',
                //     },
                //     {
                //         title: '订单管理',
                //         icon: '',
                //         href: 'list',
                //     },
                //         <?php if ($conf["onlineStore_open"] == '1') { ?> {
                    //             title: '接单商城',
                    //             icon: '',
                    //             href: 'tourist_spsz',
                    //         },
                    //         <?php } ?>
                //             <?php if ($conf["axqg"] == '1') { ?> {
                    //             title: '强国学习',
                    //             icon: '',
                    //             href: '',
                    //             children: [{
                    //                 title: 'ax强国',
                    //                 icon: '',
                    //                 href: 'axqg',
                    //             },],
                    //         },
                    //         <?php } ?>
                //     ],
                // },
                // {
                //     title: '个人中心',
                //     icon: 'layui-icon layui-icon-username',
                //     href: '',
                //     children: [{
                //         title: '个人信息',
                //         icon: '',
                //         href: 'userinfo',
                //     },
                //     {
                //         title: '操作日志',
                //         icon: '',
                //         href: 'log',
                //     },
                //     ],
                // },
                // {
                //     title: '在线充值',
                //     icon: 'layui-icon layui-icon-rmb',
                //     href: 'pay',
                // },
                // {
                //     title: '提交工单',
                //     icon: 'layui-icon layui-icon-survey',
                //     href: 'gongdan',
                // },
                // {
                //     title: '代理管理',
                //     icon: 'layui-icon layui-icon-user',
                //     href: 'userlist',
                // },
                // {
                //     title: '便携功能',
                //     icon: 'layui-icon layui-icon-find-fill',
                //     href: '',
                //     children: [{
                //         title: '销量热榜',
                //         icon: '',
                //         href: 'hots',
                //     }, {
                //         title: '学习价格',
                //         icon: '',
                //         href: 'myprice',
                //     },
                //     {
                //         title: '对接文档',
                //         icon: '',
                //         href: 'docking',
                //     },
                //     {
                //         title: '帮助文档',
                //         icon: '',
                //         href: 'help',
                //     },
                //     ]
                // },
                // {
                //     title: '云端任务',
                //     icon: 'layui-icon layui-icon-template-1',
                //     href: 'btManage',
                //     hide: '<?= $userrow['uid'] !== '1' ?>',
                // },
                // {
                //     title: '内置图标',
                //     icon: 'layui-icon layui-icon-template-1',
                //     href: 'components/iconPreview',
                //     hide: '<?= $userrow['uid'] !== '1' ?>',
                // },
                // ],
                appLayer: [],
                fixbarxXY: {
                    isDragging: false,
                    move: false,
                    X: 0,
                    Y: 0,
                    X1: 0,
                    Y1: 0,
                },
                themesData: <?= $conf['themesData'] ?>,
                autoGet_data: {
                    gongdan: { num: 0, need: 0 },
                    money: { num: 0, need: 0 },
                    djOrder: { num: 0, need: 0 },
                },
            }
        },
        computed: {
            theme_now() {
                return this.themesData.findIndex(i => i.id == JSON.parse(localStorage.getItem('theme')).id && i.url == JSON.parse(localStorage.getItem('theme')).url);
            },
            window_WH() {
                return {
                    width: $(window).width(),
                    height: $(window).height(),
                }
            },
            autoGet_data_need_reduce() {
                let autoGet_data_value = Object.values(this.autoGet_data);
                let need_num = autoGet_data_value.reduce((acc, cur) => acc + (cur.need ? cur.need : 0), 0);
                return need_num ? need_num : 0;
            },
        },
        mounted() {
            const _this = this;

            // 若是锁屏状态
            if (localStorage.getItem('lockscreen') != null) {
                _this.lockscreenOpen();
            }

            $("#LAY_app").ready(() => {
                $("#LAY_app").show();

                // 消息盒子
                layui.dropdown.render({
                    elem: '#messageBox_dropdown',
                    className: "messageBox_dropdown",
                    trigger: "hover",
                    click: (data) => {
                        layui.admin.tabsAdd({
                            title: data.title,
                            href: data.href,
                        })
                    }
                })
                setTimeout(() => {
                    _this.messageBox_reloadData();
                }, 2000)
                _this.messageBox_data();
                setInterval(() => {
                    _this.messageBox_data();
                }, 3000)

                <?php if ($conf['tcgonggao_open']) { ?>
                    setTimeout(function () {
                        _this.openNotice();
                    }, 300);
                <?php } ?>

                // _this.$notify({
                //     type: 'success',
                //     title: '访问成功！',
                //     dangerouslyUseHTMLString: true,
                //     duration: 10000,
                //     message: `
                //             <div>
                //                 当前访问信息：
                //                 <table class="layui-table" lay-size="sm" style="margin-top: 3px;">
                //                     <tr>
                //                         <td class="center" style="width: 20%;">
                //                             IP
                //                         </td>
                //                         <td>
                //                             <?= $real_ip ?>
                //                         </td>
                //                     </tr>
                //                     <tr>
                //                         <td class="center" style="width: 20%;">
                //                             地址
                //                         </td>
                //                         <td>
                //                             <?= get_ip_city($real_ip) ?>
                //                         </td>
                //                     </tr>
                //                 </table>
                //                 上次登录信息：
                //                 <table class="layui-table" lay-size="sm" style="margin-top: 3px;">
                //                     <tr>
                //                         <td style="width: 20%; text-align: center;">
                //                             IP
                //                         </td>
                //                         <td>
                //                             <?= $userrow["endip"] ?>
                //                         </td>
                //                     </tr>
                //                     <tr>
                //                         <td class="center" style="width: 20%;">
                //                             地址
                //                         </td>
                //                         <td>
                //                             <?= $userrow["endaddress"] ?>
                //                         </td>
                //                     </tr>
                //                     <tr>
                //                         <td class="center" style="width: 20%;">
                //                             时间
                //                         </td>
                //                         <td>
                //                             <?= $userrow["endtime"] ?>
                //                         </td>
                //                     </tr>
                //                 </table>
                //             </div>
                //         `,
                //     position: 'bottom-right',
                //     customClass: 'access_notify',
                // });
                // if ($('.lockscreenDemoBox').length) {
                //     $('.access_notify').css('z-index', $('.lockscreenDemoBox').css('z-index') - 1);
                // } else {
                //     $('.access_notify').css('z-index', 999999999);
                // }

                layui.use(() => {
                    // 设置主题
                    layui.form.on('radio(theme-radio-filter)', function (data) {
                        if (JSON.parse(localStorage.getItem('theme')).id != _this.themesData[data.elem.value].id) {
                            console.log(JSON.parse(localStorage.getItem('theme')).id, _this.themesData[data.elem.value].id)
                            let themeLoadIndex = layer.msg('切换主题中...', {
                                icon: 16,
                                shade: 0.01,
                                time: 0,
                            });
                            localStorage.setItem('theme', JSON.stringify({
                                id: _this.themesData[data.elem.value].id,
                                url: _this.themesData[data.elem.value].url

                            }));
                            let themeLinkClassEl = document.querySelectorAll('link.themeLink');
                            if (!themeLinkClassEl.length) {
                                let new_themeLink0 = document.createElement('link');
                                new_themeLink0.classList.add('themeLink');
                                new_themeLink0.rel = 'stylesheet';
                                new_themeLink0.media = 'all';
                                new_themeLink0.href = JSON.parse(localStorage.getItem('theme')).url;
                                document.head.appendChild(new_themeLink0);
                            }
                            themeLinkClassEl.forEach(function (link) {
                                link.href = JSON.parse(localStorage.getItem('theme')).url;
                            });
                            setTimeout(() => {
                                layer.close(themeLoadIndex);
                            }, 200)
                        }

                    })
                })

                // 天气
                window.SeniverseWeatherWidget('show', {
                    flavor: "slim",
                    location: "WX4FBXXFKE4F",
                    geolocation: true,
                    language: "zh-Hans",
                    unit: "c",
                    theme: "light",
                    token: "33a5e045-e4ad-412d-9389-a8392966a726",
                    hover: "disabled",
                    container: "tp-weather-widget"
                })

            })
        },
        methods: {
            messageBox_reloadData() {
                const _this = this;

                let data3 = _this.autoGet_data;
                console.log("data3", data3)

                let data4 = [];
                let needNum = 0;
                for (let i in data3) {
                    if (data3[i].need) {
                        data4.push({
                            id: i,
                            title: "",
                            templet: `
                                <div lay-href="${data3[i].href}" lay-text="${data3[i].text}" style="display: flex;justify-content: space-between;align-items: center;">
                                    <div style="width: auto;">
                                        <a>
                                            ${data3[i].abnormal_t}
                                        </a>
                                    </div>
                                    <div style="flex: auto; padding-left: 1px; position: relative; top: -1px;">
                                        <span style="display: inline-block; background: #ff5722; color: #ffffff; border-radius: 50%; font-size: 12px; width: 18px; height: 18px; line-height: normal; text-align: center;scale: .7;">
                                            ${data3[i].num}
                                        </span>
                                    </div>
                                </div>`
                        })
                        needNum++;
                    } else {
                    }
                }
                console.log("data4", data4, needNum)

                if (!needNum) {
                    data4.push({
                        id: "no_need",
                        templet: `暂无需要处理的消息`,
                        disabled: true,
                    })
                }

                data4.unshift({
                    id: "messageBox_title",
                    title: "消息盒子",
                    templet: `
                            <div style="font-size: 12px;" class="title">
                                {{= d.title }} <span style="padding-left: 6px;scale: .8; display: inline-block; transform-origin: left center;">${_this.autoGet_data_need_reduce ? _this.autoGet_data_need_reduce + " 项待处理" : ""}</span>
                            </div>
                           `,
                    disabled: true,
                }, {
                    type: "-",
                })

                layui.dropdown.reloadData("messageBox_dropdown", {
                    data: data4,
                })

            },
            messageBox_data() {
                const _this = this;
                axios.post('/apiadmin.php?act=messageBox_data', {

                }).then(r => {
                    if (r.data.code === 1) {
                        _this.autoGet_data = r.data.data;
                        console.log("_this.autoGet_data", _this.autoGet_data)
                        _this.messageBox_reloadData();
                    } else {
                        layer.msg('获取系统状态失败')
                    }
                })
            },
            TPDS_open() {
                layer.open({
                    id: 'TPDS_Layer_ID',
                    type: 1,
                    shade: 0, // 不显示遮罩
                    title: '<i class="layui-icon layui-icon-chart-screen"></i> TPDS',
                    area: ["360px", 200 + 'px'],
                    offset: 'rt',
                    content: "2",
                })
            },
            isToday(ms) {
                ms = Number(ms);
                const today = new Date();
                const todayYear = today.getFullYear();
                const todayMonth = today.getMonth();
                const todayDate = today.getDate();

                const givenDate = new Date(ms);
                const givenYear = givenDate.getFullYear();
                const givenMonth = givenDate.getMonth();
                const givenDay = givenDate.getDate();

                return todayYear === givenYear && todayMonth === givenMonth && todayDate === givenDay;
            },
            openThemeColor() {
                const _this = this;
                let openThemeColorLayerIndex = layer.open({
                    id: 'openThemeColorLayer',
                    type: 1,
                    shade: 0, // 不显示遮罩
                    title: '主题切换器',
                    area: ["360px", _this.window_WH.height + 'px'],
                    offset: 'r',
                    scrollbar: false,
                    content: $("#openThemeColorC"), // 捕获的元素
                    success: function (layero, index) { },
                    end: function () { },
                });
            },
            // 打开链接
            openNewHref(url, type = '') {
                if (!type) {
                    window.open(url);
                } else {
                    location.href = url;
                }
            },
            // 弹窗形式打开应用
            openApp(id = 0, title = "应用未命名", content = "未指定内容", height = "auto", offset = "auto") {
                const _this = this;
                let appIndex = layer.open({
                    id: id,
                    type: 2,
                    shade: 0, // 不显示遮罩
                    title: title,
                    area: ["350px", height],
                    offset: offset,
                    maxmin: true,
                    content: content, // 捕获的元素
                    success: function (layero, index) {
                        var iframe = layero.find('iframe');
                        $(iframe).ready(() => {
                            layer.close(loadIndex);
                        })
                    },
                    end: function () {
                        _this.appLayer = _this.appLayer.filter(i => i !== id)
                    },
                });

                if (!_this.appLayer.find(i => i === id)) {
                    var loadIndex = layer.load(0);
                } else {
                    _this.appLayer = _this.appLayer.filter(i => i !== id)
                }
                _this.appLayer.push(id);
            },
            // 打开弹窗公告
            openNotice() {
                const _this = this;
                if (!_this.isToday(localStorage.getItem("globalNotice"))) {
                    _this.globalNoticeIndex = layer.open({
                        type: 1,
                        title: '公告',
                        id: "globalNoticeID",
                        content: $('#globalNotice'),
                        time: 15000,
                        btn: '朕知道了',
                        btnAlign: 'c', //按钮居中
                        shade: 0.4, //遮罩
                        closeBtn: 0,
                        area: ['360px', 'auto'],
                        time: 20 * 1000,
                        scrollbar: false,
                        success: function (layero, index) {
                            var timeNum = this.time / 1000,
                                setText = function (start) {
                                    layer.title('公告&nbsp;&nbsp;&nbsp;&nbsp;<span class="layui-font-12"><font class="layui-font-red">' + (start ? timeNum : --timeNum) + '</font> 秒后自动关闭</span>', index);
                                };
                            setText(!0);
                            this.timer = setInterval(setText, 1000);
                            if (timeNum <= 0) clearInterval(this.timer);

                        },
                        end: function () {
                            clearInterval(this.timer);
                        }
                    });
                }
            },
            sjqyOpen() {
                layer.prompt({
                    title: '请输入要转移的上级UID',
                    formType: 3
                }, function (uid, index) {
                    layer.close(index);
                    layer.prompt({
                        title: '请输入要转移的上级邀请码',
                        formType: 3
                    }, function (yqm, index) {
                        layer.close(index);
                        var load = layer.load();
                        $.ajax({
                            type: "POST",
                            url: "../apiadmin.php?act=sjqy",
                            data: {
                                "uid": uid,
                                "yqm": yqm
                            },
                            dataType: 'json',
                            success: function (data) {
                                layer.close(load);
                                if (data.code == 1) {
                                    layer.msg(data.msg, {
                                        icon: 1
                                    });
                                } else {
                                    layer.msg(data.msg, {
                                        icon: 2
                                    });
                                }
                            }
                        });
                    });
                });
            },
            szyqpriceOpen() {
                layer.prompt({
                    title: '设置邀请默认费率，首次自动生成邀请码',
                    formType: 3
                }, function (yqprice, index) {
                    var load = layer.load();
                    $.post("/apiadmin.php?act=yqprice", {
                        yqprice
                    }, function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            layer.close(index);
                            layer.msg("设置成功！")
                        } else {
                            layer.msg(data.msg);
                        }
                    });
                });
            },
            lockscreen() {
                const _this = this;
                layer.prompt({
                    title: '请自定义本次锁屏密码',
                    formType: 1
                }, function (value, index, elem) {
                    if (value === '') return elem.focus();
                    localStorage.setItem('lockscreen', value);
                    _this.lockscreenOpen();
                    // // 关闭 prompt
                    layer.close(index);
                });
            },
            lockscreenOpen() {
                layer.open({
                    type: 1,
                    shade: 0,
                    title: false, // 禁用标题栏
                    closeBtn: false, // 禁用默认关闭按钮
                    area: ['100vw', '100vh'],
                    scrollbar: false, // 暂时屏蔽浏览器滚动条
                    anim: -1, // 禁用弹出动画
                    isOutAnim: false, // 禁用关闭动画
                    resize: false, // 禁用右下角拉伸尺寸
                    id: 'lockscreenLayer',
                    skin: 'lockscreenDemoBox',
                    zIndex: 9999999999,
                    content: ['<div class="layui-form">',
                        '<div class="layui-input-wrap" style="box-shadow: 0 2px 15px rgba(0, 0, 0, .6), 0 0 6px rgba(0, 0, 0, .04);">',
                        '<input type="password" class="class-demo-layer-pin" lay-affix="eye" placeholder="请输入锁屏密码...">',
                        '<div class="layui-input-suffix">',
                        '<i class="layui-icon layui-icon-right" id="ID-layer-lockscreen-unlock"></i>',
                        '</div>',
                        '</div>',
                        '<div style="text-shadow: 1px 1px 2px black,-1px -1px 2px black,1px -1px 2px black,-1px 1px 2px black,0 0 5px #666">输入锁屏密码，即可退出锁屏示例</div>',
                        '</div>'
                    ].join(''),
                    success: function (layero, index) {
                        var input = layero.find('input');
                        var PASS = localStorage.getItem('lockscreen');

                        layui.form.render();
                        input.focus();
                        // 点击解锁按钮
                        var elemUnlock = layero.find('#ID-layer-lockscreen-unlock');
                        elemUnlock.on('click', function () {
                            if ($.trim(input[0].value) === PASS) {
                                layer.close(index);
                                layer.closeLast('dialog');
                                localStorage.removeItem('lockscreen');
                            } else {
                                console.log(layer.zIndex)
                                console.log(layer.index)
                                layer.msg('锁屏密码输入有误', {
                                    offset: '16px',
                                    anim: 'slideDown',
                                    zIndex: $('.lockscreenDemoBox').css('z-index') + 1
                                })
                                input.focus();
                            }
                        });
                        // 回车
                        input.on('keyup', function (e) {
                            var elem = this;
                            var keyCode = e.keyCode;
                            if (keyCode === 13) {
                                elemUnlock.trigger('click');
                            }
                        });
                    }
                })
            },
        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var LAY_appVm = app.mount('#LAY_app');
    // -----------------------------
</script>

<!--水印开关-->
<?php if ($conf['sykg'] == 1) { ?>
    <script src="assets/js/sy.js?v=1.0.0"></script>
    <script type="text/javascript">
        watermark('禁止截图，截图封户', '昵称 : <?= $userrow['name']; ?>', '账号:<?= $userrow['user']; ?>');
    </script>
<? } ?>

<!--天气-->
<script defer>
    (function (a, h, g, f, e, d, c, b) {
        b = function () {
            d = h.createElement(g);
            c = h.getElementsByTagName(g)[0];
            d.src = e;
            d.charset = "utf-8";
            d.async = 1;
            c.parentNode.insertBefore(d, c)
        };
        a["SeniverseWeatherWidgetObject"] = f;
        a[f] || (a[f] = function () {
            (a[f].q = a[f].q || []).push(arguments)
        });
        a[f].l = +new Date();
        if (a.attachEvent) {
            a.attachEvent("onload", b)
        } else {
            a.addEventListener("load", b, false)
        }
    }(window, document, "script", "SeniverseWeatherWidget", "//cdn.sencdn.com/widget2/static/js/bundle.js?t=" + parseInt((new Date().getTime() / 100000000).toString(), 10)));
</script>

</html>