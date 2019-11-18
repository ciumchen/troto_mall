<?php $this->title = '必买清单 - '.Yii::$app->params['site_name']; ?>
<link rel="stylesheet" type="text/css" href="/v3/css/goods-topic.css">

<div class="product">
	<h2>「 必买清单 」</h2>
	<h5>十点一刻总有一款适合您</h5>
<?php if (!empty($list)): ?>
	<ul>
	<?php foreach ($list as $goodsKey => $goodsItem): ?>
		<li>
			<div class="commodity"><a href="/goods/detail?id=<?=$goodsItem['id']?>"><img src="<?=$goodsItem['thumb1']?>"></a></div>
			<div class="commodity-text">
				<h3><a href="/goods/detail?id=<?=$goodsItem['id']?>"><?=$goodsItem['title']?></a></h3>
				<p><?=$goodsItem['fdesc']?></p>

                <div class="purchase">
                    <spam class="red-text">￥<?=$goodsItem['marketprice']?></spam><span style="text-decoration: line-through">￥<?=$goodsItem['originalprice']?></span>
                    <input type="button" value="立即抢购" onclick="window.location.href='/goods/detail?id=<?=$goodsItem['id']?>'">
                    <div class="clear"></div>
                </div>
			</div>
		</li>
	<?php endforeach; ?>
	</ul>
<?php else:?>
	一大波优质新品即将来袭，敬请持续关注~
<?php endif; ?>
</div>

<?php include '../views/layouts/wxshare.php';?>