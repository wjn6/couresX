<?php
$mod = 'blank';
$title = '价格列表';
require_once ('head.php');
?>

<div class="app-content-body" id="priceID" style="display: none;">
  <div class=" control layui-padding-1">
    <div class="row">
      <div class="col-sm-12">
        <div class=" panel-default">
          <div class="table-responsive">
            <table class="layui-table" lay-size="sm" style="width: max-content;min-width: 100%;
            margin:0;">

              <thead>
                <tr>
                  <th class="center" style="width:40px;">ID</th>
                  <th class="center" style="width:50px;">我的
                  </th>
                  <th>平台名称</th>
                  <th v-for="(item,index) in row" :key="index">{{row.length?item.rate:''}}</th>
                  <!--<th>排序</th>-->
                </tr>
              </thead>
              
              <tbody>
                <?php
                $result = $DB->query("select * from qingka_wangke_class where status=1 order by sort ");
                while ($rs = $DB->fetch($result)) {
                  echo "<tr >
                            <td class='center' >" . $rs['cid'] . "</td>
                        	    <td class='center'>" . sprintf("%.2f", $rs['price']* $userrow['addprice']) . "</td>
                        	    <td>" . $rs['name'] . "</td>
                      	  	<td  v-for='(item,key) in row' :key='key'>
                      	  	    {{ row.length? (" . json_encode($rs['price']) . " * Number(item.rate)).toFixed(2) : '1' }}
                        	    </td>
                         </tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="https://lib.baomitu.com/perfect-scrollbar/1.4.0/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/main.min.js"></script>
<script src="assets/js/aes.js"></script>

<?php include($root.'/index/components/footer.php'); ?>

<script>
    const app = Vue.createApp({
        data(){
            return{
                row: [],
            }
        },
        mounted() {
          const _this = this;
            let loadIndex = layer.load(0);
                $("#priceID").ready(()=>{
                    layer.close(loadIndex);
                    $("#priceID").show();
                    _this.getDengji()
                })
        },
    methods: {
      getDengji: function () {
        const _this = this;
        layer.load(0);
        axios.post("/apiadmin.php?act=adddjlist", {
          emulateJSON: true
        }).then(function (r) {
            layer.closeAll('loading');
          if (r.data.code == 1) {
            _this.row = r.data.data;
            console.log('row', _this.row)
          } else {
            layer.msg(r.data.msg, {
              icon: 2
            });
          }
        });
      },
    },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#priceID');
    // -----------------------------
</script>