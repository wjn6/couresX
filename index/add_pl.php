<?php
$title = '提交订单';
require_once('head.php');
$addsalt = md5(mt_rand(0, 999) . time());
$_SESSION['addsalt'] = $addsalt;

?>

<script src="assets/js/aes.js"></script>

<style>
    .el-col {
        margin-bottom: 5px;
    }

    .lioverhide {
        width: 300px
    }

    .zdyClass {
        margin-left: 0 !important;
        margin-right: 0;
        width: fit-content;
    }

    .zdyClass input {
        top: 2px;
        position: relative;
    }

    .zdyClass span {
        margin-left: 0px;
    }

    .layui-form-checkbox {
        width: -webkit-fill-available;
    }

    .layui-form-checkbox div {
        overflow: auto;
        width: -webkit-fill-available;
        text-overflow: inherit;
    }

    .layui-anim {
        animation-duration: 3s;
        -webkit-animation-duration: 3s;
    }

    .layer_AI_IR {
        color: #ffffff;
        background-color: rgb(33 31 31 / 80%);
        border: 1px solid #16b777;
    }

    .layer_AI_IR .layui-layer-title {
        color: #ffffff;
    }

    #AI_IR_DEMO .el-collapse .el-collapse-item__header {
        padding: 0 10px;
        height: 30px;
        line-height: 30px;
    }

    #AI_IR_DEMO .el-collapse .el-collapse-item__content {
        color: #ffffff;
        font-size: 12px;
        transform-origin: left top;
        padding-bottom: 0;
        padding: 2px 8px;
    }
</style>

