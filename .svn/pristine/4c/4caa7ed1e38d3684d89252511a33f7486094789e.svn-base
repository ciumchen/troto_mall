<?php $this->title = '主卡发钱 关联用户 - '.Yii::$app->params['site_name'];?>
<style type="text/css">
.blk { background: #fff; border-radius: 10px; margin: 5px 10px; height: 100%; padding: 10px; width: 95%; }
.list-group-item-text{ line-height: 1.5; }
.txtbold{ color: #3e5569; font-weight:bold; }
.btnhandout { margin-top: .5em; border-radius: 15px; background-color: #5895c7; border-color: #73b0e5; line-height: 1.18; width: 60%; }
</style>
<div class="row" style="background-color:#E7F3ED; color:#3e5569;">
    <div class="col-xs-12">
        <form role="form" style="margin-top:15px; text-align:center;">
            <div class="form-group">
                <label class="radio-inline txtbold"><input type="radio" value="" <?php if($expire==''){?> checked="checked" <?php }?> name="history" onclick="location.href='?expire='">全部</label>
                <label class="radio-inline txtbold"><input type="radio" value="-3" <?php if($expire=='-3'){?> checked="checked" <?php }?> name="history" onclick="location.href='?expire=-3'">近3天发放</label>
                <label class="radio-inline txtbold"><input type="radio" value="-1" <?php if($expire=='-1'){?> checked="checked" <?php }?> name="history" onclick="location.href='?expire=-1'">近1个月发放</label>
            </div>
        </form>
    </div>

  <?php foreach ($corpteamList as $corpteamItem): ?>
    <div class="col-xs-12 blk">
        <div class="row">
          <div class="col-xs-4 col-md-2"><img style="width: 100px;height: 100px;" src="<?=$corpteamItem['avatar']?>"></div>
          <div class="col-xs-8 col-md-2">
              <ul class='list-group'>
                <li class="list-group-item-text txtbold">姓名：<?=$corpteamItem['realname']?></li>
                <li class="list-group-item-text txtbold">电话：<?=$corpteamItem['phone']?></li>
                <li class="list-group-item-text txtbold">车牌：<?=$corpteamItem['carsn']?></li>
                <li class="list-group-item-text txtbold"><button name="uid" class="btn btn-primary btnhandout" onclick="window.location.href='/member/handl?uid=<?=$corpteamItem['uid']?>&driverid=<?=$corpteamItem['driverid']?>'">给该账户 发钱</button></li>
              </ul>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6 txtbold">累计发放：￥<?=empty($corpteamItem['amount']) ? 0 : $corpteamItem['amount']?></div>
          <div class="col-xs-6 txtbold">最近发放：<?=date('Y-m-d', $corpteamItem['createtime'])?></div>
        </div>
    </div>
  <?php endforeach; ?>

    <?php include dirname(__DIR__).'/layouts/TrotoBottomNavBar.php'; ?>
</div>

