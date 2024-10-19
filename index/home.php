<?php
include_once('head.php');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">

</head>

<style>
    .el-row > .el-col {
        margin-bottom: 5px;
    }
    
    .el-row_col-b2 {
        margin-bottom: 0 !important;
    }

    .layui-icon {
        font-size: inherit;
    }

    @media (max-width: 880px) {
        .el-card__body>div {
            height: auto !important;
        }

        .el-card__body>div>.layui-hide-xs {
            display: none;
        }
    }

    .infinite {
        animation-duration: 1.5s;
        animation-iteration-count: infinite;
    }

    .marquee1 {
        overflow: hidden;
        white-space: nowrap;
    }

    .marquee1 span {
        display: inline-block;
        padding-left: 100%;
        animation: marquee1 10s linear infinite;
    }

    @keyframes marquee1 {
        0% {
            transform: translateX(-20%);
        }

        100% {
            transform: translateX(-100%);
        }
    }

    .userinfo_top {
        position: relative;
        padding: 10px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .userinfo_top::after {
        content: "";
        position: absolute;
        width: 80%;
        height: 1px;
        background: #f6f6f6;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
    }

    .userinfo_bottom {
        position: relative;
        padding: 10px;
    }

    .userinfo_bottom .el-timeline-item {
        padding-bottom: 1px;
        height: 20px;
        line-height: 20px;
    }

    .userinfo_bottom .el-timeline-item .el-timeline-item__content {
        font-size: 12px;
        position: relative;
        top: -4px;
        left: -10px;
        color: #898383;
    }

    .userinfo_bottom .el-timeline-item .el-timeline-item__wrapper {
        top: -2px;
    }
    
    #LogsVM .el-collapse{
        border: 0;
    }
    
    #LogsVM .el-card__body{
        padding: 0 15px;
    }
    
    #LogsVM .el-timeline .bgt .el-timeline-item__node{
        background: transparent;
    }
    
    #LogsVM .el-timeline .bgt .el-timeline-item__node::after{
        content: '';
        width: 100%;
        height: 100%;
        position: absolute;
        background: #f1f0f0;
        border-radius: 50%;
        z-index: -1;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        padding: 3px 3px;
    }
    
    #LogsVM .el-timeline .bgt .el-timeline-item__node i{
        color: #409eff;
    }
</style>