<div class="layui-padding-1" id="app" style="display:none;height: 97vh;">

    <el-row :gutter="10" style="height: 100%;">
        <el-col :xs="24" :sm="12" style="margin-bottom:10px;">
            <div class="layui-card">
                <div class="layui-card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        批量提交&nbsp;
                        <button v-if="qd_notice" type="button" class="layui-btn layui-btn-primary layui-btn-xs" @click="qd_notice_open">渠道推荐</button>
                    </div>
                    <div style="position: relative;">
                        <span style="scale: .8; display: inline-block;color:#8b8181">
                            <i class="layui-icon layui-icon-rmb">
                            </i> <span>{{user_money}}</span>
                        </span>
                        <div style="position: absolute; right: 0; top: -12px;">
                            <div class="layui-anim layui-anim-fadeout layui-font-red" v-if="useMoneyAnim">- {{user_use_money?user_use_money.toFixed(3):0}}</div>
                        </div>
                    </div>
                </div>
                <blockquote class="layui-elem-quote layui-quote-nm layui-font-12" style="padding:5px;margin: 5px;">
                    1. 尽量用手机号作为账号，查课失败时请多次重试！<br />
                    2. 若账号信息正确时查课失败，请尝试切换其它渠道！<br />
                </blockquote>
                <div class="layui-card-body" style="padding: 5px;">

                    <form class="form-horizontal devform">
                        
                        <div style="margin-bottom:5px;">
                            <?php if ($conf['flkg'] == '1' && $conf['fllx'] == '1') { ?>
                                <div class="">
                                    <!--<label class="col-sm-2 control-label">项目分类</label>-->
                                    <el-select v-model="fid" @change="fenlei(fid)" popper-class="lioverhide" :popper-append-to-body="false" filterable placeholder="请点击选择渠道，支持搜索" style="scroll 99%;width:100%">
                                        <el-option label="全部分类" value="">
                                            <div style="position: relative;">
                                                <div style="float: left; width: 92%; overflow: auto;">全部分类</div>
                                            </div>
                                        </el-option>
                                        <?php
                                        $a = $DB->query('select * from qingka_wangke_fenlei where status=1  ORDER BY `sort` ASC');
                                        while ($rs = $DB->fetch($a)) {
                                        ?>

                                            <el-option label="<?= $rs['name']; ?>" :value="<?= $rs['id']; ?>">
                                                <div style="position: relative;">
                                                    <div style="float: left; width: 92%; overflow: auto;"><?= $rs['name']; ?></div>
                                                </div>
                                            </el-option>


                                        <?php } ?>


                                    </el-select>
                                </div>
                            <?php } elseif ($conf['flkg'] == '1' && $conf['fllx'] == '2') { ?>
                                <div class="">
                                    <!--<label class="col-sm-2 control-label"></label>-->
                                    <div class="col-xs-12" style="padding:5px 0;">
                                        <el-radio-group v-model="fid" size="small" @input="fenlei(fid)" style="width: 100%;">
                                            <el-row :gutter="5" style="width: 100%;">
                                                <el-col :xs="<?= 24 / (int)$conf["xs_flhnum"] ?>" :sm="<?= 24 / (int)$conf["pc_flhnum"] ?>">
                                                    <el-radio label="" border style="width: 100%;">全部</el-radio>
                                                </el-col>
                                                <el-col :xs="<?= 24 / (int)$conf["xs_flhnum"] ?>" :sm="<?= 24 / (int)$conf["pc_flhnum"] ?>" v-for="item in fenleiList" :key="item.id">
                                                    <el-radio :label="item.id" border style="width: 100%;">{{item.name}}</el-radio>
                                                </el-col>
                                            </el-row>
                                        </el-radio-group>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        
                        <div class="" style="margin-bottom:10px;">
                            <!--<label class="col-sm-2 control-label">平台</label>-->
                            <div class="">
                                <!--<select class="form-control" v-model="cid" @change="tips(cid);">-->
                                <el-select id="select"  v-model="cid" @change="tips(cid)" popper-class="lioverhide" :popper-append-to-body="false" filterable placeholder="先选渠道，再选平台，支持搜索" style="scroll 99%;width:100%">

                                    <el-option v-for="class2 in class1" :key="class2.cid" :label="class2.name+'('+class2.price+'积分)'" :value="class2.cid">
                                        <div style="position: relative;">
                                            <div style="float: left; width: 92%; overflow: auto;">{{ class2.name }}</div>
                                            <div style="color: rgb(132, 146, 166); font-size: 13px; z-index: 1; position: absolute; right: 0;">{{ parseFloat(class2.price)?class2.price:"免费"}}</div>
                                        </div>
                                    </el-option>

                                </el-select>
                            </div>
                        </div>

                        <!--<div class="form-group" v-if="activems==true">-->
                        <!--  <label class="col-sm-2 control-label" for="checkbox1">是否秒刷</label>-->
                        <!--  <div class="col-sm-9">-->
                        <!--    <div class="checkbox checkbox-success" @change="tips2">-->
                        <!--      <input style="    margin-left: 0px;" type="checkbox" v-model="miaoshua">-->
                        <!--      <label for="checkbox1" id="miaoshua"></label>-->
                        <!--    </div>-->
                        <!--  </div>-->
                        <!--</div>-->
                        <!--单学时备注定位点  在下面的v-if里面按格式添加cid即可-->
                        <div class="form-group " v-if="nochake==1">
                            <!--<div class="col-sm-2 col-xs-3 control-label" style="margin-top:9px">份数</div>-->
                            <!--<div class="col-sm-9 col-xs-8">-->
                            <!--  <el-input-number v-model="shu" :min="1" :max="100"></el-input-number>-->
                            <!--</div>-->
                            <!--<div class="col-sm-2 col-xs-3 control-label" style="margin-top:9px">课程名称</div>-->
                            <!--<div class="col-sm-9 col-xs-8">-->
                            <!--  <el-input v-model="bei" placeholder="请输入要刷的课程" style="margin-top:9px"></el-input>-->
                            <!--</div>-->
                        </div>
                        <!--单学时备注定位点-->

                        <div class="" style="margin-bottom:10px;">
                            <div class="" style="position:relative;">
                                <el-input
                                    v-model="userinfo"
                                    :rows="8"
                                    type="textarea"
                                    :placeholder="nowGlass.nocheck?'无查下单格式：\r\n学校 账号 密码 课程\r\n或：账号 密码 课程':'下单格式：学校 账号 密码\r\n一行一个账号\r\n多个账号下单必须换行\r\n格式示例：\r\n北京大学 123 123\r\n清华大学 123 123'" @input="aiInput('input')" @blur="aiInput('blur')"
                                  />
                                <!--<textarea rows="5" class="layui-textarea" style="height:140px;" v-model="userinfo" :placeholder="nowGlass.nocheck?'无查下单格式：\r\n学校 账号 密码 课程\r\n或：账号 密码 课程':'下单格式：学校 账号 密码\r\n一行一个账号\r\n多个账号下单必须换行\r\n格式示例：\r\n北京大学 123 123\r\n清华大学 123 123'" @input="aiInput('input')" @blur="aiInput('blur')">-->
                                <!--</textarea>-->
                                <span class="layui-font-12 layui-font-blue" style="position: absolute; top: 0px; right: 5px; scale: .9;pointer-events: none;color: #959c9f !important;">AI纠正</span>
                            </div>
                        </div>
                        
                        <template v-if="class1.length > 0">
                            <div class="" style="margin-bottom:10px; text-align: right;" v-if="nowGlass.nocheck">
                                <button type="button" @click="add" value="立即提交" class="layui-btn layui-bg-blue" />
                                <i class="layui-icon layui-icon-release"></i> 提交
                                </button>
                            </div>
                            <div style="margin-bottom:10px;text-align: right;" v-else class="">
                                <button type="button" @click="get" value="查询课程" class="layui-btn layui-bg-blue" />
                                <i class="layui-icon layui-icon-search"></i> 查课
                                <button class=" layui-btn " :class="nochake!=1?'layui-btn-disabled':''" style="margin-left: 6px; " type="button" @click="add" value="提交订单" /><i class="layui-icon layui-icon-release"></i> 提交</button>
                                <!--<button class="btn btn-label btn-round btn-warning" type="reset"  value="重置"><label><i class="mdi mdi-delete-empty"></i></label> 重置</button>-->
                            </div>
                        </template>
                        
                        <div class="" v-if="!xdsmopen">
                            <fieldset class="layui-elem-field" v-if="content">
                                <legend style="width:auto;font-size:14px;border-bottom: 0;margin-bottom: 0;">说明</legend>
                                <div class="layui-field-box" style="padding-top: 5px;">
                                    <pre v-html="content"></pre>
                                </div>
                            </fieldset>
                        </div>

                    </form>

                </div>
            </div>
        </el-col>

        <el-col :xs="24" :sm="12" style="margin-bottom:10px;" id="resultBox">

            <div class="layui-card">
                <div class="layui-card-header">
                    查询结果&nbsp;&nbsp;<span v-show="loadTime" class="layui-font-12 layui-font-green">耗时：{{ loadTime.toFixed(2)}}s</span>
                    <!--<a class="layui-btn layui-btn-primary layui-btn-primary layui-btn-xs" @click="selectAll()">全选</a>-->
                </div>
                <div class="layui-card-body">
                    <div v-show="!row.length" class="layui-font-green">
                        {{ nowGlass.nocheck?'无查下单':'请先查课' }}
                    </div>
                    <form v-show="row.length" class="form-horizontal devform" style="max-height: 87vh; overflow: auto;">
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            <!--<div v-for="(rs,rs_key) in row" :key="rs_key">-->
                            <!--    <div v-for="(res,res_key) in rs.data" :key="res_key" >-->
                            <!--            <input type="checkbox" :name="res.name" :title="res.name">-->
                            <!--    </div>-->
                            <!--</div>-->

                            <el-collapse v-model='active_rs_key' class="layui-form">
                                <el-collapse-item v-for="(rs,rs_key) in row" :key="rs_key" :name="rs_key">
                                    <template #title>
                                        <div style="display: flex; align-items: center; font-size: 14px; white-space: nowrap; overflow: hidden;height: 100%;">
                                            <div @click.stop="console.log(1)">
                                                <input lay-filter="selectAll" :data-key="rs_key" type="checkbox" value="0">
                                            </div>
                                            <div v-if="rs.msg=='查询成功'" style="margin:0 5px;">
                                                <b style="color: #67C23A;">{{rs.msg}}</b>
                                            </div>
                                            <div v-else-if="rs.msg!='查询成功'">
                                                <b style="color: red;">{{rs.msg}}</b>
                                            </div>
                                            <div style="overflow-x: auto; overflow-y: hidden;height: 100%;">
                                                <b>{{rs.userName}}<b /> {{ rs.userinfo}}</b>
                                            </div>

                                        </div>
                                    </template>
                                    <div v-for="(res,res_key) in rs.data" :key="res_key" style="margin-bottom: 5px; font-size: 13px; padding-left: 10px; display: flex; align-items: center;">
                                        
                                        <input type="checkbox" name="checkbox" :class="['checkboxC'+`${rs_key}`]" :title="res.name" :value="res.name" :data-data="JSON.stringify({res:res,rs:rs,z:{top:rs_key,now:res_key}})" lay-filter="demo-checkbox-filter" :disabled="/错误|异常|失败/.test(res.name)||/错误|异常|失败/.test(res.id)||/错误|异常|失败/.test(res.msg)">
                                        
                                        &nbsp;&nbsp;
                                        <el-tooltip v-if="res.id" class="item" effect="dark" :content="res.id?('ID：'+res.id):''" placement="top-end">
                                            <span class="layui-btn layui-btn-xs layui-btn-primary" style="border: 0;cursor: pointer;">
                                                <i class="layui-icon layui-icon-eye"></i>
                                            </span>
                                        </el-tooltip>

                                        <!--<label class="layui-table lyear-checkbox checkbox-inline checkbox-success">-->
                                        <!--    <li>-->
                                        <!--        <input type="checkbox" name="checkbox" :title="res.name" :value="res.name" :data-data="JSON.stringify({res:res,rs:rs})" lay-filter="demo-checkbox-filter" >-->
                                        <!--<input style="margin-left: 0px;position:relative;top: 2px;" :checked="checked" name="checkbox" type="checkbox" :value="res.name" @click="checkResources(rs.userinfo,rs.userName,rs.data,res.name,res.id,res.state)">-->
                                        <!--<span style="margin-left: 3px;" v-if="res.name">{{res.name}}{{res.id}}</span>-->
                                        <!--        <span v-if="res.state"> [{{res.state}}] </span>-->
                                        <!--    </li>-->
                                        <!--</label>-->
                                    </div>
                                </el-collapse-item>
                            </el-collapse>

                            <!--<div v-for="(rs,key) in row">-->
                            <!--  <div class="panel panel-default">-->
                            <!--    <div class="panel-heading" role="tab" id="headingOne">-->
                            <!--      <h4 class="panel-title">-->
                            <!--        <a role="button" data-toggle="collapse" data-parent="#accordion" :href="'#'+key"-->
                            <!--          aria-expanded="true">-->
                            <!--          <b>{{rs.userName}}</b> {{rs.userinfo}} <span v-if="rs.msg=='查询成功'"><b-->
                            <!--              style="color: green;">{{rs.msg}}</b></span><span v-else-if="rs.msg!='查询成功'"><b-->
                            <!--              style="color: red;">{{rs.msg}}</b></span>-->
                            <!--        </a>-->
                            <!--      </h4>-->
                            <!--    </div>-->

                            <!--    <div :id="key" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">-->
                            <!--      <div class="panel-body">-->
                            <!--        <div v-for="(res,key) in rs.data">-->
                            <!--          <label class="layui-table lyear-checkbox checkbox-inline checkbox-success">-->
                            <!--            <li><input style="margin-left: 0px;" :checked="checked" name="checkbox" type="checkbox"-->
                            <!--                :value="res.name"-->
                            <!--                @click="checkResources(rs.userinfo,rs.userName,rs.data,res.name,res.id)"><span>{{res.name}}-->
                            <!--              </span><span v-if="res.id!=''"> [课程ID:{{res.id}}] </span></li>-->
                            <!--          </label>-->
                            <!--        </div>-->
                            <!--      </div>-->
                            <!--    </div>-->
                            <!--  </div>-->
                            <!--</div>-->

                        </div>
                    </form>

                </div>
            </div>

        </el-col>
    </el-row>

    <div id="qd_notice_ID" style="display: none;" class="layui-padding-2">
        <p><?php echo $conf['qd_notice'] ?>
        </p>
    </div>

    <!--AI_IR-->
    <!--<div class="layui-padding-2 layui-font-12" id="AI_IR_DEMO" style="display:none;min-width: 250px;max-width: 250px;max-height: calc(100vh - 70px);">-->
    <!--    <div v-if="!cid">-->
    <!--        请选择一个商品-->
    <!--    </div>-->
    <!--    <div v-else style="display: flex; flex-direction: column;max-height: calc(100vh - 70px);">-->
    <!--        <div>-->
    <!--            当前商品：{{ nowGlass.name }}-->
    <!--        </div>-->
    <!--        <hr />-->
    <!--        <div style="margin-bottom: 5px;">-->
    <!--            智能推荐：-->
    <!--        </div>-->
    <!--        <div style="flex: auto;overflow-y: auto;">-->
    <!--            <el-collapse :value="['1']">-->
    <!--                <el-collapse-item title="排名前五" name="1">-->
    <!--                    <template v-for="(item,index) in AI_IR_C_class" :key="item.cid">-->
    <!--                        <li>-->
    <!--                            {{ item.name }}-->
    <!--                        </li>-->
    <!--                        <hr />-->
    <!--                    </template>-->
    <!--                </el-collapse-item>-->
    <!--            </el-collapse>-->
    <!--        </div>-->

    <!--    </div>-->
    <!--</div>-->

</div>

<script src="/assets/toc/string-similarity.min.js?v=4.0.4"></script>

<?php include_once($root . '/index/components/footer.php'); ?>

<script>
    layui.config({
        base: '../../layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'console']);
</script>

<script>
    const app = Vue.createApp({
        data(){
            return{
                fid: '',
                fenleiList: [],
                row: [],
                shu: '',
                bei: '',
                nochake: 0,
                check_row: [],
                userinfo: '',
                cid: '',
                miaoshua: '',
                class1: [],
                class3: [],
                activems: false,
                checked: false,
                content: '',
                noInfo: false,
                has_getnoun: false,
                user_money: 0,
                qd_notice: '<?= $conf['qd_notice_open'] ?>',
                xdsmopen: <?= $conf['xdsmopen'] ?>,
                loadTime: 0, // 查课加载时间
                user_use_money: 0,
                useMoneyAnim: false, // 扣费动画
            }
        },
        computed: {
            // 当前商品
            nowGlass(){
                console.log(1,this)
                const _this = this;
                console.log(2,_this)
                let nowGlass = _this.class1.find(i=>i.cid == _this.cid);
                if(nowGlass){
                    nowGlass.nocheck = Number(nowGlass.nocheck);
                    return nowGlass;
                }else{
                    return {}
                }
            },
            active_rs_key() {
                const _this = this;
                let active_rs_key_Data = [];
                for (let i in _this.row) {
                    active_rs_key_Data.push(Number(i));
                }
                return active_rs_key_Data
            },
            AI_IR_C_class() {
                const _this = this;
                let classs = JSON.parse(JSON.stringify(_this.class1));
                let className = classs.find(item => item.cid === _this.cid).name;

                // 匹配是否有各类括号
                if (/[【\[{〖\(\（][^】\]\}〗\)\）]+[】\]\}〗\)\）]/.test(className)) {
                    // 如果前面没有
                    if (!className.match(/[【\[{〖\(\（][^】\]\}〗\)\）]+[】\]\}〗\)\）](.*)/)[1]) {
                        className = className.match(/(.*)[【\[{〖\(\（][^】\]\}〗\)\）]+[】\]\}〗\)\）]/)[1];
                    } else {
                        className = className.match(/[【\[{〖\(\（][^】\]\}〗\)\）]+[】\]\}〗\)\）](.*)/)[1]
                        if (/[【\[{〖\(\（][^】\]\}〗\)\）]+[】\]\}〗\)\）]/.test(className)) {
                            // 匹配后面的各类括号
                            className = className.match(/(.*)[【\[{〖\(\（][^】\]\}〗\)\）]+[】\]\}〗\)\）]/)[1];
                        }
                    }
                }

                let similarity_className = classs.filter(i => {
                    if (/[【\[{〖\(\（][^】\]\}〗\)\）]+[】\]\}〗\)\）]/.test(i.name)) {
                        i.name = i.name.match(/[【\[{〖\(\（][^】\]\}〗\)\）]+[】\]\}〗\)\）](.*)/)[1]
                    }

                    if (i.order && stringSimilarity.compareTwoStrings(className, i.name) > 0.4) {
                        i.similarity = stringSimilarity.compareTwoStrings(className, i.name);
                        return i
                    }
                })

                // 排序
                similarity_className.sort((a, b) => {
                    if (b.similarity === a.similarity) {
                        return b.order - a.order;
                    }
                    return b.similarity - a.similarity;
                });

                similarity_className = similarity_className.slice(0, 5);
                return similarity_className;
            },
        },
        methods: {
            aiInput(t = 'input') {
                const _this = this;
                if (t === 'input') {
                    _this.userinfo = _this.userinfo
                        .replace(/，/g, ',')
                        .replace(/；/g, ';')
                        .replace(/！/g, '!')
                        .replace(/？/g, '?')
                        .replace(/！/g, ';')
                        .replace(/（/g, '(')
                        .replace(/）/g, ')')
                        .replace(/。/g, '.')
                        .replace(/——/g, '_')
                        .replace(/【/g, '[')
                        .replace(/】/g, ']')
                        .replace(/ {2,}/g, ' ')
                        .replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, '')
                        .replace(/[\u2600-\u27BF]/g, '')
                        .replace(/^\s+/, '')
                }
                if (t === 'blur') {
                    _this.userinfo = _this.userinfo
                        .replace(/^\s+/, '')
                        .replace(/\s+$/, '')
                        .split('\n')
                        .map(line => line.trim())
                        .join('\n')
                    let userinfo = _this.userinfo.replace(/\r\n/g, "[br]").replace(/\n/g, "[br]").replace(/\r/g, "[br]");
                    userinfo = userinfo.split('[br]').filter(item => item !== '');
                    userinfo = [...new Set(userinfo)];
                    _this.userinfo = userinfo.join('\n');
                }
            },
            money_get() {
                const _this = this;
                axios.post('/apiadmin.php?act=usermoney', {}, {
                    emulateJSON: true
                }).then(r => {
                    if (r.data.code === 1) {
                        _this.user_money = r.data.money;
                    } else {
                        layer.msg('余额获取失败！')
                    }
                })
            },
            qingli_row() {
                vm.check_row = [];
            },
            qd_notice_open() {
                const _this = this;
                if (!_this.qd_notice) {
                    return;
                }
                layui.use(function() {
                    layer.open({
                        type: 1,
                        title: '渠道推荐',
                        area: ['320px'], // 宽高
                        content: $('#qd_notice_ID'),
                        time: 20 * 1000,
                        success: function(layero, index) {
                            var timeNum = _this.time / 1000;
                            var title = _this.title;
                            setText = function(start) {
                                layer.title(title + '&nbsp;&nbsp;&nbsp;&nbsp;<span class="layui-font-12"><font class="layui-font-red">' + (start ? timeNum : --timeNum) + '</font> 秒后自动关闭</span>', index);
                            };
                            setText(!0);
                            _this.timer = setInterval(setText, 1000);
                            if (timeNum <= 0) clearInterval(_this.timer);
                        },
                    });
                })
            },
            get: async function(salt) {
                const _this = this;
                vm.qingli_row();
                _this.nochake = 0;
                if (_this.cid == '' || _this.userinfo == '') {
                    layer.msg("所有项目不能为空");
                    return false;
                }
                
                // 拆分账号数据为数组
                let userinfo = _this.userinfo.replace(/\r\n/g, "[br]").replace(/\n/g, "[br]").replace(/\r/g, "[br]");
                userinfo = userinfo.split('[br]').filter(item => item !== '');
                
                // 重置查课结果和选定课程
                _this.row = [];
                _this.check_row = [];
                
                var loadIndex = layer.msg('查课中，第1条...', {
                    icon: 16,
                    shade: 0.01,
                    time: 0,
                    tipsMore: true
                });
                _this.loadTime = 0;
                
                const startTime = new Date().getTime();
                for (let i = 0; i < userinfo.length; i++) {
                    var info = userinfo[i];
                    if (info === '') {
                        continue;
                    }
                    var hash = getENC('<?php echo $addsalt; ?>');

                    try {
                        // 获取课程
                        let response = await axios.post("/apiadmin.php?act=get", {
                            cid: _this.cid,
                            userinfo: info,
                            hash
                        });


                        let data = response.data; // 或根据实际情况调整访问响应数据的方式
                        
                        _this.nochake = 1;
                        
                        let msg = data.msg?data.msg:"未返回msg";
                        if (data.code == 1) {
                            console.log(2)
                            layer.msg(msg);
                            

                            // 再次解析.解析数据不存在的时候
                            if (!data.data || !data.data.length) {
                                data.data = [{
                                    name: '异常,请重试1'
                                }]
                            }
                            _this.row.push(data);
                            console.log(_this.row)

                            setTimeout(() => {
                                layui.form.render();
                            }, 20)

                            loadIndex = layer.msg('查课中，第' + (i + 2) + '条...', {
                                icon: 16,
                                shade: 0.01,
                                time: 0,
                                tipsMore: true
                            });

                            // 最后一个
                            if (i === userinfo.length - 1) {
                                layer.close(loadIndex);
                                
                                setTimeout(() => {
                                    layui.form.on('checkbox(demo-checkbox-filter)', function(data) {
                                        var elem = data.elem; // 获得 checkbox 原始 DOM 对象
                                        var checked = elem.checked; // 获得 checkbox 选中状态
                                        var value = elem.value; // 获得 checkbox 值
                                        var othis = data.othis; // 获得 checkbox 元素被替换后的 jQuery 对象
                                        let dataset_data = JSON.parse(elem.dataset.data);
                                        
                                        _this.checkResources(dataset_data.z,dataset_data.rs.userinfo, dataset_data.rs.userName, dataset_data.rs.data, dataset_data.res.name, dataset_data.res.id, dataset_data.res.state);

                                    });

                                    if ($(document).width() < 750) {
                                        $(document).scrollTop($('#resultBox').offset().top - 100);
                                    }

                                    // 监听全选
                                    layui.form.on('checkbox(selectAll)', function(data) {
                                            // console.log(88,data)
                                        let userKey = data.elem.dataset.key
                                        let child = $(`.checkboxC${userKey}`)
                                        child.each(function(index, item) {
                                            item.checked = data.elem.checked;
                                        });
                                        layui.form.render('checkbox');
                                        
                                            console.log("userKey",userKey)
                                        if (data.elem.checked) {
                                            console.log(2)
                                            vm.check_row = vm.check_row.filter(i=>i.z.split('-')[0] != userKey);
                                            userinfo = _this.row[userKey].userinfo
                                            userName = _this.row[userKey].userName
                                            console.log(99,userKey,_this.row[userKey])
                                            rs = _this.row[userKey].data ? _this.row[userKey].data : _this.row[userKey].children
                                            for (a = 0; a < rs.length; a++) {
                                                aa = rs[a]
                                                data = {
                                                    userinfo,
                                                    userName,
                                                    data: aa,
                                                    z: `${userKey}-${a}`
                                                }
                                                vm.check_row.push(data);
                                            }
                                        } else {
                                            console.log(1)
                                            vm.check_row = vm.check_row.filter(i=>i.z.split('-')[0] != userKey);
                                        }
                                    })

                                }, 10)

                                const endTime = new Date().getTime();
                                _this.loadTime = (endTime - startTime) / 1000;
                                layer.msg('查询成功');
                            }

                        }else{
                            _this.$message.error(msg);
                            layer.close(loadIndex)
                        }
                    } catch (error) {
                        layer.msg('异常：'+error)
                    }
                }
                
            },
            add() {
                const _this = this;
                if(!_this.userinfo.length){
                    layer.msg("请先填写账号信息");
                    return
                }
                
                // if(!_this.check_row.length){
                //     layer.msg("请先选择课程1");
                //     return
                // }
                
                console.log('_this.check_row', _this.check_row);
                if (_this.cid == '') {
                    // 如果没查课和需要课程
                    if (_this.nochake != 1) {
                        layer.msg("请先查课1");
                        return false;
                    }
                }
                // 如果没查课和需要课程
                if (_this.nochake != 1 && !_this.nowGlass.nocheck) {
                    layer.msg("请先查课2");
                    return false;
                }
                if (_this.check_row.length < 1) {

                    // 如果没查课和需要课程
                    if (_this.nochake != 1 && !_this.nowGlass.nocheck) {
                        layer.msg("请先选择课程2");
                        return false;
                    }
                }
                
                // 如果不需要课程
                if (_this.nowGlass.nocheck) {
                    if (_this.userinfo) {
                        _this.check_row = [];
                        let userinfo = _this.userinfo.replace(/\r\n/g, "[br]").replace(/\n/g, "[br]").replace(/\r/g, "[br]");
                        userinfo = userinfo.split('[br]').filter(item => item !== '');
                        for(let i in userinfo){
                            let userinfo_split = userinfo[i].split(' ');
                            _this.check_row.push({
                                userinfo: userinfo_split.length>=4?userinfo_split.slice(0,3).join(' '):(userinfo_split.length==3?userinfo_split.slice(0,2).join(' '):userinfo[i]),
                                data: {
                                    name: userinfo_split.length>=4?userinfo_split[3]:(userinfo_split[2]== undefined?'':userinfo_split[2])
                                },
                            });
                        }
                    } else {
                        layer.msg("请先完善账号信息");
                        return
                    }
                }
                console.log('_this.check_row2', _this.check_row);

                let loadIndex = layui.layer.msg('提交中，请耐心等待', {
                    icon: 16,
                    shade: 0.01,
                    time: 100000000,
                    offset() {
                        var viewportWidth = $(window).width();
                        var viewportHeight = $(window).height();
                        var offsetX = Math.floor(viewportWidth / 2) + 'px';
                        var offsetY = Math.floor(viewportHeight / 2) + 'px';
                        return [offsetY, offsetX];
                    }
                });
                axios.post("/apiadmin.php?act=add", {
                    cid: _this.cid,
                    data: _this.check_row,
                    shu: _this.shu,
                    bei: _this.bei,
                    userinfo: _this.userinfo,
                    nochake: _this.nochake
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    if (r.data.code == 1) {
                        vm.qingli_row();
                        _this.row = [];
                        _this.check_row = [];
                        /*_this.$message({type: 'success', showClose: true,message: r.data.msg});*/
                        _this.loadTime = 0;
                        layer.msg('提交成功', {
                            icon: 1,
                            time: 2000,
                            offset() {
                                var viewportWidth = $(window).width();
                                var viewportHeight = $(window).height();
                                var offsetX = Math.floor(viewportWidth / 2) + 'px';
                                var offsetY = Math.floor(viewportHeight / 2) + 'px';
                                return [offsetY, offsetX];
                            }
                        }, function() {
                            // setTimeout('window.location.reload()', 1000);
                        });
                        _this.user_money = r.data.money;

                        _this.user_use_money = r.data.money2;
                        setTimeout(()=>{
                            _this.user_use_money = 0;
                            _this.useMoneyAnim = false;
                        },3000)
                        _this.useMoneyAnim = false;
                        setTimeout(() => {
                            _this.useMoneyAnim = true;
                        }, 0)

                        console.log('更新钱', data)
                        _this.nochake = 0;
                    } else {

                        layer.msg(r.data.msg, {
                            offset() {
                                var viewportWidth = $(window).width();
                                var viewportHeight = $(window).height();
                                var offsetX = Math.floor(viewportWidth / 2) + 'px';
                                var offsetY = Math.floor(viewportHeight / 2) + 'px';
                                return [offsetY, offsetX];
                            }
                        });
                        //   console.log('-55')
                    }
                    layer.close(loadIndex)
                });
            },
            check888: function(userinfo, userName, rs, name) {
                var btns = document.getElementById("btns");
                var zk = document.getElementById("s1");
                var x = zk.getElementsByTagName("input");
                if (btns.checked == true) {
                    for (var i = 0; i < x.length; ++i) {
                        data = {
                            userinfo,
                            userName,
                            data: rs[i]
                        };
                        x[i].checked = true;
                        vm.check_row.push(data);
                    }
                } else {
                    for (var i = 0; i < x.length; ++i) {
                        x[i].checked = false;
                    }
                    _this.check_row = []
                }
            },
            selectAll: function(accountKey) {
                const _this = this;
                console.log(777,_this.row[accountKey])

                if (_this.cid == '') {
                    layer.msg("请先查课");
                    return false;
                }
                _this.checked = !_this.checked;
                if (_this.check_row.length < 1) {
                    userinfo = _this.row[accountKey].userinfo
                    userName = _this.row[accountKey].userName
                    rs = _this.row[accountKey].data ? _this.row[accountKey].data : _this.row[accountKey].children
                    for (a = 0; a < rs.length; a++) {
                        aa = rs[a]
                        data = {
                            userinfo,
                            userName,
                            data: aa
                        }
                        vm.check_row.push(data);
                    }
                } else {
                    vm.check_row = []
                }
                console.log(99, vm.check_row);
            },
            checkResources: function(z,userinfo, userName, rs, name, id, state) {
                const _this = this;
                console.log(z)
                for (i = 0; i < rs.length; i++) {
                    if (rs[i].name == name && rs[i].id == id && rs[i].state == state) {
                        aa = rs[i]
                    }
                }
                data = {
                    userinfo,
                    userName,
                    data: aa,
                    z: `${z.top}-${z.now}`
                }
                if (_this.check_row.length < 1) {
                    vm.check_row.push(data);
                } else {
                    var a = 0;
                    for (i = 0; i < vm.check_row.length; i++) {
                        if (vm.check_row[i].z == data.z) {
                            var a = 1;
                            vm.check_row = vm.check_row.filter(i=>i.z != data.z);
                        }
                    }
                    if (a == 0) {
                        vm.check_row.push(data);
                    }
                }
            },
            fenlei: function(id) {
                const _this = this;
                setTimeout(()=>{
                    console.log(99,_this.fid)
                    var load = layer.load(0);
                    axios.post("/apiadmin.php?act=getclassfl", {
                        id: _this.fid
                    }, {
                        emulateJSON: true
                    }).then(function(r) {
                        layer.close(load);
                        if (r.data.code == 1) {
                            _this.class1 = r.data.data;
                        } else {
                            layer.msg(r.data.msg, {
                                icon: 2
                            });
                        }
                    });
                },0)

            },
            getclass() {
                const _this = this;
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=getclass").then(function(r) {
                    if (vm.qd_notice) {
                        _this.qd_notice_open()
                    }
                    layer.close(load);
                    if (r.data.code == 1) {
                        _this.class1 = r.data.data;
                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                });

            },
            getnock: function(cid) {
                const _this = this;
                axios.post("/apiadmin.php?act=getnock").then(function(r) {
                    if (r.data.code == 1) {
                        _this.nock = r.data;
                        for (i = 0; _this.nock.length > i; i++) {
                            if (cid == _this.nock[i].cid) {
                                _this.nochake = 1;
                                break;
                            } else {
                                _this.nochake = 0;
                            }
                        }
                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                });

            },
            tips: function(message) {
                const _this = this;
                _this.noInfo = false;
                _this.has_getnoun = false;
                for (var i = 0; _this.class1.length > i; i++) {
                    if (_this.class1[i].cid == message) {
                        _this.show = true;
                        _this.content = _this.class1[i].content;
                        if (!_this.class1[i].getnoun) {
                            _this.noInfo = true;
                        }
                        if (_this.nowGlass.getnoun) {
                            _this.has_getnoun = true;
                        }
                        _this.$notify.closeAll();
                        if (_this.xdsmopen) {
                            _this.$notify({
                                title: '说明',
                                dangerouslyUseHTMLString: true,
                                message: _this.class1[i].content ? _this.class1[i].content : '暂无',
                                position: 'bottom-left'
                            });
                        }

                        return false;
                        if (_this.class1[i].miaoshua == 1) {
                            _this.activems = true;
                        } else {
                            _this.activems = false;
                        }
                        return false;

                    }

                }

            },
            /*tips: function (message) {
                const _this = this;
                for(var i=0;_this.class1.length>i;i++){
                    if(_this.class1[i].cid==message){
                          _this.$notify({
                              title: _this.class1[i].name+'说明：',
                              dangerouslyUseHTMLString: true,
                              duration: 6000,
                              // showClose: false,
                              message:'<span style="font-size:14px;">'+_this.class1[i].content+'</font>',
                          });
                          if(_this.class1[i].miaoshua==1){
                       _this.activems=true;
                 }else{
                     _this.activems=false;
                 }
                      return false;
                    }
                }
              },*/
            tips2() {
                layer.tips('开启秒刷将额外收0.05的费用', '#miaoshua');

            }
        },
        mounted() {
            const _this = this;

            let loadIndex = layer.load(0);
            $('#app').ready(() => {
                layer.close(loadIndex);
                $('#app').show();
                _this.getclass();
                _this.money_get();

                // 智能推荐
                layer.open({
                    type: 1,
                    shade: false,
                    title: '<i class="layui-icon layui-icon-senior layui-anim layui-anim-rotate layui-anim-loop"></i> AI_IR 智能推荐',
                    maxmin: true,
                    id: "layer_AI_IR",
                    skin: "layer_AI_IR",
                    content: $('#AI_IR_DEMO'),
                    offset: 'rt',
                    maxHeight: 670,
                    closeBtn: 0,
                    success(){
                      $('.layer_AI_IR .layui-layer-max').hide()  
                    },
                    end() {
                    },
                    min(){
                        setTimeout(()=>{
                            $('.layer_AI_IR .layui-layer-maxmin').show()
                        },0)
                    },
                    restore(layero, index, that){
                        setTimeout(()=>{
                            $('.layer_AI_IR .layui-layer-max').hide()  
                            $(".layer_AI_IR").height("auto")
                            $("#layer_AI_IR").height("auto")
                        },0)
                    }
                });

            })
            
            // 商品分类获取
            _this.fenleiList = <?php
                        $fenleiList_result = $DB->query('select * from qingka_wangke_fenlei where status=1  ORDER BY `sort` ASC');
                        $fenleiList = [];
                        while ($rs = $DB->fetch($fenleiList_result)) {
                            $list[] = [
                                "id" => $rs["id"],
                                "name" => $rs["name"],
                                "sort" => $rs["sort"],
                            ];
                        }
                        echo json_encode($list);
                        ?>;
                        
        }
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#app');
    // -----------------------------
    
</script>