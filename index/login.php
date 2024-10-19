<?php
include_once('../confing/common.php');
if ($islogin == 1) {
    echo "<p>已登录,跳转中...</p>";
    exit("<meta http-equiv='refresh' content='1;url=/index'>");
}

?>

<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta charset="utf-8" />
    <meta name="robots" content="noindex, nofollow">
    <title><?= $conf['sitename'] ?> - 登录</title>
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="icon" href="./favicon.ico" type="image/ico">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes" />

    <!--不蒜子统计代码-->
    <script async src="//busuanzi.ibruce.info/busuanzi/2.3/busuanzi.pure.mini.js"></script>

    <link href="/assets/css/login.css?sv=<?= $conf['version'] ?>" rel="stylesheet">

    <script src="/assets/toc/jquery.min.js"></script>

    <script src="/assets/toc/vue3.min.js"></script>
    <link href="/assets/toc/element-plus.min.css" rel="stylesheet">
    <script src="/assets/toc/element-plus.min.js"></script>
    <script src="/assets/toc/element-plus_icons-vue.min.js"></script>
    <link rel="stylesheet" href="/assets/css/toc.css?sv=<?= $conf['version'] ?>" media="all">

    <link rel="stylesheet" href="/assets/toc/layui.min.css?v=2.9.13" media="all">
    <script src="/assets/toc/layui.min.js"></script>

    <script src="/assets/toc/axios.min.js"></script>
    <script src="/assets/toc/axios_toc.min.js"></script>

    <!--谷歌字体-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php if ($conf["fontsZDY"] == 1) { ?>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC&display=swap" rel="stylesheet">
    <?php } ?>

</head>

<style>
    #app {
        background: url('<?= $conf["login_banner"] ?>') 50% fixed no-repeat;
    }

    body {
        font-family:
            <?= $conf['fontsFamily'] ?>
        ;
        font-weight: 400;
        font-style: normal;
    }
</style>

<body>

    <div id="app" style="height: 100vh; background-size: cover;">
        <div id="login1" style="display:none">
            <?php if ($conf["login_top_notice_open"] == "on") { ?>
                <div style="position: absolute; z-index: 1; width: 100%; top: -40px;opacity: .8;">
                    <el-alert class="alert_width100 layui-font-12" title="" type="info">
                        <templete slot="title">
                            <div style="display: flex;align-items: center;">
                                <div
                                    style="padding-right: 8px; margin-right: 10px; border-right: 1px solid rgb(221, 221, 221); display: flex; align-items: center; justify-content: center;">
                                    <el-icon>
                                        <Bell />
                                    </el-icon>
                                </div>
                                <div class="marquee1" style="flex: auto;">
                                    <span>
                                        <?= $conf["login_top_notice"] ?>
                                    </span>
                                </div>
                            </div>
                        </templete>
                    </el-alert>
                </div>
            <?php } ?>
            <!--<h1 style="font-size: 60px; font-weight: 400;font-style: italic;">-->
            <!--    <span>Hello！</span>-->
            <!--    <img style="width:50px;border-radius: 50%;" src="<?= $conf['login_logo'] ?>">-->
            <!--</h1>-->
            <div v-if="loginType" class="login-box"
                style="position: relative; display: flex; justify-content: space-between;">
                <div class="login-box_left" style="pointer-events: none">
                    <img src="/assets/images/login_img.png" width="360">
                </div>
                <div class="login-box_right">

                    <div style="flex: auto;">

                        <div class="layui-font-12"
                            style="position: absolute; left: 0px; top: 10px; width: -webkit-fill-available; padding: 0px 12px; display: flex; align-items: center; justify-content: space-between;color: #aaaaaa;">
                            <div>时光清浅处，一步一安然。</div>
                            <div>
                                <el-tooltip content="好好学习，天天向上" placement="left" effect="light">
                                    <el-icon>
                                        <Warning />
                                    </el-icon>
                                </el-tooltip>
                            </div>
                        </div>

                        <h1 style="margin: 0 0 27px">
                            <center><?= $conf['sitename'] ?></center>
                        </h1>
                        <h2 style="margin-bottom: 15px;">
                            Login
                        </h2>
                        <div class="user-box layui-input-wrap">
                            <el-input v-model="dl.user" size="large" placeholder="请输入账号" prefix-icon="User" clearable
                                autocomplete="off">
                            </el-input>
                        </div>
                        <br />
                        <div class="user-box layui-input-wrap">
                            <el-input v-model="dl.pass" size="large" placeholder="请输入密码" prefix-icon="Lock"
                                show-password clearable @keydown.enter.native="login" autocomplete="new-password">
                            </el-input>
                        </div>
                        <!--loginType-->
                        <div>
                            <div
                                style="font-size: 12px; color: rgb(170, 170, 170); text-align: right; display: flex; align-items: center; justify-content: flex-end;">
                                <span>没有账号？</span>
                                <el-button type="text" style="font-size:12px;color:inherit;text-decoration: underline;"
                                    @click="newlogin">点击注册</el-button>
                            </div>
                        </div>
                        <div style="float:right;display:inline-block;position:relative;margin-top: 35px;">
                            <el-button :disabled="!dl.user || !dl.pass" class="loginB" plain @click="login">
                                登入&nbsp;<el-icon>
                                    <Right />
                                </el-icon>
                            </el-button>
                            <span class="loginBS"
                                style="display: inline-block; background: #4F46E5; width: 98%; height: 20px; position: absolute; left: 50%; bottom: -3px; transform: translateX(-50%); z-index: -1; border-radius: 5px;"></span>
                        </div>
                    </div>

                    <div class="center"
                        style="display:inline-block;width:100%;color:#ccc;font-size:12px;margin-top: 20px;">
                        <div id="busuanzi_container_page_pv">
                            <span id="busuanzi_value_page_pv"></span>&nbsp;-&nbsp;
                            <span id="busuanzi_value_site_uv"></span><br />
                            <!--{{ips.ip}}&nbsp;-&nbsp;{{ips.area}}-->
                            <?= real_ip() ?>
                        </div>
                    </div>

                </div>

            </div>

            <div v-else class="login-box">
                <h1 style="margin: 0 0 27px">
                    <center><?= $conf['sitename'] ?></center>
                </h1>
                <div class="user-box">
                    <el-input v-model="reg.name" placeholder="请输入昵称" clearable>
                    </el-input>
                </div>
                <br />
                <div class="user-box">
                    <el-input v-model="reg.user" placeholder="请输入QQ" clearable>
                    </el-input>
                </div>
                <br />
                <div class="user-box">
                    <el-input v-model="reg.pass" placeholder="请输入密码" clearable>
                    </el-input>
                </div>
                <br />
                <div class="user-box">
                    <el-input v-model="reg.yqm" placeholder="请输入邀请码" clearable>
                    </el-input>
                </div>
                <div style="display:flex;justify-content: space-between;margin-top:20px;">
                    <div style="font-size:12px;color:#aaa;text-align:right">
                        有账号了？<el-button type="text" style="font-size:12px;color:inherit;text-decoration: underline;"
                            @click="newlogin">点击登录</el-button>
                    </div>
                    <a id="button" @click="register" style="margin:0">
                        <span>YouJ</span>
                        <span>YouJ</span>
                        <span>YouJ</span>
                        <span>YouJ</span>
                        注册
                    </a>
                </div>
            </div>

        </div>
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
    }
