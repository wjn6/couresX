<?php
include_once('head.php');
$uid = $userrow['uid'];

// if ($userrow['uuid'] != 1 && $userrow['uid'] != 1) {
//     exit("
// 	<script language='javascript'>

// 	    layer.confirm('请联系上级充值！', {
// 	        title:'非站长直属代理',
// 	        closeBtn: 0,
//                 shade: .5, // 不显示遮罩
//                 btn:[]
//           },function(){
//             layer.msg('第二个回调', {
//               time: 20000, // 20s 后自动关闭
//               btn: ['明白了', '知道了']
//             });
//           });
// 	</script>");
// }
?>
<style>
    .layui-timeline-item {
        padding: 0;
    }

    .layui-card {
        margin-bottom: 5px;
    }

    i {
        font-size: inherit !important;
    }
</style>

<div class="layui-padding-1" id="charge" style="display:none;">
    
    <div class="layui-card" class="" style="">
        <li class="list-group-item layui-card-body">
            <div class="edit-avatar">
                <!--<img src="http://q2.qlogo.cn/headimg_dl?dst_uin=<?= $userrow['user']; ?>&spec=100" alt="..." class="img-avatar">-->
                <!--<div class="avatar-divider"></div>-->
                <div class="edit-avatar-content">
                    <!--<div class="h5 m-t-xs"><?= $conf['sitename']; ?> 欢迎您！</div>-->
                    <!--<span style="color:red;">账号: <?= $userrow['user']; ?></span><br>-->
                    <span style="color:green">
                        <i class="layui-icon layui-icon-rmb"></i> 余额：{{ user_money?user_money:"获取中..." }}
                    </span>
                    <button style="margin-bottom:5px;margin-left: 3px;" type="button" class="layui-btn layui-btn-xs layui-btn-primary  layui-border-normal" @click="get_money();get_paylist();">
                        <i class="layui-icon layui-icon-refresh"></i>
                    </button>
                    <br />
                    最好是PC网页端操作，手机端容易被浏览器杀后台！
                </div>
            </div>

        </li>
    </div>

    <div class="layui-card list-group-item" style="padding:0;">
        <div class="layui-card-header">
            充值
            <?php if ($conf['epay_zs_open']) { ?>
                &nbsp;<button class="layui-btn layui-btn-xs layui-btn-primary layui-border-blue" @click="epay_zs_open" style="padding: 2px 5px; height: auto;">
                    <span style="background: red; color: #fff; padding: 2px 5px;">赠</span> 充值赠送规则
                </button>
            <?php } ?>
        </div>
        <div class="form-group layui-card-body" style="overflow:hidden">
            <div class="">
                <div class="form-group" style="overflow:hidden;margin:0 0 10px">

                    <div class="layui-input-group">
                        <div class="layui-input-split layui-input-prefix">
                            金额
                        </div>
                        
                        <template v-if="czAuth">
                            <input style="width: 150px; float: left;" type="text" class="layui-input"
                                placeholder="最低充值<?= $conf["zdpay"] ?>元" v-model="money" />
                            <div class="layui-input-split layui-input-suffix" style="cursor: pointer;" @click="pay">
                                <i class="layui-icon layui-icon-rmb"></i> 立即充值
                            </div>
                        </template>
                        <template v-else>
                            <input style="width: 150px; float: left;" type="text" class="layui-input"
                                placeholder="请联系上级充值" disabled />
                            <div class="layui-input-split layui-input-suffix layui-disabled" style="cursor: pointer;">
                                <i class="layui-icon layui-icon-rmb"></i> 立即充值
                            </div>
                        </template>

                    </div>
                    <hr />
                    <div>
                        <fieldset class="layui-elem-field">
                            <legend class="layui-font-14">卡密充值&nbsp;<button v-if="uid" class="layui-btn layui-btn-xs layui-btn-primary layui-border-blue" @click="kami_open">卡密管理</button></legend>
                            <div class="layui-field-box">
                                <div class="layui-input-group">
                                    <div class="layui-input-split layui-input-prefix">
                                        卡密
                                    </div>
                                    <input style="width: 150px; float: left;" type="text" class="layui-input" placeholder="请输入卡密" v-model="kamiCode" />
                                    <div class="layui-input-split layui-input-suffix" style="cursor: pointer;" @click="kami()">
                                        <i class="layui-icon layui-icon-face-smile-fine"></i> 兑换
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                </div>
                <!--<span style="color:red;">充值100以上送3% 充值200以上送5% 充值300以上送7% 充值500以上送10% <br>在线充值金额达标赠送自动赠送到账，赠送金额不计入总充值</span><br><br>-->

                <div class="layui-timeline">
                    <div class="layui-timeline-item">
                        <i class="layui-icon layui-timeline-axis layui-icon-face-smile"></i>
                        <div class="layui-timeline-content layui-text">
                            <div class="layui-timeline-title">非站长直属下级请联系上家进行在线充值</div>
                        </div>
                    </div>
                    <div class="layui-timeline-item">
                        <i class="layui-icon layui-timeline-axis layui-icon-face-smile"></i>
                        <div class="layui-timeline-content layui-text">
                            <div class="layui-timeline-title">请用电脑网页端进行充值操作，手机端网页容易被系统杀后台，如未即时到账请联系上级</div>
                        </div>
                    </div>
                    <div class="layui-timeline-item">
                        <i class="layui-icon layui-timeline-axis layui-icon-face-smile"></i>
                        <div class="layui-timeline-content layui-text">
                            <div class="layui-timeline-title">若在支付阶段误关闭网页，请等待自动回调！</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!--充值弹窗-->

    <!--  <div class="col-sm-12" id="pay2" style="display: none;">-->
    <!--  	<form action="/epay/epay.php" method="post">-->
    <!--  		<div><center style="margin-top:15px;"><h3>￥{{money}}</h3></center></div>-->

    <!--  		<input type="hidden" name="out_trade_no" v-model="out_trade_no"/><br>-->
    <!--  		 <?php if ($conf['is_alipay'] == 1) { ?>-->
    <!--	<button type="radio" name="type" value="alipay" class="btn btn-primary btn-block" >支付宝</button><br> <?php } ?>-->
    <!--	 <?php if ($conf['is_qqpay'] == 1) { ?>-->
    <!--	<button type="radio" name="type" value="qqpay" class="btn btn-danger btn-block">QQ</button><br> <?php } ?>-->
    <!--	 <?php if ($conf['is_wxpay'] == 1) { ?>-->
    <!--	<button type="radio" name="type" value="wxpay" class="btn btn-info btn-block">微信</button><br> <?php } ?>-->
    <!--</form>	-->
    <!--  </div>  -->

    <div class="layui-card list-group-item" style="padding:0;">
        <div class="layui-card-header">充值记录 <span class="layui-font-12 layui-font-green">仅展示最近100条</span>
            &nbsp;
            <!--<button class="layui-btn layui-btn-primary layui-border-red layui-btn-sm" @click="open_noPayID">点我检测已支付但未充值上的订单</button>-->
        </div>
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: auto;">
                <table id="payTable" class="layui-table" lay-size="sm" style="width: max-content;min-width:100%;margin: 0;">
                    <thead>
                        <tr>
                            <th class="center" style="width:40px;">ID</th>
                            <th style="width:70px;">支付商来自</th>
                            <th class="center">UID</th>
                            <th class="center" style="width:50px;">支付状态</th>
                            <th>订单号</th>
                            <th v-if="uid">订单号</th>
                            <th>PAY</th>
                            <th style="width:100px;">名称</th>
                            <th>余额消费</th>
                            <th>商城收入</th>
                            <th>创建时间</th>
                            <th>支付时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--user_paylist-->
                        <tr v-for="(item,index) in user_paylist" :key="index">
                            <td class="center">
                                {{item.oid}}
                            </td>
                            <td>
                                {{item.payUser === '1' || !item.payUser ? '管理员':'代理'+item.payUser}}
                            </td>
                            <td class="center">
                                {{item.uid}}
                            </td>
                            <td class="center">
                                <sapn v-if="item.status==='已支付'" class="layui-font-blue">
                                    {{item.status}}
                                </sapn>
                                <span v-else-if="item.status==='未支付'" class="layui-font-red">
                                    {{item.status}}
                                </span>
                                <span v-else>
                                    {{item.status}}
                                </span>
                            </td>
                            <td>
                                {{item.out_trade_no}}
                            </td>
                            <td v-if="uid">
                                {{item.trade_no}}
                            </td>
                            <td>
                                <span v-if="item.type == 'alipay' ">
                                    支付宝
                                </span>
                                <span v-else-if="item.type == 'wxpay' ">
                                    微信
                                </span>
                                <span v-else-if="item.type == 'qqpay' ">
                                    QQ
                                </span>
                                <span v-else-if="!item.type ">
                                    未知
                                </span>
                                <span v-else>
                                    {{item.type}}
                                </span>
                            </td>
                            <td>
                                {{item.name}}
                            </td>
                            <td>
                                {{item.money}}
                            </td>
                            <td>
                                {{item.money2}}
                            </td>
                            <td>
                                {{item.addtime}}
                            </td>
                            <td>
                                {{item.endtime}}
                            </td>
                        </tr>


                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="center" id="pay2" style="display: none;padding:5px 5px 10px;">
        <!--<form action="/epay/epay.php" method="post">-->
        <div>
            <center style="margin:15px;">
                <h1 style="font-size :44px;">￥{{money}}</h1>
            </center>
        </div>
        <div style="display: flex; justify-content: center;">
            <?php if ($conf['is_alipay'] == 1) { ?>
                <!--<button type="radio" name="type" value="alipay" class="layui-btn" style="line-height: inherit;">支付宝</button><br>-->
                <button @click="payGo('alipay')" type="radio" name="type" value="alipay" class="layui-btn layui-bg-blue" style="width: 80px;">支付宝</button><br> <?php } ?>
            <?php if ($conf['is_qqpay'] == 1) { ?>
                <button @click="payGo('qqpay')" type="radio" name="type" value="qqpay" class="layui-btn layui-bg-orange" style="width: 80px;">QQ</button><br> <?php } ?>
            <?php if ($conf['is_wxpay'] == 1) { ?>
                <button @click="payGo('wxpay')" type="radio" name="type" value="wxpay" class="layui-btn" style="width: 80px;">微信</button><br> <?php } ?>
        </div>

    </div>

