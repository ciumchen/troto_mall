<style type="text/css">
		.name form{
			width: 100%;
			display: block;
			overflow: hidden;
		}
		.name form>input{
			width: 100%;
			border-bottom: #c7c8c8 solid 1px;
			padding:15px 20px;
			color: #000;
			font-family: "微软雅黑";
		}
		.name form>ul{
			width: 80%;
			margin-left: 10%;
			display: block;
			overflow: hidden;
			margin-top: 40px;
		}
		.name form>ul>li{
			width: 48%;
			height:40px;
			float: left;
			text-align: center;
			font-family: "微软雅黑";
			line-height: 40px;
		}
		.name form>ul>li:nth-child(1){
			margin-right: 4%;
		}
		.name form>ul>li:nth-child(1) a{
			width: 100%;
			height: 40px;
			display: inline-block;
			box-sizing: border-box;
			border:solid #000 1px;
			color:#000;
			font-size: 16px;
		}
		.name form>ul>li:nth-child(2){
			background: #000;
			color:#fff;
			font-size: 16px;
		}

		#submits{
			    width: 97%;
	    height: 40px;
	    /* float: left; */
	    text-align: center;
	    font-family: "微软雅黑";
	    line-height: 40px;
	    background: #000;
	    border: 0px;
	    color: #fff;
	        font-size: 16px;
		}
    </style>
<div class="name">
<form action="/member/user-name?uid=<?=$UserInfo['uid']?>" method="post">
	<input type="text" value="<?=$UserInfo['nickname']?>"  maxlength="30" name='nickname' valType="required" msg="<font color=red>*</font>昵称不能为空" >
	<ul>
		<li><a href="/member/profile">取消</a></li>
		<li><input id='submits' type='submit' name='submit' value='确认'></li>
	</ul>
</form>
</div>
