<?php $this->title = '订单物流进度'; ?>

<style type="text/css">
.logistic-order-detail-btn{
    display: inline-block; 
    margin: 20px;
    width: 100%;
    font-family: "微软雅黑";
    color: #fff;
    font-size: 18px;
    border-radius: 4px;
    text-align: center;
}
.logistic-order-detail-btn a{
    display:block;
    width: 90%;
    background-color: #c89e6b;
    line-height: 36px;
    border-radius: 5px;
}
</style>

<div class="inquire">
    <div class="inquire-title">
        <p>订单编号:&nbsp;<a href="#"><?=$order['ordersn']?></a></p>
        <span><?php echo date('Y-m-d H:i', $order['createtime']);?></span>
    </div>

    <?php if (isset($logistic['company']['icon'])) {?>
    <div class="material-flow">
        <img src="<?=$logistic['company']['icon']?>" style="  width: 58px;">
        <span>物流单号：<?=$order['expresssn']?></span>
    </div>
    <?php } else if($order['sendexpress']>0){?>
        <div class="material-flow">
            <span>物流公司：<?=$order['expresscom']?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            物流单号：<?=$order['expresssn']?>
            </span>
        </div>
    <?php } ?>

    <ul>
    <?php foreach ($logistic['process'] as $step) {?>
        <li>
            <ol>
                <li>
                    <p><?=date('Y-m-d', strtotime($step['time']))?></p>
                    <h2><?=date('H:i', strtotime($step['time']))?></h2>
                </li>
                <li><div><?=$step['desc']?></div> <span></span></li>
            </ol>
        </li>
    <?php } ?>
    </ul>
    
    <div class="logistic-order-detail-btn">
        <a href="/order/detail?order_id=<?=$order['id']?>" style="">查看订单详情</a>
    </div>
</div>