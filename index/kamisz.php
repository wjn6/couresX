<?php
$title = '卡密充值';
require_once('head.php');

if ($userrow['uid'] != 1) {
    alert("您的账号无权限！", "index.php");
    exit();
}
?>

<div id="kamiID" class="layui-padding-2" style="display:none;">
	<div class="layui-panel layui-padding-2">
        
		<div class="">
			<button type="button" class="layui-btn layui-btn-sm layui-bg-blue" @click="add_open()">
				<i class="layui-icon layui-icon-addition"></i> 生成卡密
			</button>
		</div>

		<div class="table" style="margin: 10px 0 0">
		    <div style="display: flex; justify-content: space-between; align-items: center;">
				<div style="margin:10px 0">
					<button title="刷新" type="button" class="layui-btn layui-btn-xs layui-btn-primary" @click="kamilist(1)">
						<i class="layui-icon layui-icon-refresh"></i>
					</button>
					<button title="批量删除" v-if="uid" type="button" class="layui-btn layui-btn-xs layui-btn-primary" @click="del()">
						<i class="layui-icon layui-icon-delete"></i>
					</button>
                    <button title="导出" type="button" class="layui-btn layui-btn-xs layui-btn-primary" @click="export_table()">
                        <i class="layui-icon layui-icon-export"></i>
                    </button>
				</div>
			</div>
			<table id="listTable" layui-filter="listTable"></table>
			<table id="listTable2" name="2" lay-filter="listTable2" class="layui-table listTable2" style="display:none" border="1">
                    <thead style="white-space:nowrap">
                        <tr>
                            <th>卡号</th>
                            <th>金额</th>
                            <th>状态</th>
                            <th>密钥</th>
                            <th>使用者</th>
                            <th>使用时间</th>
                            <th>生成时间</th>
                            <th>过期时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(res,index) in row.data" :key="index">
                            <td v-html="res.code">
                            </td>
                            <td v-html="res.price">
                            </td>
                            <td v-html="res.status">
                            </td>
                            <td v-html="res.codeKey">
                            </td>
                            <td v-html="res.user">
                            </td>
                            <td v-html="res.usetime">
                            </td>
                            <td v-html="res.addtime">
                            </td>
                            <td v-html="res.endtime">
                            </td>
                        </tr>
                    </tbody>
                </table>
		</div>
		<div class="layui-panel" style="display: flex; justify-content: space-between;">
			<div></div>
			<div id="listTable_laypage" style="scale: .8;"></div>
		</div>

	</div>

	<div id="add" class="layui-padding-2" style="display: none;height: -webkit-fill-available;">

		<form id="add-form" class="layui-form" action="" lay-filter="add-form" style="display: flex; flex-direction: column;height: -webkit-fill-available;">

			<div class="layui-form-item" v-show="!editT">
				<label class="layui-form-label" style="width: 60px;">加密密钥</label>
				<div class="layui-input-block" style="margin-left: 95px;">
					<input type="text" name="codeKey" v-model="eform.codeKey" lay-verify="required" placeholder="请输入一个加密密钥" autocomplete="off" class="layui-input" lay-affix="clear">
				    <div class="layui-font-12 layui-font-red">生成后密钥禁止更改！</div>
				</div>
			    <hr />
			</div>
			<div class="layui-form-item"> 
				<label class="layui-form-label" style="width: 60px;">单个金额</label>
				<div class="layui-input-block" style="margin-left: 95px;">
					<input type="number" lay-affix="number" name="price" v-model="eform.price" lay-verify="required" placeholder="请输入单个卡密的金额" autocomplete="off" class="layui-input" lay-affix="clear" min="0.000001" step="0.01">
				</div>
			</div>
			<div class="layui-form-item" v-if="!editT">
				<label class="layui-form-label" style="width: 60px;">生成数量</label>
				<div class="layui-input-block" style="margin-left: 95px;">
					<input type="number" lay-affix="number" name="num" v-model="eform.num" lay-verify="required" placeholder="请输入要生成的数量" autocomplete="off" class="layui-input" lay-affix="clear" min="1" oninput="this.value=this.value.replace(/\D/g);if(this.value.length＞4)this.value=this.value.slice(0,4)">
				</div>
			</div>
			<div class="layui-form-item" v-if="!editT">
				<div class="layui-form-label" style="float: none; width: max-content;text-align: left;">
				    每个代理可领数量<br />
				    <span class="layui-font-12 layui-font-red">不填则为不限制该批次卡密可领数量</span>
				</div>
				<div class="layui-input-block" style="margin-left: 0;">
					<input type="number" lay-affix="number" name="onlynum" v-model="eform.onlynum"  placeholder="请输入该批次卡密每个代理可领数量" autocomplete="off" class="layui-input" lay-affix="clear" min="1" oninput="this.value=this.value.replace(/\D/g);if(this.value.length＞4)this.value=this.value.slice(0,4)">
				</div>
			</div>
			<div class="layui-form-item" v-if="!editT">
				<label class="layui-form-label" style="width: 60px;">过期时间</label>
				<div class="layui-input-block" style="margin-left: 95px;">
				    
				     <input type="text" name="endtime" class="layui-input" id="ID-laydate-type-datetime" placeholder="留空不填则为永不过期" v-model="eform.endtime">
				     
					<!--<input type="number" lay-affix="number" name="num" v-model="eform.num" lay-verify="required" placeholder="请输入要生成的数量" autocomplete="off" class="layui-input" lay-affix="clear" min="1" oninput="this.value=this.value.replace(/\D/g);if(this.value.length＞4)this.value=this.value.slice(0,4)">-->
				</div>
			</div>
			<button style="display:none;" id="add-form_reset" type="reset" class="layui-btn layui-btn-primary">重置</button>
		</form>
	</div>