</script>

<script>
    const app = Vue.createApp({
        data() {
            return {
                loginType: true,
                title: "你在看什么呢？我写的代码好看吗",
                dl: {},
                reg: {},
                ips: {
                    ip: '',
                    area: ''
                }
            }
        },
        mounted() {
            const _this = this;
            let pageLoading = layer.load(0);
            $("#login1").ready(() => {
                $("#login1").show();
                layer.close(pageLoading);
                // _this.getip();
            })
        },
        methods: {
            // getip: function () {
            //     const _this = this;
            //     axios.get('https://tenapi.cn/v2/getip').then((e) => {
            //         if (e.data.code == 200) {
            //             let data = e.data.data;
            //             _this.ips.ip = data.ip;
            //             _this.ips.area = data.area;
            //         }

            //     })
            // },
            newlogin: function () {
                const _this = this;
                _this.loginType = !_this.loginType
            },
            login: function () {
                const _this = this;

                if (!_this.dl.user || !_this.dl.pass) {
                    _this.$message('账号密码不能为空');
                    return
                }

                const loading = _this.$loading({
                    lock: true,
                    text: '登陆中，请稍等',
                    background: 'rgba(0, 0, 0, 0.7)'
                });

                axios.post("/apiadmin.php?act=login", {
                    user: _this.dl.user,
                    pass: _this.dl.pass
                }, {
                    emulateJSON: true
                }).then(function (r) {
                    loading.close();
                    if (r.data.code == 1) {
                        _this.$message.success(r.data.msg + '，跳转中...');
                        setTimeout(function () {
                            window.location.href = "/index"
                        }, 600);
                    } else if (r.data.code == 5) {
                        _this.login2();
                    } else {
                        _this.$message.error(r.data.msg);
                    }
                });
            },
            register: function () {
                const _this = this;
                if (!_this.reg.user || !_this.reg.pass || !_this.reg.name || !_this.reg.yqm) {
                    _this.$message('所有项不能为空', {
                        icon: 2
                    });
                    return
                }
                var loading = layer.load();
                axios.post("/apiadmin.php?act=register", {
                    name: _this.reg.name,
                    user: _this.reg.user,
                    pass: _this.reg.pass,
                    yqm: _this.reg.yqm
                }, {
                    emulateJSON: true
                }).then(function (r) {
                    layer.close(loading);
                    if (r.data.code == 1) {
                        _this.loginType = true;
                        _this.dl.user = _this.reg.user;
                        _this.dl.pass = _this.reg.pass;
                        _this.$message(r.data.msg);
                    } else {
                        _this.$message.error(r.data.msg);
                    }
                });
            },
            login2: function () {
                const _this = this;
                layer.prompt({
                    title: '管理员二次验证',
                    formType: 1,
                    btn: ['验证', '取消']
                }, function (value, index, elem) {
                    if (value === '') {
                        _this.$message = '请输入验证信息！';
                        return elem.focus();
                    };

                    const loadIndex = _this.$loading({
                        lock: true,
                        text: '登陆中，请稍等',
                        background: 'rgba(0, 0, 0, 0.7)'
                    });

                    axios.post("/apiadmin.php?act=login", {
                        user: _this.dl.user,
                        pass: _this.dl.pass,
                        pass2: value
                    }, {
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }).then((data) => {
                        if (data.data.code == 1) {
                            _this.$message('登陆成功，跳转中...')
                            setTimeout(function () {
                                window.location.href = "/index"
                            }, 1000);
                        } else {
                            _this.$message.error(data.data.msg);
                        };
                        layer.close(loadIndex);
                    })

                    // 关闭 prompt
                    layer.close(index);
                });

            }
        },
    });
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#app');
    // -----------------------------

</script>

<script src="/assets/webVfx/inputParticles.js"></script>
<script src="/assets/webVfx/shehuizhuyi.js"></script>

</html>