<?php
$title = '首页公告';
include_once('head.php');

if ($userrow['uid'] != 1) {
    alert("你来错地方了", "index.php");
}
?>

<style>
    .layui-input-wrap {
        height: 100%;
    }
</style>

<div id="app" class="layui-padding-2" style="display:none;">

    <div class="layui-panel layui-padding-2">

        <div class="">
            <button type="button" class="layui-btn layui-btn-sm layui-bg-blue" @click="add_open()">
                <i class="layui-icon layui-icon-addition"></i> 添加公告
            </button>
        </div>

        <div class="table" style="margin: 10px 0 0">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="margin:10px 0">
                    <button title="刷新" type="button" class="layui-btn layui-btn-xs layui-btn-primary" @click="hnlist(1)">
                        <i class="layui-icon layui-icon-refresh"></i>
                    </button>
                    <button title="批量删除" v-if="uid" type="button" class="layui-btn layui-btn-xs layui-btn-primary" @click="del()">
                        <i class="layui-icon layui-icon-delete"></i>
                    </button>
                </div>
            </div>
            <div style="width:100%;overflow: auto;" id="tableBox">
                <table id="listTable" layui-filter="listTable" style="width:100%;"></table>
            </div>
        </div>

        <div class="layui-panel" style="display: flex; justify-content: space-between;">
            <div></div>
            <div id="listTable_laypage" style="scale: .8;"></div>
        </div>

    </div>

    <div id="add" class="layui-padding-2" style="display: none;height: -webkit-fill-available;">

        <div id="add-form" class="layui-form" action="" lay-filter="add-form" style="display: flex; flex-direction: column;height: -webkit-fill-available;">

            <div class="layui-form-item">
                <label class="layui-form-label" style="width: 60px;">标题</label>
                <div class="layui-input-block" style="margin-left: 95px;">
                    <input type="text" name="title" v-model="eform.title" lay-verify="required" placeholder="请输入标题" autocomplete="off" class="layui-input" lay-affix="clear">
                </div>
            </div>
            <div class="layui-form-item" style="flex: 1 1 auto;">
                <div style="height: 100%;width:100%;">
                    <!--<div id="editID"></div>-->
                    <textarea type="text" name="content" v-model="eform.content" lay-verify="required" placeholder="请输入内容，支持HTML" autocomplete="off" class="layui-textarea" lay-affix="clear" style="height: 100%"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: 60px;">作者</label>
                <div class="layui-input-block" style="margin-left: 95px;">
                    <input type="text" name="author" v-model="eform.author" lay-verify="required" placeholder="请输入作者" autocomplete="off" class="layui-input" lay-affix="clear">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label" style="width: 60px;">显示</label>
                    <div class="layui-input-block" style="margin-left: 95px;">
                        <input type="checkbox" name="status" :checked="eform.status" value="1" lay-verify="required" lay-skin="switch" lay-filter="addstatus-checkbox-filter" >
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label" style="width: 60px;">置顶</label>
                    <div class="layui-input-block" style="margin-left: 95px;">
                        <input type="checkbox" name="top" :checked="eform.top" value="1" lay-verify="required" lay-skin="switch">
                    </div>
                </div>
            </div>
            <button style="display:none;" id="add-form_reset" type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>

</div>

<script type="text/html" id="listTable_user_caoz">
    <div style="display: grid; grid-template-columns: repeat(4,auto)">
        <button title="上移" lay-event="listTable_user_up" type="button" class="layui-btn layui-btn-primary layui-border-green layui-btn-xs" style="margin:0 0 0 0;width: max-content;">
            <i class="layui-icon layui-icon-up"></i>
        </button>
        <button title="下移" lay-event="listTable_user_down" type="button" class="layui-btn layui-btn-primary layui-border-green layui-btn-xs" style="margin:0 0 0 0;width: max-content;">
            <i class="layui-icon layui-icon-down"></i>
        </button>
        <button title="编辑" lay-event="listTable_user_edit" type="button" class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs" style="margin:0;width: max-content;">
            <i class="layui-icon layui-icon-edit"></i>
        </button>
        <button title="删除" lay-event="listTable_user_del" type="button" class="layui-btn layui-btn-primary layui-border-red layui-btn-xs" style="margin:0 0 0 0;width: max-content;">
            <i class="layui-icon layui-icon-delete"></i>
        </button>
    </div>
