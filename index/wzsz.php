<?php
$title = '系统设置';
require_once('head.php');

if ($userrow['uid'] != 1) {
    alert("您的账号无权限！", "index.php");
    exit();
}
?>

<style>
    .zIndex_TOP {
        z-index: 999999999 !important;
    }

    .layui-form-switch {
        margin-top: 0 !important;
    }

    i {
        font-size: inherit !important;
    }

    .layui-tab .layui-tab-title li {
        padding: 0 5px;
    }

    .menuListTools {
        display: flex;
        align-items: center;
    }

    .menuListTools .menuListTool {
        margin-left: 5px;
    }

    .menuListTools .menuListTool button {
        margin: 0;
        height: 18px;
        line-height: 18px;
        padding: 0 1px;
    }

    .menuListTools .menuListTool button i {
        display: block;
        scale: .8;
    }

    .el-switch .layui-form-checkbox {
        display: none;
    }

    .menu_tabs .el-tabs__item {
        padding: 0 10px;
    }

    .menu_tabs .el-tab-pane {
        min-height: calc(100vh - 130px);
    }
</style>

<script src="/assets/toc/pinyin-pro.min.js?v=3.22.2"></script>

<div class=" layui-padding-1 " id="webset" style="display:none">
    <div class="layui-panel layui-padding-1 layui-font-13" style="margin-bottom: 5px; display: flex; align-items: center; position: fixed; top: 3px; left: 50%; transform: translateX(-50%); z-index: 999; width: calc(100% - 18px);">
        <div style="flex: 1 1 auto; display: flex; align-items: center; gap: 3px; padding: 0 0 0 5px;">
            <el-icon><Warning /></el-icon> 保存后最好刷新整个网站看效果~
        </div>
        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" @click="add">保存</button>
    </div>

    <div class="layui-panel layui-padding-2" style="margin-top: 42px;">
        <form class="form-horizontal devform layui layui-form" id="form-web">
            
            <el-tabs v-model="tabList_active" :stretch="true" class="menu_tabs" @tab-click="tabClick">

                <el-tab-pane label="基础配置" name="basic">

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>一键跑路</b>
                        </legend>
                        <div class="layui-field-box">
                            <div>
                                <p class="layui-font-12 layui-font-green" style="margin-bottom:2px">
                                    花有重开日，坤无再少年，有缘不见！<br />
                                    <span class="layui-font-red">
                                        跑路后访问 /index/wzsz.php 或 修改数据库 的 qingka_wangke_config表 的 paolu字段为0 即可解除跑路!
                                    </span>
                                </p>
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(2,49%);justify-content: space-between;">
                                <div>
                                    <div style="margin: 5px 0;">
                                        运营状态
                                    </div>
                                    <select name="paolu" class="layui-select">
                                        <option value="0" <?php if ($conf['paolu'] == 0) {
                                                                echo 'selected';
                                                            } ?>>继续运营</option>
                                        <option value="1" <?php if ($conf['paolu'] == 1) {
                                                                echo 'selected';
                                                            } ?>>跑路了~</option>
                                    </select>
                                </div>
                                <div>
                                    <div style="margin: 5px 0;">
                                        跑路页路径
                                    </div>
                                    <input type="text" class="layui-input" name="paolu_u" value="<?= $conf['paolu_u'] ?>" placeholder="请输入跑路页路径" required>
                                </div>
                            </div>
                            <div>
                                <div style="margin: 5px 0;">
                                    跑路页标题
                                </div>
                                <input type="text" class="layui-input" name="paolu_t" value="<?= $conf['paolu_t'] ?>" placeholder="请输入跑路页标题" required>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>侧边栏收缩小Logo&nbsp;<i style="cursor: pointer;" class="layui-icon layui-icon-eye" @click="seePhoto('<?= $conf['cb_logo'] ?>')"></i></b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="cb_logo" value="<?= $conf['cb_logo'] ?>" placeholder="请输入logo地址" required>
                        </div>
                    </fieldset>

                    <div style="display: grid; grid-template-columns: repeat(2,49%);justify-content: space-between;">
                        <fieldset class="layui-elem-field">
                            <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                <b>站点标题</b>
                            </legend>
                            <div class="layui-field-box">
                                <input type="text" class="layui-input" name="sitename" value="<?= $conf['sitename'] ?>" placeholder="请输入站点名字" required>
                            </div>
                        </fieldset>

                        <fieldset class="layui-elem-field">
                            <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                <b>站点副标题</b>
                            </legend>
                            <div class="layui-field-box">
                                <input type="text" class="layui-input" name="subsitename" value="<?= $conf['subsitename'] ?>" placeholder="请输入站点副标题" required>
                            </div>
                        </fieldset>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(2,49%);justify-content: space-between;">

                        <fieldset class="layui-elem-field">
                            <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                <b>SEO关键词</b>
                            </legend>
                            <div class="layui-field-box">
                                <input type="text" class="layui-input" name="keywords" value="<?= $conf['keywords'] ?>" placeholder="请输入站点名字" required>
                            </div>
                        </fieldset>

                        <fieldset class="layui-elem-field">
                            <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                <b>SEO介绍</b>
                            </legend>
                            <div class="layui-field-box">
                                <input type="text" class="layui-input" name="description" value="<?= $conf['description'] ?>" placeholder="请输入站点名字" required>
                            </div>
                        </fieldset>

                    </div>

                    <!--<fieldset class="layui-elem-field">-->
                    <!--    <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">-->
                    <!--        <b>登录页面LOGO&nbsp;<i style="cursor: pointer;" class="layui-icon layui-icon-eye" @click="seePhoto('<?= $conf['logo'] ?>')"></i></b>-->
                    <!--    </legend>-->
                    <!--    <div class="layui-field-box">-->
                    <!--        <input type="text" class="layui-input" name="logo" value="<?= $conf['logo'] ?>" placeholder="请输入logo地址" required>-->
                    <!--    </div>-->
                    <!--</fieldset>-->

                    <!--<fieldset class="layui-elem-field">-->
                    <!--    <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">-->
                    <!--        <b>主页顶部LOGO地址</b>-->
                    <!--    </legend>-->
                    <!--    <div class="layui-field-box">-->
                    <!--        <input type="text" class="layui-input" name="hlogo" value="<?= $conf['hlogo'] ?>" placeholder="请输入logo地址" required>-->
                    <!--    </div>-->
                    <!--</fieldset>-->

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>是否开启水印</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="sykg" class="layui-select">
                                <option value="1" <?php if ($conf['sykg'] == 1) {
                                                        echo 'selected';
                                                    } ?>>开启</option>
                                <option value="0" <?php if ($conf['sykg'] == 0) {
                                                        echo 'selected';
                                                    } ?>>关闭</option>
                            </select>
                        </div>
                    </fieldset>

                    <div id="webVfxHot" class="layui-padding-2" style="display:none;">
                        <div class="layui-font-green layui-font-12">
                            <li>
                                注意：部分js和css之间可能存在不兼容！
                            </li>
                            <li>
                                js文件用 &lt;script src=" "&gt;&lt;/script&gt;</pre> 包裹
                            </li>
                            <li>
                                css文件用 &lt;link href=" " rel="stylesheet"&gt;</pre> 包裹
                            </li>
                        </div>
                        <table class="layui-table" lay-even="true" lay-size="sm">
                            <tbody>
                                <template v-for="(item,index) in webVfxHotList" :key="index">
                                    <tr>
                                        <td>
                                            {{ item.t }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{ item.u }}
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>全局特效
                                &nbsp;<button class="layui-btn layui-btn-xs layui-btn-primary layui-border-blue" @click='layui.use(()=>{layer.open({type: 1,title:"推荐特效",shade: false,area:["350px","auto"],content: $("#webVfxHot"),})})'>推荐特效</button></b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="webVfx_open" class="layui-select" style="width:100%">
                                <option value="1" <?php if ($conf['webVfx_open'] == 1) {
                                                        echo 'selected';
                                                    } ?>>开启</option>
                                <option value="0" <?php if ($conf['webVfx_open'] == 0) {
                                                        echo 'selected';
                                                    } ?>>关闭</option>
                            </select>
                            <textarea type="text" name="webVfx" class="layui-textarea" rows="5" style="margin-top:3px;"><?= $conf['webVfx'] ?></textarea>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>全站字体自定义</b>
                        </legend>
                        <div class="layui-field-box">
                            <div class="layui-font-13 layui-font-red layui-padding-2">
                                字体获取网址：<a href="https://fonts.google.com/" target="_blank">点我访问</a>
                            </div>

                            <select name="fontsZDY" class="layui-select" style="width:100%">
                                <option value="1" <?php if ($conf['fontsZDY'] == 1) {
                                                        echo 'selected';
                                                    } ?>>开启</option>
                                <option value="0" <?php if ($conf['fontsZDY'] == 0) {
                                                        echo 'selected';
                                                    } ?>>关闭</option>
                            </select>

                            <div class="layui-font-13 layui-font-red layui-padding-2">
                                必须将 字体URL 和 字体名称 正确配对！！！
                            </div>
                            <fieldset class="layui-elem-field">
                                <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                    <b>字体URL</b>
                                </legend>
                                <div class="layui-field-box">
                                    <textarea type="text" name="fontsZDY_jscss" class="layui-textarea" rows="5" style="margin-top:3px;"><?= $conf['fontsZDY_jscss'] ?></textarea>
                                </div>
                            </fieldset>

                            <fieldset class="layui-elem-field">
                                <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                    <b>字体名称</b>
                                </legend>
                                <div class="layui-field-box">
                                    <div class="layui-font-13 layui-font-red">
                                        字体名称不要加引号！！
                                    </div>
                                    <input type="text" class="layui-input" name="fontsFamily" value="<?= $conf['fontsFamily'] ?>" placeholder="请输入站点名字" required>
                                </div>
                            </fieldset>

                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="登录配置" name="login">

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>管理登录二次安全验证密码</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="verification" value="<?= $conf['verification'] ?>" placeholder="请输入安全验证密码" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field" style="">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>登录过期时间(分钟)</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="number" min="0" step="1" class="layui-input" name="login_etime" value="<?= $conf['login_etime'] ?>" placeholder="请输入登录过期时间" required>
                            <span class="layui-font-12 layui-font-red">
                                单位：分钟&nbsp;&nbsp;&nbsp;例如两天过期：2*24*60=2880
                            </span>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>登录页图片</b>
                        </legend>
                        <div class="layui-field-box" style="display: grid; grid-template-columns: repeat(2,50%);justify-content: space-between;">

                            <fieldset class="layui-elem-field">
                                <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                    <b>登录页Logo&nbsp;<i style="cursor: pointer;" class="layui-icon layui-icon-eye" @click="seePhoto('<?= $conf['login_logo'] ?>')"></i>
                                    </b>
                                </legend>
                                <div class="layui-field-box">
                                    <input type="text" class="layui-input" name="login_logo" value="<?= $conf['login_logo'] ?>" placeholder="请输入登录页路径" required>
                                </div>
                            </fieldset>

                            <fieldset class="layui-elem-field">
                                <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                    <b>登录页背景图&nbsp;<i style="cursor: pointer;" class="layui-icon layui-icon-eye" @click="seePhoto('<?= $conf['login_banner'] ?>')"></i>
                                    </b>
                                </legend>
                                <div class="layui-field-box">
                                    <input type="text" class="layui-input" name="login_banner" value="<?= $conf['login_banner'] ?>" placeholder="请输入登录页背景图路径" required>
                                </div>
                            </fieldset>

                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>登录页顶部公告</b>
                        </legend>
                        <div class="layui-field-box">
                            请到【公告配置】里配置！
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="安全配置" name="safe">

                    <fieldset class="layui-elem-field" style="">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>Redis定时任务安全</b>
                        </legend>
                        <div class="layui-field-box">
                            <fieldset class="layui-elem-field" style="">
                                <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                    <b>IP识别方式</b>
                                </legend>
                                <div class="layui-field-box">
                                    <select name="serverIP_type" class="layui-select">
                                        <option value="0" <?php if ($conf["serverIP_type"] == 0) {
                                                                echo 'selected';
                                                            } ?>>自动识别(直连时选这个，但易被伪造)</option>
                                        <option value="1" <?php if ($conf['serverIP_type'] == 1) {
                                                                echo 'selected';
                                                            } ?>>UID和Key验证(套CDN或怕被恶意伪造时选这个)</option>

                                    </select>
                                </div>
                            </fieldset>
                            <fieldset class="layui-elem-field" style="">
                                <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                    <b>绑定安全账户</b>
                                </legend>
                                <div class="layui-field-box">
                                    <select name="serverIP_uid" class="layui-select">
                                        <?php
                                        $a = $DB->query('select * from qingka_wangke_user ORDER BY `uid` ASC');
                                        while ($rs = $DB->fetch($a)) {
                                            $rs_disabled = empty($rs['key']) ? 'disabled' : '';
                                            $rs_selected = $rs['uid'] == $conf["serverIP_uid"] ? 'selected' : '';
                                            echo "<option {$rs_disabled} {$rs_selected} value='{$rs['uid']}'>【{$rs['uid']}】{$rs['user']} | {$rs['name']} " . ($rs_disabled ? " -> 未开通key" : " -> " . $rs['key']) . "</option>";
                                        }
                                        ?>

                                    </select>
                                    <span class="layui-font-12 layui-font-red">
                                        绑定安全账户 只有在识别方式为【UID和Key验证】时才有效<br />
                                        定时任务示例：/redis/redis_ru.php?uid=1&key=tI3t7PEF1mMDitET
                                    </span>
                                </div>
                            </fieldset>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field" style="">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>是否运行代理调用浏览器F12</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="useF12_d" class="layui-select">
                                <option value="0" <?php if ($conf["useF12_d"] == 0) {
                                                        echo 'selected';
                                                    } ?>>不允许</option>
                                <option value="1" <?php if ($conf['useF12_d'] == 1) {
                                                        echo 'selected';
                                                    } ?>>允许</option>

                            </select>
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="公告配置" name="notice">

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b style="display: flex; align-items: center;">登录页顶部公告&nbsp;
                                <span @click="add">
                                    <input type="hidden" name="login_top_notice_open" value="">
                                    <input type="checkbox" name="login_top_notice_open" title="开启|关闭" lay-skin="switch" <?= $conf['login_top_notice_open'] ? checked : '' ?> lay-filter="notice_open">
                                </span>
                            </b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="login_top_notice" value="<?= $conf['login_top_notice'] ?>" placeholder="请输登录页顶部公告" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b style="display: flex; align-items: center;">首页实时公告&nbsp;
                                <span @click="add">
                                    <input type="hidden" name="notice_open" value="">
                                    <input type="checkbox" name="notice_open" title="开启|关闭" lay-skin="switch" <?= $conf['notice_open'] ? checked : '' ?> lay-filter="notice_open">
                                </span>
                            </b>
                        </legend>
                        <div class="layui-field-box" style="padding-left: 30px;">
                            <button class="layui-btn layui-btn-sm layui-bg-blue" @click="homenotice_open">点击管理</button>
                            <!--<textarea type="text" name="notice" class="layui-textarea" rows="15"><?= $conf['notice'] ?></textarea>-->
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b style="display: flex; align-items: center;">渠道推荐公告&nbsp;
                                <span @click="add">
                                    <input type="hidden" name="qd_notice_open" value="">
                                    <input type="checkbox" name="qd_notice_open" title="开启|关闭" lay-skin="switch" <?= $conf['qd_notice_open'] ? checked : '' ?>>
                                </span>
                            </b>
                        </legend>
                        <div class="layui-field-box">
                            <!--<div id="edit_qd_notice"></div>-->
                            <textarea type="text" name="qd_notice" class="layui-textarea" rows="5"><?= $conf['qd_notice'] ?></textarea>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b style="display: flex; align-items: center;">首页顶部公告&nbsp;
                                <span @click="add">
                                    <input type="hidden" name="home_top_notice_open" value="">
                                    <input type="checkbox" name="home_top_notice_open" title="开启|关闭" lay-skin="switch" <?= $conf['home_top_notice_open'] ? checked : '' ?> lay-filter="notice_open">
                                </span>
                            </b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="home_top_notice" value="<?= $conf['home_top_notice'] ?>" placeholder="请输入首页顶部公告" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b style="display: flex; align-items: center;">首次访问弹窗公告&nbsp;
                                <span @click="add">
                                    <input type="hidden" name="tcgonggao_open" value="">
                                    <input type="checkbox" name="tcgonggao_open" title="开启|关闭" lay-skin="switch" <?= $conf['tcgonggao_open'] ? checked : '' ?> lay-filter="notice_open">
                                </span>
                            </b>
                        </legend>
                        <div class="layui-field-box">
                            <!--<div id="edit_tcgonggao"></div>-->
                            <textarea type="text" name="tcgonggao" class="layui-textarea" rows="5"><?= $conf['tcgonggao'] ?></textarea>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b style="display: flex; align-items: center;">代理管理页公告&nbsp;
                                <span @click="add">
                                    <input type="hidden" name="dlgl_notice_open" value="">
                                    <input type="checkbox" name="dlgl_notice_open" title="开启|关闭" lay-skin="switch" <?= $conf['dlgl_notice_open'] ? checked : '' ?> lay-filter="notice_open">
                                </span>
                            </b>
                        </legend>
                        <div class="layui-field-box">
                            <!--<div id="edit_dlgl_notice"></div>-->
                            <textarea type="text" name="dlgl_notice" class="layui-textarea" rows="5"><?= $conf['dlgl_notice'] ?></textarea>
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="支付配置" name="pay">

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>最低充值</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="zdpay" value="<?= $conf['zdpay'] ?>" placeholder="请输入你的商户KEY" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field" style="width: max-content;">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b style="display: flex; align-items: center;">充值赠送规则&nbsp;
                                <span @click="add">
                                    <input type="hidden" name="epay_zs_open" value="">
                                    <input type="checkbox" name="epay_zs_open" title="开启|关闭" lay-skin="switch" <?= $conf['epay_zs_open'] ? checked : '' ?> lay-filter="epay_zs_open">
                                </span>
                            </b>
                        </legend>
                        <div class="layui-field-box layui-padding-3">
                            <span class="layui-font-12 layui-font-blue">
                                最小值 ~ 最大值 赠送百分比
                            </span>
                            <div class="layui-form">
                                <div class="layui-form-item" v-for="(rule, index) in epay_zs.rules" :key="index" style="display: flex;align-items: center;">
                                    <div class="layui-inline" style="display:flex;align-items: center;margin: 0;">
                                        <div class="" style="width: 70px;">
                                            <input type="number" min="0" lay-precision="2" v-model="rule.min" class="layui-input" @blur="czzs_addRule(1,index)">
                                        </div>
                                        <div class="layui-form-mid">~</div>
                                        <div class="" style="width: 70px;">
                                            <input type="number" min="0" lay-precision="2" v-model="rule.max" class="layui-input" @blur="czzs_addRule(1)">
                                        </div>
                                    </div>
                                    <div class="layui-inline" style="display: flex;align-items: center;margin: 0;">
                                        <label class="layui-form-label" style="width: max-content;">赠</label>
                                        <div class="layui-input-inline" style="width: 50px !important; margin-left: 0;">
                                            <input type="number" min="0" lay-precision="2" v-model="rule.zsprice" class="layui-input">
                                        </div>
                                        <div class="layui-form-mid" style="margin:0;">%</div>
                                    </div>
                                    <div class="layui-inline" style="margin: 0;margin-left: 10px;">
                                        <button class="layui-btn layui-btn-danger layui-btn-xs" @click="czzs_deleteRule(index)">
                                            <i class="layui-icon layui-icon-delete"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="layui-form-item" style="text-align: right;">
                                    <div class="layui-input-block">
                                        <button class="layui-btn" @click="czzs_addRule()">添加规则</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field" style="display:inline-block;width: 30%;">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>QQ</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="is_qqpay" class="layui-select">
                                <option value="1" <?php if ($conf['is_qqpay'] == 1) {
                                                        echo 'selected';
                                                    } ?>>开启</option>
                                <option value="0" <?php if ($conf['is_qqpay'] == 0) {
                                                        echo 'selected';
                                                    } ?>>关闭</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field" style="display:inline-block;width: 30%;">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>微信</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="is_wxpay" class="layui-select">
                                <option value="1" <?php if ($conf['is_wxpay'] == 1) {
                                                        echo 'selected';
                                                    } ?>>开启</option>
                                <option value="0" <?php if ($conf['is_wxpay'] == 0) {
                                                        echo 'selected';
                                                    } ?>>关闭</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field" style="display:inline-block;width: 30%;">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>支付宝</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="is_alipay" class="layui-select">
                                <option value="1" <?php if ($conf['is_alipay'] == 1) {
                                                        echo 'selected';
                                                    } ?>>开启</option>
                                <option value="0" <?php if ($conf['is_alipay'] == 0) {
                                                        echo 'selected';
                                                    } ?>>关闭</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>易支付回调协议</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="epay_protocol" class="layui-select">
                                <option value="https" <?php if ($conf['epay_protocol'] == 'https') {
                                                            echo 'selected';
                                                        } ?>>https</option>
                                <option value="http" <?php if ($conf['epay_protocol'] == 'http') {
                                                            echo 'selected';
                                                        } ?>>http</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>易支付API</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="epay_api" value="<?= $conf['epay_api'] ?>" placeholder="格式：http://www.baidu.com/" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>商户ID</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="epay_pid" value="<?= $conf['epay_pid'] ?>" placeholder="请输入你的商户ID" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>商户KEY</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="epay_key" value="<?= $conf['epay_key'] ?>" placeholder="请输入你的商户KEY" required>
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="代理配置" name="user">

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>上级迁移功能</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="sjqykg" class="layui-select">
                                <option value="1" <?php if ($conf['sjqykg'] == 1) {
                                                        echo 'selected';
                                                    } ?>>开启</option>
                                <option value="0" <?php if ($conf['sjqykg'] == 0) {
                                                        echo 'selected';
                                                    } ?>>关闭</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>邀请码注册</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="user_yqzc" class="layui-select">
                                <option value="1" <?php if ($conf['user_yqzc'] == 1) {
                                                        echo 'selected';
                                                    } ?>>允许</option>
                                <option value="0" <?php if ($conf['user_yqzc'] == 0) {
                                                        echo 'selected';
                                                    } ?>>拒绝</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>后台开户</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="user_htkh" class="layui-select">
                                <option value="1" <?php if ($conf['user_htkh'] == 1) {
                                                        echo 'selected';
                                                    } ?>>允许</option>
                                <option value="0" <?php if ($conf['user_htkh'] == 0) {
                                                        echo 'selected';
                                                    } ?>>拒绝</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>开通价格(额外手续费)</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="user_ktmoney" value="<?= $conf['user_ktmoney'] ?>" placeholder="" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>默认密码</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="user_pass" value="<?= $conf['user_pass'] ?>" placeholder="" required>
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="任务处理配置" name="task">

                    <div class="layui-font-13 layui-font-red" style="margin-bottom: 10px;">
                        <i class="layui-icon layui-icon-tips"></i> 用|符号分割，注意是英文状态的符号，最后面不要加这个符号
                    </div>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>不需要补刷的任务状态</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="bs0_rw" value="<?= $conf['bs0_rw'] ?>" placeholder="请输入不需要补刷的任务状态" required>
                            <span class="layui-font-12 layui-font-red">
                                填任务状态标识
                            </span>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>需要补刷的处理状态</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="bs_cl" value="<?= $conf['bs_cl'] ?>" placeholder="请输入需要补刷的处理状态" required>
                            <span class="layui-font-12 layui-font-red">
                                填处理状态码，比如1就是处理成功，2就是处理失败，0就是待处理，99就是自营，其他状态码自行了解
                            </span>
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="下单页配置" name="buy">

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>分类开关</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="flkg" class="layui-select">
                                <option value="1" <?php if ($conf['flkg'] == 1) {
                                                        echo 'selected';
                                                    } ?>>开启</option>
                                <option value="0" <?php if ($conf['flkg'] == 0) {
                                                        echo 'selected';
                                                    } ?>>关闭</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field" style="">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>分类样式</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="fllx" class="layui-select">
                                <option value="1" <?php if ($conf['fllx'] == 1) {
                                                        echo 'selected';
                                                    } ?>>下拉选择框</option>
                                <option value="2" <?php if ($conf['fllx'] == 2) {
                                                        echo 'selected';
                                                    } ?>>单选框</option>

                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>电脑端分类每行数量</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="number" min="0" max="24" class="layui-input" name="pc_flhnum" value="<?= $conf['pc_flhnum'] ?>" placeholder="请输入数字" required>
                            <span class="layui-font-12 layui-font-red">只能输入小于24的正偶数</span>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>手机端页分类每行数量</b>
                        </legend>
                        <div class="layui-field-box">
                            <span class="layui-font-12 layui-font-red">用于解决当商品名称太长时手机端页面显示异常</span>
                            <input type="number" class="layui-input" name="xs_flhnum" value="<?= $conf['xs_flhnum'] ?>" placeholder="请输入数字" required>
                            <span class="layui-font-12 layui-font-red">只能输入小于24的正偶数</span>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>商品说明展示方式</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="xdsmopen" class="layui-select">
                                <option value="0" <?php if ($conf['xdsmopen'] == 0) {
                                                        echo 'selected';
                                                    } ?>>嵌入式</option>
                                <option value="1" <?php if ($conf['xdsmopen'] == 1) {
                                                        echo 'selected';
                                                    } ?>>浮窗式</option>

                            </select>
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="查单页配置" name="query">

                    <p class="layui-font-14 layui-font-blue" style="margin:0 10px 15px;">
                        查单页url：<a style="color: inherit; text-decoration: underline;" target="_blank" :href="'//'+location.host+'/query?user=&t='">{{location.host}}/query?user=订单账号&t=代理UID</a>
                    </p>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>是否开启</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="chadan_open" class="layui-select">
                                <option value="1" <?php if ($conf['chadan_open'] == 1) {
                                                        echo 'selected';
                                                    } ?>>开启</option>
                                <option value="0" <?php if ($conf['chadan_open'] == 0) {
                                                        echo 'selected';
                                                    } ?>>关闭</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>是否可补刷</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="chadan_bs" class="layui-select">
                                <option value="1" <?php if ($conf['chadan_bs'] == 1) {
                                                        echo 'selected';
                                                    } ?>>开启</option>
                                <option value="0" <?php if ($conf['chadan_bs'] == 0) {
                                                        echo 'selected';
                                                    } ?>>关闭</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>默认查询UID</b>
                        </legend>
                        <div class="layui-field-box">
                            <p class="layui-font-12 layui-font-red">
                                t为空时查询该uid的订单
                            </p>
                            <input type="text" class="layui-input" name="chadan_default" value="<?= $conf['chadan_default'] ?>" placeholder="为空时默认可查询所有订单" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>查单页弹窗公告</b>
                        </legend>
                        <div class="layui-field-box">
                            <span class="layui-font-12 layui-font-red">留空则不显示弹窗公告</span>
                            <textarea type="text" name="chadan_t_notice" class="layui-textarea" rows="5" placeholder="请输入内容，支持HTML"><?= $conf['chadan_t_notice'] ?>
                                </textarea>
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="页面路径" name="pagePath">

                    <div style="display: grid; grid-template-columns: repeat(2,49%);justify-content: space-between;">
                        <fieldset class="layui-elem-field">
                            <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                <b>后台首页路径</b>
                            </legend>
                            <div class="layui-field-box">
                                <input type="text" class="layui-input" name="homePath" value="<?= $conf['homePath'] ?>" placeholder="请输入首页路径" required>
                                <div class="layui-font-12 layui-font-red">
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="layui-elem-field">
                            <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                <b>前台首页路径</b>
                            </legend>
                            <div class="layui-field-box">
                                <input type="text" class="layui-input" name="f_homePath" value="<?= $conf['f_homePath'] ?>" placeholder="请输入首页路径" required>
                                <div class="layui-font-12 layui-font-red">
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>接单商城路径</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="storePath" value="<?= $conf['storePath'] ?>" placeholder="请输入接单商城路径" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>侧边栏菜单配置(仅PC端支持拖拽，最多三级)</b>
                        </legend>
                        <div class="layui-field-box">
                            <div class="layui-font-12 layui-font-red">
                                <p>
                                    <i class="fa-solid fa-user-tie" title="仅管理员可见"></i> 代表仅管理员可见
                                </p>
                                <p>
                                    <i class="layui-icon layui-icon-eye-invisible" title="全员不可见(包括管理员)"></i> 代表全员不可见(包括管理员)
                                </p>
                            </div>
                            <hr />
                            <button title="添加一级路径" type="button" class="layui-btn layui-bg-blue layui-btn-sm" style="margin: 5px 0;" @click.stop="() => appendMenuOne()">
                                <i class="layui-icon layui-icon-addition"></i> 添加一级路径
                            </button>
                            <el-tree v-if="menuListIF" :data="menuList" name="menuList" :props="menuList_defaultProps" default-expand-all draggable :allow-drop="menuList_allowdrop">
                                <template #default="{ node, data }">
                                    <div class="custom-tree-node menuListTools">
                                        <div>
                                            <i v-if="node.data.type === 'shou3ye4_home' " class="fa-solid fa-lock layui-font-red" title="主页锁定"></i>
                                            <i v-if="node.data.admin" class="fa-solid fa-user-tie layui-font-blue" title="仅管理员可见"></i>
                                            <i v-if="node.data.hidden" class="layui-icon layui-icon-eye-invisible layui-font-blue" title="全员不可见(包括管理员)"></i>
                                            {{ node.label }} <i :class="node.data.icon"></i>
                                        </div>
                                        <div class="menuListTool">
                                            <button v-if="node.data.type!=='shou3ye4_home'" v-show="node.level != 3" :title="'添加下级菜单'" type="button" class="layui-btn layui-btn-primary layui-btn-xs" @click.stop="() => appendMenu(node,data)">
                                                <i class="layui-icon layui-icon-addition"></i>
                                            </button>
                                            <button :title="'编辑['+node.label+']'" type="button" class="layui-btn layui-btn-primary layui-btn-xs" @click.stop="() => editMenu(node,data)">
                                                <i class="layui-icon layui-icon-edit"></i>
                                            </button>
                                            <button v-if="node.data.type!=='shou3ye4_home'" :title="'删除['+node.label+']'" type="button" class="layui-btn layui-btn-primary layui-btn-xs" @click.stop="() => removeMenu(node,data)">
                                                <i class="layui-icon layui-icon-delete"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </el-tree>
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane v-if="false" label="打卡配置" name="punchCard">

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">sx打卡</legend>
                        <div class="layui-field-box">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Token</label>
                                <div class="col-sm-9">
                                    <input type="text" class="layui-input" name="sxdk_token" value="<?= $conf['sxdk_token'] ?>" placeholder="Token" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">账号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="layui-input" name="sxdk_user" value="<?= $conf['sxdk_user'] ?>" placeholder="账号" required>
                                </div>
                            </div>
                            <!--<div class="form-group">-->
                            <!--    <label class="col-sm-2 control-label">系数（下单价格 = 等级费率 * 系数 * 天数）</label>-->
                            <!--    <div class="col-sm-9">-->
                            <!--        <input type="number" class="layui-input" name="axqg_price" value="<?= $conf['axqg_price'] ?>" placeholder="系数" required>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">弹窗公告</label>
                                <div class="col-sm-9">
                                    <textarea type="text" name="sxdk_notice" class="layui-textarea" rows="5"><?= $conf['sxdk_notice'] ?></textarea>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="邮箱配置" name="mail">

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>邮件提醒</b>
                        </legend>
                        <div class="layui-field-box">

                            <fieldset class="layui-elem-field">
                                <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                    <b>总开关 <span class="layui-font-12 layui-font-red">最高优先级</span></b>
                                </legend>
                                <div class="layui-field-box">
                                    <select name="smtp_open" class="layui-select">
                                        <option value="1" <?php if ($conf['smtp_open'] == 1) {
                                                                echo 'selected';
                                                            } ?>>全部开启</option>
                                        <option value="0" <?php if ($conf['smtp_open'] == 0) {
                                                                echo 'selected';
                                                            } ?>>全部关闭</option>
                                    </select>
                                </div>
                            </fieldset>
                            <div class="layui-font-16 layui-font-red" style="margin-bottom: 5px;">
                                请勿开启【下单提醒】，否则容易因短时间内发件过多被邮箱官方封禁！
                            </div>

                            <fieldset class="layui-elem-field" style="display:inline-block;">
                                <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                    <b>登录提醒</b>
                                </legend>
                                <div class="layui-field-box">
                                    <select name="smtp_open_login" class="layui-select">
                                        <option value="1" <?php if ($conf['smtp_open_login'] == 1) {
                                                                echo 'selected';
                                                            } ?>>开启</option>
                                        <option value="0" <?php if ($conf['smtp_open_login'] == 0) {
                                                                echo 'selected';
                                                            } ?>>关闭</option>
                                    </select>
                                </div>
                            </fieldset>

                            <fieldset class="layui-elem-field" style="display:inline-block;">
                                <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                    <b>下单提醒</b>
                                </legend>
                                <div class="layui-field-box">
                                    <select name="smtp_open_xd" class="layui-select">
                                        <option value="1" <?php if ($conf['smtp_open_xd'] == 1) {
                                                                echo 'selected';
                                                            } ?>>开启</option>
                                        <option value="0" <?php if ($conf['smtp_open_xd'] == 0) {
                                                                echo 'selected';
                                                            } ?>>关闭</option>
                                    </select>
                                </div>
                            </fieldset>

                            <fieldset class="layui-elem-field" style="display:inline-block;">
                                <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                    <b>工单提醒</b>
                                </legend>
                                <div class="layui-field-box">
                                    <select name="smtp_open_gd" class="layui-select">
                                        <option value="1" <?php if ($conf['smtp_open_gd'] == 1) {
                                                                echo 'selected';
                                                            } ?>>开启</option>
                                        <option value="0" <?php if ($conf['smtp_open_gd'] == 0) {
                                                                echo 'selected';
                                                            } ?>>关闭</option>
                                    </select>
                                </div>
                            </fieldset>

                            <fieldset class="layui-elem-field" style="display:inline-block;">
                                <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                    <b>充值提醒</b>
                                </legend>
                                <div class="layui-field-box">
                                    <select name="smtp_open_cz" class="layui-select">
                                        <option value="1" <?php if ($conf['smtp_open_cz'] == 1) {
                                                                echo 'selected';
                                                            } ?>>开启</option>
                                        <option value="0" <?php if ($conf['smtp_open_cz'] == 0) {
                                                                echo 'selected';
                                                            } ?>>关闭</option>
                                    </select>
                                </div>
                            </fieldset>

                            <fieldset class="layui-elem-field" style="display:inline-block;">
                                <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                    <b>货源余额不足提醒</b>
                                </legend>
                                <div class="layui-field-box">
                                    <select name="smtp_open_huo" class="layui-select">
                                        <option value="1" <?php if ($conf['smtp_open_huo'] == 1) {
                                                                echo 'selected';
                                                            } ?>>开启</option>
                                        <option value="0" <?php if ($conf['smtp_open_huo'] == 0) {
                                                                echo 'selected';
                                                            } ?>>关闭</option>
                                    </select>
                                </div>
                            </fieldset>

                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field" style="display:inline-block;">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>SMTP服务器</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="smtp_host" value="<?= $conf['smtp_host'] ?>" placeholder="请输入SMTP服务器" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field" style="display:inline-block;">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>SMTP端口</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="smtp_port" value="<?= $conf['smtp_port'] ?>" placeholder="请输入SMTP端口" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field" style="display:inline-block;">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>安全协议 <span class="layui-font-12 layui-font-red">ssl 或 tls</span></b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="smtp_secure" value="<?= $conf['smtp_secure'] ?>" placeholder="ssl或tls" required>

                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>邮箱账号</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="smtp_user" value="<?= $conf['smtp_user'] ?>" placeholder="请输入邮箱账号" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>邮箱密码</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="smtp_pass" value="<?= $conf['smtp_pass'] ?>" placeholder="请输入邮箱密码" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>收信邮箱(用于测试发信是否正常)</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="smtp_cuser" value="<?= $conf['smtp_cuser'] ?>" placeholder="请输入收信邮箱" required>
                            <div class="layui-font-12 layui-font-red layui-padding-2">
                                <a href="javascript:0" style="text-decoration: underline;" @click="mailtest()">测试邮件发送↗</a>
                            </div>
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane v-if="false" label="聚合登录" name="aggregateLogin">

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>彩虹api聚合登录</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="login_apiurl" value="<?= $conf['login_apiurl'] ?>" placeholder="请输入聚合登录地址" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>应用ID</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="login_appid" value="<?= $conf['login_appid'] ?>" placeholder="请输入聚合登录应用ID" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>应用key</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="login_appkey" value="<?= $conf['login_appkey'] ?>" placeholder="请输入聚合登录应用key" required>
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="对接配置" name="dock">

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>API调用</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="settings" class="layui-select">
                                <option value="1" <?php if ($conf['settings'] == 1) {
                                                        echo 'selected';
                                                    } ?>>开启</option>
                                <option value="0" <?php if ($conf['settings'] == 0) {
                                                        echo 'selected';
                                                    } ?>>关闭</option>
                            </select>

                        </div>
                    </fieldset>


                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>查课差值限制</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="number" min="0" onblur="if(value<0){value=0}" class="layui-input" name="api_ck_threshold" value="<?= $conf['api_ck_threshold'] ?>" placeholder="请输入查课差值限制" required>
                            <span class="layui-font-12 layui-font-red">差值 = API查课次数 - API下单次数，大于该差值则视为恶意查课行为，将禁止对应的UID查课</span>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>查课单次扣费</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="number" min="0" onblur="if(value<0){value=0}" class="layui-input" name="api_ckkf" value="<?= $conf['api_ckkf'] ?>" placeholder="输入0或不填则查课不扣费" required>
                            <span class="layui-font-12 layui-font-red">输入0或不填则查课不扣费</span>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>查课余额限制</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="number" min="0" onblur="if(value<0){value=0}" class="layui-input" name="api_ck" value="<?= $conf['api_ck'] ?>" placeholder="请输入API查课余额限制金额" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>下单余额限制</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="number" min="0" onblur="if(value<0){value=0}" class="layui-input" name="api_xd" value="<?= $conf['api_xd'] ?>" placeholder="请输入API下单余额限制金额" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>补刷次数限制</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="number" min="0" onblur="if(value<0){value=0}" class="layui-input" name="api_bs" value="<?= $conf['api_bs'] ?>" placeholder="请输入补刷次数限制" required>
                            <span class="layui-font-12 layui-font-red">填0则不限制</span>
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="接单商城配置" name="store">

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>启用接单商城功能</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="onlineStore_open" class="layui-select">
                                <option value="1" <?php if ($conf['onlineStore_open'] == 1) {
                                                        echo 'selected';
                                                    } ?>>启用</option>
                                <option value="0" <?php if ($conf['onlineStore_open'] == 0) {
                                                        echo 'selected';
                                                    } ?>>关闭</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>右上方代理登录跳转</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="onlineStore_trdltz" class="layui-select">
                                <option value="1" <?php if ($conf['onlineStore_trdltz'] == 1) {
                                                        echo 'selected';
                                                    } ?>>显示</option>
                                <option value="0" <?php if ($conf['onlineStore_trdltz'] == 0) {
                                                        echo 'selected';
                                                    } ?>>隐藏</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>默认增加价格</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="onlineStore_add" value="<?= $conf['onlineStore_add'] ?>" placeholder="请输入默认增加价格" required>
                            <span class="layui-font-12 layui-font-red">
                                代理初始化商城时，各商品售价将设置为：该值 + 商品成本(定价*费率)<br />
                                填纯数字则为加法，若需百分比增加，请加上百分比符号：%
                            </span>
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="主题配置" name="theme">

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>默认主题</b>
                        </legend>
                        <div class="layui-field-box">
                            <div class="layui-font-12 layui-font-blue " style="margin-bottom: 5px;">
                                注：添加 [自定义主题树] 并保存后，需刷新本页面此处选择框里才会有新添加的主题！
                            </div>
                            <select name="themesData_default" class="layui-select">

                                <option v-for="(item,index) in themesData" :value="item.id" :selected="item.id == '<?= $conf['themesData_default'] ?>'">
                                    {{ item.name }} | {{ item.id }}
                                </option>

                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>自定义主题树</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="themesData_default" class="layui-select">

                                <option v-for="(item,index) in themesData" :value="item.id" :selected="item.id == '<?= $conf['themesData_default'] ?>'">
                                    {{ item.name }} | {{ item.id }}
                                </option>

                            </select>

                            <button title="添加下级菜单" type="button" class="layui-btn layui-bg-blue layui-btn-sm" style="margin: 5px 0;" @click.stop="() => appendThemeOne()">
                                <i class="layui-icon layui-icon-addition"></i> 添加主题
                            </button>

                            <div class="layui-font-12 layui-font-blue ">
                                注：下方主题树支持拖拽排序！
                            </div>
                            <hr />

                            <el-tree v-if="themesDataIF" :data="themesData" name="themesData" :props="themesData_defaultProps" default-expand-all draggable :allow-drop="themesData_allowdrop">
                                <template #default="{ node, data }">
                                    <div class="custom-tree-node menuListTools" slot-scope="{ node, data }">
                                        <div>
                                            <i class="layui-icon layui-icon-theme"></i> {{ node.data.name }} | ID： {{ node.data.id }}
                                        </div>
                                        <div class="menuListTool">
                                            <button :title="'编辑['+node.label+']'" type="button" class="layui-btn layui-btn-primary layui-btn-xs" @click.stop="() => editTheme(node,data)">
                                                <i class="layui-icon layui-icon-edit"></i>
                                            </button>
                                            <button v-if="node.data.type!=='shou3ye4_home'" :title="'删除['+node.label+']'" type="button" class="layui-btn layui-btn-primary layui-btn-xs" @click.stop="() => removeTheme(node,data)">
                                                <i class="layui-icon layui-icon-delete"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </el-tree>
                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="强国配置" name="qg">

                    <div class="layui-font-12 layui-font-red">
                        更多配置请到相关单页自行配置
                    </div>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">ax强国</legend>
                        <div class="layui-field-box">

                            <fieldset class="layui-elem-field">
                                <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                    <b>是否开启</b>
                                </legend>
                                <div class="layui-field-box">
                                    <select name="axqg" class="layui-select">
                                        <option value="1" <?php if ($conf['axqg'] == 1) {
                                                                echo 'selected';
                                                            } ?>>开启</option>
                                        <option value="0" <?php if ($conf['axqg'] == 0) {
                                                                echo 'selected';
                                                            } ?>>关闭</option>
                                    </select>
                                </div>
                            </fieldset>

                            <fieldset class="layui-elem-field">
                                <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                                    <b>弹窗公告</b>
                                </legend>
                                <div class="layui-field-box">
                                    <textarea type="text" name="axqg_notice" class="layui-textarea" rows="5" placeholder="支持HTML"><?= $conf['axqg_notice'] ?></textarea>
                                    <span class="layui-font-12 layui-font-red">
                                        留空不填则不显示
                                    </span>
                                </div>
                            </fieldset>

                        </div>
                    </fieldset>

                </el-tab-pane>

                <el-tab-pane label="GPT配置" name="gpt">

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>是否开启</b>
                        </legend>
                        <div class="layui-field-box">
                            <select name="gpt" class="layui-select">
                                <option value="1" <?php if ($conf['gpt'] == 1) {
                                                        echo 'selected';
                                                    } ?>>开启</option>
                                <option value="0" <?php if ($conf['gpt'] == 0) {
                                                        echo 'selected';
                                                    } ?>>关闭</option>
                            </select>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>GPT地址</b>
                        </legend>
                        <div class="layui-field-box">
                            <input type="text" class="layui-input" name="gpt_url" value="<?= $conf['gpt_url'] ?>" placeholder="请输入GPT地址" required>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>弹窗公告</b>
                        </legend>
                        <div class="layui-field-box">
                            <textarea type="text" name="gpt_notice" class="layui-textarea" rows="5" placeholder="支持HTML"><?= $conf['gpt_notice'] ?></textarea>
                            <span class="layui-font-12 layui-font-red">
                                留空不填则不显示
                            </span>
                        </div>
                    </fieldset>

                </el-tab-pane>

            </el-tabs>

        </form>
    </div>

    <div id="menuList_menuSet" class="layui-padding-2" style="display: none;">
        <div class="layui-font-12 layui-font-red">
            注意此处只是暂时保存修改，若需要生效，请在全部修改后点击右上方 [保存] 按钮！！！
        </div>
        <hr />
        <div class="layui-form">


            <div class="layui-form-item">
                <div class="layui-font-13" style="margin-bottom: 3px;">
                    新标题
                </div>
                <!--<label class="layui-form-label" style="width:95px">对接台分类ID</label>-->
                <div class="layui-input-inline" style="width: 100%;">
                    <input type="text" name="title" v-model="now_menu.title" placeholder="请输入新的页面标题" autocomplete="off" class="layui-input" @input="menuNewTitle_pinyin">
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-font-13" style="margin-bottom: 3px;">
                    标识(自动生成，由[拼音+声调序号+当前毫秒]生成)
                </div>
                <!--<label class="layui-form-label" style="width:95px">对接台分类ID</label>-->
                <div class="layui-input-inline" style="width: 100%;">
                    <input disabled="" type="text" name="type" v-model="now_menu.type" placeholder="自动生成" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-font-13" style="margin-bottom: 3px;">
                    图标(右方为预览)
                </div>
                <div class="layui-input-inline" style="width: 100%; display: flex; align-items: center;">
                    <input type="text" name="icon" v-model="now_menu.icon" placeholder="请输入显示图标" autocomplete="off" class="layui-input">
                    <div class="center" style="width: 35px; height: 35px;display: flex; align-items: center; justify-content: center; border: 1px dashed #dddddd; margin-left: 5px;position: relative;">
                        <i :class="now_menu.icon"></i>
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-font-13" style="margin-bottom: 3px;">
                    路径
                </div>
                <!--<label class="layui-form-label" style="width:95px">对接台分类ID</label>-->
                <div class="layui-input-inline" style="width: 100%;">
                    <input v-show="now_menu.type!=='shou3ye4_home'" type="text" name="href" v-model="now_menu.href" placeholder="请输入页面地址" autocomplete="off" class="layui-input">
                    <input disabled="" v-show="now_menu.type==='shou3ye4_home'" type="text" placeholder="跟随[首页路径]" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div v-show="now_menu.type!=='shou3ye4_home'" style="display: flex;">
                <div class="layui-form-item" style="width: 50%;">
                    <div class="layui-font-13" style="margin-bottom: 3px;">
                        是否仅管理员可见
                    </div>
                    <!--<label class="layui-form-label" style="width:95px">对接台分类ID</label>-->
                    <div class="layui-input-inline" style="width: 100%;margin: auto;">
                        <!--<input type="text" name="href" v-model="now_menu.href"  placeholder="请输入页面地址" autocomplete="off" class="layui-input">-->
                        <el-switch v-model="now_menu.admin" active-color="#13ce66" inactive-color="#ff4949">
                        </el-switch>
                    </div>
                </div>

                <div class="layui-form-item" style="width: 50%;">
                    <div class="layui-font-13" style="margin-bottom: 3px;">
                        是否全员隐藏(包括管理员)
                    </div>
                    <!--<label class="layui-form-label" style="width:95px">对接台分类ID</label>-->
                    <div class="layui-input-inline" style="width: 100%;margin: auto;">
                        <!--<input type="text" name="href" v-model="now_menu.href"  placeholder="请输入页面地址" autocomplete="off" class="layui-input">-->
                        <el-switch v-model="now_menu.hidden" active-color="#13ce66" inactive-color="#ff4949">
                        </el-switch>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div id="themesData_themeSet" class="layui-padding-2" style="display: none;">
        <div class="layui-font-12 layui-font-red">
            注意此处只是暂时保存修改，若需要生效，请在全部修改后点击右上方 [保存] 按钮！！！
        </div>
        <hr />
        <div class="layui-form">

            <div class="layui-form-item">
                <div class="layui-font-13" style="margin-bottom: 3px;">
                    名称(勿超过四个字)
                </div>
                <!--<label class="layui-form-label" style="width:95px">对接台分类ID</label>-->
                <div class="layui-input-inline" style="width: 100%;">
                    <input :disabled="now_theme.id==='dark' || now_theme.id==='light'" type="text" name="title" v-model="now_theme.name" placeholder="请输入新的名称" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-font-13" style="margin-bottom: 3px;">
                    标识(必须填写，请自行判断唯一性)
                </div>
                <!--<label class="layui-form-label" style="width:95px">对接台分类ID</label>-->
                <div class="layui-input-inline" style="width: 100%;">
                    <input :disabled="now_theme.id==='dark' || now_theme.id==='light'" type="text" name="id" v-model="now_theme.id" placeholder="请输入标识" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-font-13" style="margin-bottom: 3px;">
                    CSS资源路径
                </div>
                <!--<label class="layui-form-label" style="width:95px">对接台分类ID</label>-->
                <div class="layui-input-inline" style="width: 100%;">
                    <input v-show="!(now_theme.id==='dark' || now_theme.id==='light')" type="text" name="url" v-model="now_theme.url" placeholder="请输入资源路径" autocomplete="off" class="layui-input">
                    <input disabled="" v-show="now_theme.id==='dark' || now_theme.id==='light'" type="text" :placeholder="now_theme.url" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-font-blue layui-font-12">
                注：此处的主色和辅色配置，只是为了在【主题选择题】里起参考的作用，具体主题资源的配色在你自己的css资源里写！
            </div>
            <div style="display: flex;">
                <div class="layui-form-item" style="margin-right: 15px;">
                    <div class="layui-font-13" style="margin-bottom: 3px;">
                        主色
                    </div>
                    <div class="layui-input-inline" style="width: 100%;margin: auto;">
                        <el-color-picker v-model="now_theme.c1" size="small" popper-class="zIndex_TOP"></el-color-picker>
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-font-13" style="margin-bottom: 3px;">
                        辅色
                    </div>
                    <div class="layui-input-inline" style="width: 100%;margin: auto;">
                        <el-color-picker v-model="now_theme.c2" size="small" popper-class="zIndex_TOP"></el-color-picker>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<?php include_once($root . '/index/components/footer.php'); ?>