</div>

<script type="text/html" id="listTable_user_caoz">
	<div style="">
		<div style="display: grid; grid-template-columns: repeat(auto-fill,30px);">
			<button title="删除" lay-event="listTable_user_del" type="button" class="layui-btn layui-btn-primary layui-border-red layui-btn-xs" style="margin:0 5px 0 0;">
				<i class="layui-icon layui-icon-delete"></i>
			</button>
			<button title="编辑" lay-event="listTable_user_edit" type="button" class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs" style="margin:0;">
				<i class="layui-icon layui-icon-edit"></i>
			</button>
		</div>
	</div>
</script>

<script type="text/html" id="table_status_templet">
{{# if(d.status === '1') { }}
	<button type="button" class="layui-btn layui-bg-blue layui-btn-xs" lay-event="table_status_on">未使用</button>
	{{# } else if(d.status === '2') { }}
		<button type="button" class="layui-btn layui-bg-green layui-btn-xs layui-disabled" >已过期</button>
	{{# } else { }}
		<button type="button" class="layui-btn layui-bg-red layui-btn-xs layui-disabled" >已使用</button>
		{{# } }}
</script>

<?php include($root.'/index/components/footer.php'); ?>

<script>
	const that = this;
	
	const app = Vue.createApp({
	    data(){
	        return{
				uid: '<?= $userrow['uid'] ?>' === '1' ? true : false,
				row: {
					data: [],
				},
				cx: {
					pagesize: 10,
				},
				sex: [],
				eform: {
					codeKey: location.host,
					price: '0.1',
					num: '10',
					endtime: '',
					onlynum: '',
				},
				eform2: {
					codeKey: location.host,
					price: '0.1',
					num: '10',
					endtime: '',
					onlynum: '',
				},
				editT: 0,
	        }
	    },
			mounted() {
				const _this = this;
				_this.kamilist(1, 'one');
				_this.table_init();
				let loadIndex = layer.load(0);
				$('#kamiID').ready(() => {
					$("#kamiID").show();
					layer.close(loadIndex);
				})
				
			},
			methods: {
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
								none: '哦吼一条卡密都没得'
							},
							cols: [
								[ //标题栏

									{
										type: 'checkbox',
										// 	fixed: 'left'
										hide: !_this.uid
									},
									{
										field: 'code',
										title: '卡号',
										width: 140,
									},
									{
										field: 'price',
										title: '金额',
										width: 80,
										align: 'center',
									},
									{
										field: 'status',
										title: '状态',
										width: 80,
										align: 'center',
									    templet: '#table_status_templet'
									},
									{
										field: 'codeKey',
										title: '加密密钥',
										width: 130,
									},
									{
										field: 'user',
										title: '使用者',
										width: 90,
									},
									{
										field: 'usetime',
										title: '使用时间',
										width: 140,
										align: 'center',
									},
									{
										field: 'addtime',
										title: '生成时间',
										width: 140,
										align: 'center'
									},
									{
										field: 'endtime',
										title: '过期时间',
										width: 140,
										align: 'center',
										templet: '{{d.endtime?d.endtime:"永不过期"}}'
									},
									{
										field: 'onlynum',
										title: '批次最大可领',
										width: 100,
										align: 'center',
										templet: '{{d.onlynum?d.onlynum + "个":"不限制"}}'
									},
									{
										field: 'id',
										title: 'ID',
										align: 'center',
										hide: !_this.uid
									},
									{
										field: '',
										title: '操作',
										align: 'center',
										fixed: 'right',
										width: 85,
										templet: '#listTable_user_caoz'
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
								case 'listTable_user_del':
									_this.del(data.id);
									break;
								case 'listTable_user_edit':
								    _this.editT = 1;
									_this.add_open('edit', data);
									break;
								case 'table_status_on':
									data.status = 0;
									layer.confirm('是否将状态设置为已使用？', {icon: 3}, function(){
									    _this.edit(data.id, data);
                                      }, function(){
                                      });
									break;
								case 'table_status_off':
									data.status = 1;
									_this.edit(data.id, data);
									break;
								default:
									break;
							}

						});


					})
				},
				kamilist: function(page, type) {
					const _this = this;
					layui.use(function() {
						var util = layui.util;
						data = {
							cx: _this.cx,
							page: page ? page : 1,
						}
					    let loadIndex = layer.load(0);
						axios.post('/api/kami.php?act=kamilist', data, {
							emulateJSON: true
						}).then(r => {
							if (r.data.code === 1) {
								if (r.data.data) {
									_this.row = r.data;
								} else {}
								_this.table_init();
								if (true) {
									layui.use('table', function() {
										var laypage = layui.laypage;
										laypage.render({
											elem: 'listTable_laypage', // 元素 id
											count: _this.row.count, // 数据总数
											limit: _this.row.pagesize,
											limits: [10, 30, 50, 100],
											curr: _this.row.current_page,
											layout: ['count', 'prev', 'page', 'next', 'limit'], // 功能布局
											prev: '<i class="layui-icon layui-icon-left"></i>',
											next: '<i class="layui-icon layui-icon-right"></i>',
											jump: function(obj, first) {
												if (!first) {
													_this.cx.pagesize = obj.limit;
													_this.kamilist(obj.curr, '');
												}
											}
										});
									})

								} else {}
							} else {
								layer.msg(r.data.msg);
							}
							layer.close(loadIndex);
						})
					})
				},
				range: function(start, end){
				    var result = [];
                      for (var i = start; i < end; i++) {
                        result.push(i);
                      }
                      return result;
				},

				add_open: function(type, d) {
					const _this = this;
					_this.eform = JSON.parse(JSON.stringify(_this.eform2));
					if (type || _this.editT) {
						_this.eform = {
							status: Number(d.status) ? 1 : 0,
							price: d.price,
							codeKey: d.codeKey,
						}
					}
					layui.use(function() {
						layer.open({
							type: 1,
							content: $("#add"),
							title: type ? '编辑' : '生成卡密',
							maxmin: true,
							btn: [type ? '修改' : '生成', '取消'],
							scrollbar: false,
							yes: function(index) {
								layui.form.submit('add-form', function(data) {
								    
								    data.field.endtime = data.field.endtime?new Date(data.field.endtime).getTime():'';
								    
									if (type) {
									    data.field.status = d.status;
										_this.edit(d.id, data.field, index);
									} else {
										_this.add(data.field, index);
									}
								})
							},
							success: function() {
							 //   let today = new Date();
							 //   let year = today.getFullYear();
        //                         let month = today.getMonth() + 1; // 月份从0开始，所以要加1
        //                         let day = today.getDate();
        //                         if (month < 10) {
        //                             month = '0' + month;
        //                         }
        //                         if (day < 10) {
        //                             day = '0' + day;
        //                         }
        //                         let formattedDate = year + '-' + month + '-' + (day + 3 ) + ' 00:00:00';
							    
							      layui.laydate.render({
                                    elem: '#ID-laydate-type-datetime',
                                    type: 'datetime',
                                    value: '',
                                    isInitValue: true,
                                    disabledDate: function(date, type){
                                          return date.getTime() < new Date().getTime() - 24 * 60 * 60 * 1000;
                                        },
                                        disabledTime: function(date, type){
                                          return {
                                            hours: function(){
                                              return _this.range(0, new Date().getHours());
                                            },
                                            minutes:function(hour){
                                              return  hour===new Date().getHours()?_this.range(0, new Date().getMinutes()):[];
                                            },
                                            seconds:function(hour, minute){
                                              return hour===new Date().getHours()&&minute===new Date().getMinutes()?_this.range(0,  new Date().getSeconds()):[];
                                            }
                                          };
                                        }
                                  });
							    
								layui.form.render();
							},
							end: function() {
							    $("#add").hide();
								for (let i in _this.eform) {
									_this.eform[i] = '';
								}
								_this.eform.status = 1;
								_this.editT = 0;
							}
						})
					})
				},
				add: function(d, index) {
					const _this = this;
					layer.load(0);
					axios.post('/api/kami.php?act=km_sc', d, {
						emulateJSON: true
					}).then((r) => {
						if (r.data && r.data.code === 1) {
							layer.msg("生成成功！")
							layer.close(index);
							layer.closeAll('loading');
							_this.kamilist(1);
							
						} else {
							layer.msg("生成失败！")
						}
					})
				},
    			edit: function(id, d, index) {
				    console.log('d2',d);
    				const _this = this;
    				let loadIndex = layer.load(0);
    				data = {
        					id: id,
        					data: {
        						codeKey: d.codeKey,
        						price: d.price,
        						status: d.status,
        					}
        				};
    				axios.post("/api/kami.php?act=kami_up", data, {
    					emulateJSON: true
    				}).then(r => {
    					if (r.data.code === 1) {
    						layer.msg('修改成功！')
    						layer.close(index);
    						_this.kamilist(1);
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
							layer.msg('请选择订单');
							return
						}
					}

					layer.confirm('是否删除？', {
						title: '警告',
						btn: ['删除', '算了'] //按钮
					}, function(index) {
						let loadIndex = layer.load(0);
						_this.sex = checkData.map(item => item.id);
						axios.post('/api/kami.php?act=kami_del', {
							sex: _this.sex
						}, {
							emulateJSON: true
						}).then(r => {
							layer.close(loadIndex);
							if (r.data.code === 1) {
								_this.kamilist(1);
								layer.msg('删除成功');
							} else {
								layer.msg('删除失败');
							}
							layer.close(index);
						})
					}, function() {});

				})
			},
                export_table: function() {
                    layui.use(function() {
    
                        let table = layui.table;
                        let util = layui.util;
                        let layer = layui.layer;
    
                        const e_time = util.toDateString(new Date(), "yyyy_MM_dd HH_mm_ss");
                        const e_title = '卡密_' + e_time;
                        const fileName = e_title + '.xls'; // 定义文件名
    
                        layer.open({
                            type: 1,
                            title: "是否导出当前页数据",
                            content: '<div class="layui-padding-3"><span style="color:red">' + fileName + '<br/>数据合计：' + vm.row.data.length + '条</span><hr />将会为您导出当前页所有数据！<br />若需要按条件导出，请先设置好条件！</div>',
                            btn: ['导出', '取消'],
                            area: ['350px'],
                            yes: function(index) {
                                vm.exportFile(fileName);
                                layer.close(index);
                                layer.msg('导出成功');
                            }
                        });
    
                    })
                },
                exportFile: function(fileName, data) {
                    // 将数据转换为表格形式，这里仅为示例，需要根据实际数据格式进行调整
                    let tableHTML = '<table  border="1" style="font-family:微软雅黑">' + $('#listTable2').html() + '</table>';
                    $('#listTable2').hide();
                    // 创建Blob对象
                    let blob = new Blob([tableHTML], {
                        type: "application/vnd.ms-excel"
                    });
                    let downloadUrl = URL.createObjectURL(blob);
                    let a = document.createElement("a");
                    a.href = downloadUrl;
                    a.download = fileName;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(downloadUrl);
                },
			},
	})
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var LAY_appVm = app.mount('#kamiID');
    // -----------------------------
    
</script>