<?php $this->title = '收货地址管理'; ?>
<?php include "../views/prompt/information.php"; ?>
<!--没有地址时-->
<script type="text/javascript">
    $(function(e) {
        var vali=new Validators();
        $("#btn").bind("click", vali.subByJs);
    });
</script>
<SCRIPT LANGUAGE = JavaScript>  
<!--  
//** Power by Fason(2004-3-11)  
//** Email:fason_pfx@hotmail.com  
  
var s=["s1","s2","s3"];  
var opt0 = ["","",""];  
function setup()  
{  
for(i=0;i<s.length-1;i++)    
  document.getElementById(s[i]).onchange=new Function("change("+(i+1)+")");    
    change(0);    
    //document.getElementById('s1').value='北京市';  
   // change(1);  
    //document.getElementById('s2').value='北京市';  
    //change(2);  
    //document.getElementById('s3').value='延庆镇';
}  
//-->
</SCRIPT>  
<!-- 添加地址 -->
<div class="append" style="display: none;">
    <h2>新增地址</h2>
    <form action="/member/new-address" method='post'>
        <ul id="memu">
            <li>
                <label for="">收货人名字</label><input type="text" name='realname' placeholder="点击输入名字" class="user" valType="required" msg="<font color=red>*</font>收货人不能为空" onBlur="textBlur(this)" onFocus="textFocus(this)"/>
                <!-- <span>*收货人姓名要和身份证信息保持一致</span> -->
            </li>
            <li>
                <label for="idno">身份证号码</label><input type="text" name='idno' placeholder="点击输入身份证" class="user" valType="IDENTITY" msg="<font color=red>*</font>身份证格式不正确" />
            </li>
            <li><label for="mobile">手机号码</label><input type="text" name='mobile' placeholder="点击输入手机" class="user" valType="MOBILE" msg="<font color=red>*</font>手机格式不正确" /></li>
            <li><label for="s_province">省&nbsp;份</label><select id="s1" name="s_province" valType="required" msg="<font color=red>*</font>省&nbsp;份不能为空" ></select></li>
            <li><label for="s_city">市&nbsp;区</label><select id="s2" name="s_city" valType="required" msg="<font color=red>*</font>市&nbsp;区不能为空"></select></li>
            <li><label for="s_county">区/县</label><select id="s3" name="s_county" valType="required" msg="<font color=red>*</font>区/县不能为空"></li>
            <li><input type="text" / style="display: none;"></li>
            <li><label for="address">详细地址</label><textarea type="text" name='address' placeholder="点击输入地址" class="user" valType="required" msg="<font color=red>*</font>详细地址不能为空"/></textarea></li>
            <li><label for="" class="bel">设为默认地址</label><span class="bel bor"><input type="checkbox"  name='isdefault' class="select"  checked /></span></li>
        </ul>
        <ol class="present">
            <li onclick="cancel()"><a href="javascript:;">取消</a></li>
            <li><input type='submit' name='submit' value='确认' ></li>
        </ol>
    </form>
</div>

<!-- 有地址时样式 -->
<div class="possess">
<?php $num=2; foreach($UserAddress as $key=>$address):?>
    <div class="location-box location-box<?=$address['id']?>">

        <h2><?=($address['isdefault']==1) ? '默认': $num.'号'?>送货地址<a href="javascript:;" class="edit" onclick="addressEdit(<?=$num?>)">编辑</a></h2>
        <ul class="location">
            <li>收货人：<p><?=$address['realname']?></p> <span><?=$address['mobile']?></span></li>
            <li>收货地址：<p><?=$address['address']?></p></li>
        </ul>
        <!-- 地址修改 -->
        <ol class="revamp revamp<?=$num?>" >
            <li><a href="javascript:;" onclick="editAddress(<?=$address['id']?>,<?=$num?>)">修改</a></li>
            <li><span></span><a href="javascript:;" onclick="DelectAddress(<?=$address['id']?>)">删除</a></li>
        </ol>
        <div class="bullet">
            <p>是否确定删除？</p>
            <ul>
                <li>否</li>
                <li>是<span></span></li>
            </ul>
        </div>
    </div>
<?php $num++;endforeach;?>
    <div class="home-page"><a><span class="icon-position"></span>添加新的地址<i></i></a></div>
</div>
<script type="text/javascript">
    function editAddress(id,num){
        window.location.href='/member/addressedit?id='+id+'&num='+num;
    }
    function addressEdit(parameter){
         $(this).text(($(this).text()=='完成') ? '编辑' : '完成');
            $(".revamp"+parameter).slideToggle("slow");
    }
    function DelectAddress(id){
        $.get("/member/delete-address?id=" + id, function (resources) {
                if(resources.msg==true){
                     $('.location-box'+id).remove();
                }else{
                    alert('删除失败，请稍后重试！');
                }
            }, "JSON");
    }
    function cancel(){
       $(".append").css('display','none');
      $(".tip-yellowsimple").css('display','none');
      $(".possess").css('display','block');
    }
</script>
<script type="text/javascript">
	$(function(){
        $(".home-page").click(function(){
			$(".append").css('display','block');
            $(".tip-yellowsimple").css('display','block');
            $(".possess").css('display','none');
        });
        $(".bel").click(function(){
          $(this).toggleClass("bor");
        });
    });


</script>
