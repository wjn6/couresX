<?php
include('../confing/common.php');
include('components/jscss.php');
?>

<!DOCTYPE html>


<div id="monitor" style="display: none;">
    <div class="layui-panel">
        <div style="padding: 15px;">
            <h3 class="center"><i class="layui-icon layui-icon-chart"></i> 监控中心</h3>
        </div>
    </div>
    <!--device().mobile-->
    <div class="layui-panel" style="margin:5px 0;">
        <div style="padding: 10px 13px;font-size:12px;">
            8秒自动更新一次
        </div>
    </div>
    <div class="monitorBox">

        <el-collapse style="min-height:100px" v-loading="!list.length" :value='!device().mobile?[0,1,2,3]:[0]' class="layui-collapse layui-row layui-col-space2">
            <el-row>
                <el-col :xs="24" :sm="8" :md="6" v-if="list.length>0" v-for="(item,index) in list" :key="index">
                    <el-collapse-item class=" layui-colla-item" :name="index" style="margin: 0 2px 5px;">
                        <template #title>
                            <i class="layui-icon layui-icon-loading-1 layui-anim layui-anim-rotate layui-anim-loop"></i>&nbsp;{{item.type}}&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:red;">监控中</font>
                        </template>
                        <div style="max-height:400px;overflow: auto;">

                            <template v-if="index==1 && !uid">
                                <pre class="layui-code code-demo" lay-options="{theme: 'dark'}" style="white-space: pre-line;">
        						    权限不足 ，不允查看
        						</pre>
                            </template>
                            <template v-else>
                                <pre class="layui-code code-demo" lay-options="{theme: 'dark'}" style="white-space: pre-line;">
                                {{item.log?item.log:'暂无'}}
                                </pre>
                            </template>

                        </div>
                    </el-collapse-item>
                </el-col>
            </el-row>
        </el-collapse>

    </div>

</div>

<script src="https://lib.baomitu.com/axios/1.6.7/axios.min.js"></script>

<?php include($root.'/index/components/footer.php'); ?>

<script>
    function device() {
        return layui.device()
    }
    
    const app = Vue.createApp({
        data(){
            return{
                uid: '<?= $userrow['uid'] ?>' === '1' ? true : false,
                list: [],
            }
        },
        mounted() {
            const _this = this;
            $("#monitor").ready(()=>{
                $("#monitor").show();
                _this.fetchMonitor();
                setInterval(this.fetchMonitor, 8000);
            })
        },
        methods: {
            device() {
                return layui.device()
            },
            fetchMonitor: function() {
                const _this = this;
                axios.get('/redis/monitor.php')
                    .then(function(response) {
                        _this.list = response.data
                    })
                    .catch(function(error) {
                        layer.msg('数据加载失败！');
                        console.error('Error fetching monitor data:', error);
                    });
            }
        }
    })
    
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#monitor');
    // -----------------------------
</script>
<style>
    #monitor {
        min-height: 100vh;
        margin: 10px;
    }

    .monitorBox {
        background: #fff;
    }

    .layui-collapse {
        border: 0;
    }

    .el-collapse-item {
        margin: 0 0 10px;
        border: 1px solid #ebeef5;
        border-top: 1px solid #ebeef5 !important;
    }

    .el-collapse-item__header {
        padding: 0 5px;
    }

    .el-collapse-item__content {
        padding-bottom: 0;
    }

    @media screen and (min-width: 768px) {
        .layui-col-sm3 {
            width: 24% !important;
        }
    }
</style>