<body>

    <div id="userinfo" class="layui-padding-1" style="display:none;">

        <div v-if="home_top_notice_open" class="layui-panel layui-padding-2" style="margin-bottom: 5px; white-space: nowrap; display: flex; align-items: center; overflow: hidden; height: 19px;">
            <i class="layui-icon layui-icon-speaker" style="border-right: 1px solid #c5afaf; padding-right: 4px; margin-right: 6px;color: inherit;"></i>
            <div style="flex: auto; overflow-x: auto;padding: 15px 0;">
                <? echo $conf['home_top_notice'] ?>
            </div>
        </div>

        <!--快捷操作-->
        <el-row id="quickOP_HOME" :gutter="5" class="">

            <el-col :sm="8" :md="6">
                <el-row style="height: 100%;">
                    <el-col :md="24" class="el-row_col-b2">
                        <div class="layui-panel " style="height: 100%;">

                            <div class="userinfo_top">
                                <div style="position: absolute; right: 3px; top: 3px; color: #aaaaaa;">
                                    <el-button circle size="small" text title="个人信息设置" lay-href="userinfo" lay-text="个人信息">
                                        <el-icon>
                                            <Setting />
                                        </el-icon>
                                    </el-button>
                                    <el-tooltip
                                        class="box-item"
                                        effect="dark"
                                        content="若有问题请优先联系上级解决，还未解决再交工单！"
                                        placement="top">
                                        <el-icon style="top: 4px;">
                                            <Warning />
                                        </el-icon>
                                    </el-tooltip>

                                </div>
                                <div>
                                    <img style="border-radius:50%;width:auto;height: 40px;"
                                        src="https://q2.qlogo.cn/headimg_dl?dst_uin=<?= $userrow['user']; ?>&spec=100"
                                        alt="<?= $userrow['name']; ?>">
                                </div>
                                <div>
                                    <div class="layui-font-16" style="font-weight: bold;">
                                        Hello！<?= $userrow['name']; ?>
                                    </div>
                                    <div class="layui-font-12" style="top: 0; position: relative;scale: .8; transform-origin: left;white-space: nowrap;">
                                        <el-tag type="success" style="padding: 0 6px;margin-right:3px;margin-bottom:3px;">
                                            UID：<?= $userrow['uid']; ?>
                                        </el-tag>
                                        <el-tag type="primary" style="padding: 0 6px;margin-right:3px;margin-bottom:3px;">
                                            费率：<?= $userrow['addprice']; ?>
                                        </el-tag>
                                        <el-tag type="danger" style="padding: 0 6px;margin-right:3px;margin-bottom:3px;">
                                            ¥ {{userInfo.money}}
                                        </el-tag>
                                    </div>
                                </div>
                            </div>

                            <div class="userinfo_bottom">
                                <el-timeline>
                                    <el-timeline-item :hollow="true">
                                        <div style="display: flex; align-items: center; justify-content: space-between; overflow-x: hidden; white-space: nowrap;">
                                            <div>
                                                Key: <span v-if="row.key">{{ row.key }}</span><span v-else>未开通</span>
                                            </div>
                                            <div v-if="row.key" style="opacity: .4;" title="点击复制">
                                                <el-button @click="copyT(row.key)" circle size="small" text>
                                                    <el-icon><Document-Copy /></el-icon>
                                                </el-button>
                                            </div>
                                            <div v-else style="opacity: .4;" title="点击开通">
                                                <el-button @click="ktapi()" size="small" text>
                                                    点击开通
                                                </el-button>
                                            </div>
                                        </div>
                                    </el-timeline-item>
                                    <el-timeline-item :hollow="true">
                                        <div style="display: flex; align-items: center; justify-content: space-between; overflow-x: hidden; white-space: nowrap;">
                                            <div>
                                                邀请码: <span v-if="row.yqm">{{ row.yqm }}</span><span v-else>未开通</span>
                                            </div>
                                            <div v-if="row.yqm" style="opacity: .4;" title="点击复制">
                                                <el-button @click="copyT(row.yqm)" circle size="small" text>
                                                    <el-icon><Document-Copy /></el-icon>
                                                </el-button>
                                            </div>
                                            <div v-else style="opacity: .4;" title="点击生成">
                                                <el-button @click="scyqm()" size="small" text>
                                                    点击生成
                                                </el-button>
                                            </div>
                                        </div>
                                    </el-timeline-item>
                                    <el-timeline-item :hollow="true">
                                        <div style="display: flex; align-items: center; justify-content: space-between; overflow-x: hidden; white-space: nowrap;">
                                            <div>
                                                上级: <? echo '【' . $sj['uid'] . '】' . $sj['qq'] ?>
                                            </div>
                                            <div style="opacity: .4;" title="点击联系">
                                                <el-button @click="contactUU" circle size="small" text>
                                                    <el-icon>
                                                        <Service />
                                                    </el-icon>
                                                </el-button>
                                            </div>
                                        </div>
                                    </el-timeline-item>
                                    <el-timeline-item :hollow="true">
                                        <div style="display: flex; align-items: center; justify-content: space-between; overflow-x: hidden; white-space: nowrap;">
                                            <div>
                                                <el-icon style="position: relative;top: 2px;">
                                                    <Cpu />
                                                </el-icon> CPU {{ osIfnoData.cpu }}&nbsp;
                                                负载 {{ osIfnoData.fz }}&nbsp;
                                                内存 {{ osIfnoData.nc }}
                                            </div>
                                            <div style="opacity: .4;" title="点击联系">
                                            </div>
                                        </div>
                                    </el-timeline-item>
                                </el-timeline>
                            </div>

                        </div>
                    </el-col>

                </el-row>
            </el-col>

            <el-col :sm="16" :md="18" class="layui-hide-xs el-row_col-b2">
                <el-row :gutter="5" style="height: 100%;">
                    <template v-for="(item,index) in quickNav" :key="index">
                        <el-col :xs="12" :sm="8">
                            <div class="layui-panel" style="height: 100%;">
                                <a :lay-href="item.u" :lay-text="item.t2" style="height:  100%;display:block;">
                                    <div class="center" style="padding: 15px;">
                                        <div style="margin-bottom:6px;" :class="item.c">
                                            <el-icon v-if="item.ei" style="font-size:28px;">
                                                <component :is="item.ei"></component>
                                            </el-icon>
                                            <i v-else :class="item.i" style="font-size:28px;"></i>
                                        </div>
                                        <div class="layui-font-13">
                                            {{ item.t1 }}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </el-col>
                    </template>
                </el-row>
            </el-col>

        </el-row>

        <el-row :gutter="5">
            <el-col :xs="24" :sm="12" class="el-row_col-b2">
                <el-row :gutter="5">
                    <el-col :xs="24" :sm="24">
                        <div class="layui-panel">
                            <div slot="header" class="clearfix layui-card-header">
                                <span>上级公告(<?php echo $sj['uid']; ?>)</span>
                                <el-link class="layui-font-12" style="float:right;" @click="contactUU">
                                    联系上级
                                </el-link>
                            </div>
                            <div class="layui-card-body" style="height: 30px; overflow-y: auto;">
                                <?php echo $sj['notice'] ? $sj['notice'] : '<span class="layui-font-green">暂无</span>'; ?>
                            </div>
                        </div>
                    </el-col>
                    <el-col :xs="24" :sm="24" class="el-row_col-b2">
                        <el-row :gutter="5">
                            <?php if($userrow["uid"] == 1){ ?>
                                <div lay-href="XCharts" lay-text="XCharts" title="XCharts" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); z-index: 1; background: #ffffff; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;box-shadow: 0px 12px 32px 4px rgba(0, 0, 0, .04), 0px 8px 20px rgba(0, 0, 0, .08);cursor: pointer;">
                                    <el-icon class="animate__animated animate__jello infinite" :size="18"><pie-chart /></el-icon>
                                </div>
                            <?php } ?>
                            <el-col :xs="12" :sm="12">
                                <div class="layui-panel">
                                    <div slot="header" class="clearfix layui-card-header">
                                        <span><el-icon>
                                                <Wallet />
                                            </el-icon> 余额</span>
                                        <el-link lay-href="pay" class="layui-font-12" style="float:right;">
                                            充值 &nbsp;<el-icon>
                                                <Right />
                                            </el-icon>
                                        </el-link>
                                    </div>

                                    <div class="layui-card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;height: 65px;">
                                        <div>
                                            <h3 style="margin-bottom: 5px;">

                                                {{userInfo.money}}
                                            </h3>
                                            <div class="layui-font-gray layui-font-12">
                                                总充值：<?php echo $userrow['zcz'] . ""; ?>
                                            </div>
                                        </div>
                                        <div class="layui-hide-xs">
                                            <el-progress type="circle" :percentage="
											    progressOK?(<?= number_format((is_nan($userrow['money'] / $userrow['zcz']) ? 0 : $userrow['money'] / $userrow['zcz']) * 100, 2) ?>).toFixed(2):0
											" width="60" :stroke-width="4"></el-progress>
                                        </div>
                                    </div>
                                </div>
                            </el-col>
                            <el-col :xs="12" :sm="12">
                                <div class="layui-panel">
                                    <div slot="header" class="clearfix layui-card-header">
                                        <span><el-icon><Shopping-Bag /></el-icon> 订单</span>
                                        <el-link lay-href="add_pl" class="layui-font-12" style="float:right;">
                                            提交&nbsp;<el-icon>
                                                <Right />
                                            </el-icon>
                                        </el-link>
                                    </div>
                                    <div class="layui-card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;height: 65px;">
                                        <div>
                                            <h3 style="margin-bottom: 5px;">
                                                <?php
                                                if ($userrow['uid'] != "1") {
                                                    $a = $DB->count("select count(*) from qingka_wangke_order where uid='{$userrow['uid']}'");
                                                    echo $a . "";
                                                } else {
                                                    $a = $DB->count("select count(*) from qingka_wangke_order");
                                                    echo $a . "";
                                                }
                                                ?>

                                            </h3>
                                            <div class="layui-font-gray layui-font-12">
                                                今日：
                                                <?php
                                                if ($userrow['uid'] != "1") {
                                                    $a = $DB->count("select count(*) from qingka_wangke_order where addtime>'$jtdate' and uid='{$userrow['uid']}'");
                                                    echo $a . "";
                                                } else {
                                                    $a = $DB->count("select count(*) from qingka_wangke_order where addtime>'$jtdate'");
                                                    echo $a . "";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="layui-hide-xs">
                                            <el-progress type="circle" :percentage="progressOK?((isNaN(<?php
                                                                                                        if ($userrow['uid'] != "1") {
                                                                                                            $a = $DB->count("select count(*) from qingka_wangke_order where addtime>'$jtdate' and uid='{$userrow['uid']}'");
                                                                                                            echo $a . "";
                                                                                                        } else {
                                                                                                            $a = $DB->count("select count(*) from qingka_wangke_order where addtime>'$jtdate'");
                                                                                                            echo $a . "";
                                                                                                        }
                                                                                                        ?>/<?php
                                                                                                            if ($userrow['uid'] != "1") {
                                                                                                                $a = $DB->count("select count(*) from qingka_wangke_order where uid='{$userrow['uid']}'");
                                                                                                                echo $a . "";
                                                                                                            } else {
                                                                                                                $a = $DB->count("select count(*) from qingka_wangke_order");
                                                                                                                echo $a . "";
                                                                                                            }
                                                                                                            ?>)?0:<?php
                                                            if ($userrow['uid'] != "1") {
                                                                $a = $DB->count("select count(*) from qingka_wangke_order where addtime>'$jtdate' and uid='{$userrow['uid']}'");
                                                                echo $a . "";
                                                            } else {
                                                                $a = $DB->count("select count(*) from qingka_wangke_order where addtime>'$jtdate'");
                                                                echo $a . "";
                                                            }
                                                            ?>/<?php
                                                                if ($userrow['uid'] != "1") {
                                                                    $a = $DB->count("select count(*) from qingka_wangke_order where uid='{$userrow['uid']}'");
                                                                    echo $a . "";
                                                                } else {
                                                                    $a = $DB->count("select count(*) from qingka_wangke_order");
                                                                    echo $a . "";
                                                                }
                                                                ?>)*100).toFixed(2):0" width="60" :stroke-width="4">

                                            </el-progress>
                                        </div>
                                    </div>
                                </div>
                            </el-col>
                            <el-col :xs="12" :sm="12">
                                <div class="layui-panel">
                                    <div slot="header" class="clearfix layui-card-header">
                                        <span><el-icon>
                                                <User />
                                            </el-icon> 代理</span>
                                        <el-link lay-href="userlist" class="layui-font-12" style="float:right;">
                                            添加 &nbsp;<el-icon>
                                                <Right />
                                            </el-icon>
                                        </el-link>
                                    </div>
                                    <div class="layui-card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;height: 65px;">
                                        <div>
                                            <h3 style="margin-bottom: 5px;">
                                                <?php
                                                if ($userrow['uid'] != "1") {
                                                    $a = $DB->count("select count(*) from qingka_wangke_user where uuid='{$userrow['uid']}'");
                                                    echo $a . "";
                                                } else {
                                                    $a = $DB->count("select count(*) from qingka_wangke_user");
                                                    echo $a - 2 . "";
                                                }
                                                ?>

                                            </h3>
                                            <div class="layui-font-gray layui-font-12">
                                                今日开通：
                                                <?php
                                                if ($userrow['uid'] != "1") {
                                                    $a = $DB->count("select count(*) from qingka_wangke_user where addtime>'$jtdate' and uuid='{$userrow['uid']}'");
                                                    echo $a  . "";
                                                } else {
                                                    $a = $DB->count("select count(*) from qingka_wangke_user where addtime>'$jtdate'");
                                                    echo $a  . "";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="layui-hide-xs">
                                            <el-progress type="circle" :percentage="progressOK?computed_todaykaitong:0" width="60" :stroke-width="4">
                                            </el-progress>
                                        </div>
                                    </div>
                                </div>
                            </el-col>
                            <el-col :xs="12" :sm="12">
                                <div class="layui-panel">
                                    <div slot="header" class="clearfix layui-card-header">
                                        <span><i class="layui-icon layui-icon-test"></i> API下单率</span>
                                        <el-link lay-href="log" class="layui-font-12" style="float:right;">
                                            日志 &nbsp;<el-icon>
                                                <Right />
                                            </el-icon>
                                        </el-link>
                                    </div>
                                    <div class="layui-card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;height: 65px;">
                                        <div>
                                            <h3 style="margin-bottom: 5px;">
                                                {{ progressOK?(isNaN(<?= $userrow['xd'] ?> / <?= $userrow['xd'] + $userrow['ck'] ?>  )?0:(<?= $userrow['xd'] ?> / <?= $userrow['xd'] + $userrow['ck'] ?>*100).toFixed(2)):0 }} %
                                            </h3>
                                            <div class="layui-font-gray layui-font-12">
                                                查课差值限制：<?php echo $conf['api_ck_threshold']; ?>
                                            </div>
                                        </div>
                                        <div class="layui-hide-xs">
                                            <el-progress type="circle" :percentage="progressOK?isNaN(<?= $userrow['xd'] ?> / <?= $userrow['xd'] + $userrow['ck'] ?>  )?0:(<?= $userrow['xd'] ?> / <?= $userrow['xd'] + $userrow['ck'] ?>*100).toFixed(2):0" width="60" :stroke-width="4"></el-progress>
                                        </div>
                                    </div>
                                </div>
                            </el-col>
                        </el-row>
                    </el-col>
                </el-row>
            </el-col>
            <el-col :xs="24" :sm="12">
                <div class="layui-panel">
                    <div class="layui-card-header cl_position">
                        <div style="display: inline-flex; align-items: center; justify-content: left; gap: 2px;position: relative;">
                            <i class="layui-icon layui-icon-notice "></i>
                            &nbsp;实时公告
                        </div>
                        <span v-if="uid" style="float:right;">
                            &nbsp;<button class="layui-btn layui-btn-xs layui-bg-blue" @click="homenotice_open">管理公告</button>
                        </span>
                    </div>
                    <div class="layui-card-body" style="height: 300px !important; overflow-y: auto; word-wrap: break-word; word-break: normal;">
                        
                        <template v-if="(notice_open&&row.homenotice)">
                            <div v-if="!row.homenotice.length">
                                加载中...
                            </div>
                            <template v-else>
                                <div v-for="(item,key) in row.homenotice" :key="key">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <h4 v-html="item.title"></h4>
                                        <p>
                                            <button type="button" class="layui-btn layui-btn-primary layui-btn-xs layui-font-green" style="border: 0;"><i class="layui-icon layui-icon-eye"></i>{{item.readUIDS}}</button>
                                        </p>
                                    </div>
                                    <p class="layui-font-12 layui-font-green">
                                        {{item.uptime?item.uptime.split("--")[0]:item.addtime.split("--")[0]}}
                                    </p>
                                    <p class="layui-font-13" v-html="item.content"></p>
                                    <div class="layui-font-12" style="display: flex; align-items: center; scale: 0.8; transform-origin: left center;">
                                        <p v-if="Number(item.top)">

                                            <button type="button" class="layui-btn layui-btn-xs layui-bg-blue">置顶</button>
                                        </p>&nbsp;&nbsp;
                                        <p>
                                            <button type="button" class="layui-btn layui-btn-xs">{{item.author}}</button>
                                        </p>&nbsp;&nbsp;
                                    </div>
                                    <hr v-if="key!==row.homenotice.length-1" />
                                </div>
                                <div class="layui-font-12 layui-font-green ban center" style="margin-top: 30px;">
                                    没有更多了...
                                </div>
                            </template>
                        </template>
                        <template v-else>
                            暂无公告
                        </template>
                    </div>
                </div>
            </el-col>

        </el-row>
        <?php if ($userrow['uid'] == 1) { ?>
            <el-row :gutter="5">
                <el-col :xs="24" :sm="12">
                    <div class="layui-panel" id="ProgramInfo">
                        <div class="layui-card-header">
                            <!--<i class="layui-icon layui-icon-component" style="font-weight:bold"></i>-->
                            <div style="display: inline-flex; align-items: center; justify-content: left; gap: 2px;">
                                <img src="/assets/images/favicon.ico" width="20">
                                &nbsp;程序信息
                            </div>
                            <span class="layui-font-12 layui-font-green" style="float:right;">仅管理员可见</span>
                        </div>
                        <div class="layui-card-body" style="height:auto;">

                            <table class="layui-table font12Table" style="table-layout: fixed;">
                                <colgroup>
                                    <col width="90">
                                    <col>
                                </colgroup>
                                <thead>
                                    <tr>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>访问耗时</td>
                                        <td> {{(docusetime/1000).toFixed(2)}}s</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div>模板名称</div>
                                        </td>
                                        <td>
                                            CourseX
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>模板作者</td>
                                        <td>X</td>
                                    </tr>
                                    <tr>
                                        <td>版本</td>
                                        <td>
                                            <p>
                                                <span class="layui-font-12 ">
                                                    v<?php echo $conf['version'] ? $conf['version'] : '无法获取' ?>
                                                </span>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>模板简介</td>
                                        <td class="layui-font-12">
                                            <p>
                                            <div class="marquee1" style="background: #f56c6c; color: #ffffff; padding: 1px 5px; border-radius: 3px;">
                                                <span>
                                                    <i class="layui-icon layui-icon-vercode"></i>
                                                    不提供货源，不破解第三方，仅作为商城类源码交流学习，请勿用于违法行为和商业行为！
                                                </span>
                                            </div>
                                            </p>
                                            <p>
                                                开源 · 高阶扩展 · 学习 · 无后门
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Github</td>
                                        <td> <el-link type="primary" @click="window.open('https://github.com/time-demon/couresX')">https://github.com/time-demon/couresX</el-link></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </el-col>
                <el-col :xs="24" :sm="12">
                    <div class="layui-panel">
                        <div class="layui-card-header">
                            <div style="display: inline-flex; align-items: center; justify-content: left; gap: 2px;">
                                <i class="fa-solid fa-server"></i>
                                &nbsp;系统信息
                            </div>
                            <span class="layui-font-12 layui-font-green" style="float:right;">仅管理员可见</span>
                        </div>
                        <div class="layui-card-body" style="height:auto;">

                            <table class="layui-table font12Table">
                                <colgroup>
                                    <col width="92">
                                    <col>
                                </colgroup>
                                <thead>
                                    <tr>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>服务器状态</td>
                                        <td class="layui-font-12">
                                            CPU {{ osIfnoData.cpu }}&nbsp;
                                            负载 {{ osIfnoData.fz }}&nbsp;
                                            内存 {{ osIfnoData.nc }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>PHP版本</td>
                                        <td><?php echo PHP_VERSION ?>&nbsp;<el-button text bg size="small" @click="open_phpinfo">phpinfo</el-button></td>
                                    </tr>
                                    <tr>
                                        <td>Mysql版本</td>
                                        <td><?= str_replace('-log', '', $DB->get_row("select VERSION() as version")["version"]);?></td>
                                    </tr>
                                    <tr>
                                        <td>操作系统</td>
                                        <td><?php echo php_uname('s') ?></td>
                                    </tr>
                                    <tr>
                                        <td>系统架构</td>
                                        <td>
                                            <?= php_uname('m') ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>服务器</td>
                                        <td><?php echo  GetHostByName($_SERVER['SERVER_SOFTWARE']) ?></td>
                                    </tr>
                                    <tr>
                                        <td>服务器IP</td>
                                        <td><?php echo  GetHostByName($_SERVER['SERVER_NAME']) ?></td>
                                    </tr>
                                    <tr>
                                        <td>客户端IP</td>
                                        <td><?php echo $_SERVER['REMOTE_ADDR'] ?></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </el-col>
            </el-row>
        <?php } ?>
        
        <script>
        </script>

    </div>

</body>

<?php include_once($root . '/index/components/footer.php'); ?>

<script>
    layui.config({
        base: '../../layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'console']);
</script>


<script>
    const app = Vue.createApp({
        data() {
            return {
                window: window,
                osIfnoData: {
                    cpu: 'loading',
                    fz: 'loading',
                    nc: 'loading',
                },
                progressOK: false,
                docusetime: 0,
                uid: '<?= $userrow['uid'] ?>' === '1' ? true : false,
                userInfo: {
                    
                },
                row: {
                    "homenotice": null
                },
                inte: '',
                version: {
                    v: '',
                    logs: [
                        {
                            zdy: {
                                icon: ''
                            },
                        }
                    ],
                    logsT: {
                        
                    },
                    msg: '',
                },
                home_top_notice_open: <?= json_encode($conf['home_top_notice_open']) ?>,
                notice_open: <?= json_encode($conf['notice_open']) ?>,
                quickNav: [{
                        t1: '交单',
                        t2: '提交订单',
                        u: 'add_pl',
                        i: 'layui-icon layui-icon-release ',
                        ei: 'Shopping-Cart', // 比i优先级高，主要用于ElementPlus的图标
                        c: 'animate__animated animate__flipInY infinite',
                    },
                    {
                        t1: '代理',
                        t2: '代理管理',
                        u: 'userlist',
                        i: 'layui-icon layui-icon-user',
                        ei: 'User',
                    },
                    {
                        t1: '订单',
                        t2: '订单管理',
                        u: 'list',
                        i: 'layui-icon layui-icon-form',
                        ei: 'Shopping-Bag',
                    },
                    {
                        t1: '充值',
                        t2: '在线充值',
                        u: 'pay',
                        i: 'layui-icon layui-icon-cart',
                        ei: 'Wallet',
                    },
                    {
                        t1: '日志',
                        t2: '操作日志',
                        u: 'log',
                        i: 'layui-icon layui-icon-file-b',
                        ei: 'Tickets',
                    },
                    {
                        t1: '对接',
                        t2: '对接文档',
                        u: 'docking',
                        i: 'layui-icon layui-icon-senior',
                        ei: 'Connection',
                    },
                ],
                open_phpinfo_index: 0,
            }
        },
        computed: {
            computed_todaykaitong() {
                let aa = (<?php
                            if ($userrow['uid'] != "1") {
                                $a = $DB->count("select count(*) from qingka_wangke_user where addtime>'$jtdate' and uuid='{$userrow['uid']}'");
                                echo $a   . "";
                            } else {
                                $a = $DB->count("select count(*) from qingka_wangke_user where addtime>'$jtdate'");
                                echo $a  . "";
                            }
                            ?> /
                    <?php
                    if ($userrow['uid'] != "1") {
                        $a = $DB->count("select count(*) from qingka_wangke_user where uuid='{$userrow['uid']}'");
                        echo $a  . "";
                    } else {
                        $a = $DB->count("select count(*) from qingka_wangke_user");
                        echo $a - 1 . "";
                    }
                    ?> * 100).toFixed(2);

                return isNaN(aa) ? 0 : aa
            },
        },
        mounted() {
            const _this = this;
            layer.load(0);
            $("#userinfo").ready(() => {
                $('#userinfo').show()
                _this.userinfo();
                _this.progressOK = true;
                _this.osIfno();
                setInterval(() => {
                    _this.osIfno();
                }, 6000)
            })


            window.addEventListener('load', () => {
                setTimeout(() => {
                    // 监控访问耗时
                    _this.docusetime = window.performance.timing.loadEventEnd - window.performance.timing.navigationStart;
                }, 300)
            })

        },
        destroyed() {
            console.log('destroyed')
        },
        methods: {
            scyqm(){
                var load = layer.msg('生成中，请稍等...', {
                    icon: 16,
                    shade: 0.01
                });;
                axios.post('/apiadmin.php?act=szyqm', {
                    type: 'newyqm'
                }, {
                    emulateJSON: true
                }).then(function (r) {
                    layer.close(load);
                    if (r.data.code === 1) {
                        layer.msg('生成成功')
                    } else {
                        layer.msg('生成失败')
                    }
                })
            },
            ktapi(){
                layer.confirm('后台剩余积分满300积分可免费开通，反之需花费10积分开通', {
                    title: '温馨提示',
                    icon: 1,
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    var load = layer.load(2);
                    axios.get("/apiadmin.php?act=ktapi&type=1")
                        .then(function (data) {
                            layer.close(load);
                            if (data.data.code == 1) {
                                layer.alert(data.data.msg, {
                                    icon: 1,
                                    title: "温馨提示"
                                }, function () {
                                    setTimeout(function () {
                                        window.location.href = ""
                                    });
                                });
                            } else {
                                layer.msg(data.data.msg, {
                                    icon: 2
                                });
                            }
                        });

                });
            },
            copyT(text = '') {
                const _this = this;
                navigator.clipboard.writeText(text).then(function() {
                    _this.$message({
                        message: '复制成功',
                        type: "success",
                    });
                }).catch(function(error) {
                    _this.$message({
                        message: '复制失败: ' + error,
                        type: "error",
                    });
                });
            },
            osIfno() {
                const _this = this;
                axios.post('/apiadmin.php?act=osIfno', {"z":1,"a":"2"}, {
                    emulateJSON: true
                }).then(r => {
                    if (r.data.code === 1) {
                        for (let i in r.data.data) {
                            r.data.data[i] = r.data.data[i].toFixed(1) + '%'
                        }
                        _this.osIfnoData = r.data.data;
                        
                        for(let i in r.data.userInfo){
                            _this.userInfo[i] = r.data.userInfo[i];
                        }
                    } else {
                        layer.msg('获取系统状态失败')
                    }
                })
            },
            open_phpinfo() {
                const _this = this;
                layer.open({
                    type: 2,
                    title: 'phpinfo',
                    shadeClose: true,
                    maxmin: true, //开启最大化最小化按钮
                    scrollbar: false,
                    maxmin: true,
                    content: 'components/phpinfo.php',
                    success: function(layero, index) {
                        layer.full(index); // 最大化
                        layer.close(_this.open_phpinfo_index);
                    }
                });
                _this.open_phpinfo_index = layer.load(0);
            },
            // 联系上级
            contactUU() {
                layer.open({
                    type: 1,
                    title: "联系上级",
                    content: `<div class="layui-padding-2"><p>上级QQ：<?= $sj["qq"] ? $sj["qq"] : $sj["user"] ?></p><p>上级微信：<?= $sj["wx"] ?></p></div>`,
                })
            },
            homenotice_open: function() {
                layer.open({
                    type: 2,
                    title: '首页实时公告管理',
                    shadeClose: true,
                    maxmin: true, //开启最大化最小化按钮
                    area: ['100%', '100%'],
                    content: 'homenotice.php',
                    scrollbar: false,
                });
            },
            userinfo: function() {
                const _this = this;
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=userinfo")
                    .then(function(r) {
                        layer.close(load);
                        if (r.data.code == 1) {
                            _this.row = r.data;
                            if (_this.row.homenotice) {
                                _this.row.homenotice.sort((a, b) => {
                                    if (a.top == 1 && b.top != 1) {
                                        return -1; // a在前，b在后
                                    } else if (a.top != 1 && b.top == 1) {
                                        return 1; // b在前，a在后
                                    } else {
                                        // 如果top值相同，则按照sort值降序排序
                                        return Number(b.sort) - Number(a.sort);
                                    }
                                });
                            }
                        } else {
                            layer.msg(r.data.msg);
                        }
                    });
            },
            yecz: function() {
                const _this = this;
                layer.alert('请联系您的上级QQ：' + _this.row.sjuser + '，进行充值。（好友点充值，此处将显示您的QQ）', {
                    icon: 1,
                    title: "温馨提示"
                });
            },
            ktapi: function() {
                layer.confirm('后台余额满300可免费开通，反之需花费10余额开通', {
                    title: '温馨提示',
                    icon: 1,
                    btn: ['确定', '取消'] //按钮
                }, function() {
                    var load = layer.load();
                    axios.get("/apiadmin.php?act=ktapi&type=1")
                        .then(function(data) {
                            layer.close(load);
                            if (data.data.code == 1) {
                                layer.alert(data.data.msg, {
                                    icon: 1,
                                    title: "温馨提示"
                                }, function() {
                                    setTimeout(function() {
                                        window.location.href = ""
                                    });
                                });
                            } else {
                                layer.msg(data.data.msg, {
                                    icon: 2
                                });
                            }
                        });

                });
            },
            szyqprice: function() {
                layer.prompt({
                    title: '设置好友默认等级，首次自动生成邀请码',
                    formType: 3
                }, function(yqprice, index) {
                    layer.close(index);
                    var load = layer.load();
                    $.post("/apiadmin.php?act=yqprice", {
                        yqprice
                    }, function(data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.userinfo();
                            layer.alert(data.msg, {
                                icon: 1
                            });
                        } else {
                            layer.msg(data.msg, {
                                icon: 2
                            });
                        }
                    });
                });
            },
            connect_qq: function() {
                var ii = layer.load(0, {
                    shade: [0.1, '#fff']
                });
                $.ajax({
                    type: "POST",
                    url: "../apiadmin.php?act=connect",
                    data: {},
                    dataType: 'json',
                    success: function(data) {
                        layer.close(ii);
                        if (data.code == 0) {
                            window.location.href = data.url;
                        } else {
                            layer.alert(data.msg, {
                                icon: 7
                            });
                        }
                    }
                });
            },
            szgg: function() {
                layer.prompt({
                    title: '设置代理公告，您的代理可看到',
                    formType: 2
                }, function(notice, index) {
                    layer.close(index);
                    var load = layer.load();
                    $.post("/apiadmin.php?act=user_notice", {
                        notice
                    }, function(data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.userinfo();
                            layer.msg(data.msg, {
                                icon: 1
                            });
                        } else {
                            layer.msg(data.msg, {
                                icon: 2
                            });
                        }
                    });
                });
            },

        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#userinfo');
    // -----------------------------
</script>

</html>