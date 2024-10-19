<?php
include_once('head.php');

if ($userrow['uid'] != 1) {
    alert("您的账号无权限！", "index.php");
    exit();
}

?>

<style>
    .xPanel {
        height: 340px;
        overflow-y: auto;
    }

    .xELRow>.el-col {
        margin-bottom: 5px;
    }

    .colCenter>.el-col {
        text-align: center;
    }

    .chartBox {
        height: 100%;
        min-height: 260px;
        padding: 10px;
    }

    .layui-panel {
        height: -webkit-fill-available;
    }

    .layui-card-header {
        font-weight: bold;
    }

    .statistic-card {
        height: 100%;
        padding: 20px 5px;
        border-radius: 4px;
        background-color: var(--el-bg-color-overlay);
    }

    .statistic-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        font-size: 12px;
        color: var(--el-text-color-regular);
        margin-top: 10px;
    }

    .statistic-footer .footer-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .statistic-footer .footer-item span:last-child {
        display: inline-flex;
        align-items: center;
        margin-left: 4px;
    }

    .lrInfo>div {
        color: #ffffff;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        border-radius: 0px;
        overflow: hidden;
        font-size: 12px;
        margin-right: 5px;
        margin-bottom: 3px;
        box-shadow: 0px 0px 6px rgba(0, 0, 0, .12);
    }

    .lrInfo>div>div:first-child {
        background: #555555;
        padding: 2px 5px;
    }

    .lrInfo>div>div:nth-child(2) {
        background: #4eb1ba;
        padding: 2px 5px;
    }
</style>

