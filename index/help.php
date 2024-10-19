<?php
$mod = 'blank';
$title = '帮助文档';
include('head.php');
?>

<div class="layui-padding-1" id="helpID" style="display:none;">
    <div class="layui-panel layui-padding-3">
        <div style="border-bottom: 1px solid var(--border-color-4);padding-bottom: 12px;">
            帮助文档&nbsp;<button type="button" class="layui-btn layui-btn-xs layui-btn-primary" @click="helplist"><i class="layui-icon layui-icon-refresh"></i></button>
        </div>
        <div style="padding-top: 10px;">
            <div v-if="row.data.length" class="layui-collapse" lay-filter="filter-collapse">
                <div class="layui-colla-item" v-for="(item,index) in row.data" :key="index">
                    <template v-if="Number(item.status)">
                        <div class="layui-colla-title" style="display: flex; align-items: center; justify-content: space-between;">
                            <p v-html="item.title"></p>
                            <p class="layui-font-12 layui-font-green">
                                <i class="layui-icon layui-icon-eye"></i> <span v-html="item.readUIDS"></span>
                            </p>
                        </div>
                        <div class="layui-colla-content layui-show">
                            <p class="layui-font-12 layui-font-green">
                                <span>更新时间：{{item.upTime}}</span>
                            </p>
                            <p v-html="item.content"></p>
                        </div>
                    </template>
                </div>
            </div>
            <div v-else class="layui-font-green layui-font-13">
                暂无文档
            </div>

        </div>
    </div>
</div>

<?php include($root . '/index/components/footer.php'); ?>

<script>
    const app = Vue.createApp({
        data() {
            return {
                uid: '<?= $userrow['uid'] ?>' === '1' ? true : false,
                row: {
                    data: [],
                }
            }
        },
        mounted() {
            const _this = this;
            let loadIndex = layer.load(0);
            $("#helpID").ready(() => {
                layer.close(loadIndex)
                $("#helpID").show();
                _this.helplist();
            })
        },
        methods: {
            helplist: function() {
                const _this = this;
                layui.use(function() {
                    let loadIndex = layer.load(0);
                    axios.post('/apiadmin.php?act=helplist', {
                        type: 1,
                    }, {
                        emulateJSON: true
                    }).then(r => {
                        if (r.data.code === 1) {
                            _this.row = r.data;
                            if (!r.data.data) {
                                _this.row.data = [];
                            }
                            setTimeout(() => {
                                layui.element.render('collapse', 'filter-collapse');
                                layer.close(loadIndex);
                            }, 100)
                        } else {
                            layer.close(loadIndex);
                            layer.msg('网络请求错误！');
                        }
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
    var vm = app.mount('#helpID');
    // -----------------------------
</script>