<?php
$title = '编辑分类';
require_once('head.php');
if ($userrow['uid'] != 1) {
    exit("<script language='javascript'>window.location.href='login.php';</script>");
}
$uid = $_GET['uid'];
?>

<style>
    .layui-input-group .layui-form-label {
        padding: 10px 5px 0 0;
        min-width: 60px;
        max-width: 90px;
        width: auto;
    }
</style>

<div class="layui-padding-1" id="app" style="display:none">
    <div class="layui-panel">
        <div>
            <div class="panel-heading font-bold layui-padding-2">
                分类列表&nbsp;&nbsp;
                <button type="button" class="layui-btn layui-btn-xs layui-btn-primary  layui-border-normal" @click="get(row.current_page)">
                    <i class="layui-icon layui-icon-refresh"></i>
                </button>
                <button type="button" class="layui-btn layui-bg-blue layui-btn-sm" @click="fenlei_add_open">
                    <i class="layui-icon layui-icon-addition"></i>添加分类
                </button>
            </div>
            <div class="" v-if="row">
                <div class="table-responsive">
                    <table class="layui-table" style="margin :0;" lay-size="sm">
                        <thead>
                            <tr>
                                <th class="center">操作</th>
                                <th class="center">ID</th>
                                <th>分类名称</th>
                                <th class="center">状态</th>
                                <th class="center">商品数量</th>
                                <th>添加时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="layui-form" v-for="res in row.data" :key="res.fid">
                                <td style="width: 90px;">
                                    <div style=" display: flex; align-items: center;">
                                        <div style="display: grid; grid-template-columns: repeat(auto-fill,30px);">
                                            <button @click="sort('up',res.id)" type="button" class="layui-btn layui-btn-primary layui-btn-xs" style="margin:0 0 2px;">
                                                <i class="layui-icon layui-icon-up"></i>
                                            </button>
                                            <button @click="sort('down',res.id)" type="button" class="layui-btn layui-btn-primary layui-btn-xs" style="margin:0;">
                                                <i class="layui-icon layui-icon-down"></i>
                                            </button>
                                        </div>&nbsp;
                                        <button class="layui-btn layui-btn-primary layui-border-red layui-btn-sm" @click="del(res.id)">删除</button>
                                    </div>
                                </td>
                                <td class="center" style="width:50px;">{{res.id}}</td>
                                <td style="width:160px;">
                                    <div class="layui-input-group" style="width:160px;">
                                        <input style="height: 30px;" name="" v-model="res.name" :value="res.name" type="text" lay-affix="number" :placeholder="res.name" class="layui-input layui-btn-sm">
                                        <div class="layui-input-split layui-input-suffix" style="cursor: pointer;" @click="setShop(res,{name:res.name})">
                                            <i class="layui-icon layui-icon-edit"></i>
                                        </div>
                                    </div>
                                </td>
                                <td class="center" style="width:60px;">
                                    <span class="layui-btn layui-bg-green layui-btn-sm" v-if="res.status==1" @click="setShop(res,{status:0})">
                                        显示
                                    </span>
                                    <span class="layui-btn layui-bg-red layui-btn-sm" v-else-if="res.status==0" @click="setShop(res,{status:1})">
                                        隐藏
                                    </span>
                                </td>
                                <td class="center" style="width:70px;">{{res.cnum}}</td>
                                <td>{{res.time}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <ul class="pagination" v-if="row.last_page>1"><!--by 青卡 Vue分页 -->
                    <li class="disabled"><a @click="get(row.current_page)">首页</a></li>
                    <li class="disabled"><a @click="row.current_page>1?get(row.current_page-1):''">&laquo;</a></li>
                    <li @click="get(row.current_page-3)" v-if="row.current_page-3>=1"><a>{{ row.current_page-3 }}</a></li>
                    <li @click="get(row.current_page-2)" v-if="row.current_page-2>=1"><a>{{ row.current_page-2 }}</a></li>
                    <li @click="get(row.current_page-1)" v-if="row.current_page-1>=1"><a>{{ row.current_page-1 }}</a></li>
                    <li :class="{'active':row.current_page==row.current_page}" @click="get(row.current_page)" v-if="row.current_page"><a>{{ row.current_page }}</a></li>
                    <li @click="get(row.current_page+1)" v-if="row.current_page+1<=row.last_page"><a>{{ row.current_page+1 }}</a></li>
                    <li @click="get(row.current_page+2)" v-if="row.current_page+2<=row.last_page"><a>{{ row.current_page+2 }}</a></li>
                    <li @click="get(row.current_page+3)" v-if="row.current_page+3<=row.last_page"><a>{{ row.current_page+3 }}</a></li>
                    <li class="disabled"><a @click="row.last_page>row.current_page?get(row.current_page+1):''">&raquo;</a></li>
                    <li class="disabled"><a @click="get(row.last_page)">尾页</a></li>
                </ul>
            </div>
        </div>

        <div  id="fenlei_add" class="layui-padding-2" style="display: none;">
            <div>
                <form class="layui-form" id="form_add" lay-filter="form_add">

                    <div class="layui-input-group" style="margin: 10px 0;width: 100%;padding: 0 0 0 45px;scale: .9;">
                        <!--<input lay-filter="addType-radio-filter" type="radio" name="addType" value="1" title="自定义" checked>-->
                        <input lay-filter="addType-radio-filter" type="radio" name="addType" value="2" checked title="插入到指定分类后">
                    </div>

                    <div v-if="addm.addType === '2'" class="layui-input-group" style="margin: 10px 0;width: 100%;">
                        <label class="layui-form-label">插入位置</label>
                        <div class="layui-input-block">
                            <select lay-filter="put-select-filter" name="put" class="layui-select" v-model="addm.put"  lay-append-to="body">
                                <option value="">请选择，不选择则插入首位</option>
                                <option v-for="(item,index) in row.data" :key="index" :value="item.sort">
                                    【{{item.id}}】{{item.name}}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div v-else class="layui-input-group" style="margin: 10px 0;width: 100%;">
                        <label class="layui-form-label">排序Sort</label>
                        <div class="layui-input-block">
                            <input name="sort" v-model="addm.sort" :value="addm.sort" type="text" placeholder="请输入排序sort" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                        <label class="layui-form-label">分类名称</label>
                        <div class="layui-input-block">
                            <input name="name" v-model="addm.name" :value="addm.name" type="text" placeholder="请输入名称" class="layui-input" lay-affix="clear">
                        </div>
                    </div>
                    
                    <button type="reset" class="layui-btn layui-btn-primary" style="display: none;" id="form_add_reset">重置</button>

                </form>
            </div>
        </div>

    </div>
</div>

<?php include($root . '/index/components/footer.php'); ?>

<script>
    const app = Vue.createApp({
        data() {
            return {
                row: {

                },
                uid: '<?= $userrow['uid'] ?>' === '1' ? true : false,
                storeInfo: {},
                fenlei_add_open_layer: null,
                addm: {
                    addType: '2',
                    put: '',
                    name: '',
                },
                upm: {},

            }
        },
        mounted() {
            const _this = this;
            layui.use(function() {
                var util = layui.util;
                // 自定义固定条
                util.fixbar({
                    margin: 100
                })

            })

            _this.get(1);
        },
        methods: {
            fenlei_add_open() {
                const _this = this;
                layui.form.render(null, "form_add");
                
                
                _this.fenlei_add_open_layer = layer.open({
                    type: 1,
                    id: "fenlei_add_open_layer",
                    title: "添加分类",
                    content: $("#fenlei_add"),
                    area: ["350px"],
                    scrollbar: false,
                    btn: ["添加", "取消"],
                    success: function() {
                        layui.form.on('radio(addType-radio-filter)', (data) => {
                            var elem = data.elem; // 获得 radio 原始 DOM 对象
                            var checked = elem.checked; // 获得 radio 选中状态
                            var value = elem.value; // 获得 radio 值
                            _this.addm.addType = value;
                            setTimeout(() => {
                                layui.form.render(null, "form_add");
                            }, 0);
                        })
                        layui.form.on('select(put-select-filter)', (data) => {
                            var elem = data.elem; // 获得 radio 原始 DOM 对象
                            var checked = elem.checked; // 获得 radio 选中状态
                            var value = elem.value; // 获得 radio 值
                            _this.addm.put = value;
                            setTimeout(() => {
                                layui.form.render(null, "form_add");
                            }, 0);
                        })
                    },
                    yes: function() {
                        console.log(_this.addm)
                        _this.add_m();
                    },
                    end(){
                        $("#form_add_reset").click();
                        _this.addm.put = "";
                        _this.addm.name = "";
                    },
                })
            },
            sort: function(type, id) {
                const _this = this;
                var load = layer.load(0);

                axios.post("/apiadmin.php?act=fenlei_sort", {
                    type: type,
                    id: id
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        vm.get(vm.row.current_page);
                    } else {
                        _this.$message.error(r.data.msg);
                    }
                })

            },

            setShop: function(upm, res) {
                const _this = this;
                _this.upm = upm
                console.log(res)
                // return
                for (let i in res) {
                    _this.upm[i] = res[i]
                }
                console.log(_this.upm)
                _this.up_m()
            },
            get: function(page) {
                const _this = this;
                var load = layer.load(0);

                axios.post("/apiadmin.php?act=fllist", {
                    uid: _this.uid,
                    page: page
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        _this.row = r.data;
                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                    $("#app").ready(() => {
                        $("#app").show();
                    })
                });
            },
            add_m: function() {
                const _this = this;
                let verify = [
                    {
                      a: 'name',
                      b: '分类名称',
                    },
                ];
                for(let i in verify){
                    if(!_this.addm[verify[i].a]){
                        _this.$message.error(`请完善${verify[i].b}`);
                        return;
                    }
                }
                
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=fl", {
                    data: _this.addm,
                    active: 1
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        vm.get(vm.row.current_page);
                        _this.$message.success(r.data.msg);
                        layer.close(_this.fenlei_add_open_layer);
                    } else {
                        _this.$message.error(r.data.msg);
                    }
                });
            },
            up_m: function() {
                const _this = this;
                console.log(_this.upm)
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=fl", {
                    data: _this.upm,
                    active: 2
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        vm.get(vm.row.current_page);
                        layer.msg(r.data.msg, {
                            icon: 1
                        });
                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            del: function(id) {
                const _this = this;
                layui.use(function() {
                    layer.confirm('注：将会删除该分类下的所有平台', {
                        title: '确认删除？',
                        btn: ['确定', '算了'] //按钮
                    }, function() {
                        var load = layer.load(0);
                        axios.post("/apiadmin.php?act=fl_del", {
                            id: id
                        }, {
                            emulateJSON: true
                        }).then(function(r) {
                            layer.close(load);
                            if (r.data.code == 1) {
                                vm.get(vm.row.current_page);
                                layer.msg(r.data.msg, {
                                    icon: 1
                                });
                            } else {
                                layer.msg(r.data.msg, {
                                    icon: 2
                                });
                            }
                        });
                    })
                })
            },
        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#app');
    // -----------------------------
</script>