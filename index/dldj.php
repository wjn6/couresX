<?php
$title = '编辑用户密价';
require_once('head.php');
if ($userrow['uid'] != 1) {
    alert("您的账号无权限！", "index.php");
    exit();
}

?>

<style>
    body {
        font-family: "Noto Serif SC", serif !important;
    }

    .layui-input-group .layui-form-label {
        padding: 10px 5px 0 0;
        min-width: 60px;
        max-width: 90px;
        width: auto;
    }
</style>

<div class="layui-padding-1">
    <div class="layui-panel" id="orderlist" style="display:none">

        <div class="layui-card-header">
            等级列表&nbsp;
            <button type="button" class="layui-btn layui-btn-xs layui-btn-primary" @click="get(1)">
                <i class="layui-icon layui-icon-refresh"></i>
            </button>
            <button type="button" class="layui-btn layui-bg-blue layui-btn-sm" @click="dengji_add_open">
                <i class="layui-icon layui-icon-addition"></i>添加等级
            </button>
            <button type="button" class="layui-btn layui-bg-red layui-btn-sm" @click='del($refs.listTable.getSelectionRows().map(i=>i.id))'>
                <i class="layui-icon layui-icon-delete"></i>批量删除
            </button>

        </div>
        <div class="panel-body">
            <div class="table-responsive">
                
                <el-table ref="listTable" :data="row.data" stripe border show-overflow-tooltip empty-text="无等级，请添加" size="small" style="width: 100%">
                    
                    <el-table-column fixed="left" type="selection" width="28" align="center" ></el-table-column>
                    <el-table-column prop="id" label="ID" width="40" align="center" ></el-table-column>
                    <el-table-column prop="name" label="等级名称" width="130" >
                        <template #default="scope">
                            <el-input v-model="scope.row.name" size="small">
                                <template #append>
                                    <el-button @click="up_m2(scope.row,{name:scope.row.name})" style="padding: 8px 0;"><el-icon><Edit-Pen /></el-icon></el-button>
                                </template>
                            </el-input>
                        </template>
                    </el-table-column>
                    <el-table-column prop="rate" label="费率" width="130" >
                        <template #default="scope">
                            <el-input v-model="scope.row.rate" size="small">
                                <template #append>
                                    <el-button @click="up_m2(scope.row,{rate:scope.row.rate})" style="padding: 8px 0;"><el-icon><Edit-Pen /></el-icon></el-button>
                                </template>
                            </el-input>
                        </template>
                    </el-table-column>
                    <el-table-column prop="money" label="开通价格" width="130" >
                        <template #default="scope">
                            <el-input v-model="scope.row.money" size="small">
                                <template #append>
                                    <el-button @click="up_m2(scope.row,{money:scope.row.money})" style="padding: 8px 0;"><el-icon><Edit-Pen /></el-icon></el-button>
                                </template>
                            </el-input>
                        </template>
                    </el-table-column>
                    <el-table-column prop="addkf" label="添加扣费" width="70" >
                        <template #default="scope">
                            <el-switch v-model="scope.row.addkf" inline-prompt active-text="开启" active-value="1" inactive-text="关闭" inactive-value="0" @change="up_m2(scope.row,{addkf:scope.row.addkf})">
                                
                            </el-switch>
                        </template>
                    </el-table-column>
                    <el-table-column prop="gjkf" label="改价扣费" width="70" >
                        <template #default="scope">
                            <el-switch v-model="scope.row.gjkf" inline-prompt active-text="开启" active-value="1" inactive-text="关闭" inactive-value="0" @change="up_m2(scope.row,{gjkf:scope.row.gjkf})">
                                
                            </el-switch>
                        </template>
                    </el-table-column>
                    <el-table-column prop="status" label="当前状态" width="70" >
                        <template #default="scope">
                            <el-switch v-model="scope.row.status" inline-prompt active-text="开启" active-value="1" inactive-text="关闭" inactive-value="0" @change="up_m2(scope.row,{status:scope.row.status})">
                                
                            </el-switch>
                        </template>
                    </el-table-column>
                    <el-table-column prop="czAuth" label="非直属充值权限" width="105" >
                        <template #default="scope">
                            <el-switch v-model="scope.row.czAuth" inline-prompt active-text="开启" active-value="1" inactive-text="关闭" inactive-value="0" @change="up_m2(scope.row,{czAuth:scope.row.czAuth})">
                                
                            </el-switch>
                        </template>
                    </el-table-column>
                    <el-table-column fixed="right" label="主控"  align="center"  width="80">
                        <template #default="scope">
                            <el-button @click="del([scope.row.id])" link type="primary" size="small" style="margin: 0;">
                                <el-icon><Delete /></el-icon>
                            </el-button>
                            <el-button @click="dj_sort('up',scope.row.id)" title="点击上移" link type="primary" size="small" style="margin: 0;">
                                <el-icon><Arrow-Up /></el-icon>
                            </el-button>
                            <el-button @click="dj_sort('down',scope.row.id)" title="点击下移" link type="primary" size="small" style="margin: 0;">
                                <el-icon><Arrow-Down /></el-icon>
                            </el-button>
                        </template>
                    </el-table-column>
                    <el-table-column prop="time" label="添加时间"></el-table-column>
                        
                </el-table>
                
            </div>

            <ul class="pagination" v-if="row.last_page>1"><!--by 青卡 Vue分页 -->
                <li class="disabled"><a @click="get(1)">首页</a></li>
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

        <div v-if="false" class="modal fade primary" id="modal-update">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">密价修改</h4>
                    </div>

                    <div class="modal-body">
                        <form class="form-horizontal" id="form-update">
                            <input type="hidden" name="action" value="update" />
                            <input type="hidden" name="cid" :value="storeInfo.cid" />
                            <!--div class="form-group">
                               <label class="col-sm-3 control-label">MID</label>
                                <div class="col-sm-9">             
                                  <input type="text" v-model="upm.mid" class="form-control" :value="upm.mid" >
                               </div>
                            </div-->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">排序</label>
                                <div class="col-sm-9">
                                    <input type="text" v-model="upm.sort" class="form-control" :value="upm.sort">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">等级名称</label>
                                <div class="col-sm-9">
                                    <input type="text" v-model="upm.name" class="form-control" :value="upm.name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">等级费率</label>
                                <div class="col-sm-9">
                                    <input type="text" v-model="upm.rate" class="form-control" :value="upm.rate">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">开通价格</label>
                                <div class="col-sm-9">
                                    <input type="text" v-model="upm.money" class="form-control" :value="upm.money">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">添加用户扣费</label>
                                <div class="col-sm-9">
                                    <select v-model="upm.addkf" :value="upm.addkf" class="layui-select" style="width:100%">
                                        <option value="1">打开</option>
                                        <option value="0">关闭</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">修改费率扣费</label>
                                <div class="col-sm-9">
                                    <select v-model="upm.gjkf" :value="upm.gjkf" class="layui-select" style="width:100%">
                                        <option value="1">打开</option>
                                        <option value="0">关闭</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">状态</label>
                                <div class="col-sm-9">
                                    <select v-model="upm.status" :value="upm.status" class="layui-select" style="width:100%">
                                        <option value="1">启用</option>
                                        <option value="0">关闭</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-success" data-dismiss="modal" @click="up_m">确定</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="dengji_add" class="layui-padding-2" style="display: none;">

            <div>
                <form class="layui-form" id="form-add" lay-filter="form-add">
                    
                    <div class="layui-input-group" style="margin: 10px 0;width: 100%;padding: 0 0 0 45px;scale: .9;">
                        <!--<input lay-filter="addType-radio-filter" type="radio" name="addType" value="1" title="自定义" checked>-->
                        <input lay-filter="addType-radio-filter" type="radio" name="addType" value="2" checked title="插入到指定等级后">
                    </div>
                    <div v-if="addm.addType === '2'" class="layui-input-group" style="margin: 10px 0;width: 100%;">
                        <label class="layui-form-label">插入位置</label>
                        <div class="layui-input-block">
                            <select lay-filter="put-select-filter" name="put" class="layui-select" v-model="addm.put" lay-append-to="body">
                                <option value="">请选择，不选择则插入首位</option>
                                <option v-for="(item,index) in row.data" :key="index" :value="item.sort">
                                    【{{item.id}}】{{item.name}} | {{item.rate}}
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <hr />
                    
                    <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                        <label class="layui-form-label">等级名称</label>
                        <div class="layui-input-block">
                            <input name="name" v-model="addm.name" :value="addm.name" type="text" placeholder="请输入等级名称" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                        <label class="layui-form-label">等级费率</label>
                        <div class="layui-input-block">
                            <input name="rate" v-model="addm.rate" :value="addm.rate" type="text" placeholder="请输入等级费率" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                        <label class="layui-form-label">开通价格</label>
                        <div class="layui-input-block">
                            <input name="money" v-model="addm.money" :value="addm.money" type="text" placeholder="请输入开通价格" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                        <label class="layui-form-label">开通扣费</label>
                        <div class="layui-input-block">
                            <select lay-filter="addkf-select-filter" name="addkf" class="layui-select" v-model="addm.addkf" lay-append-to="body">
                                <option value="1" checked>开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>

                    <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                        <label class="layui-form-label">修改费率扣费</label>
                        <div class="layui-input-block">
                            <select lay-filter="gjkf-select-filter" name="gjkf" class="layui-select" v-model="addm.gjkf" lay-append-to="body">
                                <option value="1" checked>开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>

                    <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                        <label class="layui-form-label">非直属在线充值权限</label>
                        <div class="layui-input-block">
                            <select lay-filter="czAuth-select-filter" name="czAuth" class="layui-select" v-model="addm.czAuth" lay-append-to="body">
                                <option value="1">开启</option>
                                <option value="0" checked>关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-font-12 layui-font-red">
                        注：若需给单个非直属代理设置在线充值权限，请到【代理管理】页设置！
                        <br />优先级为：单个非直属代理是否可在线充值 > 其代理等级是否可在线充值
                    </div>

                </form>
            </div>

        </div>

    </div>