<div id="XChartsID" class="layui-padding-1" style="display:none;">

    <div class="">

        <div class="layui-panel layui-padding-2 layui-hide-sm" style="margin-bottom: 5px;">
            请使用PC浏览器访问！
        </div>

        <el-row class="xELRow" :gutter="5">

            <el-col class="xPanel" v-if="JSON.stringify(row) != '{}'" :xs="24" :md="7">
                <div class=" layui-panel layui-padding-2">
                    <div class="lrInfo">

                        <div v-for="(item,index) in lrInfo_list" :key="index">
                            <div>
                                {{item.name}}
                            </div>
                            <div>
                                {{item.info}}
                            </div>
                        </div>

                    </div>
                    <hr>
                    <el-row class="colCenter" :gutter="0">
                        <el-col :xs="8" :sm="8">
                            <el-progress :striped="true" type="circle" width="90" :percentage="osIfnoData.cpu" :stroke-width="4" :indeterminate="true">
                                <template #default="{ percentage }">
                                    <div class="percentage-value layui-font-16">{{ percentage }}%</div>
                                    <div class="percentage-label layui-font-12" style="scale: .9;margin-top: 5px;color: #aaaaaa;">CPU</div>
                                </template>
                            </el-progress>
                        </el-col>
                        <el-col :xs="8" :sm="8">
                            <el-progress type="circle" width="90" :percentage="osIfnoData.fz" :stroke-width="4" :indeterminate="true">
                                <template #default="{ percentage }">
                                    <div class="percentage-value layui-font-16">{{ percentage }}%</div>
                                    <div class="percentage-label layui-font-12" style="scale: .9;margin-top: 5px;color: #aaaaaa;">负载</div>
                                </template>
                            </el-progress>
                        </el-col>
                        <el-col :xs="8" :sm="8">
                            <el-progress type="circle" width="90" :percentage="osIfnoData.nc" :stroke-width="4" :indeterminate="true">
                                <template #default="{ percentage }">
                                    <div class="percentage-value layui-font-16">{{ percentage }}%</div>
                                    <div class="percentage-label layui-font-12" style="scale: .9;margin-top: 5px;color: #aaaaaa;">内存</div>
                                </template>
                            </el-progress>
                        </el-col>
                    </el-row>
                    <div class="layui-font-12 layui-font-green center" style="scale: .8;">
                        <?php
                        if (stripos(php_uname(), 'Linux') !== false) {
                            echo shell_exec('grep "model name" /proc/cpuinfo | head -n 1 | cut -d ":" -f2 | xargs') . " " . trim(shell_exec('grep -c "^processor" /proc/cpuinfo')) . "核 " . round(shell_exec('free -m | awk \'NR==2{printf "%.2f", $2}\'') / 1024, 2) . "G";
                        } else if (stripos(php_uname(), 'window') !== false) {
                        }
                        ?>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">

                        <div class="statistic-card">
                            <el-statistic :value="row.dd[`dd_${dropdownName.dd}_count`]">
                                <template #title>
                                    <div style="display: inline-flex; align-items: center">
                                        {{dropdownNameList.find(i=>i.type == dropdownName.dd).a}}单量
                                        <el-tooltip
                                            effect="dark"
                                            :content="dropdownNameList.find(i=>i.type == dropdownName.dd).b+'单量：'+row.dd[`dd_last${dropdownName.dd}_count`]"
                                            placement="top">
                                            <el-icon style="margin-left: 4px" :size="12">
                                                <Warning />
                                            </el-icon>
                                        </el-tooltip>
                                    </div>
                                </template>
                            </el-statistic>
                            <div class="statistic-footer">
                                <div class="footer-item">
                                    <span>比{{dropdownNameList.find(i=>i.type == dropdownName.dd).b}}</span>
                                    <template v-if="percent(row.dd[`dd_${dropdownName.dd}_count`],row.dd[`dd_last${dropdownName.dd}_count`])>0">
                                        <span class="layui-font-orange">
                                            {{ percent(row.dd[`dd_${dropdownName.dd}_count`],row.dd[`dd_last${dropdownName.dd}_count`]) }}%
                                            <el-icon>
                                                <Caret-Top />
                                            </el-icon>
                                        </span>
                                    </template>
                                    <template v-else-if="!percent(row.dd[`dd_${dropdownName.dd}_count`],row.dd[`dd_last${dropdownName.dd}_count`])">
                                        <span class="layui-font-gray">
                                            <el-icon>
                                                <Semi-Select />
                                            </el-icon>
                                        </span>
                                    </template>
                                    <template v-else>
                                        <span class="layui-font-red">
                                            {{ percent(row.dd[`dd_${dropdownName.dd}_count`],row.dd[`dd_last${dropdownName.dd}_count`]) }}%
                                            <el-icon>
                                                <Caret-Bottom />
                                            </el-icon>
                                        </span>
                                    </template>
                                </div>
                            </div>
                            <el-dropdown size="small" split-button style="margin-top: 2px;">
                                {{dropdownNameList.find(i=>i.type == dropdownName.dd).text}}
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item @click="dropdownName.dd = 'day' ">
                                            按天
                                        </el-dropdown-item>
                                        <el-dropdown-item @click="dropdownName.dd = 'week' ">
                                            按周
                                        </el-dropdown-item>
                                        <el-dropdown-item @click="dropdownName.dd = 'month' ">
                                            按月
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </div>

                        <div class="statistic-card">
                            <el-statistic :value="row.cz[`cz_${dropdownName.cz}_money`]" precision="2">
                                <template #title>
                                    <div style="display: inline-flex; align-items: center">
                                        {{dropdownNameList.find(i=>i.type == dropdownName.cz).a}}收入
                                        <el-tooltip
                                            effect="dark"
                                            :content="dropdownNameList.find(i=>i.type == dropdownName.cz).b+'收入：'+row.cz[`cz_last${dropdownName.cz}_money`]"
                                            placement="top">
                                            <el-icon style="margin-left: 4px" :size="12">
                                                <Warning />
                                            </el-icon>
                                        </el-tooltip>
                                    </div>
                                </template>
                            </el-statistic>
                            <div class="statistic-footer">
                                <div class="footer-item">
                                    <span>比{{dropdownNameList.find(i=>i.type == dropdownName.cz).b}}</span>
                                    <template v-if="percent(row.cz[`cz_${dropdownName.cz}_money`],row.cz[`cz_last${dropdownName.cz}_money`])>0">
                                        <span class="layui-font-orange">
                                            {{ percent(row.cz[`cz_${dropdownName.cz}_money`],row.cz[`cz_last${dropdownName.cz}_money`]) }}%
                                            <el-icon>
                                                <Caret-Top />
                                            </el-icon>
                                        </span>
                                    </template>
                                    <template v-else-if="!percent(row.cz[`cz_${dropdownName.cz}_money`],row.cz[`cz_last${dropdownName.cz}_money`])">
                                        <span class="layui-font-gray">
                                            <el-icon>
                                                <Semi-Select />
                                            </el-icon>
                                        </span>
                                    </template>
                                    <template v-else>
                                        <span class="layui-font-red">
                                            {{ percent(row.cz[`cz_${dropdownName.cz}_money`],row.cz[`cz_last${dropdownName.cz}_money`]) }}%
                                            <el-icon>
                                                <Caret-Bottom />
                                            </el-icon>
                                        </span>
                                    </template>
                                </div>
                            </div>
                            <el-dropdown size="small" split-button style="margin-top: 2px;">
                                {{dropdownNameList.find(i=>i.type == dropdownName.cz).text}}
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item @click="dropdownName.cz = 'day' ">
                                            按天
                                        </el-dropdown-item>
                                        <el-dropdown-item @click="dropdownName.cz = 'week' ">
                                            按周
                                        </el-dropdown-item>
                                        <el-dropdown-item @click="dropdownName.cz = 'month' ">
                                            按月
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </div>

                        <div class="statistic-card">
                            <el-statistic :value="row.dd.dd_day_count">
                                <template #title>
                                    <div style="display: inline-flex; align-items: center">
                                        今日开通代理
                                        <el-tooltip
                                            effect="dark"
                                            :content="'昨日单量：'+row.dd.dd_lastday_count"
                                            placement="top">
                                            <el-icon style="margin-left: 4px" :size="12">
                                                <Warning />
                                            </el-icon>
                                        </el-tooltip>
                                    </div>
                                </template>
                            </el-statistic>
                            <div class="statistic-footer">
                                <div class="footer-item">
                                    <span>比昨日</span>
                                    <template v-if="percent(row.dd.dd_day_count,row.dd.dd_lastday_count)>0">
                                        <span class="layui-font-orange">
                                            {{ percent(row.dd.dd_day_count,row.dd.dd_lastday_count) }}%
                                            <el-icon>
                                                <Caret-Top />
                                            </el-icon>
                                        </span>
                                    </template>
                                    <template v-else-if="!percent(row.dd.dd_day_count,row.dd.dd_lastday_count)">
                                        <span class="layui-font-gray">
                                            <el-icon>
                                                <Semi-Select />
                                            </el-icon>
                                        </span>
                                    </template>
                                    <template v-else>
                                        <span class="layui-font-red">
                                            {{ percent(row.dd.dd_day_count,row.dd.dd_lastday_count) }}%
                                            <el-icon>
                                                <Caret-Bottom />
                                            </el-icon>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </el-col>

            <el-col class="xPanel" :xs="24" :md="17">
                <div class="layui-panel layui-padding-1">

                    <el-tabs v-model="tabs1_active" class="demo-tabs">

                        <el-tab-pane label="分类商品数量" name="fenlei" lazy>
                            <div id="fenlei_chart_ID" class="chartBox"></div>
                        </el-tab-pane>

                        <el-tab-pane label="商品单量" name="glass" lazy>
                            2
                        </el-tab-pane>

                    </el-tabs>

                </div>
            </el-col>

            <el-col :xs="24" :sm="24">
                <div class="layui-panel ">
                    <div id="moreLine_chart_ID" class="chartBox"></div>
                </div>
            </el-col>

        </el-row>

    </div>