<script>
    var {
        pinyin
    } = pinyinPro;

    const app = Vue.createApp({
        data() {
            return {
                row: <? echo json_encode($conf) ?>,
                tabList_active: "basic",
                menuListIF: true,
                menuList: [],
                now_menu: {

                },
                menuList_defaultProps: {
                    children: 'children',
                    label: 'title'
                },
                themesDataIF: true,
                now_theme: {

                },
                themesData: <?= $conf['themesData'] ?>,
                themesData_defaultProps: {
                    children: 'children',
                    label: 'name'
                },
                edits: {
                    qd_notice: null,
                    tcgonggao: null,
                    edit_dlgl_notice: null,
                },
                epay_zs: {
                    rules: JSON.parse(<? echo json_encode($conf["epay_zs"]) ?>),
                    money: '',
                    giftAmount: '',
                },
                webVfxHotList: [{
                        t: '新年灯笼',
                        u: '/assets/webVfx/china-lantern.js',
                    },
                    {
                        t: '整体黑白灰色(默哀专用)',
                        u: '/assets/webVfx/moai.js',
                    },
                    {
                        t: '鼠标移动跟随星星',
                        u: '/assets/webVfx/cursorStyle.js',
                    },
                    {
                        t: '鼠标点击显示社会主义彩色文字',
                        u: '/assets/webVfx/shehuizhuyi.js',
                    },
                    {
                        t: '鼠标点击显示爱心',
                        u: '/assets/webVfx/love.js',
                    },
                    {
                        t: '输入框输入特效',
                        u: '/assets/webVfx/inputParticles.js',
                    },
                ],
            }
        },
        mounted() {
            const _this = this;
            layui.use('form', async function() {
                var util = layui.util;
                var form = layui.form;
                // 自定义固定条
                util.fixbar({
                    margin: 100
                })

                var element = layui.element;
                // hash 地址定位
                var hashName = 'tabid'; // hash 名称
                var layid = location.hash.replace(new RegExp('^#' + hashName + '='), ''); // 获取 lay-id 值
                let loadIndex = layer.load(0);
                $("#webset").ready(() => {
                    $("#webset").show();
                    layer.close(loadIndex);


                })
                // 初始切换
                element.render('tab', 'test-hash');
                element.tabChange('test-hash', layid);
                // 切换事件
                element.on('tab(test-hash)', function(obj) {
                    location.hash = hashName + '=' + this.getAttribute('lay-id');
                });

                _this.czzs_addRule(1);

                let viewportWidth = $(window).width();
                setTimeout(() => {
                    let isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                    if (isMobile) {
                        $(".el-tabs__nav-prev").hide();
                        $(".el-tabs__nav-next").hide();
                        $(".el-tabs__nav-wrap").css("padding", "0 0");
                    } else {
                        $(".el-tabs__nav-prev").show();
                        $(".el-tabs__nav-next").show();
                        $(".el-tabs__nav-wrap").css("padding", "0 20px");
                    }
                }, 100)
                $(window).on("resize", () => {
                    let viewportWidth = $(window).width();
                    setTimeout(() => {
                        let isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                        console.log(isMobile)
                        if (isMobile) {
                            $(".el-tabs__nav-prev").hide();
                            $(".el-tabs__nav-next").hide();
                            $(".el-tabs__nav-wrap").css("padding", "0 0");
                        } else {
                            $(".el-tabs__nav-prev").show();
                            $(".el-tabs__nav-next").show();
                            $(".el-tabs__nav-wrap").css("padding", "0 20px");
                        }
                    }, 100)
                })

                let startX, currentX, moveX = 0;
                $('.el-tabs__nav').on({
                    touchstart: (e) => {
                        let touch = e.originalEvent.touches[0];
                        startX = touch.pageX;
                        currentX = $('.el-tabs__nav').css("transform").match(/matrix.*\((.+)\)/)[1].split(', ')[4];
                        // console.log("touchstart",touch, Math.abs($('.el-tabs__nav').css("transform").match(/matrix.*\((.+)\)/)[1].split(', ')[4]) );
                    },
                    touchmove: (e) => {
                        let = touch = e.originalEvent.touches[0];
                        moveX = startX - touch.pageX;
                        console.log(moveX, currentX, currentX - moveX, -($('.el-tabs__nav').width() - 300))
                        // if((Math.abs($('.el-tabs__nav').css("transform").match(/matrix.*\((.+)\)/)[1].split(', ')[4]) - (startX - touch.pageX) ) < 0){
                        if (currentX - moveX <= 10 && currentX - moveX > -($('.el-tabs__nav').width() - 330)) {
                            $('.el-tabs__nav').css('transform', 'translateX(' + (currentX - moveX) + 'px)');
                            $(".el-tabs__nav-prev").removeClass('is-disabled');
                        }
                        // }
                        e.preventDefault();
                    },
                    touchend: (e) => {
                        // console.log("touchend",e);
                    },
                })

                // await _this.edit_qd_notice_init();
                // _this.edits.qd_notice.setValue(_this.edits.qd_notice.html2md(_this.row.qd_notice), true);
                // await _this.edit_tcgonggao_init();
                // _this.edits.tcgonggao.setValue(_this.edits.tcgonggao.html2md(_this.row.tcgonggao), true);
                // await _this.edit_dlgl_notice_init();
                // _this.edits.dlgl_notice.setValue(_this.edits.dlgl_notice.html2md(_this.row.dlgl_notice), true);


            })

        },
        created() {
            const _this = this;
            let menuList = <?= $conf["menuList"] ?>;
            _this.menuList = menuList;
            _this.foraddID(_this.menuList);
            
            if(window.location.hash.split('#')[1] ){
                _this.tabList_active = window.location.hash.split('#')[1];
            }
            
        },
        computed: {
            pinyinGo() {
                return (t = '') => {
                    return pinyin(t, {
                        toneType: 'num',
                        type: 'array'
                    }).join('')
                }
            },
            location() {
                return location
            }
        },
        methods: {
            tabClick(p,e){
                window.location.hash = p.paneName;
            },
            menuNewTitle_pinyin() {
                const _this = this;
                _this.now_menu.type = _this.now_menu.type ? _this.now_menu.type : '';
                _this.now_menu.type = _this.now_menu.type.replace(/[\s\n\r]+/g, '');

                if (_this.now_menu.type !== 'shou3ye4_home') {
                    let pinyin_title = JSON.parse(JSON.stringify(_this.pinyinGo(_this.now_menu.title)));
                    pinyin_title = pinyin_title.replace(/[\s\n\r]+/g, '');
                    _this.now_menu.type = _this.pinyinGo(pinyin_title) + '_' + new Date().getMilliseconds()
                }
            },
            foraddID(data, prefix = '') {
                const _this = this;
                for (let i = 0; i < data.length; i++) {
                    let zid = prefix ? `${prefix}-${i + 1}` : `${i + 1}`;
                    data[i].zid = zid;
                    if (data[i].children && data[i].children.length > 0) {
                        _this.foraddID(data[i].children, zid);
                    }
                }
            },
            forsubID(data) {
                const _this = this;
                for (let i = 0; i < data.length; i++) {
                    delete data[i].zid;
                    if (data[i].children && data[i].children.length > 0) {
                        _this.forsubID(data[i].children);
                    }
                }
            },
            // 从多层级数组中寻找所在位置
            forfindMenu(arr, zid) {
                let foundPath = [];

                // 使用递归方式查找
                function find(arr, path) {
                    for (let i = 0; i < arr.length; i++) {
                        const item = arr[i];
                        if (item.zid === zid) {
                            foundPath = [...path, i];
                            break;
                        }
                        if (item.children && item.children.length > 0) {
                            find(item.children, [...path, i]);
                            if (foundPath.length > 0) {
                                break;
                            }
                        }
                    }
                }

                find(arr, []);

                return foundPath;
            },
            editMenu(node, data) {
                const _this = this;
                _this.now_menu = [];
                _this.now_menu = JSON.parse(JSON.stringify(data));
                setTimeout(() => {
                    layer.open({
                        type: 1,
                        id: "menuList_menuSet_ID",
                        shade: 0.3, // 不显示遮罩
                        // scrollbar: false,
                        area: ['360px', 'auto'],
                        title: '编辑：' + (data.title ? data.title : '未命名'),
                        content: $('#menuList_menuSet'),
                        btn: '暂时保存修改',
                        end() {
                            _this.now_menu = [];
                        },
                        btn1: function(index, layero, that) {
                            let thisPath = _this.forfindMenu(_this.menuList, _this.now_menu.zid);
                            for (let i in _this.now_menu) {
                                if (i != 'children' && i != '$treeNodeId') {
                                    node.data[i] = _this.now_menu[i];
                                }
                            }
                            layer.close(index);
                            layer.msg('修改成功！别忘了保存哟~');
                            _this.menuListIF = false;
                            setTimeout(() => {
                                _this.menuListIF = true;
                            }, 0)
                        },
                    })
                }, 0)
            },
            appendMenu(node, data) {
                const _this = this;
                if (data.zid.split('-').length >= 3) {
                    layer.msg('最多三级菜单！')
                    return
                }
                const newChild = {
                    title: '未命名页面',
                    icon: '',
                    href: '',
                    children: []
                };
                if (!data.children) {
                    _this.$set(data, 'children', []);
                }
                data.children.push(newChild);
                _this.foraddID(_this.menuList);
            },
            removeMenu(node, data) {
                const parent = node.parent;
                const children = parent.data.children || parent.data;
                const index = children.findIndex(d => d.zid === data.zid);
                children.splice(index, 1);
            },
            menuList_allowdrop(draggingNode, dropNode, type) {
                if (draggingNode.data.type === 'shou3ye4_home') {
                    return false
                } else if (dropNode.data.type === 'shou3ye4_home') {
                    return false
                } else if (draggingNode.level == dropNode.level && draggingNode.level >= 3) {
                    return type === 'prev' || type === 'next'
                } else {
                    return true
                }
            },
            appendMenuOne(){
                const _this = this;
                const newChild = {
                    title: '未命名页面',
                    icon: '',
                    href: '',
                    children: []
                };
                _this.menuList.unshift(newChild);
            },
            themesData_allowdrop(draggingNode, dropNode, type) {
                return type === 'prev' || type === 'next'
            },
            appendThemeOne() {
                const _this = this;
                const newChild = {
                    name: '未命名',
                    id: '',
                    url: '',
                    c1: '',
                    c2: '',
                    author: ''
                };
                _this.themesData.push(newChild);
                // _this.foraddID(_this.menuList);
            },
            removeTheme(node, data) {
                const parent = node.parent;
                const children = parent.data.children || parent.data;
                console.log(children)
                const index = children.findIndex(d => d.id === data.id);
                children.splice(index, 1);
            },
            editTheme(node, data) {
                const _this = this;
                _this.now_theme = [];
                _this.now_theme = JSON.parse(JSON.stringify(data));
                setTimeout(() => {
                    layer.open({
                        type: 1,
                        id: "themesData_themeSet_ID",
                        shade: 0.3, // 不显示遮罩
                        // scrollbar: false,
                        area: ['360px', 'auto'],
                        title: '编辑：' + (data.name ? data.name : '未命名'),
                        content: $('#themesData_themeSet'),
                        btn: '暂时保存修改',
                        end() {
                            _this.now_theme = [];
                        },
                        btn1: function(index, layero, that) {
                            if (!_this.now_theme.id) {
                                layer.msg('必须输入标识！');
                                return
                            }
                            let thisPath = _this.forfindMenu(_this.themesData, _this.now_theme.id);
                            for (let i in _this.now_theme) {
                                if (i != 'children' && i != '$treeNodeId') {
                                    node.data[i] = _this.now_theme[i];
                                }
                            }
                            layer.close(index);
                            layer.msg('修改成功！别忘了保存哟~');
                            _this.themesDataIF = false;
                            setTimeout(() => {
                                _this.themesDataIF = true;
                            }, 0)
                        },
                    })
                }, 0)
            },
            themeNewTitle_pinyin() {
                const _this = this;
                _this.now_theme.id = _this.now_theme.id ? _this.now_theme.id : '';
                _this.now_theme.id = _this.now_theme.id.replace(/[\s\n\r]+/g, '');

                if (_this.now_theme.id !== 'shou3ye4_home') {
                    let pinyin_title = JSON.parse(JSON.stringify(_this.pinyinGo(_this.now_theme.name)));
                    pinyin_title = pinyin_title.replace(/[\s\n\r]+/g, '');
                    _this.now_theme.id = _this.pinyinGo(pinyin_title)
                }
            },
            // 查看图片
            seePhoto(url = '', alt = "未命名", title = "未命名", start = 0) {
                layer.photos({
                    photos: {
                        "title": title,
                        "start": start,
                        "data": [{
                            "alt": alt,
                            "pid": url,
                            "src": url,
                        }]
                    },
                    footer: true // 是否显示底部栏 --- 2.8.16+
                });
            },
            czzs_addRule: function(type = 0, index) {
                const _this = this;
                if (!type) {
                    _this.epay_zs.rules.push({
                        min: '',
                        max: '',
                        zsprice: ''
                    });
                }
                if (type && index) {
                    if (!_this.epay_zs.rules[index].max) {
                        return
                    }
                }
                _this.czzs_sortRules();
                _this.czzs_adjustRules();

                for (let i in _this.epay_zs.rules) {
                    if (!_this.epay_zs.rules[i]['zsprice']) {
                        _this.epay_zs.rules[i]['zsprice'] = 0;
                    }
                }

                // event.preventDefault();
            },
            czzs_deleteRule: function(index) {
                const _this = this;
                _this.epay_zs.rules.splice(index, 1);
                _this.czzs_addRule(1);
            },
            czzs_sortRules: function() {
                const _this = this;
                _this.epay_zs.rules.sort((a, b) => {
                    const minA = parseFloat(a.min);
                    const minB = parseFloat(b.min);
                    if (minA < minB) return -1;
                    if (minA > minB) return 1;
                    return 0;
                });

            },
            czzs_adjustRules: function() {
                const _this = this;
                for (let i = 0; i < _this.epay_zs.rules.length - 1; i++) {
                    const currentMax = parseFloat(_this.epay_zs.rules[i].max);
                    const nextMin = parseFloat(_this.epay_zs.rules[i + 1].min);
                    if (!isNaN(currentMax) && !isNaN(nextMin) && currentMax >= nextMin) {
                        _this.epay_zs.rules[i].max = (nextMin - 0.01).toFixed(2);
                    }
                }
            },
            calculateGiftAmount() {
                const _this = this;
                console.log('sd', _this.epay_zs.rules);
                const money = parseFloat(_this.epay_zs.money);
                let giftAmount = 0;

                for (const rule of _this.epay_zs.rules) {
                    if (money >= parseFloat(rule.min) && (rule.max === '' || money < parseFloat(rule.max))) {
                        giftAmount = money * (parseFloat(rule.zsprice) / 100);
                        break;
                    }
                }

                _this.epay_zs.giftAmount = giftAmount.toFixed(2);
            },
            edit_qd_notice_init: function() {
                const _this = this;
                let loadIndex = layer.load(0);
                return new Promise((resolve) => {
                    _this.edits.qd_notice = new Vditor("edit_qd_notice", {
                        "cdn": "assets/vditor",
                        "height": 300,
                        "placeholder": "请输入内容",
                        "icon": "material",
                        "toolbar": ['emoji', "headings", "bold", "line", "italic", "strike", "|", "line", "quote", "list", "ordered-list", "check", "outdent", "indent", "code", "insert-after", "insert-before", "undo", "redo", "link", "table", "edit-mode", "both", "preview", "fullscreen", "outline"],
                        after() {
                            layer.close(loadIndex);
                            resolve();
                        },
                        input(md) {
                            $("#form-web").find("textarea[name='qd_notice']").val(`${_this.edits.qd_notice.getHTML()}`);
                        },
                    });
                });
            },
            edit_tcgonggao_init: function() {
                const _this = this;
                let loadIndex = layer.load(0);
                return new Promise((resolve) => {
                    _this.edits.tcgonggao = new Vditor("edit_tcgonggao", {
                        "cdn": "assets/vditor",
                        "height": 300,
                        "placeholder": "请输入内容",
                        "icon": "material",
                        "toolbar": ['emoji', "headings", "bold", "line", "italic", "strike", "|", "line", "quote", "list", "ordered-list", "check", "outdent", "indent", "code", "insert-after", "insert-before", "undo", "redo", "link", "table", "edit-mode", "both", "preview", "fullscreen", "outline"],
                        after() {
                            layer.close(loadIndex);
                            resolve();
                        },
                        input(md) {
                            $("#form-web").find("textarea[name='tcgonggao']").val(`${_this.edits.tcgonggao.getHTML()}`);
                        },
                    });
                });
            },
            edit_dlgl_notice_init: function() {
                const _this = this;
                let loadIndex = layer.load(0);
                return new Promise((resolve) => {
                    _this.edits.dlgl_notice = new Vditor("edit_dlgl_notice", {
                        "cdn": "assets/vditor",
                        "height": 300,
                        "placeholder": "请输入内容",
                        "icon": "material",
                        "toolbar": ['emoji', "headings", "bold", "line", "italic", "strike", "|", "line", "quote", "list", "ordered-list", "check", "outdent", "indent", "code", "insert-after", "insert-before", "undo", "redo", "link", "table", "edit-mode", "both", "preview", "fullscreen", "outline"],
                        after() {
                            layer.close(loadIndex);
                            resolve();
                        },
                        input(md) {
                            $("#form-web").find("textarea[name='dlgl_notice']").val(`${_this.edits.dlgl_notice.getHTML()}`);
                        },
                    });
                });
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
            add: function() {
                const _this = this;
                _this.forsubID(_this.menuList)
                // console.log(_this.themesData)
                // return
                var loading = layer.load(0);
                let formWeb = $("#form-web").serialize();

                for (let i = 0; i < _this.epay_zs.rules.length; i++) {
                    if (_this.epay_zs.rules[i].min === '' || _this.epay_zs.rules[i].max === '') {
                        _this.epay_zs.rules.splice(i, 1);
                        i--; // 因为删除了一个元素，需要将索引减一，否则会跳过下一个元素
                    }
                }
                let epay_zs_rules = 'epay_zs' + '=' + JSON.stringify(_this.epay_zs.rules) + '&';

                // 侧边菜单
                let menuList = '&menuList' + '=' + JSON.stringify(_this.menuList);

                // 主题
                let themesData = '&themesData' + '=' + JSON.stringify(_this.themesData);

                formWeb = epay_zs_rules + formWeb + menuList + themesData;

                axios.post("/apiadmin.php?act=webset", {
                    data: formWeb
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    _this.foraddID(_this.menuList);
                    layer.close(loading);
                    if (r.data.code == 1) {
                        console.log(85, r.data);
                        layer.msg(r.data.msg)

                        setTimeout(function() {
                            // 			window.location.reload()
                        }, 600);
                    } else {
                        layer.alert(r.data.msg, {
                            icon: 2,
                            title: "温馨提示"
                        });
                    }
                });
            },
            mailtest: function() {
                const _this = this;
                _this.add();
                layer.closeAll();

                let loadIndex = layer.msg('发送邮件中，请稍等...', {
                    icon: 16,
                    shade: 0.01,
                    time: 100000000,
                })
                axios.post("/apiadmin.php?act=mailtest", {}, {
                    emulateJSON: true
                }).then(r => {
                    if (r.data.code === 1 && r.data.status) {
                        layer.msg(r.data.status)
                    } else {
                        layer.msg('发送失败，请检查配置！')
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
    var vm = app.mount('#webset');
    // -----------------------------
</script>