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
                                    <img style="border-radius:50%;width:auto;height: 40px;cursor: pointer;"
                                        :src="'https://q1.qlogo.cn/g?b=qq&nk='+(<?= $userrow['qq']; ?>?<?= $userrow['qq']; ?>:<?= $userrow['user']; ?>)+'&s=100'"
                                        alt="<?= $userrow['name']; ?>" @click="photosView(1,[{alt:<?= $userrow['name']; ?>,pid:1,src:'https://q1.qlogo.cn/g?b=qq&nk='+(<?= $userrow['qq']; ?>?<?= $userrow['qq']; ?>:<?= $userrow['user']; ?>)+'&s=100'}])" >
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
                                                <el-button @click="copyT(row.key)" circle  size="small" text style="">
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
                                                <el-button @click="copyT(row.yqm)" circle  size="small" text style="">
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
                                                <el-button @click="contactUU" circle  size="small" text style="">
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
                            &nbsp;实时公告<el-button title="点击刷新" text bg size="small" style="padding: 6px;" @click="homenotice_get"><el-icon><Refresh /></el-icon></el-button>
                        </div>
                        <span v-if="uid" style="float:right;">
                            &nbsp;<button class="layui-btn layui-btn-xs layui-bg-blue" @click="homenotice_set">管理公告</button>
                        </span>
                    </div>
                    <div class="layui-card-body" style="height: 300px !important; overflow-y: auto; word-wrap: break-word; word-break: normal;">
                        
                        <template v-if="homenotice_open">
                            <template v-if="homenotice_loading">
                                <i class="layui-icon layui-icon-loading-1 layui-anim layui-anim-rotate layui-anim-loop"></i> 加载中...
                            </template>
                            <template v-else>
                                <template v-if="!homenotice.length">
                                    <i class="layui-icon layui-icon-tips"></i> 暂无公告
                                </template>
                                <template v-else>
                                    <div v-for="(item,key) in homenotice" :key="key">
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
                                        <hr v-if="key!==homenotice.length-1" />
                                    </div>
                                    <div class="layui-font-12 layui-font-green ban center" style="margin-top: 30px;">
                                        没有更多了...
                                    </div>
                                </template>
                            </template>
                        </template>
                        <template v-else>
                            <i class="layui-icon layui-icon-tips"></i> 暂未开启公告显示
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
                                    <col width="80">
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
                                        <td>标签</td>
                                        <td>开源 · 高阶扩展 · 学习 · 无后门</td>
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
                                        <td>免责声明</td>
                                        <td class="layui-font-12">
                                            <p>
                                            <div class="marquee1" style="background: #f56c6c; color: #ffffff; padding: 1px 5px; border-radius: 3px;">
                                                <span>
                                                    <i class="layui-icon layui-icon-vercode"></i>
                                                    不提供货源，不破解第三方，仅作为商城类源码交流学习，请勿用于违法行为和商业行为！
                                                </span>
                                            </div>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>所用技术</td>
                                        <td> 
                                            <div style="display: flex; gap: 4px 3px; flex-wrap: wrap; transform-origin: left center;">
                                                <el-tag type="info" effect="light" round>
                                                    <i class="fa-brands fa-vuejs"></i> Vue3
                                                </el-tag>
                                                <el-tag type="info" effect="light" round>
                                                    <svg t="1730034379950" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="5402" width="11" height="11"><path d="M65.450667 250.624c-90.581333 130.133333-79.36 299.392-10.112 437.589333 1.578667 3.370667 3.328 6.570667 5.034666 9.770667 0.981333 2.218667 2.090667 4.266667 3.285334 6.357333 0.554667 1.194667 1.322667 2.432 2.005333 3.541334 1.109333 2.218667 2.304 4.352 3.456 6.485333l6.698667 11.306667c1.237333 2.090667 2.432 4.138667 3.84 6.229333 2.346667 4.010667 5.12 7.978667 7.552 11.989333 1.109333 1.664 2.133333 3.328 3.370666 4.992a271.36 271.36 0 0 0 13.226667 18.944c3.328 4.565333 6.656 9.002667 10.24 13.44 1.152 1.621333 2.432 3.242667 3.626667 4.864l9.429333 11.477334c1.152 1.322667 2.304 2.858667 3.541333 4.224 4.181333 5.034667 8.618667 9.941333 13.056 14.890666 0 0.085333 0.128 0.170667 0.213334 0.298667a133.546667 133.546667 0 0 0 18.090666 18.773333c3.413333 3.498667 6.826667 6.997333 10.453334 10.410667l4.309333 4.138667c4.736 4.437333 9.472 8.874667 14.464 13.141333 0.085333 0 0.128 0.085333 0.213333 0.128l2.432 2.133333c4.352 3.797333 8.746667 7.594667 13.226667 11.093334l5.333333 4.48c3.626667 2.901333 7.381333 5.674667 11.093334 8.533333l5.802666 4.437333c3.968 2.986667 8.192 5.930667 12.245334 8.832 1.493333 1.066667 2.986667 2.133333 4.522666 3.114667l1.237334 0.981333 11.989333 7.893334 5.12 3.413333c6.272 4.010667 12.501333 7.808 18.688 11.562667 1.792 0.896 3.584 1.877333 5.248 2.901333 4.608 2.645333 9.386667 5.333333 14.037333 7.808 2.56 1.450667 5.205333 2.688 7.850667 4.010667 3.2 1.792 6.528 3.541333 9.941333 5.333333a13.824 13.824 0 0 1 2.389334 0.981333c1.408 0.64 2.730667 1.322667 4.096 2.005334 5.12 2.56 10.453333 4.992 16 7.424 1.024 0.426667 2.133333 0.853333 3.242666 1.450666 6.144 2.688 12.288 5.248 18.645334 7.765334 1.450667 0.426667 2.986667 1.152 4.48 1.706666 5.76 2.176 11.690667 4.394667 17.536 6.485334l2.133333 0.768c6.528 2.218667 12.970667 4.352 19.584 6.4 1.536 0.426667 3.114667 0.981333 4.736 1.408 6.784 2.048 13.354667 4.48 20.181333 5.802666 437.76 79.786667 564.992-263.210667 564.992-263.210666-106.88 139.178667-296.533333 175.872-476.16 135.04-6.656-1.536-13.312-3.669333-20.010666-5.632a576.938667 576.938667 0 0 1-24.192-7.722667l-2.645334-1.024c-5.802667-1.962667-11.392-4.138667-17.066666-6.314667a68.821333 68.821333 0 0 0-4.693334-1.749333c-6.272-2.517333-12.373333-5.12-18.432-7.808-1.322667-0.426667-2.432-1.024-3.754666-1.536a998.826667 998.826667 0 0 1-15.402667-7.253333 63.274667 63.274667 0 0 1-4.522667-2.218667c-4.010667-1.877333-8.021333-4.010667-11.946666-6.058667a168.192 168.192 0 0 1-7.978667-4.096c-4.821333-2.56-9.642667-5.333333-14.464-7.978666-1.450667-1.024-3.114667-1.877333-4.778667-2.816a678.485333 678.485333 0 0 1-18.688-11.477334 89.770667 89.770667 0 0 1-5.034666-3.370666 256.085333 256.085333 0 0 1-13.312-8.789334c-1.493333-0.981333-2.858667-2.048-4.394667-3.114666a407.082667 407.082667 0 0 1-12.544-9.045334c-1.792-1.450667-3.712-2.816-5.632-4.266666-3.754667-2.944-7.552-5.76-11.306667-8.874667l-5.034666-4.010667a451.413333 451.413333 0 0 1-14.250667-11.989333 11.008 11.008 0 0 0-1.578667-1.28l-14.805333-13.482667-4.266667-4.010666c-3.498667-3.541333-7.082667-6.954667-10.666666-10.453334l-4.138667-4.266666a386.986667 386.986667 0 0 1-13.184-13.781334l-0.64-0.682666a765.610667 765.610667 0 0 1-13.354667-15.104c-1.152-1.322667-2.218667-2.730667-3.413333-4.138667l-9.642667-11.818667a906.581333 906.581333 0 0 1-14.506666-19.114666C92.16 502.869333 56.106667 315.136 135.850667 161.152" fill="" p-id="5403"></path><path d="M346.496 141.013333c-65.664 94.250667-61.952 220.288-10.837333 319.957334a388.266667 388.266667 0 0 0 28.885333 48.298666c9.813333 14.08 20.650667 30.72 33.792 42.069334 4.565333 5.205333 9.514667 10.24 14.677333 15.317333l3.84 3.84c4.864 4.693333 9.856 9.301333 14.933334 13.866667l0.64 0.554666a420.48 420.48 0 0 0 17.664 14.592c1.450667 0.981333 2.688 2.133333 4.096 3.114667 5.973333 4.608 11.989333 9.045333 18.218666 13.44l0.64 0.384c2.645333 1.92 5.461333 3.669333 8.448 5.546667 1.194667 0.768 2.56 1.792 3.84 2.56 4.522667 2.901333 8.96 5.632 13.525334 8.405333 0.725333 0.298667 1.365333 0.682667 2.048 0.981333 3.84 2.346667 8.021333 4.608 12.032 6.698667 1.408 0.853333 2.773333 1.493333 4.224 2.304 2.858667 1.408 5.674667 2.901333 8.405333 4.352l1.365333 0.597333c5.76 2.816 11.648 5.461333 17.408 8.106667 1.450667 0.554667 2.688 1.024 3.925334 1.621333 4.736 2.048 9.557333 4.010667 14.293333 5.845334 2.133333 0.725333 4.138667 1.578667 6.144 2.218666 4.352 1.621333 8.917333 3.114667 13.226667 4.608l5.973333 1.92c6.229333 1.92 12.544 4.437333 19.114667 5.504 337.92 56.021333 416.170667-204.245333 416.170666-204.245333-70.442667 101.376-206.762667 149.674667-352.042666 111.957333a342.613333 342.613333 0 0 1-19.114667-5.546666c-2.048-0.554667-3.84-1.194667-5.802667-1.792-4.437333-1.536-9.002667-3.029333-13.312-4.650667l-6.144-2.304c-4.778667-1.92-9.642667-3.712-14.293333-5.76-1.450667-0.64-2.773333-1.066667-3.882667-1.706667-5.973333-2.688-11.989333-5.333333-17.792-8.192l-8.789333-4.565333-5.077333-2.56a241.92 241.92 0 0 1-11.306667-6.4 26.453333 26.453333 0 0 1-2.645333-1.450667c-4.522667-2.816-9.216-5.546667-13.525334-8.448-1.450667-0.810667-2.773333-1.792-4.138666-2.645333l-8.874667-5.802667c-6.144-4.266667-12.16-8.874667-18.218667-13.312-1.365333-1.237333-2.688-2.261333-4.010666-3.370666-63.872-50.218667-114.261333-118.869333-138.154667-196.608-25.173333-80.810667-19.626667-171.434667 23.850667-244.992" fill="" p-id="5404"></path><path d="M582.826667 59.050667c-38.741333 56.832-42.453333 127.402667-15.786667 190.08 28.330667 66.56 86.272 118.826667 153.770667 143.573333 2.773333 1.066667 5.461333 1.962667 8.32 2.986667l3.754666 1.152c3.925333 1.237333 7.893333 2.688 11.946667 3.584 186.709333 36.010667 237.226667-95.872 250.837333-115.242667-44.458667 63.829333-118.997333 79.146667-210.432 56.917333a206.677333 206.677333 0 0 1-22.016-6.826666 270.677333 270.677333 0 0 1-26.325333-10.837334 278.229333 278.229333 0 0 1-46.08-28.16c-81.92-62.037333-132.650667-180.48-79.232-276.949333" fill="" p-id="5405"></path></svg> Jquery
                                                </el-tag>
                                                <el-tag type="info" effect="light" round>
                                                    <svg t="1730034059342" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="4281" width="11" height="11"><path d="M25.6 975.104c0-1.536 7.424-14.848 16.64-29.44 54.272-86.272 83.968-151.808 108.8-239.104 20.992-75.008 33.792-152.576 43.776-267.52 4.352-51.2 3.84-165.12-1.28-203.52-11.264-86.528-24.832-140.544-47.616-187.648-4.352-8.448-7.68-16.384-7.68-17.664 0-1.024 16.128-2.048 35.84-2.048h35.584l9.984 13.312c16.128 22.272 37.632 56.064 58.88 93.44 71.424 124.928 96.512 181.248 125.696 283.648 34.816 122.88 47.872 203.776 56.576 352 1.536 26.112 3.84 62.208 5.376 80.384 1.28 18.432 2.304 36.608 2.304 40.448v7.168l133.376-0.512 133.376-0.768 10.24-24.32c16.896-40.704 28.672-81.408 41.472-142.08 3.072-15.104 5.888-52.992 7.68-108.8l0.256-11.52 29.44 0.768c34.304 1.024 30.72-1.536 57.344 40.448 27.648 43.776 43.008 77.312 69.888 154.624 19.2 55.552 26.112 81.92 30.208 118.272 2.048 17.408 4.352 36.352 5.376 42.496l1.536 10.752H507.136c-346.112 0-481.536-0.768-481.536-2.816zM809.472 529.92c-13.568-2.048-37.376-14.336-46.592-24.32-10.24-10.752-22.528-35.072-25.344-49.664-5.888-29.696 4.352-60.672 27.136-82.432 14.848-14.592 27.904-21.248 48.64-25.6 38.144-7.936 80.384 13.824 98.56 50.688 7.68 15.616 8.448 19.2 8.192 40.704 0 21.248-1.024 25.344-8.448 40.448-18.432 37.376-57.856 56.832-102.144 50.176z" fill="#009687" p-id="4282"></path></svg> Layui
                                                </el-tag>
                                                <el-tag type="info" effect="light" round>
                                                    <el-icon><Element-Plus /></el-icon>Element Plus
                                                </el-tag>
                                                <el-tag type="info" effect="light" round>
                                                    <svg t="1730034725653" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="6609" id="mx_n_1730034725654" width="11" height="11"><path d="M472.234667 123.733333v827.605334L382.037333 1024V335.530667H212.266667L472.234667 123.733333zM637.184 0v672.725333h174.549333l-264.533333 217.130667V62.677333L637.226667 0z" fill="#000000" p-id="6610"></path></svg> Axios
                                                </el-tag>
                                                <el-tag type="info" effect="light" round>
                                                    <i class="fa-brands fa-php"></i> PHP
                                                </el-tag>
                                                <el-tag type="info" effect="light" round>
                                                    <svg t="1730034851194" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="7618" width="11" height="11"><path d="M1001.632 793.792c-7.84-13.856-26.016-37.536-93.12-83.2a1096.224 1096.224 0 0 0-125.152-74.144c-30.592-82.784-89.824-190.112-176.256-319.36-93.056-139.168-201.12-197.792-321.888-174.56a756.608 756.608 0 0 0-40.928-37.696C213.824 78.688 139.2 56.48 96.32 60.736c-19.424 1.952-34.016 9.056-43.36 21.088-21.664 27.904-14.432 68.064 85.504 198.912 19.008 55.616 23.072 84.672 23.072 99.296 0 30.912 15.968 66.368 49.984 110.752l-32 109.504c-28.544 97.792 23.328 224.288 71.616 268.384 25.76 23.552 47.456 20.032 58.176 15.84 21.504-8.448 38.848-29.472 50.048-89.504 5.728 14.112 11.808 29.312 18.208 45.6 34.56 87.744 68.352 136.288 106.336 152.736a32.032 32.032 0 0 0 25.44-58.688c-9.408-4.096-35.328-23.712-72.288-117.504-31.168-79.136-53.856-132.064-69.376-161.856a32.224 32.224 0 0 0-35.328-16.48 32.032 32.032 0 0 0-25.024 29.92c-3.872 91.04-13.056 130.4-19.2 147.008-26.496-30.464-68.128-125.984-47.232-197.536 20.768-71.232 32.992-112.928 36.64-125.248a31.936 31.936 0 0 0-5.888-29.28c-41.664-51.168-46.176-75.584-46.176-83.712 0-29.472-9.248-70.4-28.288-125.152a31.104 31.104 0 0 0-4.768-8.896c-53.824-70.112-73.6-105.216-80.832-121.888 25.632 1.216 74.336 15.04 91.008 29.376a660.8 660.8 0 0 1 49.024 46.304c8 8.448 19.968 11.872 31.232 8.928 100.192-25.92 188.928 21.152 271.072 144 87.808 131.328 146.144 238.048 173.408 317.216a32 32 0 0 0 16.384 18.432 1004.544 1004.544 0 0 1 128.8 75.264c7.392 5.024 14.048 9.696 20.064 14.016h-98.848a32.032 32.032 0 0 0-24.352 52.736 3098.752 3098.752 0 0 0 97.856 110.464 32 32 0 1 0 46.56-43.872 2237.6 2237.6 0 0 1-50.08-55.328h110.08a32.032 32.032 0 0 0 27.84-47.776z" p-id="7619"></path><path d="M320 289.472c12.672 21.76 22.464 37.344 29.344 46.784 8.288 16.256 21.184 29.248 29.44 45.536l2.016-1.984c14.528-9.952 25.92-49.504 2.752-75.488-12.032-18.176-51.04-17.664-63.552-14.848z" p-id="7620"></path></svg> 
                                                   MySQL
                                                </el-tag>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>开源地址</td>
                                        <td>
                                            <el-link type="primary" @click="window.open('https://github.com/time-demon/couresX')">
                                                <i class="layui-icon layui-icon-github"></i> https://github.com/time-demon/couresX
                                            </el-link>
                                        </td>
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
                                            <el-icon style="position: relative;top: 2px;">
                                                    <Cpu />
                                                </el-icon> CPU {{ osIfnoData.cpu }}&nbsp;
                                            负载 {{ osIfnoData.fz }}&nbsp;
                                            内存 {{ osIfnoData.nc }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>PHP版本</td>
                                        <td>
                                            <i class="fa-brands fa-php"></i> <?php echo PHP_VERSION ?>&nbsp;<el-button text bg size="small" @click="open_phpinfo">phpinfo</el-button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Mysql版本</td>
                                        <td><i class="fa-solid fa-database"></i> <?= str_replace('-log', '', $DB->get_row("select VERSION() as version")["version"]);?></td>
                                    </tr>
                                    <tr>
                                        <td>操作系统</td>
                                        <td>
                                            <template v-if="/Linux/i.test('<?= php_uname('s') ?>')">
                                                <i class="fa-brands fa-linux"></i> <?= php_uname('s') ?>
                                            </template>
                                            <template v-else-if="/Windows/i.test('<?= php_uname('s') ?>')">
                                                <i class="fa-brands fa-windows"></i> <?= php_uname('s') ?>
                                            </template>
                                            <template v-else-if="/Darwin/i.test('<?= php_uname('s') ?>')">
                                                <i class="fa-brands fa-apple"></i> <?= php_uname('s') ?>
                                            </template>
                                            <template v-else>
                                                <i class="fa-solid fa-question"></i> <?= php_uname('s') ?>
                                            </template>
                                        </td>
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
                },
                homenotice_loading: true,
                homenotice: [],
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
                homenotice_open: <?= json_encode($conf['notice_open']) ?>,
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
                _this.homenotice_get();
                
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
            homenotice_set: function() {
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
            homenotice_get(){
                const _this = this;
                _this.homenotice_loading = true;
                axios.post("/apiadmin.php?act=homenotice_get").then(r=>{
                    if (r.data.code == 1) {
                        _this.homenotice = r.data.data;
                    }else{
                        _this.$message.error("获取实时公告失败");
                    }
                    _this.homenotice_loading = false;
                })
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
            photosView(title="未命名",list = [{}],start=0){
                layer.photos({
                    photos: {
                      "title": title,
                      "start": start,
                      "data": list
                    }
              });
            }
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