<?php
include '../confing/common.php';

$user = $DB->get_row("select name from qingka_wangke_user where uid='{$_GET['t']}' LIMIT 1 ");
$sitename = empty($user['name']) || empty($_GET['t']) || $_GET['t'] ==1 ?$conf['sitename']:$user['name'];
?>

<!DOCTYPE html>
<html lang="zh-cn" style="font-size: 20px !important;text-size-adjust: none;">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover,user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<title>查进度系统 - <?= $sitename ?></title>
	<meta name="keywords" content="<?php echo $conf['keywords'] ?>">
	<meta name="description" content="<?php echo $conf['description'] ?>">
	<link rel="shortcut icon" href="<?php echo $conf['default_ico_url'] ?>">

	<?php include_once($root . '/index/components/jscss.php'); ?>

	<script crossorigin="anonymous"
		integrity="sha512-NQfB/bDaB8kaSXF8E77JjhHG5PM6XVRxvHzkZiwl3ddWCEPBa23T76MuWSwAJdMGJnmQqM0VeY9kFszsrBEFrQ=="
		src="https://lib.baomitu.com/axios/1.6.7/axios.min.js"></script>


</head>

<style>
	.mainBox {
		position: relative;
		left: 50%;
		transform: translateX(-50%);
		max-width: 620px;
		min-height: 100vh;
	}

	.mainBox .searchBox {
		margin: 20px 0;
	}

	.mainBox .searchBox .layui-input-group {
		position: relative;
		left: 50%;
		transform: translateX(-50%);
		width: 80%;
	}
</style>

<body style="background: transparent;">
	<div id="app" style="width: 100%;display:none;">
		<div class="mainBox">
			<div class="layui-panel" style="padding: 15px;height: 100%;">
				<div class="head layui-font-16" style="text-align: center;font-weight: bold;">
				    <span v-if="!t || t  ==1"><?php echo $conf['sitename'] ?></span>
				    <span v-else>
				        <?= $sitename ?>
				    </span>
				    &nbsp;|&nbsp;查进度系统
				</div>
				<div class="searchBox">
					<div class="layui-input-group" >
						<input v-model="user" type="text" lay-affix="clear" :placeholder="!chadan_open?'禁止输入':'请输入下单账号...'"
							class="layui-input" :class="!chadan_open?'layui-disabled':''" :disabled="!chadan_open">
						<div  class="layui-input-split layui-input-suffix" style="cursor: pointer;" :class="!chadan_open?'layui-disabled':''"
							@click="chadan_open?query():layer.msg('查单功能已禁用!')" >
							<i class="layui-icon layui-icon-search"></i>
						</div>
					</div>
				</div>
				<div v-if="chadan_open">
					<hr>

					<div id="orderListBox" style="display:none;">
						<div class="layui-panel" style="margin: 0 0 10px;padding: 10px;"
							v-for="(res,index) in orderList" :key="index">
							<div style="display: flex; justify-content: space-between; align-items: center;">
								<h4>
									{{res.kcname}}
								</h4>
								<span class="layui-badge layui-bg-blue" style="padding: 6px 8px; opacity: 0.7; overflow-x: auto; text-align: left; white-space: nowrap; word-break: normal;">
									{{c_ptname(res.ptname)}}
								</span>

								<!--{{res.ptname.includes('】')}}-->
							</div>
							<hr />
							<table class="layui-table" lay-even>
								<colgroup>
									<col width="60">
									<col width="150">
									<col>
								</colgroup>
								<thead style="display: none;">
									<tr>
										<th></th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>状态</td>
										<td>
										    <div style="display: flex; align-items: center;">
										        <span>{{res.status}}</span>
    										    <span v-if="res.status.search(/已完成|已完成|已经完成|已经全部完成|已取消|已退款|补刷中|待处理|已提交|待支付/) == -1">
    										        <?php if($conf["chadan_bs"] == 1) { ?>
                									    &nbsp;&nbsp;<button class="layui-btn layui-btn-xs" @click="budan(res.id)">补刷</button>
                									<?php } ?>
    										    </span>
										    </div>
										</td>
									</tr>
									<tr>
										<td>下单时间</td>
										<td>{{res.addtime}}</td>
									</tr>
									<tr>
										<td>学校</td>
										<td>{{res.school}}</td>
									</tr>
									<tr>
										<td>参考进度</td>
										<td>
										    <div class="layui-progress layui-progress-big" lay-showpercent="true" lay-filter="progress-filter">
                                              <div class="layui-progress-bar" :lay-percent=" process_num(res.process) + '%' "></div>
                                            </div>
										</td>
									</tr>
									<tr>
										<td>日志</td>
										<td>已完成：{{res.remarks?res.remarks:process_num(res.process)}}%</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div v-else>
					<hr />
					查单功能已关闭
				</div>
			</div>
		</div>
	</div>
</body>