</script>

<script type="text/html" id="table_top_templet">
{{# if(Number(d.top)) { }}
    <button type="button" class="layui-btn layui-bg-blue layui-btn-xs" lay-event="table_top_on">
        <i class="layui-icon layui-icon-return" style="transform: rotate(90deg); display: inline-block;"></i>
    </button>
    {{# } else { }}
        <button type="button" class="layui-btn layui-btn-xs " lay-event="table_top_off">
            <i class="layui-icon layui-icon-return" style="transform: rotate(90deg); display: inline-block;"></i>
        </button>
        {{# } }}
</script>
<script type="text/html" id="table_status_templet">
{{# if(Number(d.status)) { }}
    <button type="button" class="layui-btn layui-bg-blue layui-btn-xs" lay-event="table_status_on">显示</button>
    {{# } else { }}
        <button type="button" class="layui-btn layui-btn-xs" lay-event="table_status_off">隐藏</button>
        {{# } }}
</script>

<?php include_once($root . '/index/components/footer.php'); ?>

<script>
    const app = Vue.createApp({
        data() {
            return {
                uid: '<?= $userrow['uid'] ?>' === '1' ? true : false,
                row: {
                    data: [],
                },
                cx: {
                    pagesize: 15,
                },
                sex: [],
                eform: {
                    title: '',
                    content: '',
                    author: '',
                    status: 1,
                    top: 0,
                },
                eform2: {
                    title: '',
                    content: '',
                    author: '管理员',
                    status: 1,
                    top: 0,
                },
                editT: 0,
                editIframe: null,
            }
        },
        mounted() {
            const _this = this;
            _this.hnlist(1, 'one');
            _this.table_init();
            $("#app").ready(() => {
                $("#app").show();

            })

        },
        methods: {
            edit_init: function() {
                const _this = this;
                let loadIndex = layer.load(0);
                return new Promise((resolve) => {
                    _this.editIframe = new Vditor("editID", {
                        "cdn": "assets/vditor",
                        "height": 360,
                        "placeholder": "请输入内容",
                        "icon": "material",
                        "toolbar": ['emoji', "headings", "bold", "line", "italic", "strike", "|", "line", "quote", "list", "ordered-list", "check", "outdent", "indent", "code", "insert-after", "insert-before", "undo", "redo", "link", "table", "edit-mode", "both", "preview", "fullscreen", "outline"],
                        after() {
                            layer.close(loadIndex);
                            resolve();
                        },
                        input(md) {
                            _this.eform.content = `${_this.editIframe.getHTML()}`;
                        },
                    });
                });
            },
            table_init: function() {
                const _this = this;
                layui.use('table', function() {
                    var table = layui.table;
                    // 已知数据渲染
                    var inst = table.render({
                        elem: '#listTable',
                        id: 'listTable',
                        size: 'sm',
                        text: {
                            none: '哦吼一条公告都没得'
                        },
                        cols: [
                            [ //标题栏

                                {
                                    type: 'checkbox',
                                    // 	fixed: 'left'
                                    hide: !_this.uid
                                },
                                {
                                    field: 'title',
                                    title: '标题',
                                    width: 80,
                                },
                                {
                                    field: 'content',
                                    title: '内容',
                                    minWidth: 200,
                                },
                                {
                                    field: 'status',
                                    title: '状态',
                                    width: 30,
                                    align: 'center',
                                    templet: '#table_status_templet'
                                },
                                {
                                    field: 'top',
                                    title: '置顶',
                                    width: 30,
                                    align: 'center',
                                    templet: '#table_top_templet'
                                },
                                {
                                    field: 'author',
                                    title: '作者',
                                    align: 'center',
                                    width: 40,
                                },
                                {
                                    field: 'readUIDS',
                                    title: '阅读量',
                                    align: 'center',
                                    width: 40,
                                },
                                {
                                    field: 'addtime',
                                    title: '添加时间',
                                    width: 140,
                                    align: 'center'
                                },
                                {
                                    field: 'uptime',
                                    title: '更新时间',
                                    width: 140,
                                    align: 'center',
                                },
                                {
                                    field: 'id',
                                    title: 'ID',
                                    align: 'center',
                                    width: 50,
                                    hide: !_this.uid
                                },
                                {
                                    field: 'id',
                                    title: '操作',
                                    align: 'center',
                                    width: 140,
                                    templet: '#listTable_user_caoz',
                                    fixed: 'right',
                                },
                            ]
                        ],
                        data: _this.row.data,
                        cellExpandedMode: 'tips',
                        //skin: 'line', // 表格风格
                        even: true,
                        page: false, // 是否显示分页
                        limits: [5, 10, 15],
                        limit: _this.row.pagesize // 每页默认显示的数量0
                    });
                    table.on('tool(listTable)', function(obj) {
                        let data = obj.data;
                        switch (obj.event) {
                            case 'listTable_user_up':
                                var load = layer.load(0);
                                axios.post("/apiadmin.php?act=homenotice_sort", {
                                    type: 'up',
                                    id: data.id
                                }, {
                                    emulateJSON: true
                                }).then(function() {
                                    layer.close(load);
                                    _this.hnlist();
                                })
                                break;
                            case 'listTable_user_down':
                                var load = layer.load(0);
                                axios.post("/apiadmin.php?act=homenotice_sort", {
                                    type: 'down',
                                    id: data.id
                                }, {
                                    emulateJSON: true
                                }).then(function() {
                                    layer.close(load);
                                    _this.hnlist();
                                })
                                break;
                            case 'listTable_user_del':
                                _this.del(data.id);
                                break;
                            case 'listTable_user_edit':
                                _this.editT = 1;
                                _this.add_open('edit', data);
                                break;
                            case 'table_status_on':
                                _this.edit(data.id, {
                                    status: 0
                                });
                                break;
                            case 'table_status_off':
                                _this.edit(data.id, {
                                    status: 1
                                });
                                break;
                            case 'table_top_on':
                                _this.edit(data.id, {
                                    top: 0
                                });
                                break;
                            case 'table_top_off':
                                _this.edit(data.id, {
                                    top: 1
                                });
                                break;
                            default:
                                break;
                        }

                    });


                })
            },
            hnlist: function(page, type) {
                const _this = this;
                layui.use(function() {
                    var util = layui.util;
                    data = {
                        cx: _this.cx,
                        page: page ? page : 1,
                    }
                    let loadIndex = layer.load(0);
                    axios.post('/apiadmin.php?act=hnlist', data, {
                        emulateJSON: true
                    }).then(r => {
                        if (r.data.code === 1) {
                            if (r.data.data) {
                                _this.row = r.data;
                            } else {
                                _this.row.data = [];
                            }

                            if (type === 'one') {
                                layui.use('table', function() {
                                    var laypage = layui.laypage;
                                    laypage.render({
                                        elem: 'listTable_laypage', // 元素 id
                                        count: _this.row.count, // 数据总数
                                        limit: _this.row.pagesize,
                                        limits: [15, 30, 50, 100],
                                        curr: _this.row.current_page,
                                        layout: ['count', 'prev', 'page', 'next', 'limit'], // 功能布局
                                        prev: '<i class="layui-icon layui-icon-left"></i>',
                                        next: '<i class="layui-icon layui-icon-right"></i>',
                                        jump: function(obj, first) {
                                            if (!first) {
                                                _this.cx.pagesize = obj.limit;
                                                _this.hnlist(obj.curr, '');
                                            }
                                        }
                                    });
                                })

                            } else {}

                            _this.table_init();
                        } else {
                            layer.msg(r.data.msg);
                        }
                        layer.close(loadIndex);
                    })
                })
            },
            add_open: async function(type, d) {
                const _this = this;

                // await this.edit_init();

                _this.eform = JSON.parse(JSON.stringify(_this.eform2));
                if (type || _this.editT) {
                    _this.eform = {
                        status: Number(d.status) ? 'on' : 0,
                        title: d.title,
                        content: d.content,
                        author: d.author,
                        top: Number(d.top) ? 'on' : 0,
                    }
                }
                layui.use(function() {
                    layer.open({
                        type: 1,
                        content: $("#add"),
                        title: type ? '编辑' : '添加公告',
                        area: ["auto", '98%'],
                        maxmin: true,
                        btn: [type ? '修改' : '添加', '取消'],
                        scrollbar: false,
                        yes: function(index) {
                            layui.form.submit('add-form', function(data) {
                                if (type) {
                                    // 修改
                                    var formData = layui.form.val('add-form');
                                    data.field.status = formData.status ? 1 : 0;
                                    data.field.top = formData.top ? 1 : 0;
                                    _this.edit(d.id, data.field, index);
                                } else {
                                    // 添加
                                    data.field.status = data.field.status ? 1 : 0;
                                    data.field.top = data.field.top ? 1 : 0;
                                    _this.add(data.field, index);
                                }
                            })
                        },
                        success: function() {
                             layui.form.render();
                        },
                        end: function() {
                            for (let i in _this.eform) {
                                _this.eform[i] = '';
                            }
                            _this.eform.status = 1;
                            _this.editT = 0;
                            // _this.editIframe.destroy();
                            // _this.editIframe.clearStack();
                            // _this.editIframe = null;
                        }
                    })
                })
            },
            add: function(d, index) {
                const _this = this;
                layer.load(0);
                axios.post('/apiadmin.php?act=homenotice_add', d, {
                    emulateJSON: true
                }).then((r) => {
                    if (r.data && r.data.code === 1) {
                        layer.msg("添加成功！")
                        layer.close(index);
                        layer.closeAll('loading');
                        _this.hnlist(1);

                    } else {
                        layer.msg("添加失败！")
                    }
                })
            },
            edit: function(id, d, index) {
                const _this = this;
                let loadIndex = layer.load(0);
                data = {
                    id: id,
                    data: d
                };
                axios.post("/apiadmin.php?act=homenotice_up", data, {
                    emulateJSON: true
                }).then(r => {
                    if (r.data.code === 1) {
                        layer.msg('修改成功！')
                        layer.close(index);
                        _this.hnlist(1);
                    } else {
                        layer.msg(r.data.msg)
                    }
                    layer.close(loadIndex);
                })
            },
            del: function(id) {
                const _this = this;
                layui.use('table', function() {
                    let table = layui.table;
                    let checkData = table.checkStatus('listTable').data;

                    if (id) {
                        checkData = [{
                            id: id
                        }]
                    } else {
                        if (!checkData.length) {
                            layer.msg('请选择公告');
                            return
                        }
                    }

                    layer.confirm('是否删除？', {
                        title: '警告',
                        btn: ['删除', '算了'] //按钮
                    }, function(index) {
                        let loadIndex = layer.load(0);
                        _this.sex = checkData.map(item => item.id);
                        axios.post('/apiadmin.php?act=homenotice_del', {
                            sex: _this.sex
                        }, {
                            emulateJSON: true
                        }).then(r => {
                            layer.close(loadIndex);
                            if (r.data.code === 1) {
                                _this.hnlist(1);
                                layer.msg('删除成功');
                            } else {
                                layer.msg('删除失败');
                            }
                            layer.close(index);
                        })
                    }, function() {});

                })
            },
        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var LAY_appVm = app.mount('#app');
    // -----------------------------
</script>