</div>

<script>
    const app = Vue.createApp({
        data() {
            return {
                row: {
                    data: []
                },
                uid: '<?= $userrow['uid'] ?>' === '1' ? true : false,
                storeInfo: {},
                addm: {
                    addType: '2',
                    name: '',
                    put: '',
                    rate: '',
                    money: '',
                    addkf: '1',
                    gjkf: '1',
                    czAuth: '0',
                },
                upm: {}
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
            dengji_add_open() {
                const _this = this;
                layui.form.render(null, "form-add");
                _this.dengji_add_open_layer = layer.open({
                    type: 1,
                    id: "dengji_add_open_layer",
                    title: "添加等级",
                    content: $("#dengji_add"),
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
                                layui.form.render(null, "form-add");
                            }, 0);
                        })
                        layui.form.on('select(addkf-select-filter)', (data) => {
                            var elem = data.elem; // 获得 radio 原始 DOM 对象
                            var checked = elem.checked; // 获得 radio 选中状态
                            var value = elem.value; // 获得 radio 值
                            _this.addm.addkf = value;
                            setTimeout(() => {
                                layui.form.render(null, "form-add");
                            }, 0);
                        })
                        layui.form.on('select(gjkf-select-filter)', (data) => {
                            var elem = data.elem; // 获得 radio 原始 DOM 对象
                            var checked = elem.checked; // 获得 radio 选中状态
                            var value = elem.value; // 获得 radio 值
                            _this.addm.gjkf = value;
                            setTimeout(() => {
                                layui.form.render(null, "form-add");
                            }, 0);
                        })
                        layui.form.on('select(czAuth-select-filter)', (data) => {
                            var elem = data.elem; // 获得 radio 原始 DOM 对象
                            var checked = elem.checked; // 获得 radio 选中状态
                            var value = elem.value; // 获得 radio 值
                            _this.addm.czAuth = value;
                            setTimeout(() => {
                                layui.form.render(null, "form-add");
                            }, 0);
                        })
                        layui.form.on('select(put-select-filter)', (data) => {
                            var elem = data.elem; // 获得 radio 原始 DOM 对象
                            var checked = elem.checked; // 获得 radio 选中状态
                            var value = elem.value; // 获得 radio 值
                            _this.addm.put = value;
                            setTimeout(() => {
                                layui.form.render(null, "form-add");
                            }, 0);
                        })
                    },
                    yes: function() {
                        console.log(_this.addm)
                        _this.add_m();
                    },
                })
            },
            get: function(page) {
                const _this = this;
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=djlist", {
                    uid: _this.uid
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
                    $("#orderlist").ready(() => {
                        $("#orderlist").show();
                    })
                });
            },
            add_m: function() {
                const _this = this;
                let verify = [
                    {
                      a: 'name',
                      b: '等级名称',
                    },
                    {
                      a: 'rate',
                      b: '等级费率',
                    },
                    {
                      a: 'money',
                      b: '开通价格',
                    },
                ];
                for(let i in verify){
                    if(!_this.addm[verify[i].a]){
                        _this.$message.error(`请完善${verify[i].b}`);
                        return;
                    }
                }
                
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=dj", {
                    data: _this.addm,
                    active: 1
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        vm.get(1);
                        layer.msg(r.data.msg, {
                            icon: 1
                        });
                        layer.close(_this.dengji_add_open_layer)
                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            up_m2: function(data, res) {
                for (let i in res) {
                    data[i] = res[i]
                }
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=dj", {
                    data: data,
                    active: 2
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        vm.get(1);
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
            up_m: function() {
                const _this = this;
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=dj", {
                    data: _this.upm,
                    active: 2
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        vm.get(1);
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
                if(!id){
                    _this.$message.error("请选择数据");
                    return
                }
                if(!id.length){
                    _this.$message.error("请选择数据");
                    return
                }
                layui.use(function() {
                    layer.confirm('确认删除？', {
                        btn: ['删除', '算了'], //按钮
                        yes: function(index) {
                            var load = layer.load(0);
                            axios.post("/apiadmin.php?act=dj_del", {
                                id: id
                            }, {
                                emulateJSON: true
                            }).then(function(r) {
                                layer.close(load);
                                layer.close(index);
                                if (r.data.code == 1) {
                                    vm.get(1);
                                    layer.msg(r.data.msg, {
                                        icon: 1
                                    });
                                } else {
                                    layer.msg(r.data.msg, {
                                        icon: 2
                                    });
                                }
                            });
                        }
                    })
                })


            },
            dj_sort(type, id){
                const _this = this;
                var load = layer.load(0);

                axios.post("/apiadmin.php?act=dj_sort", {
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
        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#orderlist');
    // -----------------------------
</script>