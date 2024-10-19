<?php
include('head.php');
if ($userrow['uid'] != 1) {
	exit("<script language='javascript'>window.location.href='login.php';</script>");
}
?>
<div class="app-content-body ">
	<div class="wrapper-md control" id="userindex">
		<div class="row">
			<div class="col-lg-6">
            <div class="card" style="box-shadow: 18px 18px 30px #d1d9e6, -18px -18px 30px #fff;border-radius: 8px;">
              <div class="panel-heading font-bold bg-white">数据统计</div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                          <li class="list-group-item"><i class="glyphicon glyphicon-yen"></i>总用户:<span class="badge label label-black">
								<?php
									$a = $DB->count("select count(*) from qingka_wangke_user ");
									echo $a."人";
									?>
								</span></span></li>
                          <li class="list-group-item"><i class="glyphicon glyphicon-yen"></i>今日新增用户<span class="badge label label-black">
								    <?php
									$a = $DB->count("select count(*) from qingka_wangke_user where addtime>'$jtdate'  ");
									echo $a."人";
									?>
								</span></span></li>
                          <li class="list-group-item"><i class="glyphicon glyphicon-ok"></i>今日订单<span class="badge label label-black">
                              <?php
									$a = $DB->count("select count(*) from qingka_wangke_order where addtime>'$jtdate'  ");
									echo $a."条";
									?>
                          </span></span></li>
                          <li class="list-group-item"><i class="glyphicon glyphicon-yen"></i>今日销售<span class="badge label label-black">
                              <?php
									$a = $DB->query("select * from qingka_wangke_order where addtime>'$jtdate'  ");
									while ($c = $DB->fetch($a)) {
										$zcz += $c['fees'];
									}
									echo $zcz."元";
									?>
                          </span></span></li>
			            </div>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-6">
            <div class="card" style="box-shadow: 18px 18px 30px #d1d9e6, -18px -18px 30px #fff;border-radius: 8px;">
              <div class="panel-heading font-bold bg-white">单个统计</div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped">
								<thead>
									<tr>
										<th>ID</th>
										<th>平台名称</th>
										<th>今日单量</th>
										<th>总单量</th>
									</tr>
								</thead>
								<tbody>
										
									<?php
									$a = $DB->query("select * from qingka_wangke_order order by oid desc limit 50");
									$b = $DB->query("select cid,name from qingka_wangke_class where status!=0 order by cid");
									while ($rs = $DB->fetch($b)) {
										$count1 = $DB->count("select count(*) from qingka_wangke_order where cid='{$rs['cid']}' and addtime>'$jtdate' ");
										$count2 = $DB->count("select count(*) from qingka_wangke_order where cid='{$rs['cid']}' ");
										echo "<tr>
										        <td>" . $rs['cid'] . "</td>
										        <td>" . $rs['name'] . "</td>
										        <td>" . $count1 . "</td>
										        <td>" . $count2 . "</td>
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
          <div class="row">
			    
	</div>
</div>
</div>

<?php include($root.'/index/components/footer.php'); ?>

</html>