<?php
$title = '工单系统';
require_once('head.php');
?>
<style>
    .liclass {
        font-size: 14px;
        text-indent: 2em;
        margin: 5px;
    }

    .null {
        font-size: 18px;
        text-align: center;
    }

    .infinite {
        animation-duration: 1.5s;
        animation-iteration-count: infinite;
    }
</style>

<div class="layui-padding-1" id="gdlist" style="display: none;">

    <!--提交工单版块-->
    <div class="" id="tjgdID" style="display:none;padding:10px;">
        <div class="">
            <span style="color:dodgerblue;text-indent:2em">
                1，反馈问题时务必按格式反馈！否则将不会处理！</span><br>
            <span style="color:red;text-indent:2em">
                2，订单问题请填写清楚有问题的订单ID，账号密码课程，
                以及有什么问题，尽量简洁清楚！</span><br><br>

            <el-form ref="form" :model="form" label-width="80px" :rules="rules">
                <el-form-item label="工单分类">
                    <el-select v-model="form.region" placeholder="请选择.." :teleported="false" :popper-append-to-body="false" style="width:180px;">
                        <el-option label="申请收录" value="申请收录"></el-option>
                        <el-option label="订单问题" value="订单问题"></el-option>
                        <el-option label="充值问题" value="充值问题"></el-option>
                        <el-option label="代理问题" value="代理问题"></el-option>
                        <el-option label="提出意见" value="提出意见"></el-option>
                        <el-option label="bug反馈" value="bug反馈"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="工单标题" prop="title">
                    <el-input v-model="form.title"></el-input>
                </el-form-item>

                <el-form-item v-if="form.region == '订单问题'" label="订单绑定" prop="title">
                    <el-select v-model="form.oid" placeholder="请选择订单.." :teleported="false" :popper-append-to-body="false" style="width:180px;">
                        <?php
                        $a = $DB->query("select oid,user,kcname from qingka_wangke_order where uid='{$userrow['uid']}' order by oid desc ");
                        while ($row = $DB->fetch($a)) {
                            echo '<el-option label="[' . $row['oid'] . '] '. $row['user'] .' '. $row['kcname'] .'" value="' . $row['oid'] . '"></el-option>';
                        }
                        ?>
                    </el-select>
                </el-form-item>

                <el-form-item label="工单内容">
                    <el-input type="textarea" :autosize="{ minRows: 6, maxRows: 10}" v-model="form.content" placeholder="请按照格式填写！"></el-input>
                </el-form-item>
                <el-form-item>
                    <!--<el-button type="primary" @click="submit">提交工单</el-button>-->
                    <!--<el-button @click="show = !show">取消</el-button>-->
                </el-form-item>
            </el-form>
        </div>
    </div>
    <!--提交工单版块-->

    <div class="col-sm-12">
        <div>
            <div class="clearfix layui-panel layui-padding-1">
                <div class="panel-heading font-bold " style="border-top-left-radius: 8px; border-top-right-radius: 8px;">工单提交
                    <button type="button" class="layui-btn layui-bg-blue layui-btn-sm" style="margin-left:10px;" @click="tjgdID_open">提交工单</button>
                </div>

            </div>

            <div class="text item layui-panel">

                <!--工单列表模块-->
                <div class="table-responsive" lay-size="sm" v-if="show==false">
                    <el-table ref="multipleTable" :data="order" size="small" empty-text="啊哦！一条工单都没有哦！" highlight-current-row border>

                        <el-table-column label="主控" width="100" align="center">
                            <template #default="scope">

                                <el-dropdown split-button type="primary" size="small" @click.stop="commandvalue({gid:scope.row.gid,type:'hf'},scope.row)">
                                    {{/已完结|忽略/i.test(scope.row.state)?'查看':'回复'}}
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item :disabled="/已完结|忽略/i.test(scope.row.state)" @click="commandvalue({gid:scope.row.gid,type:'wj'})">
                                                <p style="margin: 0;">
                                                    {{/已完结/i.test(scope.row.state)?'已':''}}完结
                                                </p>
                                            </el-dropdown-item>
                                            <?php if($userrow["uid"] == 1){ ?>
                                                <el-dropdown-item :disabled="/已完结|忽略/i.test(scope.row.state)" @click="commandvalue({gid:scope.row.gid,type:'bcl'})">
                                                    <p style="margin: 0;">
                                                        {{/忽略/i.test(scope.row.state)?'已忽略':'忽略'}}
                                                    </p>
                                                </el-dropdown-item>
                                                <el-dropdown-item @click="shan(scope.row.gid)">
                                                    <p style="margin: 0;">
                                                        删除
                                                    </p>
                                                </el-dropdown-item>
                                            <?php } ?>
                                        </el-dropdown-menu>
                                    </template>
                                </el-dropdown>

                            </template>
                        </el-table-column>
                        <el-table-column property="status" label="状态" width="80" align="center">
                            <template #default="scope">
                                <el-tag size="small" v-if="scope.row.state=='待回复'" effect="plain">{{scope.row.state}}</el-tag>
                                <el-tag type="success" size="small" v-else-if="scope.row.state=='已回复'" effect="plain">{{scope.row.state}}</el-tag>
                                <el-tag type="info" size="small" v-else-if="scope.row.state=='已关闭'" effect="plain">{{scope.row.state}}</el-tag>
                                <el-tag type="danger" size="small" v-else-if="scope.row.state=='已驳回'" effect="plain">{{scope.row.state}}</el-tag>
                                <el-tag type="warning" size="small" v-else="" effect="plain">{{scope.row.state}}</el-tag>
                            </template>
                        </el-table-column>
                        <?php if ($userrow['uid'] == 1) { ?>
                            <!--<el-table-column property="uid" label="UID" width="50"></el-table-column>-->

                        <?php } ?>

                        <el-table-column property="region" label="分类" width="80" show-overflow-tooltip></el-table-column>
                        <el-table-column property="title" label="标题" show-overflow-tooltip></el-table-column>


                        <el-table-column property="addtime" label="添加时间" width="160">
                            <template #default="scope">
                                {{new_date(scope.row.addtime, 'yyyy-MM-dd HH:mm:ss')}}
                            </template>

                        </el-table-column>

                        <el-table-column property="gid" label="ID" width="58" align="center"></el-table-column>
                    </el-table>

                    <div id="huifu_id" style="display: none;height: 100%;">
                        <!--{{now_gongdan.gid}}-->
                        <!--{{now_gongdan.answer}}-->
                        <div style="display: flex; flex-direction: column; height: 100%;">
                            <ul class="layui-padding-3" style="flex: 1; overflow: auto; overflow-y: auto;" id="huifuT_content_id">
                                <div v-if="now_gongdan.oid" class="layui-font-12 layui-font-green" style="position: absolute; z-index: 10; background: #fff; width: -webkit-fill-available; left: 0; top: 0; padding: 3px 9px;">
                                    当前绑定订单：{{ `${now_gongdan.oidInfo.user} ${now_gongdan.oidInfo.kcname} ${now_gongdan.oidInfo.ptname}` }}
                                </div>
                                <li v-for="(item,index) in now_gongdan.answer" :key="index" style="display: inline-block; width: 100%;margin-bottom: 10px;" :style="{textAlign:item.uid === '1'?'left':'right'}">
                                    <div style="margin : 0 0 2px;">
                                        {{item.uid === '1' ?'官方':'我'}}
                                    </div>
                                    <div style="display: inline-block; border-radius: 10px; padding: 10px; text-align: left; max-width: 70%;position: relative;" :class="item.uid === '1'?'layui-bg-blue':'layui-bg-green'">
                                        <i v-if="item.uid === '1'" class="layui-edge layui-edge-top" style="left: 3px; top: -9px; position: absolute; border-bottom-color: #1e9fff;"></i>
                                        <i v-else class="layui-edge layui-edge-top" style="right: 3px; top: -9px; position: absolute; border-bottom-color: #16baaa;"></i>
                                        <span style="white-space: pre-line; word-wrap: break-word;" v-html="item.content"></span>
                                    </div>
                                    <div style="font-size: 12px; color: #bbb; padding-left: 5px;">
                                        {{ new_date(item.time)}}
                                    </div>
                                </li>
                            </ul>

                            <div class="layui-input-group layui-form">
                                <div class="ban" style="position: absolute; top: -20px; right: 15px; z-index: 1; font-size: 12px;scale: .8;">
                                    {{ huifugongdan_t.length }} 字
                                </div>
                                <div v-if="now_gongdan_new_answer_count" @click="huifuT_content_id_scrollTop_bottom();now_gongdan_new_answer_count=0;" class="animate__animated animate__flipInY infinite" style="position: absolute; top: -30px; right: 90px; z-index: 1; font-size: 12px;scale: .8; cursor: pointer;">
                                    <i class="fa-solid fa-location-pin" style="font-size: 30px;"></i>
                                    <span style="color: rgb(255, 255, 255); position: absolute; left: 50%; top: 50%; transform: translate(-50%, -60%); z-index: 1;">
                                        {{now_gongdan_new_answer_count}}
                                    </span>
                                </div>
                                <textarea :disabled="/已完结|忽略/i.test(now_gongdan.state)" rows="1" name="" placeholder="请输入消息..." v-model="huifugongdan_t" class="layui-textarea" style="resize: none; overflow: auto; min-height: 40px;" lay-affix="clear"></textarea>
                                <div class="layui-btn layui-bg-blue  layui-btn-sm" style="cursor: pointer;" @click="huifugongdan">
                                    <i class="layui-icon layui-icon-release"></i>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <!--工单列表模块-->

            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="assets/LightYear/js/main.min.js"></script>
