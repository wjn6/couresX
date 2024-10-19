<?php
$mod = 'blank';
$title = '日志列表';
require_once('head.php');
?>

<div class="layui-padding-1" id="loglist">
    <div class="layui-panel">

        <form class="layui-form layui-padding-3" action="">
            <div class="layui-form-item" style="margin: 0;">
                <div class="layui-inline">
                    <select name="type">
                        <option value="">所有</option>
                        <option value="登录">登录</option>
                        <option value="添加任务">添加任务</option>
                        <option value="批量提交">批量提交</option>
                        <option value="API添加任务">API添加任务</option>
                        <option value="上级充值">上级充值</option>
                        <option value="代理充值">代理充值</option>
                        <option value="修改费率">修改费率</option>
                        <option value="查课">查课</option>
                        <option value="API查课">API查课</option>
                        <option value="在线充值">在线充值</option>
                        <option value="订单退款">订单退款</option>
                    </select>
                </div>
                <div class="layui-inline">
                    <select name="types">
                        <option value="">所有</option>
                        <option value="1">用户UID</option>
                        <option value="2">操作内容</option>
                        <option value="3">积分变动</option>
                        <option value="4">操作时间</option>
                    </select>
                </div>
                <div class="layui-inline">
                    <input type="text" name="qq" placeholder="请输入查询内容" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-inline">
                    <button type="submit" class="layui-btn" lay-submit lay-filter="queryForm">查询</button>
                </div>
            </div>
        </form>

        <template v-if="row">
            <div style="width: auto; overflow: auto;">
                <table class="layui-table" lay-size="sm" style="margin: 0px;">
                    <thead>
                        <tr>
                            <th>
                                <label class="lyear-checkbox checkbox-info" style="display: flex; align-items: center; gap: 3px;">
                                    <input type="checkbox" id="check-all"><span>ID</span>
                                </label>
                            </th>
                            <th>UID</th>
                            <th>类型</th>
                            <th class="center">余额变动</th><!--<th>积分</th>-->
                            <th>余额</th>
                            <th>操作内容</th>
                            <th>操作时间</th>
                            <th>操作IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="res in row.data" :key="res">
                            <!--<td>{{res.id}}</td>-->
                            <td>
                                <label class="lyear-checkbox checkbox-info" style="display: flex; align-items: center; gap: 3px;">
                                    <input type="checkbox" v-model="sex"><span>{{res.id}}</span>
                                </label>
                            </td>
                            <td>{{res.uid}}</td>
                            <td>
                                <div style="width: 80px;">
                                    <!--<span class="btn btn-xs btn-success" v-if="res.type=='批量提交' ||res.type=='添加任务' || res.type=='API添加任务'">{{res.type}}</span>-->
                                    <!--<span class="btn btn-xs btn-danger" v-else-if="res.type=='删除订单信息'">{{res.type}}</span>-->
                                    <!--<span class="btn btn-xs btn-warning" v-else-if="res.type=='查课' || res.type=='API查课'">{{res.type}}</span>-->
                                    <!--<span class="btn btn-xs btn-warning" v-else-if="res.type=='代理充值'">代理充值</span>-->
                                    <!--<span class="btn btn-xs btn-success" v-else-if="res.type=='上级充值'">上级充值</span>-->
                                    <!--<span class="btn btn-xs btn-primary" v-else-if="res.type=='添加商户'">添加商户</span>-->
                                    <!--<span class="btn btn-xs btn-info" v-else-if="res.type=='修改费率' || res.type=='登录'">{{res.type}}</span>-->
                                    {{res.type}}
                                </div>
                            </td>
                            <td>
                                <div class="center" style="width: 60px;">
                                    {{res.money}}
                                </div>
                            </td>
                            <td>
                                <div style="width: 60px;">
                                    {{res.smoney}}
                                </div>
                            </td>
                            <!--<td>{{res.smoney}}</td>-->
                            <td style="minWidth: 160px;">{{res.text}}</td>
                            <td>
                                <div style="width: 120px;">
                                    {{res.addtime}}
                                </div>
                            </td>
                            <td>{{res.ip}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </template>
        <div style="text-align: right;margin-right:20px;">
            <div id="demo-laypage-normal-1"></div>
        </div>

    </div>
</div>
</div>

<script type="text/javascript" src="assets/LightYear/js/main.min.js"></script>
<script src="assets/js/aes.js"></script>

<?php include($root . '/index/components/footer.php'); ?>

<script>
    const app = Vue.createApp({
        data() {
            return {
                row: null,
                sex: [],
                type: '',
                types: '',
                qq: '',
                queryData: {},
                laypage: null,
                limit: 20,
                curr: 1,
            }
        },
        mounted() {
            const _this = this;
            _this.get(1, 20);

            layui.use('form', function() {
                var form = layui.form;
                form.render();
                form.on('submit(queryForm)', function(data) {
                    _this.queryData = data.field; // 获取表单字段值
                    _this.get(_this.row.current_page, _this.limit);
                    // 此处可执行 Ajax 等操作
                    // …
                    return false; // 阻止默认 form 跳转
                });

            })

        },
        methods: {
            get: function(page, limit) {
                let _this = this;
                var load = layer.load(0);
                _this.queryData.page = page;
                _this.queryData.limit = limit;
                axios.post("/apiadmin.php?act=loglist", _this.queryData, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        _this.row = r.data;
                        if (!vm.laypage) {
                            vm.laypage = 1;
                            layui.laypage.render({
                                elem: 'demo-laypage-normal-1',
                                count: r.data.count, // 数据总数
                                limit: vm.limit,
                                curr: vm.curr,
                                first: '首',
                                last: '尾',
                                prev: false,
                                next: false,
                                layout: ['count', 'page', 'limit', 'last'],
                                jump: function(obj, first) {
                                    if (obj.limit != vm.limit) {
                                        vm.laypage = 0;
                                        vm.limit = obj.limit;
                                    }
                                    // 首次不执行
                                    if (!first) {
                                        // do something
                                        _this.get(obj.curr, obj.limit)
                                    } else {}
                                }
                            });
                        }
                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
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
    var vm = app.mount('#loglist');
    // -----------------------------
</script>