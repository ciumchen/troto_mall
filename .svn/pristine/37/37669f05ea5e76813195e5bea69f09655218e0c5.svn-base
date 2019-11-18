<?php $this->title = '支付订单 - '.Yii::$app->params['site_name'];?>
<style type="text/css">
body{ background-color:#f2f2f2; }
.blk { background:#fff; border-radius:10px; margin:5px 10px; height:100%; padding:10px; width:95%; }
.blk button {font-size:18px;}
.paychannel li { width:100%; height:60px; line-height:60px; list-style:none; background:#fff; border-bottom:1px solid #eee; }
.list-group-item-text{ line-height:1.5; }
.ordetail{ color:#3e5569; font-weight:bold; padding-left:15% }
.btnpay , .btnpay:hover { padding:.5em; border-radius:0; border-color:#0e91d8; color:#fff; background-color:#0e91d8; line-height:2.68; width:100%; position:fixed; font-size:16px; bottom:0; left:0;}
.paychannel img { width:40px; height:40px; margin-left:10px; margin-right:10px; }
.paychannel label { width: 100%; height: 60px; display: inline-block; }
.paychannel input[type="radio"] { display: none; }
.paychannel input[type="radio"]:checked + span {
    border: 1px solid #0e91d8;  border-radius: 20px;
    background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2lpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDo1MmMyZTliNy02M2MxLTc2NDctOWYzZC0xMDM1ZjdiODk1NTYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6ODVFMDIzNjIzRjkxMTFFODgyMTZEM0ZEOTM3NzYyREIiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6ODVFMDIzNjEzRjkxMTFFODgyMTZEM0ZEOTM3NzYyREIiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjhFRjAyQjU0OUIyMzExRTU5OUI3OEYyM0IxNUJFNEMxIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjhFRjAyQjU1OUIyMzExRTU5OUI3OEYyM0IxNUJFNEMxIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+J/e5fgAAAtBJREFUeNrsmEtIVFEYgMeU2shUBENgagU9VkULi+xJYRC0SHpuKkgjMrDHQrCN1iZchJlElMymVhHRIoPKsiQqwkWPRRRRoIsoFypBVONM0/fDH9yGOXPPvXNmjOjAxz+D9/H533P+888tSafTkb9plPwTQgcHDs0g1EAlLIAojMAwPIPXtte6uO7CH9/LAorsIhyA1TAtx6Hj8BBOwosg9yizFKkjnIPFlteVDG5VnsJuzZ7vmGIh00O4G0Amc6yE99CUV4YQkUfSB2sczFW5z3lYAfvCZqjfkYx37IWewEJkJ06oLdDKboTD1kLIbCDsL3C5OQsVthmKF6H+yZy64itEduoJc4tUlNfDIr8MHXF801dwHHqz7RJwzChEdqJaM1yNR7owOqHVcEx9rgxJjZjqSOYWrIWvusWcMRwXIxHzTEIVjmSuwhb9HNXiuslwbAKWmITmG076BHVaYRM+MnHdt2TMggcWxbXStHWUG054R4twT+fZEOGOYafvgqOebN/PtoqyTOxyU4ZGDCfVItKivcuA/sfjGce0eWQWwhMLGRkT8N0kNGQ4qRQ6kGpXqUHCKhjTv8vSPaWfl+nqqrJtEOGzSeilz8ltSHWqlHSEG2GPbgMRXVXSlMUCLAB59G+MLSw3HCXM9Ju4CDV6zoloI3bdpr/KGFIWpnO9lKlS91lcpAGJa/B7QTTAjRAyItHrlcnWoElF3Wlxse3S3CP1kbg5ZL2SR3Mp516G7QdtzGzG0jxkUtr899u0HzsgWYTdvsmqHyJLozovCjW+wWkYtG5hkbpM6C6QjCyc9sBNPlLNjqVE5rZOiVSo32UqJZvqjzxEJlSgQ2USef1Q1Mc3W3ucIC8CEiryHJbrz+qU05cN1J05hBOwTbcIucFP3ZOSGmU7+AI3Qd4kPA7ysiH06xjkYtpcVatEUjsG2Zve2mbTmdD/F1aTNX4JMADNgc1Lluf4XgAAAABJRU5ErkJggg==) no-repeat;
    background-size: 20px 20px;
}
.paychannel input[type="radio"] + span { border: 1px solid #0e91d8; border-radius:20px; width:22px; height:22px; float:right; margin-top: 20px; margin-right:20px; }
.paychannel input[type="radio"]:checked + span{}
</style>
<div class="row" style="padding-top:5px">
    <div class="col-xs-12 blk">
        <ul class='list-group'>
          <li class="list-group-item-text"><center><h2>￥<?=$amount?></h2></center></li>
          <li class="list-group-item-text ordetail">订单编号：<?=$sn?></li>
          <li class="list-group-item-text ordetail" id="timer"></li>
          <li class="list-group-item-text" style="padding:15px 10px; font-size:12px;">注：下单后15分钟内完成支付 超时未支付订单将被自动关闭</li>
        </ul>      
    </div>

    <div class="col-xs-12 blk paychannel">
      <?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'Wechat') === false):?>
        <li><label><img src="/images/icon_wxpay.png">微信支付<input name="paytype" type="radio" value="wx" checked="checked"><span></span></label> </li>
      <?php elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Alipay') === false):?>
        <li><label><img src="/images/icon_alipay.png">支付宝支付<input name="paytype" type="radio" value="ali" checked="checked"><span></span></label> </li>
      <?php endif; ?>
        <li><label><img src="/images/icon_wallet.png">余额支付<input name="paytype" type="radio" value="qb" disabled="disabled"><span></span></label> </li>
    </div>

    <div class="col-xs-12">
      <button class="btn btnpay" onclick="payNow();return false;">确认支付：￥<?=$amount?></button>
    </div>
</div>

<?php include '../views/layouts/TrotoWxshare.php';?>
<script type="text/javascript">
<?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'Wechat') === false):?>
//数组内为jssdk授权可用的方法，按需添加，详细查看微信jssdk的方法
wx.config(<?php echo $payment->jssdk->buildConfig(['chooseWXPay']) ?>);
// 发起支付
function payNow(){
  wx.chooseWXPay({
      timestamp: <?= $paymentJson['timestamp'] ?>,
      nonceStr: '<?= $paymentJson['nonceStr'] ?>',
      package: '<?= $paymentJson['package'] ?>',
      signType: '<?= $paymentJson['signType'] ?>',
      paySign: '<?= $paymentJson['paySign'] ?>',
      success: function (res) {
        window.location.href='/order/payment?sn=<?=$sn?>';
      }
  });
}
<?php endif; ?>

/** 倒计时 **/
var maxtime = 15*60;
function CountDown() {
  if (maxtime >= 0) {
      minutes = Math.floor(maxtime / 60);
      seconds = Math.floor(maxtime % 60);
      msg = "支付剩时：" + minutes + "分" + seconds + "秒";
      document.all["timer"].innerHTML = msg;
      --maxtime;
  } else{
      clearInterval(timer);
      alert("时间到，结束!");
  }
}
timer = setInterval("CountDown()", 1000);
</script>