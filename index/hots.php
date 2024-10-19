<?php
$mod = 'blank';
$title = '销量热榜';
require_once('head.php');
?>

<style>
    .el-col{
        margin-bottom: 10px;
    }
    .layui-card-header{
        
    }
    .demo{
        max-height: 95vh;
        min-height: 350px;
    }
    .h_img{
        width: auto;
        height: 40px;
        position: absolute;
        right: 5px;
        top: 2px;
        pointer-events: none;
    }
    .r_img{
        width: auto;
        height: 26px;
        position: absolute;
        right: 5px;
        top: 2px;
        pointer-events: none;
    }
    .li{
        display: flex; 
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        padding: 5px 0;
        position: relative;
    }
    .li .li_left{
        flex:auto;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .li .li_right{
        width: 60px;
        text-align: right;
    }
</style>

<div id="hotsID" style="display: none;">
    <div class="layui-padding-1">
        <el-row :gutter="10" >
            <!--<el-col  :xs="24" :sm="8" :md="6" >-->
            <!--    <div class="layui-panel demo">-->
            <!--        <div class="layui-card-header">-->
            <!--            代理热力榜-->
                        <!--<img class="h_img" src="/assets/images/马蹄莲.svg">-->
            <!--        </div>-->
            <!--        <div class="layui-card-body">-->
            <!--            <span v-if="!daili_data.length">-->
            <!--                加载中...-->
            <!--            </span>-->
            <!--            <ul v-else>-->
            <!--                <template v-for="(item,index) in daili_data" :key="index" >-->
            <!--                    <li class="li" >-->
            <!--                        <div class="li_left">-->
            <!--                            {{item.name}}-->
            <!--                        </div>-->
            <!--                        <div  class="li_right">-->
            <!--                            <span v-if="index === 0">-->
            <!--                                <img class="r_img" src="/assets/images/no1.svg">-->
            <!--                            </span>-->
            <!--                            <span v-else-if="index === 1">-->
            <!--                                <img class="r_img" src="/assets/images/no2.svg">-->
            <!--                            </span>-->
            <!--                            <span v-else-if="index === 2">-->
            <!--                                <img class="r_img" src="/assets/images/no3.svg">-->
            <!--                            </span>-->
            <!--                            <span v-else>-->
            <!--                                --->
            <!--                            </span>-->
            <!--                        </div>-->
            <!--                    </li>-->
            <!--                    <hr class="margin0" />-->
            <!--                </template>-->
            <!--            </ul>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</el-col>-->
            <el-col  :xs="24" :sm="8" :md="6" >
                <div  class="layui-panel demo">
                    <div class="layui-card-header">
                        今日销量榜
                        <!--<img class="h_img" src="/assets/images/花束.svg">-->
                    </div>
                    <div class="layui-card-body">
                        <span v-if="!today_data.length">
                            加载中...
                        </span>
                        <ul v-else>
                            <template v-for="(item,index) in today_data" :key="index">
                                <li class="li" >
                                    <div class="li_left">
                                        {{item.name}}
                                    </div>
                                    <div  class="li_right">
                                        <span v-if="index === 0">
                                            <i class="fa-solid fa-fire" style="color: red;"></i>
                                        </span>
                                        <span v-else-if="index === 1">
                                            <i class="fa-solid fa-star" style="color: cornflowerblue;"></i>
                                        </span>
                                        <span v-else-if="index === 2">
                                            <i class="fa-solid fa-star-half-stroke" style="color: cadetblue;"></i>
                                        </span>
                                        <span v-else>
                                            -
                                        </span>
                                    </div>
                                </li>
                                <hr class="margin0" />
                            </template>
                        </ul>
                    </div>
                </div>
            </el-col>
            <el-col  :xs="24" :sm="8" :md="6" >
                <div  class="layui-panel demo">
                    <div class="layui-card-header">
                        总销量榜
                        <!--<img class="h_img" src="/assets/images/盆栽.svg">-->
                    </div>
                    <div class="layui-card-body">
                        <span v-if="!all_data.length">
                            加载中...
                        </span>
                        <ul v-else>
                            <template v-for="(item,index) in all_data" :key="index">
                                <li class="li" >
                                    <div class="li_left">
                                        {{item.name}}
                                    </div>
                                    <div  class="li_right">
                                        <span v-if="index === 0">
                                            <i class="fa-solid fa-fire" style="color: red;"></i>
                                        </span>
                                        <span v-else-if="index === 1">
                                            <i class="fa-solid fa-star" style="color: cornflowerblue;"></i>
                                        </span>
                                        <span v-else-if="index === 2">
                                            <i class="fa-solid fa-star-half-stroke" style="color: cadetblue;"></i>
                                        </span>
                                        <span v-else>
                                            -
                                        </span>
                                    </div>
                                </li>
                                <hr class="margin0" />
                            </template>
                        </ul>
                    </div>
                </div>
            </el-col>
            <el-col  :xs="24" :sm="8" :md="6" >
                <div  class="layui-panel demo">
                    <div class="layui-card-header">
                        本周销量榜
                        <!--<img class="h_img" src="/assets/images/植物.svg">-->
                    </div>
                    <div class="layui-card-body">
                        <span v-if="!week_data.length">
                            加载中...
                        </span>
                        <ul v-else>
                            <template v-for="(item,index) in week_data" :key="index">
                                <li class="li" >
                                    <div class="li_left">
                                        {{item.name}}
                                    </div>
                                    <div  class="li_right">
                                        <span v-if="index === 0">
                                            <i class="fa-solid fa-fire" style="color: red;"></i>
                                        </span>
                                        <span v-else-if="index === 1">
                                            <i class="fa-solid fa-star" style="color: cornflowerblue;"></i>
                                        </span>
                                        <span v-else-if="index === 2">
                                            <i class="fa-solid fa-star-half-stroke" style="color: cadetblue;"></i>
                                        </span>
                                        <span v-else>
                                            -
                                            <!--{{item.orderNum}}-->
                                        </span>
                                    </div>
                                </li>
                                <hr class="margin0" />
                            </template>
                        </ul>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
</div>

<script>
    const app = Vue.createApp({
        data(){
            return{
                daili_data: {
                    
                },
                today_data: [
                
                ],
                all_data: {
                    
                },
                week_data: {
                    
                },
            }
        },
        mounted(){
            const _this = this;
            
            let loadIndex = layer.load(0);
            $("#hotsID").ready(()=>{
                layer.close(loadIndex);
                $("#hotsID").show();
                _this.getHots('daili');
                _this.getHots();
                setTimeout(()=>{
                    _this.getHots('all');
                    setTimeout(()=>{
                        _this.getHots('week');
                    },200);
                },200);
            })
            
        },
        methods: {
            getHots(type='today'){
                const _this = this;
                let loadIndex = layer.load(0);
                axios.post('/apiadmin.php?act=hotsList',{
                    type: type,
                },{emulateJSON:true}).then(r=>{
                    layer.close(loadIndex);
                    if(r.data.code == 1){
                        _this[`${type}_data`] = r.data.data;
                    }else{
                        layer.msg(r.data.msg?r.data.msg:'网络异常');
                    }
                })
            }
        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var hotsIDVM = app.mount('#hotsID');
    // -----------------------------
</script>