<script>
    const app = Vue.createApp({
        data(){
            return{
    			urlC: {},
    			user: '',
    			t: '',
    			orderList: [],
    			chadan_open: <?= $conf["chadan_open"] ?>,
            }
        },
		mounted() {
			const _this = this;
			$("#app").ready(() => {
				$('#app').show();
				
				<?php if(!empty($conf["chadan_t_notice"])){ ?>
				    _this.chadan_t_notice_open();
				<?php } ?>
				
				<?php if($conf["chadan_open"]){ ?>
				    // 解析url
        			for (const [key, value] of new URLSearchParams(window.location.search).entries()) {
        				_this.urlC[key] = value;
        			}
        			if (_this.urlC.t) {
        				_this.t = _this.urlC.t;
        			}
        			if (_this.urlC.user) {
        				_this.user = _this.urlC.user;
        				_this.query();
        
        			}
				    
				<?php } ?>
				
				layui.use(function() {
                    var util = layui.util;
                    // 自定义固定条
                    util.fixbar({
                        margin: 100
                    })
                })
				
			})

		},
		methods: {
            process_num: function(process=0){
                if(parseFloat(process)==100 || process?process.search(/已完成|已经完成|已经全部完成/) !== -1:0){
                    return 100;
                }else{
                    if(process){
                        if(isNaN(parseFloat(process))){
                            let match =process.match(/(\d+)\/(\d+)/);
                            if(match){
                                return (match[1] / match[2] *100).toFixed(2);
                            }else{
                                match2 = process.match(/(\d+(\.\d+)?)%/);
                                if (match2) {
                                    return parseFloat(match2[1]);
                                }
                                return 0
                            }
                        }
                        return parseFloat(process) ;
                    }else{
                        return 0;
                    }
                }
            },
		    // 打开弹窗公告
			chadan_t_notice_open() {
			    const _this = this;
				layer.open({
					type: 1,
					title: '',
					content: '<div class="layui-padding-2"><?= $conf['chadan_t_notice'] ?></div>',
					time: 15000,
					btn: '朕知道了',
					btnAlign: 'c', //按钮居中
					shade: 0.4, //遮罩
					closeBtn: 0,
					area: ['360px', 'auto'],
					time: 20 * 1000,
					scrollbar: false,
					success: function (layero, index) {
						var timeNum = _this.time / 1000,
							setText = function (start) {
								layer.title('公告&nbsp;&nbsp;&nbsp;&nbsp;<span class="layui-font-12"><font class="layui-font-red">' + (start ? timeNum : --timeNum) + '</font> 秒后自动关闭</span>', index);
							};
						setText(!0);
						_this.timer = setInterval(setText, 1000);
						if (timeNum <= 0) clearInterval(_this.timer);

					},
					end: function () {
						clearInterval(_this.timer);
					}
				});
			},
			c_ptname: function (rp) {
				let includesC = (['】']).find(item => rp.includes(item));
				return includesC ? rp.split(includesC)[1] : rp;
			},
			// 补刷
			budan(id=''){
				const _this = this;
			    if(!id){
			        layer.msg('无法获取订单ID');
			        return
			    }
			    layer.confirm('建议漏看或者进度被重置的情况下使用。<br />频繁点击补刷会出现不可预测的结果请问是否补刷所选的任务？',{title:'是否补刷？'},()=>{
    				layer.load(0);
    				axios.post('/api.php?act=budan',{
    				        id: id
    				},{emulateJSON:true}).then(r=>{
    				    layer.closeAll("loading")
    				    if(r.data.code === 1){
    				        layer.msg('成功进入补刷队列')
    				       setTimeout(()=>{
    				            _this.query();
    				       },1200)
    				    }else{
    				        
    				        layer.msg(r.data.msg?r.data.msg:'补刷失败')
    				    }
    				        
				    })
			    })
			},
			query: async function (user=this.user) {
				const _this = this;
				if(!_this.chadan_open){
				    layer.msg("查单功能已禁用！")
				    return
				}
				
				let params = new URLSearchParams(window.location.search);
				params.set('user', user);
				window.history.replaceState({}, '', '?' + params.toString());
				
				_this.orderList = [];

				layui.use(async function () {
					if (!_this.user) {
						layer.msg('请输入账号！')
						return
					};
					var loadIndex = layer.msg('查询中...', {
						icon: 16,
						shade: 0.01,
						time: 0
					});

					// 发起一个post请求
					const dataReturn = await axios({
						method: 'post',
						url: '/api.php?act=chadan2',
						data: `username=${_this.user}&t=${_this.t}`
					})
					if (dataReturn.data.code !== 1) {
						layer.msg('异常！请重试')
						return
					}
					if (!dataReturn.data.data) {
						layer.msg('账号不存在')
						return
					}
					layer.close(loadIndex)
					layer.msg('查询成功')
					_this.orderList = dataReturn.data.data;
					$('#orderListBox').show();
					layui.use(()=>{layui.element.render()})
					console.log(_this.orderList)
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

</html>