</div>

<script>
    const app = Vue.createApp({
        data() {
            return {
                window: window,
                lrInfo_list: [{
                        name: 'CourseX',
                        info: '<?= $conf["version"] ?>',
                    },
                    {
                        name: '系统',
                        info: '<?= php_uname('s') . ' ' . php_uname('m') ?>',
                    }, {
                        name: 'PHP',
                        info: '<?= PHP_VERSION ?>',
                    },
                    {
                        name: 'Mysql',
                        info: '<?= str_replace('-log', '', $DB->get_row("select VERSION() as version")["version"]); ?>',
                    },
                    {
                        name: 'Redis',
                        info: '<?php
                                $redis = new Redis();
                                if ($redis->connect('127.0.0.1', 6379)) {
                                    $info = $redis->info();
                                    echo $info['redis_version'];
                                } else {
                                    echo '获取失败';
                                }
                                $redis->close();
                                ?>',
                    },
                    {
                        name: 'Nginx',
                        info: '<?php
                                if (preg_match('/nginx version: nginx\/([\d\.]+)/', shell_exec("nginx -v 2>&1"), $matches)) {
                                    echo $matches[1];
                                } else {
                                    echo '获取失败';
                                }
                                ?>',
                    },
                ],
                row: {

                },
                dropdownName: {
                    dd: 'day',
                    cz: 'day',
                    dl: 'day',
                },
                dropdownNameList: [{
                        type: "day",
                        text: '按天',
                        a: '今日',
                        b: '昨日',
                    },
                    {
                        type: "week",
                        text: '按周',
                        a: '本周',
                        b: '上周',
                    },
                    {
                        type: "month",
                        text: '按月',
                        a: '本月',
                        b: '上月',
                    },
                ],
                osIfnoData: {
                    cpu: 0,
                    fz: 0,
                    nc: 0,
                },
                fenlei_chart_option: {
                    series: [{
                        type: 'treemap',
                        leafDepth: 1,
                        data: [{
                            name: 'nodeA',
                            value: 10,
                        }]
                    }]
                },
                tabs1_active: "fenlei",
                moreLine_chart_option: {
                    title: {
                        text: '数据分析'
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['Email', 'Union Ads', 'Video Ads', 'Direct', 'Search Engine']
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    toolbox: {
                        feature: {
                            saveAsImage: {}
                        }
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [{
                            name: 'Email',
                            type: 'line',
                            stack: 'Total',
                            data: [120, 132, 101, 134, 90, 230, 210]
                        },
                        {
                            name: 'Union Ads',
                            type: 'line',
                            stack: 'Total',
                            data: [220, 182, 191, 234, 290, 330, 310]
                        },
                        {
                            name: 'Video Ads',
                            type: 'line',
                            stack: 'Total',
                            data: [150, 232, 201, 154, 190, 330, 410]
                        },
                        {
                            name: 'Direct',
                            type: 'line',
                            stack: 'Total',
                            data: [320, 332, 301, 334, 390, 330, 320]
                        },
                        {
                            name: 'Search Engine',
                            type: 'line',
                            stack: 'Total',
                            data: [820, 932, 901, 934, 1290, 1330, 1320]
                        }
                    ]
                }
            }
        },
        computed: {
            percent() {
                return (now, old) => {
                    let result = 0;
                    if (now && old) {
                        result = (now - old) / old * 100;
                    } else if (now && !old) {
                        result = (now - old) / 1 * 100;
                    } else if (!now && old) {
                        result = (now - old) / 1 * 100;
                    }

                    return result.toFixed(0)
                }
            },
        },
        mounted() {
            const _this = this;

            layer.load(0)
            $("#XChartsID").ready(() => {
                _this.get();
                _this.osIfno();
                setInterval(() => {
                    _this.osIfno();
                }, 4000)
            })
        },
        methods: {
            async get() {
                const _this = this;
                const r = await axios.post('/api/XCharts.php?act=XChartsData');
                if (r.data.code == 1) {
                    _this.row = r.data.data;
                    console.log('r', _this.row)
                } else {

                }
                layer.closeAll("loading")
                $("#XChartsID").show();
                _this.fenlei_chart_init();
                _this.moreLine_chart_init();
            },
            osIfno() {
                const _this = this;
                axios.post('/apiadmin.php?act=osIfno', {}, {
                    emulateJSON: true
                }).then(r => {
                    if (r.data.code === 1) {
                        for (let i in r.data.msg) {
                            r.data.msg[i] = r.data.msg[i].toFixed(1)
                        }
                        _this.osIfnoData = r.data.data;
                        console.log(111,r.data)
                    } else {
                        layer.msg('获取系统状态失败')
                    }
                })
            },
            fenlei_chart_init() {
                const _this = this;

                let chartDom = document.getElementById('fenlei_chart_ID');
                let fenlei_chart = echarts.init(chartDom);
                // console.log(8,fenlei_chart)
                for (let i in _this.row.fenlei.fenlei_list) {
                    _this.fenlei_chart_option.series[0].data[i] = {
                        name: `${_this.row.fenlei.fenlei_list[i].name}`,
                        value: _this.row.fenlei.fenlei_list[i].cnum,
                        children: [],
                    }
                    for (let j in _this.row.fenlei.fenlei_list[i].cdata) {
                        _this.fenlei_chart_option.series[0].data[i].children[j] = {
                            name: _this.row.fenlei.fenlei_list[i].cdata[j].name,
                            value: 10,
                        };
                    }
                }
                console.log(_this.fenlei_chart_option)
                fenlei_chart.setOption(_this.fenlei_chart_option);
            },
            moreLine_chart_init() {
                const _this = this;

                let chartDom = document.getElementById('moreLine_chart_ID');
                let fenlei_chart = echarts.init(chartDom);
                // console.log(8,fenlei_chart)
                // for (let i in _this.row.fenlei.fenlei_list) {
                //     _this.fenlei_chart_option.series[0].data[i] = {
                //         name: `${_this.row.fenlei.fenlei_list[i].name}`,
                //         value: _this.row.fenlei.fenlei_list[i].cnum,
                //         children: [],
                //     }
                //     for (let j in _this.row.fenlei.fenlei_list[i].cdata) {
                //         _this.fenlei_chart_option.series[0].data[i].children[j] = {
                //             name: _this.row.fenlei.fenlei_list[i].cdata[j].name,
                //             value: 10,
                //         };
                //     }
                // }
                console.log(_this.moreLine_chart_init)
                fenlei_chart.setOption(_this.moreLine_chart_option);
            },
        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#XChartsID');
    // -----------------------------
</script>