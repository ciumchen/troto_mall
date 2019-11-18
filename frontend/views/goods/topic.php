<?php
use yii\helpers\Html;

$this->title = $topicDetail['title'];
?>

<style type="text/css">
.topic-content {padding: 10px; background-color: #fff;}
.topic-content img { outline-width:0px; vertical-align:top;}
.topic-content p {
	font-family: microsoft yahei, Arial, Helvetica, sans-serif;
	font-size: 14px; line-height: 20px;
}
.topic-content h4 { font-size: 14px; font-weight: bold; line-height: 20px; }
</style>
<div class="topic-content">
	<?php echo html_entity_decode($topicDetail['content']);?>
</div>