</div>

<script type="text/javascript" src="assets/LightYear/js/main.min.js"></script>
<script src="assets/js/aes.js"></script>

<script>
    $("pay2").hide();

    const app = Vue.createApp({
        data() {
            return {
                czAuth: (  '<?= $userrow['uid'] == 1 ?>' || '<?= $userrow['uuid'] == 1 ?>' || '<?= $userrow['czAuth'] == 1 ?>' || '<?php $czAuth = $DB->get_row("select * from qingka_wangke_dengji where rate={$userrow['addprice']}")["czAuth"];echo $czAuth; ?>' ) === '1'?true:false,
                uid: '<?= $userrow['uid'] ?>' === '1' ? true : false,
                user_money: '',
                user_paylist: [],
                money: '',
                out_trade_no: '',
                kamiCode: '',
                epay_zs: [],
            }
        },
        mounted() {
            const _this = this;

            $("#charge").ready(() => {
                $("#charge").show();
                <?php if ($conf['epay_zs_open']) { ?>
                    _this.epay_zs = <?= json_encode($conf["epay_zs"]) ?>;
                    _this.epay_zs_open();
                <?php } ?>
                _this.get_money();
                _this.get_paylist();
            })

            window.touristPageVue = _this;

            layui.use(function() {
                var util = layui.util;
                // 自定义固定条
                util.fixbar({
                    margin: 100
                })
            })

        },
        methods: {
            epay_zs_open: function() {
                const _this = this;
                layer.open({
                    type: 1,
                    area: ["320px"],
                    title: '<span style="background: red; color: #fff; padding: 2px 5px;">赠</span> 充值赠送规则',
                    content: `<div class="layui-padding-2"><p>注意：是单次一次性充值金额</p><hr >${_this.epay_zs_t(JSON.parse(_this.epay_zs))}<hr ><p>更高额度请联系管理员获取更高赠送！！！</p></div>`
                })
            },
            epay_zs_t: function(arr) {
                let description = "";
                arr.forEach((item, index) => {
                    const min = parseFloat(item.min);
                    const max = item.max === '' ? Infinity : parseFloat(item.max);
                    const zsprice = parseFloat(item.zsprice);

                    if (index > 0) {
                        description += "；<br />";
                    }

                    if (max === Infinity) {
                        description += `当充值金额<span class="layui-font-blue"> ≥ ${min}</span> 时，赠送 <span class="layui-font-blue">${zsprice}%</span>`;
                    } else {
                        description += `当充值金额<span class="layui-font-blue"> ≥ ${min}</span> 且<span class="layui-font-blue"> ≤ ${max}</span> 时，赠送 <span class="layui-font-blue">${zsprice}%</span>`;
                    }
                });

                return description;
            },
            returnMethod: function(type, msg) {
                const _this = this;
                if (type) {
                    layer.closeAll();
                }
                layer.msg(msg);
            },
            get_money: function() {
                const _this = this;
                let loadIndex = layer.load(0);
                axios.post("/apiadmin.php?act=usermoney", {}, {
                    emulateJSON: true
                }).then(r => {
                    layer.close(loadIndex);
                    if (r.data.code === 1) {
                        _this.user_money = r.data.money;
                    } else {
                        layer.msg("获取余额失败！");
                    }
                })
            },
            get_paylist: function() {
                const _this = this;
                let loadIndex = layer.load(0);
                axios.post("/apiadmin.php?act=paylist", {
                    page: 1,
                    limit: 100
                }, {
                    emulateJSON: true
                }).then(r => {
                    layer.close(loadIndex);
                    if (r.data.code === 1) {
                        _this.user_paylist = r.data.data;
                    } else {
                        layer.msg("获取余额失败！");
                    }
                })
            },
            kami_open: function() {
                layer.open({
                    type: 2,
                    title: '卡密管理',
                    shadeClose: true,
                    maxmin: true, //开启最大化最小化按钮
                    area: ['100%', '100%'],
                    content: 'kamisz.php',
                    scrollbar: false,
                });
            },
            open_noPayID: function() {
                layer.open({
                    type: 1,
                    title: '已支付但未充值上的订单',
                    shade: .5, // 不显示遮罩
                    content: $('#noPayID'), // 捕获的元素
                    end: function() {
                        // layer.msg('关闭后的回调', {icon:6});
                    }
                });
            },
            payGo: function(type) {
                const _this = this;

                layui.use(function() {
                    layer.open({
                        type: 2,
                        title: '<i class="layui-icon layui-icon-tips"></i>&nbsp;&nbsp;支付后请耐心等待回调~',
                        shadeClose: true,
                        area: ['98%', '98%'],
                        content: '/epay/epay.php?type=' + type + '&out_trade_no=' + _this.out_trade_no,
                        end: function() {
                            // location.reload();
                            layer.closeAll();
                        }
                    });
                })
            },
            pay: function(page) {
                if (!this.money) {
                    layer.msg('请输入金额！');
                    return
                }
                var load = layer.load(0);
                const _this = this;
                _this.money = _this.money.toString().replace(/^0+/, '').replace(/^\.(\d)/, '0.$1');

                axios.post("/apiadmin.php?act=pay", {
                    money: _this.money
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        _this.get_paylist();
                        $("pay2").show();
                        _this.out_trade_no = r.data.out_trade_no;
                        layer.msg(r.data.msg, {
                            icon: 1
                        });
                        layer.open({
                            type: 1,
                            title: '<i class="layui-icon layui-icon-vercode"></i>&nbsp;&nbsp;请选择支付方式',
                            // closeBtn: 0,
                            area: ['300px', 'auto'],
                            skin: 'layui-bg-gray', //没有背景色
                            shadeClose: true,
                            content: $('#pay2'),
                            end: function() {
                                $("#pay2").hide();
                                _this.get_money();
                                _this.get_paylist();
                            }
                        });
                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            kami: function(type) {
                const _this = this;
                if (!_this.kamiCode) {
                    layer.msg('请输入卡密！');
                    return
                }
                var loadIndex = layer.msg(type ? '充值中，请稍等...' : '验证卡密中，请稍等...', {
                    icon: 16,
                    shade: 0.01,
                    time: 0
                });;
                let data = {
                    kamiCode: _this.kamiCode,
                };
                if (type) {
                    data.type = 1
                }
                axios.post("/api/kami.php?act=kami_v", data, {
                    emulateJSON: true
                }).then((r) => {
                    layer.close(loadIndex);
                    if (r.data && r.data.code === 1) {
                        layer.msg('充值成功！');
                        _this.get_money();
                        _this.get_paylist();
                    } else if (r.data.code === 2) {
                        layer.confirm(`价值：${r.data.price}元\r\n<br>是否使用该卡密？`, {
                            title: "恭喜！卡密可用"
                        }, function() {
                            _this.kami(1);
                        }, function() {
                            layer.msg('已取消使用卡密');
                        });
                    } else {
                        layer.msg(r.data.msg ? r.data.msg : "网络错误");
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
    var vm = app.mount('#charge');
    // -----------------------------
</script>