<?php
$title = "加速优化工具";
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

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="/assets/css/buttons.css">
</head>


<style>
  #oatool {
    position: relative;
    background: url("/assets/images/bg.webp");
  }

  #oneID {
    position: relative;
    left: 50%;
    transform: translateX(-50%);
    width: max-content;
    margin: 0 0 15px 0;
    background: transparent;
  }

  .oneID_box {
    cursor: pointer;
    width: 200px;
    height: 200px;
    background: transparent;
    border-radius: 50%;
  }

  .oneID_box1 {
    width: 100%;
    height: 100%;
    background: #1B9AFA;
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
    border-radius: 50%;
    position: relative;
  }

  .oneID_box2 {
    width: 100%;
    height: 100%;
    position: absolute;
    z-index: 1;
    background: #fff;
    scale: .92;
    border-radius: 50%;
  }

  .oneID_box2:hover {
    cursor: url('data:image/svg+xml;utf8,<svg height="26" width="26" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="fill:cornflowerblue; "><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M96 256H128V512H0V352H32V320H64V288H96V256zM512 352V512H384V256H416V288H448V320H480V352H512zM320 64H352V448H320V416H192V448H160V64H192V32H224V0H288V32H320V64zM288 128H224V192H288V128z"/></svg>'), auto;
  }

  .oneID_box2 .main {
    font-size: 14px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .oneID_box2 .main .mainok {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    width: max-content;
  }

  .oneID_box2 .main .mainok .num {
    font-size: 70px;
    color: #1B9AFA;

  }

  .oneID_box2 .main .mainok .title {
    color: #ffffff;
    width: max-content;
    padding: 7px 15px;
    border-radius: 5px;
  }

  .layui-anim {
    animation-duration: 1s;
    -webkit-animation-duration: 1s;
  }

  .el-collapse-item__content {
    padding-bottom: 5px;
  }

  .loadingBox {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    margin: 60px 0 0 0;
  }

  .orders_radio .layui-form-radio .layui-icon,
  .orders_radio .layui-form-radio div {
    font-size: 12px;
  }
</style>

<body>
  <div id="oatool" style="display:none;max-width:400px;min-height:100vh;box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);">

    <div class="layui-padding-2 layui-font-12 layui-font-green" style="border-bottom:1px solid #eee;">
      加速优化工具&nbsp;&nbsp;不用太频繁，推荐两周操作一次！
    </div>

    <div class="layui-padding-3">
      <div id="oneID">
        <div class="oneID_box button-glow button-inverse">
          <div class="oneID_box1" title="立即加速" @click="Object.values(list.data).filter(subObj => subObj.need === true).length?optimizeGo():''">
            <div class="oneID_box2">
              <div class="main" style="width: max-content;">
                <template v-if="!jcok">
                  <div class="layui-font-26 layui-font-blue layui-font-18 layui-anim layui-anim-scale layui-anim-loop"
                    style="font-weight: bold;">
                    检测中...
                  </div>
                </template>
                <template v-else>
                  <div class="mainok layui-anim layui-anim-fadein">
                    <div v-if="yhok.ok" class="layui-font-26 layui-font-blue" :class="yhok.t!=='优化成功'?'layui-anim layui-anim-scale layui-anim-loop':''" style="font-weight: bold;">
                      {{yhok.t}}
                    </div>
                    <template v-else-if="!Object.values(list.data).filter(subObj => subObj.need === true).length">
                      <div @click.stop class="num" style="margin-bottom: 17px; font-size: 38px;">
                        太棒啦
                      </div>
                      <div @click.stop class="title button-3d button-primary   ">
                        无需优化
                      </div>
                    </template>
                    <template v-else>
                      <div class="num">
                        <!--filter -->
                        {{ Object.values(list.data).filter(subObj => subObj.need === true).length }}<span
                          class="layui-font-12">项</span>
                      </div>
                      <div class="title button-3d button-primary  ">
                        立即加速
                      </div>
                    </template>
                    <div class="layui-font-12 layui-font-green"
                      style="margin: 10px 0 0; text-decoration: underline;cursor: pointer;" @click.stop="optimizeGet()">
                      重新检测
                    </div>
                  </div>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div id="twoID" class="layui-form" lay-filter="listForm" v-if="list.length">
        <div class="layui-font-13 layui-font-green">
          请勾选需要优化的项目
        </div>
        <el-collapse v-model="needOpenList">

          <el-collapse-item name="orders">
            <template #title>
              <div @click.stop>
                <input type="checkbox" name="orders" lay-filter="orders_checkbox" value="orders" :disabled="!list.data.orders.need">
              </div>
              订单数据
              <span class="layui-font-12 layui-font-green">&nbsp;
                <span v-if="!list.data.orders.need">无需优化</span>
                <span v-else>大小:{{ jssize(list.data.orders.size) }}</span>
              </span>
            </template>
            <div class="">
              <div v-if="!list.data.orders.need">
                无订单
              </div>
              <div class="orders_radio" v-else style="font-size: 12px;">
                <input type="radio" name="orders_del" lay-filter="orders_del_radio" value="week" title="清理一周内的订单">
                <input type="radio" name="orders_del" lay-filter="orders_del_radio" value="month" title="清理一个月内的订单">
                <input type="radio" name="orders_del" lay-filter="orders_del_radio" value="half_year" title="清理半年内的订单">
                <input type="radio" name="orders_del" lay-filter="orders_del_radio" value="year" title="清理一年内的订单">
                <input type="radio" name="orders_del" lay-filter="orders_del_radio" value="all" title="所有订单">
                <input type="radio" name="orders_del" lay-filter="orders_del_radio" value="zdy" title="自定义日期范围">
                <div v-show="zdy_ordersDel" style="display: flex; justify-content: space-between; width: 100%;">
                  <input type="text" class="layui-input" name="orders_del_zdy_start" id="orders_del_zdy_start" placeholder="请选择开始日期">
                  <input type="text" class="layui-input" name="orders_del_zdy_end" id="orders_del_zdy_end" placeholder="请选择结束日期">
                </div>
              </div>
            </div>
          </el-collapse-item>

          <el-collapse-item name="emails">
            <template #title>
              <div @click.stop>
                <input type="checkbox" name="emails" value="emails" :disabled="!list.data.emails.need">
              </div>
              邮件队列<span v-if="!list.data.emails.need" class="layui-font-12 layui-font-green">&nbsp;无需优化</span>
            </template>
            <div class="layui-font-blue">
              <div v-if="!list.data.emails.need">
                邮件已全部发送完毕！
              </div>
              <div v-else>
                <table class="layui-table" lay-size="sm">
                  <tbody>
                    <tr>
                      <td width="50%">
                        共 {{list.data.emails.count}} 条
                      </td>
                      <td width="50%">
                        占用 {{jssize(list.data.emails.size)}}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </el-collapse-item>

          <el-collapse-item name="log">
            <template #title>
              <div @click.stop>
                <input type="checkbox" name="log" value="log" :disabled="!list.data.log.need">
              </div>
              日志数据
              <span class="layui-font-12 layui-font-green">&nbsp;
                <span v-if="!list.data.log.need">无需优化</span>
                <span v-else>大小:{{ jssize(list.data.log.size) }}</span>
              </span>
            </template>
            <div class="layui-font-blue">
              <div v-if="!list.data.log.need">
                无日志数据
              </div>
              <div v-else>
                <table class="layui-table" lay-size="sm">
                  <tbody>
                    <tr>
                      <td width="50%">
                        共 {{list.data.log.count}} 条
                      </td>
                      <td width="50%">
                        占用 {{jssize(list.data.log.size)}}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </el-collapse-item>

          <el-collapse-item name="pay">
            <template #title>
              <div @click.stop>
                <input type="checkbox" name="pay" value="pay" :disabled="!list.data.pay.need">
              </div>
              支付记录数据
              <span class="layui-font-12 layui-font-green">&nbsp;
                <span v-if="!list.data.pay.need">无需优化</span>
                <span v-else>大小:{{ jssize(list.data.pay.size) }}</span>
              </span>
            </template>
            <div class="layui-font-blue">
              <div v-if="!list.data.pay.need">
                无支付记录
              </div>
              <div v-else>
                <table class="layui-table" lay-size="sm">
                  <tbody>
                    <tr>
                      <td width="50%">
                        共 {{list.data.pay.count}} 条
                      </td>
                      <td width="50%">
                        占用 {{jssize(list.data.pay.size)}}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </el-collapse-item>

          <el-collapse-item name="redis">
            <template #title>
              <div @click.stop>
                <input type="checkbox" name="redis" value="redis" :disabled="!list.data.redis.need">
              </div>
              Redis多线程日志
              <span class="layui-font-12 layui-font-green">&nbsp;
                <span v-if="!list.data.redis.need">无需优化</span>
                <span v-else>大小:{{ jssize(list.data.redis.size) }}</span>
              </span>
            </template>
            <div class="layui-font-blue">
              <div v-if="!list.data.redis.need">
                无日志
              </div>
              <div v-else>
                <table class="layui-table" lay-size="sm">
                  <tbody>
                    <tr v-for="(item,index) in list.data.redis.list" :key="index">
                      <td width="50%">
                        {{index}}
                      </td>
                      <td width="50%">
                        {{jssize(item)}}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </el-collapse-item>

          <el-collapse-item name="mysql_log">
            <template #title>
              <div @click.stop>
                <input type="checkbox" name="mysql_log" value="mysql_log" disabled="">
              </div>
              数据库二进制日志数据
              <span class="layui-font-12 layui-font-green">&nbsp;
                <span v-if="!list.data.mysql_log.need">小于1GB，无需优化</span>
                <span v-else>大小:{{ jssize(list.data.mysql_log.size) }}</span>
              </span>
            </template>
            <div class="layui-font-blue">
              <div v-if="!list.data.mysql_log.need">
                小于1GB，无需优化
              </div>
              <div v-else>
                <p class="layui-font-red layui-font-12">请到 <span class="layui-font-blue">/www/server/data/</span>
                  文件夹下自行删除前缀为
                  <span class="layui-font-blue">mysql-bin</span> 的文件，并关闭mysql的<span class="layui-font-blue">二进制日志</span>
                  且重启mysql服务
                </p>
                <table class="layui-table" lay-size="sm">
                  <tbody>
                    <tr v-for="(item,index) in list.data.mysql_log.list" :key="index">
                      <td width="50%">
                        {{index}}
                      </td>
                      <td width="50%">
                        {{jssize(item)}}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </el-collapse-item>

        </el-collapse>
      </div>

      <div class="loadingBox" v-else>
        <img style="height: 150px;" src="/index/assets/loading.svg" alt="Example SVG">
      </div>
    </div>

  </div>

</body>

</html>

<script>
  const app = Vue.createApp({
    data() {
      return {
        jcok: false,
        yhok: {
          ok: false,
          t: '',
        },
        list: {
          data: {},
          length: 0,
        },
        needOpenList: [],
        needList: [],
        zdy_ordersDel: false,
      }
    },
    mounted() {
      const _this = this;
      let loadIndex = layer.load(0);
      $("#oatool").ready(() => {
        layer.close(loadIndex);
        $("#oatool").show();
        _this.optimizeGet();
      })
    },
    methods: {

      optimizeGet() {
        const _this = this;
        _this.zdy_ordersDel = false;
        _this.jcok = false;
        _this.yhok.ok = false;
        _this.yhok.t = '';
        _this.needOpenList = [];
        _this.list.data = [];
        _this.list.length = 0;
        axios.post('/api/api.php?act=optimize', {}, {
          emulateJSON: true
        }).then(r => {

          _this.jcok = true;
          if (r.data.code === 1) {
            _this.list.data = r.data.data;
            _this.list.length = Object.keys(r.data.data).length;
            for (let i in r.data.data) {
              if (r.data.data[i].need) {
                _this.needOpenList.push(i);
              }
            }

            layui.use(() => {
              layui.laydate.render({
                elem: '#orders_del_zdy_start',
                format: 'yyyy-MM-dd',
                max: 0,
                done(value, date, endDate) {
                  console.log(value)
                },
              });
              layui.laydate.render({
                elem: '#orders_del_zdy_end',
                format: 'yyyy-MM-dd',
                max: 0,
                done(value, date, endDate) {
                  console.log(value)
                },
              });
              layui.form.on('checkbox(orders_checkbox)', (data) => {
                let elem = data.elem;
                let checked = elem.checked;
                let value = elem.value;
                console.log("checked", checked)
                if (checked) {
                  layui.form.val('listForm', {
                    orders_del: "week",
                  })
                } else {
                  layui.form.val('listForm', {
                    orders_del: false,
                  })
                }
              })
              layui.form.on('radio(orders_del_radio)', (data) => {
                let elem = data.elem;
                let value = elem.value;
                console.log(value)
                if (value == 'zdy') {
                  _this.zdy_ordersDel = true;
                  orders_del_zdy_start = '';
                  // orders_del_zdy_end = layui.util.toDateString(new Date(), "yyyy-MM-dd");
                  orders_del_zdy_end = '';
                } else {
                  _this.zdy_ordersDel = false;
                  orders_del_zdy_start = '1';
                  orders_del_zdy_end = '1';
                }
                layui.form.val('listForm', {
                  orders: "orders",
                  orders_del_zdy_start: orders_del_zdy_start,
                  orders_del_zdy_end: orders_del_zdy_end,
                })
              })
              layui.form.render();
            })

          }

        })
      },
      optimizeGo() {
        const _this = this;
        layui.use(() => {
          let data = layui.form.val('listForm')
          console.log("data", data)
          for (let i in data) {
            if (!data[i]) {
              layer.msg("请选择优化项")
              return
            }
          }
          if (JSON.stringify(data) === '{}') {
            layer.msg("请选择优化项")
            return false
          }
          _this.yhok.ok = true;
          _this.yhok.t = '优化中...';

          axios.post("/api/api.php?act=optimizeGo", {
            needList: data,
          }, {
            emulateJSON: true
          }).then(r => {

            setTimeout(() => {
              if (r.data.code === 1) {
                layer.msg(`成功优化 ${r.data.oknum} 项`);
                _this.yhok.ok = true;
                _this.yhok.t = '优化成功';
                _this.needOpenList = [];
                layui.use(() => {
                  layui.form.val('listForm', {
                    emails: false,
                    log: false,
                    pay: false,
                    redis: false,
                    mysql_log: false,
                    orders: false,
                    orders_del: false,
                  })
                })
                _this.zdy_ordersDel = false;
                let loadIndex = layer.load(0);
                axios.post('/api/api.php?act=optimize', {}, {
                  emulateJSON: true
                }).then(r => {
                  if (r.data.code === 1) {
                    _this.list.data = r.data.data;
                    for (let i in r.data.data) {
                      if (r.data.data[i].need) {
                        _this.needOpenList.push(i);
                      }
                    }
                  }
                  layer.close(loadIndex);
                  layui.use(function() {
                    layui.form.render()
                  })
                })
              } else {
                layer.msg(r.data.msg ? r.data.msg : "优化失败，请重试！");
              }
            }, 3000)



          })

        })
        return false
      },
      jssize(size = 0) {
        return size <= 1024 * 1 ? size + ' KB' : (size / 1024).toFixed(2) + ' MB'
      },
    },
  })
  app.use(ElementPlus)
  for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component)
  }
  var LAY_appVm = app.mount('#oatool');
  // -----------------------------
</script>