<?php
$title = '编辑用户密价';
require_once('head.php');
if ($userrow['uid'] != 1) {
    exit("<script language='javascript'>window.location.href='login.php';</script>");
}

$uid = $_GET['uid'];
?>
<style>
    .layui-form-selected dl {
        width: 100%;
    }

    .layer_el_select {
        z-index: 999999999 !important;
    }

    .el-popper {
        z-index: 999999999 !important;
    }
</style>

<div class="layui-padding-1" id="orderlist" style="display:none">
    <div class="layui-panel layui-padding-2">

        <div class="">
            密价列表&nbsp;&nbsp;
            <button type="button" class="layui-btn layui-btn-xs layui-btn-primary  layui-border-normal" @click="get(1)">
                <i class="layui-icon layui-icon-refresh"></i>
            </button>
            <div style="display: flex; align-items: center;margin:15px 0;">
                <button class="layui-btn layui-bg-blue layui-btn-sm" @click="modal_add_open">添加密价</button>&nbsp;&nbsp;
                <div style="display: inline-block;">
                    <div class="layui-input-group">
                        <input type="text" lay-affix="clear" placeholder="请输入代理UID" class="layui-input " v-model="query_data.uid">
                        <div class="layui-input-split layui-input-suffix" style="cursor: pointer;" @click="get(1,{uid:query_data.uid})">
                            <i class="layui-icon layui-icon-search"></i>
                        </div>
                    </div>
                </div>&nbsp;&nbsp;
                <button class="layui-btn layui-btn-primary layui-border-red layui-btn-sm" @click="get(row.current_page)">
                    重置
                </button>
            </div>
        </div>

        <div class="">
            <div class="table-responsive">
                <table id="litTable" lay-filter="litTable" lay-size="sm" lay-even></table>
                <div id="litTable_toolbar" style="display: none;">
                    <button class="layui-btn layui-btn-sm" lay-event="del_pl">
                        批量删除
                    </button>
                </div>
                <div id="litTable_more" style="display: none;">
                    <button title="编辑" lay-event="edit" type="button" class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs">
                        <i class="layui-icon layui-icon-edit"></i>
                    </button>
                    <button title="编辑" lay-event="del" type="button" class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs">
                        <i class="layui-icon layui-icon-delete"></i>
                    </button>
                </div>
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


        <div id="modal_up" style="display:none;">
            <form class="layui-padding-2 layui-form" id="form-up">
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="mid" :value="upm.mid" />

                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        代理
                    </div>
                    <!--<input type="number" step="1" name="uid" v-model="upm.uid" class="layui-input" placeholder="输入代理UID">-->
                    <select disabled="" lay-append-to="body" v-model="upm.uid" name="uid" lay-filter="uid_select" id="uid_select" class="layui-select" lay-search>
                        <option value="">点我选择代理，支持搜索</option>
                        <?php
                        $a = $DB->query("select uid,name from qingka_wangke_user where uid!=1 ORDER BY `uid` ASC");
                        while ($b = $DB->fetch($a)) {
                            echo '<option value="' . $b['uid'] . '">[' . $b['uid'] . '] ' . $b['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        商品
                    </div>
                    <select disabled="" lay-append-to="body" name="cid" v-model="upm.cid" lay-filter="up_cid_select" id="up_cid_select" class="layui-select" lay-search>
                        <option value="">点我选择，可输入搜索</option>
                        <?php
                        $a = $DB->query("select * from qingka_wangke_class where status=1");
                        while ($b = $DB->fetch($a)) {
                            echo '<option value="' . $b['cid'] . '">[' . $b['cid'] . '] ' . $b['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        类型
                    </div>
                    <select lay-append-to="body" name="mode" v-model="upm.mode" :value="upm.mode" lay-filter="up_mode_select" id="up_mode_select" class="layui-select" lay-search>
                        <option value="">点我选择，可输入搜索</option>
                        <option value="0">价格的基础上扣除</option>
                        <option value="1">系数的基础上扣除</option>
                        <option value="2">直接定价</option>
                    </select>
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        {{addm.mode == 2?'金额':'系数'}}
                    </div>
                    <input type="number" v-model="upm.price" step="0.001" name="price" class="layui-input" placeholder="">
                </div>



            </form>
        </div>


        <div id="modal_add" style="display:none;">

            <form id="form-add" class="layui-padding-2 layui-form">

                <input type="hidden" name="action" v-model="addm.action" />

                <div class="layui-input-group" style="margin: 10px 0;width: 100%;padding: 0 0 0 0;scale: .9;">
                    <input lay-filter="addType-radio-filter" type="radio" name="addType" value="0" checked title="单个商品">
                    <input lay-filter="addType-radio-filter" type="radio" name="addType" value="1" title="多个商品">
                    <input lay-filter="addType-radio-filter" type="radio" name="addType" value="2" title="单个分类">
                    <input lay-filter="addType-radio-filter" type="radio" name="addType" value="3" title="多个分类">
                </div>

                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        指定代理
                    </div>

                    <select lay-append-to="body" name="uid" lay-filter="uid_select" id="uid_select" class="layui-select" lay-search>
                        <option value="">点我选择代理，支持搜索</option>
                        <?php
                        $a = $DB->query("select uid,name from qingka_wangke_user where uid!=1 ORDER BY `uid` ASC");
                        while ($b = $DB->fetch($a)) {
                            echo '<option value="' . $b['uid'] . '">[' . $b['uid'] . '] ' . $b['name'] . '</option>';
                        }
                        ?>
                    </select>

                </div>

                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        <span v-if="addm.type === '0' || addm.type === '1'">
                            选择商品
                        </span>
                        <span v-else>
                            选择分类
                        </span>
                    </div>

                    <!--商品-->
                    <div v-if="addm.type==='0'">
                        <select lay-append-to="body" name="cid" lay-filter="cid_select" id="cid_select" class="layui-select" lay-search>
                            <option value="">点我选择，可输入搜索</option>
                            <?php
                            $a = $DB->query("select * from qingka_wangke_class where status=1 ORDER BY `sort` ASC");
                            while ($b = $DB->fetch($a)) {
                                echo '<option value="' . $b['cid'] . '">[' . $b['cid'] . '] ' . $b['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <el-select v-if="addm.type==='1'" popper-class="layer_el_select" v-model="addm.cid" multiple collapse-tags collapse-tags-tooltip placeholder="请选择分类">
                        <?php
                        $a = $DB->query("select cid,name from qingka_wangke_class where status=1 ORDER BY `sort` ASC");
                        while ($b = $DB->fetch($a)) {
                            echo '<el-option label="' . $b['name'] . '" value="' . $b['cid'] . '">[' . $b['cid'] . '] ' . $b['name'] . '</el-option>';
                        }
                        ?>
                    </el-select>
                    <!--商品-->

                    <!--分类-->
                    <div v-if="addm.type==='2'">
                        <select lay-append-to="body" name="cid" lay-filter="cid_select" id="cid_select" class="layui-select" lay-search>
                            <option value="">点我选择，可输入搜索</option>
                            <?php
                            $a = $DB->query("select id,name from qingka_wangke_fenlei where status=1 ORDER BY `sort` ASC");
                            while ($b = $DB->fetch($a)) {
                                echo '<option value="' . $b['id'] . '">[' . $b['id'] . '] ' . $b['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <el-select v-if="addm.type==='3'" popper-class="layer_el_select" v-model="addm.cid" multiple collapse-tags collapse-tags-tooltip placeholder="请选择分类">
                        <?php
                        $a = $DB->query("select id,name from qingka_wangke_fenlei where status=1 ORDER BY `sort` ASC");
                        while ($b = $DB->fetch($a)) {
                            echo '<el-option label="' . $b['name'] . '" value="' . $b['id'] . '">[' . $b['id'] . '] ' . $b['name'] . '</el-option>';
                        }
                        ?>
                    </el-select>
                    <!--分类-->

                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        密价类型
                    </div>
                    <select lay-append-to="body" name="mode" lay-filter="add_mode_select" id="add_mode_select" class="layui-select" lay-search>
                        <option value="">点我选择，可输入搜索</option>
                        <option value="0">价格的基础上扣除</option>
                        <option value="1">系数的基础上扣除</option>
                        <option value="2">直接定价</option>
                    </select>
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        {{addm.mode == 1?"系数":"金额"}}
                    </div>
                    <input type="number" step="0.01" name="price" v-model="addm.price" class="layui-input" placeholder="">
                </div>
                <button type="reset" class="layui-btn layui-btn-primary" id="form-add_reset" style="display:none;">重置</button>

            </form>

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
                uid: "<?php echo $uid ?>",
                storeInfo: {},
                modal_add_open_layer: null,
                addm: {
                    uid: "",
                    cid: "",
                    mode: "",
                    price: 0,
                    action: 1,
                    type: "0",
                },
                upm: {
                    uid: "",
                    cid: "",
                    mode: "",
                    price: 0,
                    action: 1,
                    type: "0",
                },
                query_data: {
                    uid: ''
                },
                fenleiList: <?php
                            $a = $DB->query("select id,name from qingka_wangke_fenlei");
                            $data = [];
                            while ($row = $DB->fetch($a)) {
                                $data[] = $row;
                            }
                            echo json_encode($data);
                            ?>,
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
        computed: {
        },
        methods: {
            tableInit() {
                const _this = this;
                layui.table.render({
                    elem: "#litTable",
                    size: "sm",
                    even: true,
                    cellExpandedMode: "tips",
                    toolbar: "#litTable_toolbar",
                    id: "litTable_table",
                    cols: [
                        [ //标题栏
                            {checkbox: true, fixed: true},
                            {
                                field: 'mid',
                                title: 'ID',
                                width: 60,
                                align: "center",
                            },
                            {
                                field: 'uid',
                                title: '代理',
                                width: 60,
                                align: "center",
                            },
                            {
                                field: 'name',
                                title: '商品名称',
                                minWidth: 210,
                                templet: '【{{= d.cid }}】{{= d.name?d.name:"该商品已被删除" }}',
                            },
                            {
                                field: 'fenlei',
                                title: '所属分类',
                                width: 120,
                                templet: function(d) {
                                    return `【${d.fenlei}】${_this.fenleiList.find(i=>i.id === d.fenlei)?_this.fenleiList.find(i=>i.id === d.fenlei).name:''}`;
                                },
                            },
                            {
                                field: 'city',
                                title: '类型',
                                width: 120,
                                templet: function(d) {
                                    if(d.mode == 0){
                                        return "价格的基础上扣除";
                                    }else if(d.mode == 1){
                                        return "系数的基础上扣除";
                                    }else{
                                        return "直接定价";
                                    }
                                },
                            },
                            {
                                field: 'price',
                                title: '金额/系数',
                                width: 80,
                                align: "center",
                            },
                            {
                                field: 'addtime',
                                title: '添加时间',
                                width: 150,
                                align: "center",
                            },
                            {
                                field: 'uptime',
                                title: '修改时间',
                                width: 150,
                                align: "center",
                            },
                            {width:100, title: '操作', templet: '#litTable_more',
                                align: "center",fixed:"right",}
                        ]
                    ],
                    data: _this.row.data
                })
                layui.table.on('toolbar(litTable)', function(obj){
                    var checkStatus = layui.table.checkStatus(obj.config.id); //获取选中行状态
                    switch(obj.event){
                      case 'del_pl':
                        var data = checkStatus.data;  // 获取选中行数据
                        _this.del(data.map(item => item.mid));
                      break;
                    };
                  });
                layui.table.on('tool(litTable)', function(obj){
                    var checkStatus = layui.table.checkStatus(obj.config.id); //获取选中行状态
                    switch(obj.event){
                      case 'edit':
                        var data = obj.data;  // 获取选中行数据
                        _this.modal_up_open(data);
                      break;
                      case 'del':
                        var data = obj.data;  // 获取选中行数据
                        _this.del([data.mid]);
                      break;
                    };
                  });
            },
            ql_query_data: function() {
                const _this = this;
                for (let i in _this.query_data) {
                    _this.query_data[i] = '';
                }
            },
            modal_add_open: function() {
                const _this = this;

                layui.form.render();
                layui.use(function() {
                    var $ = layui.jquery;
                    _this.modal_add_open_layer = layer.open({
                        hideOnClose: true,
                        type: 1,
                        title: '添加密价',
                        id: "modal_add_open_layer",
                        hideOnClose: false,
                        area: ['360px'],
                        content: $("#modal_add"),
                        btn: ['添加', '取消'],
                        shade: 0,
                        success: function() {
                            console.log("打开modal_add_open")
                            layui.form.render();

                            layui.form.on('radio(addType-radio-filter)', (data) => {
                                var elem = data.elem; // 获得 radio 原始 DOM 对象
                                var checked = elem.checked; // 获得 radio 选中状态
                                var value = elem.value; // 获得 radio 值
                                _this.addm.type = value;
                                setTimeout(() => {
                                    layui.form.render();
                                }, 0)
                            })
                            layui.form.on('select(uid_select)', (data) => {
                                var elem = data.elem; // 获得 radio 原始 DOM 对象
                                var checked = elem.checked; // 获得 radio 选中状态
                                var value = elem.value; // 获得 radio 值
                                _this.addm.uid = value;
                            })
                            layui.form.on('select(cid_select)', (data) => {
                                var elem = data.elem; // 获得 radio 原始 DOM 对象
                                var checked = elem.checked; // 获得 radio 选中状态
                                var value = elem.value; // 获得 radio 值
                                _this.addm.cid = value;
                            })
                            layui.form.on('select(add_mode_select)', (data) => {
                                var elem = data.elem; // 获得 radio 原始 DOM 对象
                                var checked = elem.checked; // 获得 radio 选中状态
                                var value = elem.value; // 获得 radio 值
                                _this.addm.mode = value;
                            })

                        },
                        yes: function(index) {
                            // console.log(111,_this.addm);
                            // return
                            let addm_verify = [{
                                    i: 'uid',
                                    t: '指定代理',
                                },
                                {
                                    i: 'cid',
                                    t: '商品',
                                },
                                {
                                    i: 'mode',
                                    t: '密价类型',
                                },
                                {
                                    i: 'price',
                                    t: '系数',
                                },
                            ];

                            for (let i in addm_verify) {
                                console.log(i)
                                if (_this.addm[addm_verify[i].i] === '') {
                                    _this.$message.error(`请完善${addm_verify[i].t}`);
                                    return
                                }
                            }
                            _this.add_m();
                        },
                        end: function() {
                            $("#form-add_reset").click();
                            for (let i in _this.addm) {
                                if (typeof _this.addm[i] === 'string') {
                                    _this.addm[i] = '';
                                } else if (typeof _this.addm[i] === 'number') {
                                    _this.addm[i] = 0;
                                } else if (_this.addm[i] instanceof Array) {
                                    _this.addm[i] = [];
                                };
                            }
                            _this.addm["type"] = '0';

                            console.log(_this.addm)
                        },
                    })
                })
            },
            modal_up_open: function(res) {
                const _this = this;
                _this.upm = res;
                layui.use(function() {
                    var $ = layui.jquery;
                    _this.modal_up_open_layer = layer.open({
                        hideOnClose: true,
                        type: 1,
                        title: '修改密价',
                        area: ['360px'],
                        content: $("#modal_up"),
                        btn: ['修改', '取消'],
                        success: function() {
                            setTimeout(() => {
                                layui.form.render();
                            }, 0)
                            layui.form.on('select(up_mode_select)', (data) => {
                                var elem = data.elem; // 获得 radio 原始 DOM 对象
                                var checked = elem.checked; // 获得 radio 选中状态
                                var value = elem.value; // 获得 radio 值
                                _this.upm.mode = value;
                            })
                        },
                        yes: function(index) {
                            _this.up_m();
                        },
                        end: function() {
                            _this.get(vm.row.current_page);
                        }
                    })
                })
            },
            get: function(page, cdata) {
                const _this = this;
                var load = layer.load(0);


                const jdata = {};
                jdata.page = page;
                if (cdata) {
                    for (let i in cdata) {
                        jdata[i] = cdata[i];
                    }
                } else {
                    _this.ql_query_data();
                }


                axios.post("/apiadmin.php?act=mijialist", jdata, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        _this.row = r.data;
                        _this.tableInit();
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
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=mijia", {
                    // data: $("#form-add").serialize(),
                    data: _this.addm,
                    active: 1
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        vm.get(vm.row.current_page, _this.query_data);
                        layer.msg(r.data.msg, {
                            icon: 1
                        });
                        layer.close(_this.modal_add_open_layer);
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
                axios.post("/apiadmin.php?act=mijia", {
                    data: _this.upm,
                    active: 2
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        vm.get(vm.row.current_page, _this.query_data);
                        layer.msg(r.data.msg, {
                            icon: 1
                        });
                        layer.close(_this.modal_up_open_layer);
                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            del: function(mid) {
                const _this = this;
                if(!mid || !mid.length){
                    _this.$message.error("未选择数据");
                    return
                }
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=mijia_del", {
                    mid: mid
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        _this.get(_this.row.current_page);
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