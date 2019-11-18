<?php
//处理分享信息
$domain   = Yii::$app->request->getHostInfo().'/';
$baseUrl  = $domain.Yii::$app->request->getpathinfo();
$shareUrl = $baseUrl.'?'.http_build_query($_GET);
$wxshare['title'] = isset($this->title)?$this->title:Yii::$app->params['site_name'];
$wxshare['thumb'] = isset($wxshare['thumb']) ? $wxshare['thumb'] : $domain.'images/logo_64px.jpg';
$wxshare['desc']  = isset($wxshare['desc']) ? $wxshare['desc'] : '十点一刻海淘生活馆主营母婴、保健、化妆、轻奢等商品。';
$wxshare['link']  = $baseUrl.'?'.http_build_query($_GET);
?>

<script type="text/javascript">wx.config(<?=$signPackage?>);</script>
