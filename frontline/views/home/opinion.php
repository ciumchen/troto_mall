
<!-- 日历插件 -->
<script type="text/javascript" src="/date_js/laydate.js"></script>
<link href="/css/opinion.css" rel="stylesheet">
<div id="overflow_canvas_container">	
	<div id="canvas">
		<div id="layer_250" type="media" class="cstlayer">
			<div class="wp-media_content" >
				<div class="img_over">
					<img class="img_lazy_load paragraph_image" type="zoom" src="/img/500222773.jpg">
				</div>
			</div>
		</div>
		<div id="layer_E41" type="media" class="cstlayer">
			<div class="wp-media_content">
				<div class="img_over" >
					<img class="img_lazy_load paragraph_image" type="zoom" src="/img/8cfv.png" >
				</div>
			</div>
		</div>
		<div id="layer_5C0" type="title" class="cstlayer" >
			<div class="wp-title_content">
				<div style="text-align: center;">
					<span >意见反馈</span>
				</div>
			</div>
		</div>
		<div id="layer_04F" type="title" class="cstlayer">
			<div class="wp-title_content" >
				<span>只有适合的，才是最好的，才是真正花对钱。十点一刻怀着为用户精选臻品的情怀，将十点一刻海淘生活馆打造成“线上平台＋线下体验店＋综合服务体系”的生态圈，并不会因其商品的名过其实而去贩卖一个品牌，更为有用、更为适合、更为人们所需的良品才是十点一刻倾注一切所追求的。</span>
			</div>
		</div>
		<div id="layer_1DD" type="new_message_form" class="cstlayer">
			<div class="wp-new_message_form_content" >	

				<form class="mesform" method="post" action="#" style="position:relative;overflow:hidden;" novalidate="novalidate">
					<input type="hidden" name="msid" value="1">
					<ul class="mfields">		
						<li>
							<div class="title">标题</div>
							<div class="inpbox"><input type="text" name="title" class="inptext" maxlength="50"></div>
							<div style="clear:both;overflow:hidden;"></div>
						</li>		
						<li>
							<div class="title">地址</div>
							<div class="inpbox"><input type="text" name="address" class="inptext" maxlength="50"></div>
							<div style="clear:both;overflow:hidden;"></div>
						</li>		
						<li>
							<div class="title">日期</div>
							<div class="inpbox"><input type="text" name="time" class="inptext datepicker" value='<?php echo date('Y-m-d H:i:s');?>' readonly="readonly" onClick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss'})"></div>
							<div style="clear:both;overflow:hidden;"></div>
						</li>		
						<li>
							<div class="title">内容</div>
							<div class="inpbox"><textarea name="content" id="content" class="txtarea" maxlength="150"></textarea></div>
							<div style="clear:both;overflow:hidden;"></div>
							<input type="hidden" value="<?php echo Yii::$app->request->csrfToken; ?>" name="_csrf" >
						</li>		        
					</ul>
					<div style="clear:both;overflow:hidden;"></div>
					<a href="javascript:;" class="btnsubmit">提交</a>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
 window.status=1;
 $(".btnsubmit").click(function(){
 	var title = $("input[name='title']").val();
 	var address = $("input[name='address']").val();
 	var _csrf = $("input[name='_csrf']").val();
 	var content = $("#content").val();
 	var time = $("input[name='time']").val();
 	if(title==''||address==''||content==''||window.status==0){
 		alert("请完善数据,请勿重复提交！");
 		return;
 	}
 	$.ajax({
        url:"/home/submit-opinion",
        type:"POST",
        data:{
        	title:title,
        	address:address,
        	time:time,
        	content:content,
        	_csrf:_csrf
        },
        success:function(data){
        	data=$.parseJSON(data);
            alert(data.content);
            window.status=0;
        },
        error:function(e){
            alert(错误);
        }
    }); 
 });
</script>
<!-- 结束 -->
	<script>
	$(document).ready(function(){ 
		$("#nav_layermenu li").eq(5).addClass("lihover");
		$("#nav_layermenu li>a").eq(5).addClass("ahover");
		$("#nav_fomune li").eq(5).addClass("lihover");
		$("#nav_fomune li>a").eq(5).addClass("ahover");
		$("#scroll_container_bg").css("height",1745);
	})
	</script>