<script src="assets/js/aes.js"></script>


<script>
    const app = Vue.createApp({
        data() {
            return {
                getorder_ok: false,
                uid: '<?= $userrow['uid'] === '1' ? true : false; ?>',
                row: null,
                show: false,
                ddsize: 'small',
                order: [],
                list: {
                    region: '',
                    title: '',
                    content: '',
                    answer: ''
                },
                form: {
                    title: '',
                    content: '',
                    region: '',
                    oid: '',
                },
                rules: {
                    region: [{
                        required: true,
                        message: '必填！',
                        trigger: 'blur'
                    }],
                    title: [{
                        required: true,
                        message: '必填！',
                        trigger: 'blur'
                    }],
                    content: [{
                        required: true,
                        message: '必填！',
                        trigger: 'blur'
                    }]
                },
                huifugongdan_t: '',
                now_gongdan: {
                    state: '',
                    gid: '',
                    answer: [],
                    oid: '',
                    oidInfo: {},
                    uid: '',
                    time: 0,

                },
                now_gongdan_new_answer_count: 0,
                now_gongdan_get_timer: null,
            }
        },
        mounted() {
            const _this = this;
            _this.get();
            
            console.log("getUrlParams()",_this.getUrlParams())
            
            if (_this.getUrlParams().region) {
                $(document).ready(function() {
                    setTimeout(() => {
                        vm.tjgdID_open();
                    }, 300)
                    
                    
                    
                })
                _this.form.region = _this.getUrlParams().region;
            }
        },
        methods: {
            tjgdID_open() {
                const _this = this;
                layui.use(function() {
                    layer.open({
                        id: 'tjgdID',
                        type: 1,
                        title: '提交工单',
                        area: ['360px'],
                        content: $("#tjgdID"),
                        btn: ["提交工单","取消"],
                        yes(){
                            _this.submit();
                        },
                        end(){
                            console.log("关闭")
                            _this.form = {
                                title: '',
                                content: '',
                                region: '',
                                oid: '',
                            }
                            
                        },
                    })
                })
            },
            new_date(time, type = "yyyy-MM-dd HH:mm:ss") {
                return layui.util.toDateString(time, type)
            },
            now_gongdan_get0(command) {
                const _this = this;

                // 定时器实现获取最新消息
                _this.now_gongdan_get(command, 1);
                _this.now_gongdan_get_timer = setInterval(() => {
                    _this.now_gongdan_get(command);
                }, 1500)

                // 监控当前滚动高度，若不为最下方，则获取到新消息后依然为当前滚动位置
                $('#huifuT_content_id').on('scroll', function() {
                    var scrollTopValue = $(this).scrollTop();
                    let scrollTop_height = 0;

                    $('#huifuT_content_id').find('li').each(function() {
                        scrollTop_height += $(this).outerHeight(true);
                    });


                    if (scrollTopValue - scrollTop_height > -410) {
                        _this.now_gongdan_new_answer_count = 0;
                    }
                });

            },
            // 获取单个工单的信息
            now_gongdan_get(command, first) {
                const _this = this;


                let scrollTop_height = 0;
                $('#huifuT_content_id').find('li').each(function() {
                    scrollTop_height += $(this).outerHeight(true);
                });
                let TopValue = $('#huifuT_content_id').scrollTop() - scrollTop_height;

                // 先清理
                for (let i in _this.now_gongdan) {
                    if (i === 'answer') {} else {
                        _this.now_gongdan[i] = '';
                    }
                }

                if (first) {
                    _this.huifugongdan_t = '';
                    _this.now_gongdan_new_answer_count = 0;
                }

                _this.now_gongdan.state = _this.row.data.find(r => r.gid === command.gid).state;
                _this.now_gongdan.title = _this.row.data.find(r => r.gid === command.gid).title;
                _this.now_gongdan.region = _this.row.data.find(r => r.gid === command.gid).region;
                _this.now_gongdan.oid = _this.row.data.find(r => r.gid === command.gid).oid;
                _this.now_gongdan.oidInfo = _this.row.data.find(r => r.gid === command.gid).oidInfo;
                // var load = layer.load();
                data = {
                    list: _this.list
                };
                axios.post("/gd.php?act=gdlist", data, {
                    emulateJSON: true
                }).then((r) => {
                    // layer.close(load);
                    if (r.data.code == 1) {
                        _this.row = r.data;
                        _this.order = [];
                        for (var i = 0; r.data.data.length > i; i++) {
                            _this.order[i] = r.data.data[i];
                        }
                        _this.getorder_ok = true;
                        _this.now_gongdan.gid = command.gid;
                        const this_gd = _this.row.data.find(r => r.gid === command.gid);

                        _this.now_gongdan.region = this_gd.region;
                        _this.now_gongdan.title = this_gd.title;
                        const fg_one = this_gd.content.split('^');
                        for (let i in fg_one) {
                            if (fg_one[i]) {
                                if (_this.now_gongdan.answer.findIndex(j => j.time == fg_one[i].split('ô')[1].split('∫')[1]) == -1) {
                                    _this.now_gongdan.answer.push({
                                        content: fg_one[i].split('ô')[0],
                                        uid: fg_one[i].split('ô')[1].split('∫')[0],
                                        time: fg_one[i].split('ô')[1].split('∫')[1],
                                    })
                                    if (!first) {
                                        _this.now_gongdan_new_answer_count++;
                                        console.log(88, fg_one[i].split('ô')[1].split('∫')[0])
                                        if (fg_one[i].split('ô')[1].split('∫')[0] !== '<?= $userrow["uid"] ?>') {
                                            audioPlay();
                                        }
                                    }
                                }
                            }
                        }

                        if (first) {
                            _this.huifuT_content_id_scrollTop_bottom();
                        }

                        if (TopValue > -410) {
                            setTimeout(() => {
                                $('#huifuT_content_id').scrollTop($('#huifuT_content_id')[0].scrollHeight);
                                _this.now_gongdan_new_answer_count = 0;
                            }, 0)
                        }

                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                });

            },
            huifuT_content_id_scrollTop_bottom() {
                const _this = this;
                let scrollTop_height = 0;
                setTimeout(() => {
                    $('#huifuT_content_id').find('li').each(function() {
                        scrollTop_height += $(this).height();
                    });

                    $('#huifuT_content_id').scrollTop(scrollTop_height + 5000)
                }, 0)
            },
            huifugongdan_open(command) {
                const _this = this;
                _this.now_gongdan_get0(command);
                layui.use(function() {
                    layer.open({
                        id: 'huifugongdan_layui',
                        hideOnClose: false,
                        type: 1,
                        closeBtn: 1,
                        shade: .6, // 不显示遮罩
                        area: ['360px', '500px'],
                        title: `<span class="el-tag el-tag--info el-tag--light"><span class="el-tag__content">${_this.now_gongdan.state}</span></span><font>【${_this.now_gongdan.region}】</font>` + _this.now_gongdan.title,
                        content: $('#huifu_id'),
                        scrollbar: false,
                        end() {
                            clearInterval(_this.now_gongdan_get_timer);
                        },
                    });

                })
            },
            // 回复单个工单
            huifugongdan() {
                const _this = this;
                if(/已完结|忽略/i.test(_this.now_gongdan.state)){
                    _this.$message.error(`当前工单${_this.now_gongdan.state}，不支持回复！`);
                    return;
                }
                if (_this.huifugongdan_t) {
                    var load = layer.load();
                    axios.post("/gd.php?act=answer", {
                        gid: _this.now_gongdan.gid,
                        answer: _this.huifugongdan_t,
                        time: Date.now(),
                        uid: '<?= $userrow['uid']; ?>',
                    }).then(r=> {
                        layer.close(load);
                        if (r.data.code == 1) {
                            _this.now_gongdan_get({
                                gid: _this.now_gongdan.gid
                            });
                            _this.$message.success(r.data.msg);
                            // vm.get();
                        } else {
                            _this.$message.error(r.data.msg);
                        }
                    });
                } else {
                    layer.msg('请输入消息')
                }

            },
            commandvalue(command,row) {
                const _this = this;
                console.log(command)
                //工单回复
                if (command.type == 'hf') {
                    _this.huifugongdan_open(command);
                }
                //完结工单
                if (command.type == 'wj') {
                    var load = layer.load();
                    axios.post("/gd.php?act=wjgd", {
                        gid: command.gid
                    }).then(r=> {
                        layer.close(load);
                        if (r.data.code == 1) {
                            _this.$message.success(r.data.msg);
                            vm.get();
                        } else {
                            _this.$message.error(r.data.msg);
                        }
                    });
                }
                //关闭工单
                if (command.type == 'gb') {
                    var load = layer.load();
                    axios.post("/gd.php?act=gbgd", {
                        gid: command.gid
                    }).then(r => {
                        layer.close(load);
                        if (r.data.code == 1) {
                            _this.$message.success(r.data.msg);
                            vm.get();
                        } else {
                            _this.$message.error(r.data.msg);
                        }
                    });
                }
                //不处理工单
                if (command.type == 'bcl') {
                    var load = layer.load();
                    axios.post("/gd.php?act=bclgd", {
                        gid: command.gid
                    }).then(r => {
                        layer.close(load);
                        if (r.data.code == 1) {
                            _this.$message.success(r.data.msg);
                            vm.get();
                        } else {
                            _this.$message.error(r.data.msg);
                        }
                    });
                }
            },

            //获取工单列表
            get: function() {
                const _this = this;
                var load = layer.load();
                data = {
                    list: _this.list
                };
                axios.post("/gd.php?act=gdlist", data, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        _this.row = r.data;
                        _this.order = [];
                        if (r.data.data) {
                            for (var i = 0; r.data.data.length > i; i++) {
                                _this.order[i] = r.data.data[i];
                            }
                        }
                        $('#gdlist').show()
                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            //提交工单
            submit: function() {
                const _this = this;
                var loading = layer.load();
                axios.post("/gd.php?act=addgd", {
                    title: _this.form.title,
                    region: _this.form.region,
                    content: _this.form.content,
                    oid: _this.form.oid,
                    time: Date.now(),
                    uid: <?= $userrow['uid'] ?>,
                }).then(r => {
                    layer.close(loading);
                    if (r.data.code == 1) {
                        _this.$message.success("提交成功");
                        _this.get();
                        // 		_this.show = !_this.show;
                        layer.closeAll()
                    } else {
                        _this.$message.error(r.data.msg?r.data.msg:"提交失败");
                    }
                });
            },
            //删除工单
            shan: function(gid) {
                const _this = this;
                layer.confirm('是否删除该工单？', {
                    icon: 3
                }, function(index) {
                    var loading = layer.load();
                    axios.post("/gd.php?act=shan", {
                        gid: gid
                    }).then(r=> {
                        layer.close(loading);
                        if (r.data.code == 1) {
                            _this.$message.success(r.data.msg);
                            // 			window.location.href = ""
                            _this.get();
                            layer.close(index);
                        } else {
                            _this.$message.error(r.data.msg);
                        }
                    });
                }, function() {});
            },
            getUrlParams: function() {
                var params = {};
                var regex = /[?&]([^=#]+)=([^&#]*)/g;
                var match;

                while ((match = regex.exec(location.href)) !== null) {
                    var paramName = decodeURIComponent(match[1]);
                    var paramValue = decodeURIComponent(match[2]);
                    params[paramName] = paramValue;
                }

                return params;
            }

        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#gdlist');
    // -----------------------------
</script>