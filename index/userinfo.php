<?php
include_once('head.php');
?>

<script src="assets/js/aes.js"></script>
<script src="/assets/toc/ua-parser.js?v=1.0.38"></script>

<div id="userindex" class="layui-padding-1" style="display:none;">

    <!--<button type="button" class="layui-btn" id="ID-upload-demo-btn">-->
    <!--    <i class="layui-icon layui-icon-upload"></i> 单图片上传-->
    <!--</button>-->
    <!--<div style="width: 132px;">-->
    <!--    <div class="layui-upload-list">-->
    <!--        <img class="layui-upload-img" id="ID-upload-demo-img" style="width: 100%; height: 92px;">-->
    <!--        <div id="ID-upload-demo-text"></div>-->
    <!--    </div>-->
    <!--    <div class="layui-progress layui-progress-big" lay-showPercent="yes" lay-filter="filter-demo">-->
    <!--        <div class="layui-progress-bar" lay-percent=""></div>-->
    <!--    </div>-->
    <!--</div>-->

    <el-row :gutter="5">
        <el-col :sm="24" :md="12" style="margin:0 0 5px;">
            <div class="layui-panel layui-padding-4" style="padding: 10px 15px 10px 0 !important;">
                <center style="display: flex; justify-content: left; align-items: center; padding: 10px 0 5px 40px;">
                    <img style="border-radius:50%;width:auto;height:50px;"
                        src="https://q2.qlogo.cn/headimg_dl?dst_uin=<?= $userrow['user']; ?>&spec=100"
                        alt="<?= $userrow['name']; ?>">
                    <h1 style="margin: 0px 0px 0px 20px; position: relative; display: flex; flex-direction: column;">
                        <div style="text-align: left;">
                            <?= $userrow['user']; ?> 
                        </div>
                        <div style="display: flex; gap: 2px; justify-content: left; padding: 0; margin: 0; position: relative;">
                            <el-tag type="" effect="dark" size="mini" style=" opacity: 0.8;">
                                UID：<?= $userrow['uid']; ?>
                            </el-tag>
                            <el-tag type="" effect="info" size="mini" style=" opacity: 0.8;">
                                费率：<?= $userrow['addprice']; ?>
                            </el-tag>
                        </div>
                    </h1>

                    <div class="layui-input-group" style="width: 100%; margin: 15px 0px; position: absolute; right: 10px; top: 0px;">
                        <button class="layui-btn layui-bg-blue layui-btn-sm" @click="upuser" style="float: right;">更新个人信息</button>
                    </div>
                </center>
                <div style="margin:16px 0 0;" class="layui-form">
                    <div class="layui-input-group" style="width: 100%;margin:15px 0;">
                        <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                            余额
                        </div>
                        <input disabled type="text" value="<?= $userrow['money']; ?>" class="layui-input">
                    </div>
                    <div class="layui-input-group" style="width: 100%;margin:15px 0;">
                        <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                            昵称
                        </div>
                        <input type="text" v-model="row2.name" :value="row2.name" class="layui-input" lay-affix="clear">
                    </div>
                    <div class="layui-input-group" style="width: 100%;margin:15px 0;">
                        <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                            密码
                        </div>
                        <input type="text" v-model="row2.pass" :value="row2.pass" class="layui-input" lay-affix="clear">
                    </div>
                    <div class="layui-input-group" style="width: 100%;margin:15px 0;">
                        <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                            QQ
                        </div>
                        <input type="text" v-model="row2.qq" :value="row2.qq" placeholder="QQ号" class="layui-input" lay-affix="clear">
                    </div>
                    <div class="layui-input-group" style="width: 100%;margin:15px 0;">
                        <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                            微信
                        </div>
                        <input type="text" v-model="row2.wx" :value="row2.wx" placeholder="微信号" class="layui-input" lay-affix="clear">
                    </div>
                    <div class="layui-input-group" style="width: 100%;margin:15px 0;">
                        <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                            Key
                        </div>
                        <template v-if="!!row2.key">
                            <input disabled type="text" v-model="row2.key" class="layui-input">
                            <div class="layui-input-split layui-input-suffix" style="cursor: pointer;width:60px;"
                                @click="ghapi">
                                更换
                            </div>
                        </template>
                        <template v-else>
                            <input disabled type="text" value="暂未开通Key" class="layui-input">
                            <div class="layui-input-split layui-input-suffix" style="cursor: pointer;width:60px;"
                                @click="ktapi">
                                开通
                            </div>
                        </template>
                    </div>
                    <div class="layui-input-group" style="width: 100%;margin:15px 0;">
                        <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                            邀请码
                        </div>
                        <input disabled type="text" v-model="row2.yqm" :value="row2.yqm" class="layui-input">
                        <div class="layui-input-split layui-input-suffix" placeholder="请生成邀请码"
                            style="cursor: pointer;width:60px;" @click="szyqm">
                            {{!row2.yqm?"生成":"更换"}}
                        </div>
                    </div>
                    <div class="layui-input-group" style="width: 100%;margin:15px 0;">
                        <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                            邀请费率
                        </div>
                        <input type="text" v-model="row2.yqprice" :value="row2.yqprice" class="layui-input">
                    </div>
                    <div class="layui-input-group" style="width: 100%;margin:15px 0;">
                        <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                            登录信息
                        </div>
                        <input disabled type="text" value="<?= $userrow['ip']; ?> <?= get_ip_city(real_ip()) ?>" class="layui-input">
                    </div>
                    <div class="layui-input-group" style="width: 100%;margin:15px 0;">
                        <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                            上次在线
                        </div>
                        <input disabled type="text" value="<?= $userrow['endtime']; ?>" class="layui-input">
                    </div>

                </div>
            </div>
        </el-col>
        <el-col :sm="24" :md="12" style="height:670px;overflow-y: auto;">
            <div class="layui-panel" style="margin-bottom:5px;">
                <div class="layui-card-header">
                    上级信息
                </div>
                <div class="layui-card-body">

                    <div>
                        <table class="layui-table" style="width:100%;">
                            <tr>
                                <td style="width:60px;">
                                    上级UID
                                </td>
                                <td>
                                    <?php echo $sj['uid'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:60px;">
                                    上级账号
                                </td>
                                <td>
                                    <?php echo $sj['user'] ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:60px;">
                                    上级QQ
                                </td>
                                <td>
                                    <?php echo $sj['qq'] ?: '<span class="layui-font-green">未设置</span>' ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:60px;">
                                    上级微信
                                </td>
                                <td>
                                    <?php echo $sj['wx'] ?: '<span class="layui-font-green">未设置</span>' ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:60px;">
                                    上级公告
                                </td>
                                <td>
                                    <?php echo $sj['notice'] ?: '<span class="layui-font-green">未设置</span>' ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <table v-if="false" class="layui-table" style="display:none;width: max-content;min-width: 100%;
                        margin:0; max-width: 100%;">

                        <thead>
                            <tr>
                                <th style="width:30px;">ID</th>
                                <th>平台</th>
                                <th>价格</th>
                                <th>排序</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $a = $DB->query("select * from qingka_wangke_class where status=1 order by sort ");
                            while ($rs = $DB->fetch($a)) {
                                echo "<tr><td>" . $rs['cid'] . "</td>
	                     	 	  	<td>" . $rs['name'] . "</td>
	                     	 	  	<td>{{(" . json_encode($rs['price']) . " * Number(" . $userrow['addprice'] . ")).toFixed(2)}}</td>
	                     	 	  	
	                     	 	  	</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="layui-panel">
                <div class="layui-card-header">
                    我的公告&nbsp;&nbsp;&nbsp;&nbsp;<span class="layui-font-green layui-font-12">你旗下的代理可见！</span>
                </div>
                <div class="layui-card-body">
                    <form action="" class="layui-form" onsubmit="return false">
                        <textarea type="text" name="notice" v-model="row2.notice" class="layui-textarea" rows="5"
                            lay-affix="clear"  placeholder="支持HTML" maxlength="200"
                            οnchange="this.value=this.value.substring(0, 200)"
                            οnkeydοwn="this.value=this.value.substring(0, 200)"
                            οnkeyup="this.value=this.value.substring(0, 200)">
                        </textarea>
                        <div class="layui-input-group"
                            style="width: 100%; margin: 15px 0px; display: flex; align-items: center; justify-content: space-between;;">
                            <div style="display: flex;" class="layui-font-12 layui-input-group">
                                <span>字数：{{row2.notice?row2.notice.length:0}}</span>&nbsp;&nbsp;<span
                                    class="layui-font-red">最多200字</span>
                            </div>
                            <button class="layui-btn" @click="upuser" style="">保存</button>
                        </div>
                    </form>

                </div>
            </div>
        </el-col>
    </el-row>

</div>
<script type="text/javascript" src="assets/LightYear/js/main.min.js"></script>
</body>

</html>

<?php include_once($root.'/index/components/footer.php'); ?>

<script>
    const app = Vue.createApp({
        data(){
            return{
                row: {
                    
                },
                row2: {
                    name: '',
                    pass: '',
                },
                inte: '',
            }
        },
        mounted() {
            const _this =this;
            layui.use(function () {
                var util = layui.util;
                // 自定义固定条
                util.fixbar({
                    margin: 100
                })

            })
            
            $("#userindex").ready(()=>{
                $("#userindex").show();
                _this.userinfo();
                setTimeout(()=>{
                    layui.form.render();
                },500)
            })

            $("#userindex").ready(() => {
                layui.use(() => {
                    var upload = layui.upload;
                    var layer = layui.layer;
                    var element = layui.element;
                    var $ = layui.$;
                    var uploadInst = upload.render({
                        elem: '#ID-upload-demo-btn',
                        url: '/api/api.php?act=uploads&folder=avatar', // 实际使用时改成您自己的上传接口即可。
                        before: function (obj) {
                            // 预读本地文件示例，不支持ie8
                            obj.preview(function (index, file, result) {
                                $('#ID-upload-demo-img').attr('src', result); // 图片链接（base64）
                            });

                            element.progress('filter-demo', '0%'); // 进度条复位
                            layer.msg('上传中', { icon: 16, time: 0 });
                        },
                        done: function (res) {
                            // 若上传失败
                            if (res.code !==1 ) {
                                return layer.msg(res.msg);
                            }
                            // 上传成功的一些操作
                            // …
                            $('#ID-upload-demo-text').html(''); // 置空上传失败的状态
                        },
                        error: function () {
                            // 演示失败状态，并实现重传
                            var demoText = $('#ID-upload-demo-text');
                            demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
                            demoText.find('.demo-reload').on('click', function () {
                                uploadInst.upload();
                            });
                        },
                        // 进度条
                        progress: function (n, elem, e) {
                            element.progress('filter-demo', n + '%'); // 可配合 layui 进度条元素使用
                            if (n == 100) {
                                // layer.msg('上传完毕', { icon: 1 });
                            }
                        }
                    });


                })

            })

        },
        methods: {
            uap:function(){
                return new UAParser()
            },
            upuser: function () {
                layui.use(function () {
                    var layer = layui.layer;
                    var load = layer.msg('保存中，请稍等...', {
                        icon: 16,
                        shade: 0.01
                    });
                    axios.post("/apiadmin.php?act=upuser2", {
                        data: vm.row2
                    }, {
                        emulateJSON: true
                    })
                        .then(function (r) {
                            layer.close(load)
                            if (r.data.code == 1) {
                                vm.userinfo()
                                layer.msg('保存成功')
                                if (vm.row.pass != vm.row2.pass) {
                                    top.location.reload()
                                } else {
                                    vm.userinfo()
                                }
                            } else {
                                layer.msg(r.data.msg?r.data.msg:'保存失败');
                                vm.userinfo()
                            }
                        });
                })
            },
            userinfo: function () {
                var load = layer.load(0);
                const _this =this;
                axios.post("/apiadmin.php?act=userinfo")
                    .then(function (r) {
                        layer.close(load);
                       
                        if (r.data.code === 1) {
                            _this.row = r.data;
                            _this.row2 = {
                                uid: "<?= $userrow['uid']; ?>",
                                name: _this.row.name,
                                key: _this.row.key,
                                yqm: _this.row.yqm,
                                pass: _this.row.pass,
                                yqprice: _this.row.yqprice,
                                notice: _this.row.my_notice,
                                qq: _this.row.qq,
                                wx: _this.row.wx,
                            }
                    // console.log(_this.row2, 12);
                        } else {
                            if (r.data.code === -10) {

                                layer.alert(r.data.msg, {
                                    icon: 2
                                }, function (index) {
                                    location.reload();
                                    layer.close(index)
                                });
                            } else {

                                layer.alert(r.data.msg);
                            }
                        }
                    });
            },
            yecz: function () {
                layer.alert('请联系您的上级QQ：' + this.row.sjuser + '，进行充值。（下级点充值，此处将显示您的QQ）', {
                    icon: 1,
                    title: "温馨提示"
                });
            },
            ktapi: function () {
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
            ghapi: function () {
                layer.confirm('确定更换key吗，更换之后原有的key将失效！', {
                    title: '温馨提示',
                    icon: 1,
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    var load = layer.load(2);
                    axios.get("/apiadmin.php?act=ktapi&type=3")
                        .then(function (data) {
                            layer.close(load);
                            if (data.data.code == 1) {
                                layer.alert(data.data.msg, {
                                    icon: 1,
                                    title: "温馨提示"
                                }, function (index) {
                                    vm.userinfo()
                                    layer.close(index)
                                });
                            } else {
                                layer.msg(data.data.msg, {
                                    icon: 2
                                });
                            }
                        });

                });
            },
            gbapi: function () {
                layer.confirm('确定关闭key吗，关闭之后无法使用对接功能！', {
                    title: '温馨提示',
                    icon: 1,
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    var load = layer.load(2);
                    axios.get("/apiadmin.php?act=ktapi&type=4")
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
            szyqm: function () {
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
                    vm.userinfo()
                    if (r.data.code === 1) {
                        layer.msg('生成成功')
                    } else {
                        layer.msg('生成失败')
                    }
                })
            },
            szyqprice: function () {
                layer.prompt({
                    title: '设置下级默认费率，首次自动生成邀请码',
                    formType: 3
                }, function (yqprice, index) {
                    layer.close(index);
                    var load = layer.load(2);
                    $.post("/apiadmin.php?act=yqprice", {
                        yqprice
                    }, function (r) {
                        layer.close(load);
                        if (r.code == 1) {
                            vm.userinfo();
                            layer.alert(r.msg, {
                                icon: 1
                            });
                        } else {
                            layer.msg(r.msg, {
                                icon: 2
                            });
                        }
                    });
                });
            },
            connect_qq: function () {
                var ii = layer.load(0, {
                    shade: [0.1, '#fff']
                });
                $.ajax({
                    type: "POST",
                    url: "../qq_login.php",
                    data: {
                        "type": 'qq'
                    },
                    dataType: 'json',
                    success: function (data) {
                        layer.close(ii);
                        if (data.code == 1) {
                            window.location.href = data.url;
                        } else {
                            layer.alert(data.msg, {
                                icon: 7
                            });
                        }
                    }
                });
            },
            szgg: function () {
                layer.prompt({
                    title: '设置代理公告，您的代理可看到',
                    formType: 2
                }, function (notice, index) {
                    layer.close(index);
                    var load = layer.load(2);
                    $.post("/apiadmin.php?act=user_notice", {
                        notice
                    }, function (data) {
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
            uploads() {

            },

        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#userindex');
    // -----------------------------
</script>