<?php
$title = '编辑商品网课';
require_once('head.php');
if ($userrow['uid'] != 1) {
    exit("<script language='javascript'>alert('权限不足')</script>");
}
?>

<style>
    .orderlistLable .layui-input {
        height: 27px !important;
    }

    .orderlistLable .layui-input-affix .layui-icon {
        font-size: 12px;
    }

    .orderlistLable .layui-input-affix {
        line-height: 20px;
        width: 28px;
        padding: 0;
    }

    .orderlistLable .layui-form-checkbox {
        zoom: .8;
        position: relative;
        top: -3px;
    }

    .layui-table-hover {
        box-shadow: 0px 0px 12px rgba(0, 0, 0, .12);
        position: relative;
    }
</style>

<div class="layui-padding-1">
    <div class="layui-panel" id="orderlistVM" style="display:none">

        <div class="panel-heading ">
            <div class="layui-panel layui-padding-2" style="display: flex; align-items: center;">
                商品列表&nbsp;
                <button type="button" class="layui-btn layui-btn-xs layui-btn-primary  layui-border-normal" @click="get(1,{classname:query_data.classname})">
                    <i class="layui-icon layui-icon-refresh"></i>
                </button>
                <button class="layui-btn layui-bg-blue layui-btn-sm" @click="modal_add_open">添加商品</button>
            </div>
            <span class="layui-font-13 layui-font-red">
                &nbsp;&nbsp;代理最终下单价格 = 价格系数 * 代理费率 ←- 乘法，加法同理
            </span>
            <div class="layui-padding-2" style="display: flex; align-items: center;margin:0 0 0;flex-wrap: wrap;overflow-x: auto;">

                <div style="display: inline-block;" class="layui-form">
                    <div class="layui-input-group">
                        <div style="width: 100px;margin-bottom: 5px;">
                            <select lay-append-to="body" lay-filter="search_fenlei_select">
                                <option value="">全部分类</option>
                                <?php
                                $a = $DB->query("select * from qingka_wangke_fenlei ORDER BY `sort` ASC");
                                while ($b = $DB->fetch($a)) {
                                    echo '<option value="' . $b['id'] . '">' . $b['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <input type="text" placeholder="请输入商品名称" @keydown.enter="get(1,{classname:query_data.classname,fenlei:query_data.fenlei})" class="layui-input " v-model="query_data.classname" lay-affix="clear">
                        <div class="layui-input-split layui-input-suffix" style="cursor: pointer;" @click="get(1,{classname:query_data.classname,fenlei:query_data.fenlei})">
                            <i class="layui-icon layui-icon-search"></i>
                        </div>
                    </div>
                </div>&nbsp;&nbsp;
                <button class="layui-btn layui-btn-primary layui-border-red layui-btn-sm" @click="get(row.current_page)">
                    重置
                </button>

                <div :style="windowWH.width<700?'display:block;width: 100%;':'flex:auto' ">
                    <div style="float: right;">
                        <div class="listTable_laypage" style="zoom: .8; width: max-content; transform-origin: right center;"></div>
                    </div>
                </div>

            </div>

            <div style="overflow-x: auto;padding-top: 0 !important;" class="layui-padding-2">

                <el-alert class="alert_width100 layui-font-12" title="" type="info" show-icon style="margin: 0 0 5px;">
                    <slot name="title">
                        <div class="layui-font-12">
                            更多扩展自定义，请点击 <button type="button" class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs" style="margin:0 0 0 0;width: max-content;scale: .8;"><i class="layui-icon layui-icon-edit"></i></button> 编辑！
                        </div>
                    </slot>
                </el-alert>

                <div style="overflow:hidden;margin-bottom:5px;">
                    <button type="button" class="layui-btn layui-bg-red layui-btn-xs " @click='del(orderlist_check().map(i=>i.cid))'>
                        <i class="layui-icon layui-icon-delete"></i> 批量删除
                    </button>
                    <!--{{orderlist_check}}-->
                    <button type="button" class="layui-btn layui-bg-red layui-btn-xs " @click='upclass_pl(orderlist_check().map(i=>i.cid))'>
                        <i class="layui-icon layui-icon-fonts-clear"></i> 批量修改
                    </button>
                    <button type="button" class="layui-btn layui-btn-xs " @click='getCloudStatus_go()'>
                        <i class="layui-icon layui-icon-chart"></i> 检测当前页商品上游状态
                    </button>
                </div>

                <div>
                    <table id="orderlist" layui-filter="orderlist"></table>
                </div>



            </div>

        </div>

        <div id="modal_up" style="display:none;">
            <form id="form-update" class="layui-padding-3 layui-form">
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="cid" :value="storeInfo.cid" />


                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        分类
                    </div>
                    <select v-model="storeInfo.fenlei" :value="storeInfo.fenlei" name="fenlei" lay-filter="fenlei_select" id="fenlei_select" class="layui-select">
                        <option value="">点我选择{{storeInfo.fenlei}}</option>
                        <?php
                        $a = $DB->query("select * from qingka_wangke_fenlei ORDER BY `sort` ASC");
                        while ($b = $DB->fetch($a)) {
                            echo '<option value="' . $b['id'] . '">' . $b['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        状态
                    </div>
                    <select v-model="storeInfo.status" :value="storeInfo.status" name="status" lay-filter="status_select" id="status_select" class="layui-select">
                        <option value="1">上架</option>
                        <option value="0">下架</option>
                    </select>
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        商品名称
                    </div>
                    <input :value="storeInfo.name" v-model="storeInfo.name" type="text" name="name" class="layui-input" placeholder="输入商品名称">
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        价格系数
                    </div>
                    <input v-model="storeInfo.price" :value="storeInfo.price" type="number" step="0.0001" name="price" class="layui-input" placeholder="输入价格系数">
                </div>
                <div class="layui-font-12 layui-font-red">
                    代理最终下单价格 = 价格系数 * 代理费率 ←- 乘法，加法同理
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        算法
                    </div>
                    <select v-model="storeInfo.yunsuan" :value="storeInfo.yunsuan" name="yunsuan" lay-filter="yunsuan_select" id="yunsuan_select" class="layui-select">
                        <option value="*">乘法</option>
                        <option value="+">加法</option>
                    </select>
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        查课参数
                    </div>
                    <input v-model="storeInfo.getnoun" :value="storeInfo.getnoun" type="text" name="getnoun" class="layui-input" placeholder="输入标识">
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        对接参数
                    </div>
                    <input v-model="storeInfo.noun" :value="storeInfo.noun" type="text" name="noun" class="layui-input" placeholder="输入标识">
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        查课商品
                    </div>
                    <select v-model="storeInfo.queryplat" :value="storeInfo.queryplat" name="queryplat" lay-filter="queryplat_select" id="queryplat_select" class="layui-select">
                        <option value="0">自营</option>
                        <?php
                        $a = $DB->query("select * from qingka_wangke_huoyuan");
                        while ($b = $DB->fetch($a)) {
                            echo '<option value="' . $b['hid'] . '">' . $b['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        对接商品
                    </div>
                    <select v-model="storeInfo.docking" :value="storeInfo.docking" name="docking" lay-filter="duijieplat_select" id="duijieplat_select" class="layui-select">
                        <option value="0">自营</option>
                        <?php
                        $a = $DB->query("select * from qingka_wangke_huoyuan");
                        while ($b = $DB->fetch($a)) {
                            echo '<option value="' . $b['hid'] . '">' . $b['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;vertical-align: top;">
                        商品说明
                    </div>
                    <div>
                        <textarea v-model="storeInfo.content" :value="storeInfo.content" name="content" class="layui-textarea" rows="2">

                        </textarea>
                        <span class="layui-font-12 layui-font-green">
                            {{storeInfo.content.trim().length}}
                        </span>
                    </div>
                </div>

                <fieldset class="layui-elem-field layui-field-title">
                    <legend class="layui-font-12">扩展</legend>
                </fieldset>

                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        无查下单
                    </div>
                    <select v-model="storeInfo.nocheck" :value="storeInfo.nocheck" name="nocheck" lay-filter="nocheck_select" id="nocheck_select" class="layui-select">
                        <option value="1">开启</option>
                        <option value="0">关闭</option>
                    </select>
                </div>

                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        支持改密
                    </div>
                    <select v-model="storeInfo.changePass" :value="storeInfo.changePass" name="changePass" lay-filter="changePass_select" id="changePass_select" class="layui-select">
                        <option value="1">开启</option>
                        <option value="0">关闭</option>
                    </select>
                </div>

            </form>
        </div>

        <div class="" id="modal_add" style="display:none;">
            <form id="form-add" class="layui-padding-3 layui-form">
                <input type="hidden" name="action" value="add" />
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        分类
                    </div>
                    <select name="fenlei" lay-filter="fenlei_select" id="fenlei_select" class="layui-select">
                        <option value="">点我选择</option>
                        <?php
                        $a = $DB->query("select * from qingka_wangke_fenlei ORDER BY `sort` ASC");
                        while ($b = $DB->fetch($a)) {
                            echo '<option value="' . $b['id'] . '">' . $b['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        状态
                    </div>
                    <select name="status" lay-filter="status_select" id="status_select" class="layui-select">
                        <option value="1">上架</option>
                        <option value="0">下架</option>
                    </select>
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        商品名称
                    </div>
                    <input type="text" name="name" class="layui-input" placeholder="输入商品名称">
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        价格系数
                    </div>
                    <input type="number" step="0.0001" name="price" class="layui-input" placeholder="输入价格系数">
                </div>
                <div class="layui-font-12 layui-font-red">
                    代理最终下单价格 = 价格系数 * 代理费率 ←- 乘法，加法同理
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        算法
                    </div>
                    <select name="yunsuan" lay-filter="yunsuan_select" id="yunsuan_select" class="layui-select">
                        <option value="*">乘法</option>
                        <option value="+">加法</option>
                    </select>
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        查课参数
                    </div>
                    <input type="number" step="1" name="getnoun" class="layui-input" placeholder="输入标识">
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        对接参数
                    </div>
                    <input type="number" step="1" name="noun" class="layui-input" placeholder="输入标识">
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        查课商品
                    </div>
                    <select name="queryplat" lay-filter="queryplat_select" id="queryplat_select" class="layui-select">
                        <option value="0">自营</option>
                        <?php
                        $a = $DB->query("select * from qingka_wangke_huoyuan");
                        while ($b = $DB->fetch($a)) {
                            echo '<option value="' . $b['hid'] . '">' . $b['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        对接商品
                    </div>
                    <select name="docking" lay-filter="duijieplat_select" id="duijieplat_select" class="layui-select">
                        <option value="0">自营</option>
                        <?php
                        $a = $DB->query("select * from qingka_wangke_huoyuan");
                        while ($b = $DB->fetch($a)) {
                            echo '<option value="' . $b['hid'] . '">' . $b['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        说明
                    </div>
                    <textarea name="content" class="layui-textarea" rows="2"></textarea>
                </div>
            </form>
        </div>
        
        <div id="pl_set_layer_content" class="layui-padding-2" style="display:none;">
            
            <form class="layui-form" lay-filter="pl_set_form_filter">
                
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        分类
                    </div>
                    <select name="fenlei" lay-filter="pl_set_form_fenlei" id="pl_set_form_fenlei" class="layui-select" lay-append-to="body">
                        <option value="">请选择，不选则不修改</option>
                        <?php
                        $a = $DB->query("select * from qingka_wangke_fenlei ORDER BY `sort` ASC");
                        while ($b = $DB->fetch($a)) {
                            echo '<option value="' . $b['id'] . '">' . $b['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        状态
                    </div>
                    <select name="status" lay-filter="pl_set_form_status" id="pl_set_form_status" class="layui-select" lay-append-to="body">
                        <option value="">请选择，不选则不修改</option>
                        <option value="1">上架</option>
                        <option value="0">下架</option>
                    </select>
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        价格系数
                    </div>
                    <input type="text" name="price" class="layui-input" placeholder="请填写，不填则不填写">
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        算法
                    </div>
                    <select name="yunsuan" lay-filter="pl_set_form_yunsuan" id="pl_set_form_yunsuan" class="layui-select" lay-append-to="body">
                        <option value="">请选择，不选则不修改</option>
                        <option value="*">乘法</option>
                        <option value="+">加法</option>
                    </select>
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        无查下单
                    </div>
                    <select name="nocheck" lay-filter="pl_set_form_nocheck" id="pl_set_form_nocheck" class="layui-select" lay-append-to="body">
                        <option value="">请选择，不选则不修改</option>
                        <option value="1">开启</option>
                        <option value="0">关闭</option>
                    </select>
                </div>
                <div class="layui-input-group" style="width: 100%;margin-bottom:10px;">
                    <div class="layui-input-prefix" style="width: 80px; text-align: right;">
                        支持改密
                    </div>
                    <select name="changePass" lay-filter="pl_set_form_changePass" id="pl_set_form_changePass" class="layui-select" lay-append-to="body">
                        <option value="">请选择，不选则不修改</option>
                        <option value="1">开启</option>
                        <option value="0">关闭</option>
                    </select>
                </div>
                
                <button id="pl_set_form_reset" type="reset" class="layui-btn layui-btn-primary" style="display: none;">重置</button>
            </form>
            
        </div>

    </div>
</div>


<?php include($root . '/index/components/footer.php'); ?>


<script type="text/html" id="listTable_user_caoz">
    <div style="display: flex; gap: 5px;display: flex; gap: 5px;margin-bottom: 5px;">
        <button {{= d.cid == vm.row.min_sort_cid ?'disabled':''}} title="置顶" lay-event="listTable_user_top" type="button" class="layui-btn layui-btn-primary layui-border-green layui-btn-xs {{= d.cid == vm.row.min_sort_cid ?'layui-btn-disabled':''}} " style="margin:0 0 0 0;width: max-content;">
            顶
        </button>
        <button {{= d.cid == vm.row.min_sort_cid ?'disabled':''}} title="上移" lay-event="listTable_user_up" type="button" class="layui-btn layui-btn-primary layui-border-green layui-btn-xs {{= d.cid == vm.row.min_sort_cid ?'layui-btn-disabled':''}} " style="margin:0 0 0 0;width: max-content;">
            <i class="layui-icon layui-icon-up"></i>
        </button>
        <button {{= d.cid == vm.row.max_sort_cid ?'disabled':''}} title="下移" lay-event="listTable_user_down" type="button" class="layui-btn layui-btn-primary layui-border-green layui-btn-xs {{= d.cid == vm.row.max_sort_cid ?'layui-btn-disabled':''}} " style="margin:0 0 0 0;width: max-content;">
            <i class="layui-icon layui-icon-down"></i>
        </button>
        <button {{= d.cid == vm.row.max_sort_cid ?'disabled':''}} title="置尾" lay-event="listTable_user_bottom" type="button" class="layui-btn layui-btn-primary layui-border-green layui-btn-xs {{= d.cid == vm.row.max_sort_cid ?'layui-btn-disabled':''}} " style="margin:0 0 0 0;width: max-content;">
            尾
        </button>

    </div>
    <div style="display: flex; gap: 5px;display: flex; gap: 5px;">
        <button title="编辑" lay-event="listTable_user_edit" type="button" class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs" style="margin:0;width: max-content;">
            <i class="layui-icon layui-icon-edit"></i>
        </button>
        <button title="删除" lay-event="listTable_user_del" type="button" class="layui-btn layui-btn-primary layui-border-red layui-btn-xs" style="margin:0 0 0 0;width: max-content;">
            <i class="layui-icon layui-icon-delete"></i>
        </button>
    </div>
</script>

<script>
    // window.myjQuery = window.jQuery.noConflict();
    // console.log($(window))
    const app = Vue.createApp({
        data() {
            return {
                uid: '<?= $userrow['uid'] === '1' ? true : false; ?>',
                orderlist_table: null,
                row: {
                    data: [],
                },
                storeInfo: {
                    content: '',
                },
                query_data: {
                    classname: '',
                    fenlei: '',
                },
                cx: {
                    pagesize: 15,
                },
                sex: [],
                now_scrollTop: 0,
                pl_set_layer: null,
            }
        },
        computed: {
            windowWH() {
                return {
                    width: $(window).width(),
                    height: $(window).height(),
                }
            },
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

            $("#orderlistVM").ready(function() {
                $('#orderlistVM').show()
                _this.get(1, '', "one");

                layui.form.on('select(search_fenlei_select)', (data) => {
                    var elem = data.elem; // 获得 radio 原始 DOM 对象
                    var checked = elem.checked; // 获得 radio 选中状态
                    var value = elem.value; // 获得 radio 值
                    _this.query_data.fenlei = value;
                    _this.get(1, _this.query_data);
                    // _this.addm.put = value;
                    // setTimeout(() => {
                    //     layui.form.render(null, "form-add");
                    // }, 0);
                })

            })

        },
        methods: {
            orderlist_check() {
                console.log("orderlist_check",layui.table.checkStatus("orderlist").data)
                return layui.table.checkStatus("orderlist").data;
            },
            ql_query_data: function() {
                const _this = this;
                for (let i in _this.query_data) {
                    _this.query_data[i] = '';
                }
            },
            modal_add_open: function() {
                const _this = this;
                layui.use(function() {
                    var $ = layui.jquery;
                    layer.open({
                        hideOnClose: true,
                        type: 1,
                        title: '添加商品',
                        area: ['360px', '90vh'],
                        content: $("#modal_add"),
                        btn: ['添加', '取消'],
                        success: function() {
                            setTimeout(() => {
                                layui.form.render($('#fenlei_select'));
                                layui.form.render($('#queryplat_select'));
                                layui.form.render($('#duijieplat_select'));
                                layui.form.render($('#status_select'));
                                layui.form.render($('#yunsuan_select'));
                                layui.form.render($('#nocheck_select'));
                                layui.form.render($('#changePass_select'));
                            }, 0)
                        },
                        yes: function(index) {
                            let addFormS = $("#form-add").serializeArray();
                            let new_addFormS = [];
                            for (let i in addFormS) {
                                new_addFormS[addFormS[i].name] = addFormS[i].value;
                            }
                            if (new_addFormS.name === '') {
                                layer.msg("请输入商品名称");
                            } else if (new_addFormS.price === '') {
                                layer.msg("请输入商品价格系数");
                            } else if (new_addFormS.fenlei === '') {
                                layer.msg("请输入商品分类");
                            } else {
                                vm.addForm('add');
                                layer.close(index);
                            }
                            return false;
                        }
                    })
                })
            },
            modal_up_open: function(res) {
                vm.storeInfo = res;
                let index1 = layui.use(function() {
                    var $ = layui.jquery;
                    layer.open({
                        hideOnClose: true,
                        type: 1,
                        title: '修改商品',
                        area: ['360px', '90vh'],
                        content: $("#modal_up"),
                        btn: ['修改', '取消'],
                        success: function() {
                            setTimeout(() => {
                                layui.form.render($('#fenlei_select'));
                                layui.form.render($('#queryplat_select'));
                                layui.form.render($('#duijieplat_select'));
                                layui.form.render($('#status_select'));
                                layui.form.render($('#yunsuan_select'));
                                layui.form.render($('#nocheck_select'));
                                layui.form.render($('#changePass_select'));
                            }, 0)
                        },
                        yes: function(index) {
                            vm.addForm('update');
                            layer.close(index);
                        }
                    })
                })
            },
            setClass: function(upm, res) {
                const _this = this;
                _this.storeInfo = upm;
                for (let i in res) {
                    _this.storeInfo[i] = res[i]
                }
                _this.form('update');
            },
            getCloudStatus_go() {
                const _this = this;
                layer.msg('开始检测...')
                _this.row.data.map(i => {
                    i.cloudStatus = '检测中...';
                })
                for (let i in _this.row.data) {
                    setTimeout(() => {
                        _this.getCloudStatus(_this.row.data[i].cid, _this.row.data[_this.row.data.length - 1].cid);
                    }, 300 * i)
                }
            },
            getCloudStatus(cid, last_cid) {
                const _this = this;
                if (cid === undefined || last_cid === undefined) {
                    layer.msg("未传入cid")
                    return false
                }
                axios.post("/apiadmin.php?act=get", {
                    cid: cid,
                    userinfo: "1 1 1",
                }, {
                    emulateJSON: true
                }).then(r => {
                    if (r.data.msg) {
                        if (r.data.msg.search(/已下架/) != -1) {
                            _this.row.data.map((i) => {
                                if (i.cid == cid) {
                                    i.cloudStatus = "已下架"
                                }
                            })
                        } else {
                            _this.row.data.map((i) => {
                                if (i.cid == cid) {
                                    i.cloudStatus = "正常"
                                }
                            })
                        }
                    } else {
                        _this.row.data.map((i) => {
                            if (i.cid == cid) {
                                i.cloudStatus = "未知"
                            }
                        })
                    }

                    // if(cid == last_cid){
                    //     layer.msg("检测完成")
                    // }

                    layui.use(() => {
                        _this.now_scrollTop = $(window).scrollTop();
                        layui.table.reloadData('orderlist')
                    })

                })
            },
            get: function(page, cdata, type) {
                const _this = this;
                _this.now_scrollTop = $(window).scrollTop();

                const jdata = {};
                jdata.page = page;
                if (cdata) {
                    for (let i in cdata) {
                        jdata[i] = cdata[i];
                    }
                } else {
                    _this.ql_query_data();
                }
                jdata.cx = _this.cx;

                var loadIndex = layer.load(0);
                axios.post("/apiadmin.php?act=classlist", jdata, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(loadIndex);
                    if (r.data.code == 1) {
                        layer.load(0);
                        _this.row = r.data;
                        layui.use(() => {
                            _this.orderlist_table = layui.table.render({
                                elem: '#orderlist',
                                id: "orderlist",
                                size: 'sm',
                                className: "orderlistLable",
                                text: {
                                    none: '哦吼一个商品都没得'
                                },
                                data: _this.row.data,
                                lineStyle: 'height: 80px;',
                                cols: [
                                    [{
                                            field: '',
                                            title: '排序/操作',
                                            align: 'center',
                                            fixed: 'left',
                                            width: 140,
                                            templet: '#listTable_user_caoz'
                                        },
                                        {
                                            type: 'checkbox',
                                            // 	fixed: 'left'
                                            hide: !_this.uid
                                        },
                                        {
                                            field: 'status',
                                            title: '状态',
                                            width: 95,
                                            align: 'center',
                                            templet: `<input lay-filter="orderlist_edit_b" type="checkbox" name="{{ d.cid }}" title="{{ d.status==1?"上架":"下架" }}" lay-skin="tag" data-type="status" data-cid="{{d.cid}}" {{d.status==1?"checked":""}}> `,
                                        },
                                        {
                                            field: 'cloudStatus',
                                            title: '上游状态',
                                            width: 70,
                                            align: 'center',
                                            templet: `<div>{{ d.cloudStatus?d.cloudStatus:'待检测' }}</div> `,
                                        },
                                        {
                                            field: 'cid',
                                            title: 'ID',
                                            width: 80,
                                            align: 'center',
                                        },
                                        // 	{
                                        // 		field: 'sort',
                                        // 		title: '排序',
                                        // 		width: 70,
                                        // 		align: 'center',
                                        // 	},
                                        // 	{
                                        // 		field: 'fenlei',
                                        // 		title: '分类',
                                        // 		width: 125,
                                        // 		align: 'center',
                                        // 		templet: `
                                        // 		<select style="width:90px;"  class="orderlist_edit_s" data-type="fenlei" data-cid="{{d.cid}}" lay-ignore>
                                        //                                         <?php
                                                                                    //                                         $a= $DB->query("select * from qingka_wangke_fenlei ORDER BY `sort` ASC");
                                                                                    //                                         while ($b = $DB->fetch($a)) {
                                                                                    //                                             echo '<option {{d.fenlei == '.$b['id'].'?"selected":""}}  value="' . $b['id'] . '">' . '['.$b['id'].']'.$b['name'] . '</option>';
                                                                                    //                                         }
                                                                                    //                                     
                                                                                    ?>
                                        //                                 </select>`,
                                        // 	},
                                        {
                                            field: 'name',
                                            title: '分类&商品名称',
                                            minWidth: 250,
                                            // 		width: 250,
                                            templet: `分类：&nbsp;&nbsp;<select style="width:140px;"  class="orderlist_edit_s" data-type="fenlei" data-cid="{{d.cid}}" lay-ignore>
                                                    <?php
                                                    $a = $DB->query("select * from qingka_wangke_fenlei ORDER BY `sort` ASC");
                                                    while ($b = $DB->fetch($a)) {
                                                        echo '<option {{d.fenlei == ' . $b['id'] . '?"selected":""}}  value="' . $b['id'] . '">' . '[' . $b['id'] . ']' . $b['name'] . '</option>';
                                                    }
                                                    ?>
                                            </select>
                                            <input lay-filter="orderlist_edit_i" class="layui-input" type="text" lay-affix="edit"  lay-options="{split: true}" value="{{d.name}}" data-type="name" data-cid="{{d.cid}}" >`
                                        },
                                        {
                                            field: 'price',
                                            title: '系数/算法',
                                            width: 100,
                                            align: 'center',
                                            templet: `<input lay-filter="orderlist_edit_i" class="layui-input" type="text" lay-affix="edit"  lay-options="{split: true}" value="{{d.price}}" data-type="price" data-cid="{{d.cid}}" >
    										<select style="width:75px;"  class="orderlist_edit_s" data-type="yunsuan" data-cid="{{d.cid}}" lay-ignore>
                                                    <option value="*" {{d.yunsuan == '*'?'selected':''}}>乘法</option>
                                                    <option value="%2B" {{d.yunsuan == '+'?'selected':''}}>加法</option>
                                            </select>`
                                        },
                                        {
                                            field: 'cx_name',
                                            title: '查课源/对接ID',
                                            align: 'center',
                                            width: 120,
                                            templet: `
    										<select style="width:90px;"  class="orderlist_edit_s" data-type="queryplat" data-cid="{{d.cid}}" lay-ignore>
                                                    <option value="0" {{d.queryplat == 0?'selected':''}}>[0]自营</option>
                                                    <?php
                                                    $a = $DB->query("select * from qingka_wangke_huoyuan");
                                                    while ($b = $DB->fetch($a)) {
                                                        echo '<option {{d.queryplat == ' . $b['hid'] . '?"selected":""}}  value="' . $b['hid'] . '">' . '[' . $b['hid'] . ']' . $b['name'] . '</option>';
                                                    }
                                                    ?>
                                            </select>
                                            <input lay-filter="orderlist_edit_i" class="layui-input" type="text" lay-affix="edit"  lay-options="{split: true}" value="{{d.getnoun}}" data-type="getnoun" data-cid="{{d.cid}}" >`,
                                        },
                                        {
                                            field: 'add_name',
                                            title: '交单源/对接ID',
                                            width: 120,
                                            templet: `
    										<select style="width:90px;"  class="orderlist_edit_s" data-type="docking" data-cid="{{d.cid}}" lay-ignore>
                                                    <option value="0" {{d.docking == 0?'selected':''}}>[0]自营</option>
                                                    <?php
                                                    $a = $DB->query("select * from qingka_wangke_huoyuan");
                                                    while ($b = $DB->fetch($a)) {
                                                        echo '<option {{d.docking == ' . $b['hid'] . '?"selected":""}}  value="' . $b['hid'] . '">' . '[' . $b['hid'] . ']' . $b['name'] . '</option>';
                                                    }
                                                    ?>
                                            </select>
                                            <input lay-filter="orderlist_edit_i" class="layui-input" type="text" lay-affix="edit"  lay-options="{split: true}" value="{{d.noun}}" data-type="noun" data-cid="{{d.cid}}" >`,
                                        },
                                        {
                                            field: 'nocheck',
                                            title: '无查下单',
                                            width: 90,
                                            align: 'center',
                                            templet: `
    										        <input lay-filter="orderlist_edit_b" type="checkbox" name="{{ d.cid }}" title="{{ d.nocheck==1?"开启":"关闭" }}" lay-skin="tag" data-type="nocheck" data-cid="{{d.cid}}" {{d.nocheck==1?"checked":""}}>`,
                                        },
                                        {
                                            field: 'changePass',
                                            title: '支持改密',
                                            width: 90,
                                            align: 'center',
                                            templet: `
    										        <input lay-filter="orderlist_edit_b" type="checkbox" name="{{ d.cid }}" title="{{ d.changePass==1?"开启":"关闭" }}" lay-skin="tag" data-type="changePass" data-cid="{{d.cid}}" {{d.changePass==1?"checked":""}}>`,
                                        },
                                        // 	{
                                        // 		field: 'getnoun',
                                        // 		title: '查课ID',
                                        // 		width: 140,
                                        // 		align: 'center',
                                        // 		templet: `<input lay-filter="orderlist_edit_i" class="layui-input" type="text" lay-affix="edit"  lay-options="{split: true}" value="{{d.getnoun}}" data-type="getnoun" data-cid="{{d.cid}}" >`
                                        // 	},
                                        // 	{
                                        // 		field: 'noun',
                                        // 		title: '对接ID',
                                        // 		width: 140,
                                        // 		align: 'center',
                                        // 		templet: `<input lay-filter="orderlist_edit_i" class="layui-input" type="text" lay-affix="edit"  lay-options="{split: true}" value="{{d.noun}}" data-type="noun" data-cid="{{d.cid}}" >`
                                        // 	},
                                        // 	{
                                        // 		field: 'yunsuan',
                                        // 		title: '计算',
                                        // 		width: 85,
                                        // 		align: 'center',
                                        // 		templet: `
                                        // 		<select style="width:55px;"  class="orderlist_edit_s" data-type="yunsuan" data-cid="{{d.cid}}" lay-ignore>
                                        //                                         <option value="*" {{d.yunsuan == '*'?'selected':''}}>乘法</option>
                                        //                                         <option value="%2B" {{d.yunsuan == '+'?'selected':''}}>加法</option>
                                        //                                 </select>`,

                                        // 	},
                                        {
                                            field: 'addtime',
                                            title: '添加时间',
                                            width: 180,
                                            align: 'center',
                                        },
                                    ]
                                ],
                                cellExpandedMode: 'tips',
                                even: true,
                                page: false, // 是否显示分页
                                done: function(res, curr, count) {
                                    layer.closeAll();
                                    let options = this;

                                    // 选择框
                                    let tableViewElem = this.elem.next();
                                    tableViewElem.find('.orderlist_edit_s').on('change', function() {
                                        let elem = this;
                                        var value = elem.value; // 获取选中项 value
                                        if (!value) {
                                            layer.msg('不能为空');
                                            return;
                                        };

                                        let cid = elem.dataset.cid;
                                        let type = elem.dataset.type;
                                        let thisData = _this.row.data.find(i => i.cid == cid);
                                        console.log(thisData, {
                                            [type]: value
                                        })
                                        _this.setClass(thisData, {
                                            [type]: value
                                        });
                                    });

                                    // 输入框
                                    layui.form.on('input-affix(orderlist_edit_i)', function(data) {
                                        var elem = data.elem; // 输入框
                                        var value = elem.value; // 输入框的值
                                        if (!value) {
                                            layer.msg('不能为空');
                                            return;
                                        };
                                        let cid = elem.dataset.cid;
                                        let type = elem.dataset.type;
                                        let thisData = _this.row.data.find(i => i.cid == cid);
                                        _this.setClass(thisData, {
                                            [type]: value
                                        });
                                    });
                                    // 切换按钮
                                    layui.form.on('checkbox(orderlist_edit_b)', function(data) {
                                        console.log(1)
                                        var elem = data.elem;
                                        var value = elem.value; // 输入框的值
                                        if (!value) {
                                            layer.msg('不能为空');
                                            return;
                                        };
                                        let cid = elem.dataset.cid;
                                        let type = elem.dataset.type;
                                        let thisData = _this.row.data.find(i => i.cid == cid);
                                        _this.setClass(thisData, {
                                            [type]: elem.checked ? 1 : 0
                                        });
                                    });

                                    $(window).scrollTop(_this.now_scrollTop);


                                },
                            })
                            
                            _this.orderlist_table.reloadData("orderlist",{deep: true})
                            
                            layui.table.on('tool(orderlist)', function(obj) {
                                let data = obj.data;
                                switch (obj.event) {
                                    case 'listTable_user_up':
                                        var load = layer.load(0);
                                        axios.post("/apiadmin.php?act=class_sort", {
                                            type: 'up',
                                            cid: data.cid
                                        }, {
                                            emulateJSON: true
                                        }).then(function(r) {
                                            layer.close(load);
                                            if (r.data.code == 1) {
                                                layer.msg("成功")
                                                _this.get(_this.row.current_page, _this.query_data);
                                            } else {
                                                _this.$message.error(r.data.msg ? r.data.msg : "异常")
                                            }
                                            // _this.hnlist();
                                        })
                                        break;
                                    case 'listTable_user_down':
                                        var load = layer.load(0);
                                        axios.post("/apiadmin.php?act=class_sort", {
                                            type: 'down',
                                            cid: data.cid
                                        }, {
                                            emulateJSON: true
                                        }).then(function(r) {
                                            layer.close(load);
                                            if (r.data.code == 1) {
                                                layer.msg("成功")
                                                _this.get(_this.row.current_page, _this.query_data);
                                            } else {
                                                _this.$message.error(r.data.msg ? r.data.msg : "异常")
                                            }
                                            // _this.hnlist();
                                        })
                                        break;
                                    case 'listTable_user_top':
                                        var load = layer.load(0);
                                        axios.post("/apiadmin.php?act=class_sort", {
                                            type: 'top',
                                            cid: data.cid
                                        }, {
                                            emulateJSON: true
                                        }).then(function(r) {
                                            layer.close(load);
                                            if (r.data.code == 1) {
                                                layer.msg("成功")
                                                _this.get(_this.row.current_page, _this.query_data);
                                            } else {
                                                _this.$message.error(r.data.msg ? r.data.msg : "异常")
                                            }
                                            // _this.hnlist();
                                        })
                                        break;
                                    case 'listTable_user_bottom':
                                        var load = layer.load(0);
                                        axios.post("/apiadmin.php?act=class_sort", {
                                            type: 'bottom',
                                            cid: data.cid
                                        }, {
                                            emulateJSON: true
                                        }).then(function(r) {
                                            layer.close(load);
                                            if (r.data.code == 1) {
                                                layer.msg("成功")
                                                _this.get(_this.row.current_page, _this.query_data);
                                            } else {
                                                _this.$message.error(r.data.msg ? r.data.msg : "异常")
                                            }
                                            // _this.hnlist();
                                        })
                                        break;
                                    case 'listTable_user_del':
                                        _this.del([data.cid]);
                                        break;
                                    case 'listTable_user_edit':
                                        _this.editT = 1;
                                        _this.modal_up_open(data);
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
                            layui.laypage.render({
                                elem: $(".listTable_laypage"), // 元素 id
                                count: _this.row.count, // 数据总数
                                limit: _this.row.pagesize,
                                limits: [15, 30, 50, 100, 500],
                                curr: _this.row.current_page,
                                layout: ['count', 'prev', 'page', 'next', 'limit'], // 功能布局
                                prev: '<i class="layui-icon layui-icon-left"></i>',
                                next: '<i class="layui-icon layui-icon-right"></i>',
                                jump: function(obj, first) {
                                    if (!first) {
                                        _this.cx.pagesize = obj.limit;
                                        _this.get(obj.curr, _this.query_data, '');
                                    }
                                }
                            });

                        })

                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            addForm: function(form_type) {
                const _this = this;
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=upclass", {
                    data: $("#form-" + form_type).serialize()
                }, {
                    emulateJSON: true
                }).then(function(data) {
                    layer.close(load);
                    if (data.data.code == 1) {
                        if (form_type === 'add') {
                            _this.get(_this.row.last_page, _this.query_data);
                        } else {
                            _this.get(_this.row.current_page, _this.query_data);
                        }
                        layer.msg(data.data.msg, {
                            icon: 1
                        });
                    } else {
                        layer.msg(data.data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            form: function(form) {
                const _this = this;
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=upclass", {
                    data: Object.keys(_this.storeInfo).map(key => key + '=' + _this.storeInfo[key]).join('&')
                }, {
                    emulateJSON: true
                }).then(function(data) {
                    layer.close(load);
                    if (data.data.code == 1) {
                        // _this.get(_this.row.current_page,_this.query_data);
                        layui.use(() => {
                            layui.table.reload('orderlist')
                        })
                        layer.msg(data.data.msg, {
                            icon: 1
                        });
                    } else {
                        layer.msg(data.data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            bs: function(oid) {
                layer.msg(oid);
            },
            del: function(sex) {
                console.log(sex);
                if (!sex || !sex.length) {
                    layer.msg("未选择商品");
                    return
                }
                const _this = this;
                layui.use(function() {
                    layer.confirm('确认删除？', {
                        btn: ['确定', '算了'] //按钮
                    }, function() {
                        var load = layer.load(0);
                        axios.post("/apiadmin.php?act=class_del", {
                            sex: sex
                        }, {
                            emulateJSON: true
                        }).then(function(data) {
                            layer.close(load);
                            if (data.data.code == 1) {
                                _this.get(_this.row.current_page, _this.query_data);
                                layer.msg(data.data.msg, {
                                    icon: 1
                                });
                            } else {
                                layer.msg(data.data.msg, {
                                    icon: 2
                                });
                            }
                        });
                    })
                })
            },
            upclass_pl(sex) {
                const _this = this;
                if (!sex.length) {
                    layer.msg("未选择商品");
                    return
                }
                _this.pl_set_layer = layer.open({
                    type: 1,
                    id: "pl_set_layer_id",
                    title: "批量修改",
                    width: ["360px"],
                    content: $("#pl_set_layer_content"),
                    success(){
                        layui.form.render();
                    },
                    end(){
                        $("#pl_set_form_reset").click();
                    },
                    btn: ["确认","取消"],
                    btn1(){
                        console.log("sex",_this.orderlist_check);
                        let data = layui.form.val("pl_set_form_filter");
                        data.sex = JSON.parse(JSON.stringify(sex));
                        console.log("sex2",_this.orderlist_check);
                        
                                layer.close(_this.pl_set_layer);
                        axios.post("/apiadmin.php?act=upclass_pl",data).then(r=>{
                            if(r.data.code == 1){
                                layer.close(_this.pl_set_layer);
                                _this.$message.success(r.data.msg ? r.data.msg : "修改成功");
                                _this.get(_this.row.current_page,_this.query_data);
                            }else{
                                _this.$message.error(r.data.msg ? r.data.msg : "异常");
                            }
                        })
                    },
                })
            },
        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#orderlistVM');
    // -----------------------------
</script>