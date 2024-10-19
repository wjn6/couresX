<?php
include_once('../../confing/common.php');
include_once('jscss.php');
if ($userrow['uid'] != 1) {
    alert("您的账号无权限！", "/index.php");
    exit();
}
// 控制权限
if ($islogin != 1 || $userrow['uid'] != "1") {
    exit("<script language='javascript'>window.location.href='//" . $_SERVER['HTTP_HOST'] . "/index';</script>");
}

?>

<div id="EailsQueue" class="layui-padding-2 layui-font-red" style="dispaly:none;">
	<div v-if="!emailsListGetStatus" class="layui-font-12 layui-font-blue">
		获取中...
	</div>
	<div v-else>
		<div class="layui-font-14 layui-font-green" v-if="!emailsList.length">
			无待发送的邮件
		</div>
		<div v-else>
			<button class="layui-btn layui-btn-xs layui-bg-red" @click="emailsListClean"
				style="margin-bottom: 5px; float: right;">清理</button>
			<table class="layui-table" lay-size="sm" style="margin: 0;">
				<thead>
					<th>
						类型
					</th>
					<th>
						状态
					</th>
					<th>
						接收人
					</th>
					<th>
						内容
					</th>
					<th>
						备注
					</th>
					<th>
						进程
					</th>
					<th>
						创建时间
					</th>
				</thead>
				<tbody>
					<tr v-for="(item,index) in emailsList" :key="index">
					    
						<td style="width: 50px;minWidth: 50px">
							{{ item.type }}
						</td>
						<td style="width: 50px; min-width: 50px;">
							{{ item.status == 0?'等待发送':'等待下次' }}
						</td>
						<td style="width: 140px">
							{{ item.j }}
						</td>
						<td  style="minWidth: 180px">
							<div style="width: 80px; white-space: nowrap; overflow-x: hidden; text-overflow: ellipsis;">
								{{ item.f_t }}
							</div>
						</td>
						<td style="width: 120px; min-width: 120px;">
							{{ item.status_t?item.status_t:'无' }}
						</td>
						<td style="width: 50px; min-width: 50px;">
							{{ item.cpid }}
						</td>
						<td style="width: 140px;min-width: 140px;">
							{{ item.addtime }}
						</td>
						
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
    const app = Vue.createApp({
        data(){
            return{
    			emailsList: [],
    			emailsListGetStatus: false,
    			emailsListGetTimer: null,
            }
        },
		mounted() {
			const _this = this;
			
            let loadIndex = layer.load(0);
              $("#EailsQueue").ready(()=>{
                  layer.close(loadIndex);
                  $("#EailsQueue").show();
                  _this.emailsListGet();
			        _this.emailsListGetTimer = setInterval(_this.emailsListGet, 1000);
              })
             
		},
		methods: {
			emailsListGet() {
				const _this = this;
				let loadIndex = !_this.emailsListGetStatus ? layer.load(0) : 0;
				$.post("/apiadmin.php?act=emailsListGet", {}, {
					emulateJSON: true
				}).then(r => {
					console.log(r)
					layer.close(loadIndex);
					_this.emailsListGetStatus = true;
					if (r.code === 1) {
						_this.emailsList = r.data;
					} else {
						layer.msg('获取失败');
					}
				})
			},
			emailsListClean: function () {
				const _this = this;
				let loadIndex = layer.msg('清理中', {
					icon: 16,
					shade: 0.01,
					time: 0,
				});
				$.post("/apiadmin.php?act=emailsListGet", {
					type: "clean",
				}, {
					emulateJSON: true
				}).then(r => {
					layer.close(loadIndex);
					_this.emailsListGetStatus = true;
					layer.close(loadIndex);
					if (r.code === 1) {
						layer.msg('清理成功');
						_this.emailsListGet();
					} else {
						layer.msg('清理失败');
					}
				})
			},
		},
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#EailsQueue');
    // -----------------------------
</script>