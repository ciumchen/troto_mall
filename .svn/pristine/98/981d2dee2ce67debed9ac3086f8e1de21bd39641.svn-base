<?php include "../views/prompt/information.php"; ?>

<style type="text/css">
    input[value], textarea[value] {
        color: #fff;
    }

    ::-webkit-input-placeholder { /* WebKit browsers */
        color: #0e0d0c;
    }
</style>
<script type="text/javascript">
    $(function (e) {
        var vali = new Validators();
        $("#btn").bind("click", vali.subByJs);
    });
</script>

<SCRIPT LANGUAGE=JavaScript>
    <!--
    //** Power by Fason(2004-3-11)
    //** Email:fason_pfx@hotmail.com

    var s = ["s1", "s2", "s3"];
    var opt0 = ["", "", ""];
    function setup() {
        for (i = 0; i < s.length - 1; i++)
            document.getElementById(s[i]).onchange = new Function("change(" + (i + 1) + ")");
        change(0);
        document.getElementById('s1').value = "<?=$edit['province']?>";
        change(1);
        document.getElementById('s2').value = "<?=$edit['city']?>";
        change(2);
        document.getElementById('s3').value = "<?=$edit['area']?>";
    }
    //-->
</SCRIPT>

<!-- 添加地址 -->
<div class="append">
    <?php if ($edit['isdefault']): ?>
        <h2>默认送货地址</h2>
    <?php else: ?>
        <h2><?= ($num) ? $num : '无' ?>号地址</h2>
    <?php endif; ?>

    <form action="/member/address-submit?id=<?=$id?>&num=<?=$num?>" method="POST">
        <ul id="memu">
            <li>
                <label for="">收货人名字</label>
                <input type="text" value="<?= $edit['realname'] ?>" name='realname' class="user" onBlur="textBlur(this)" onFocus="textFocus(this)"
                       valType="required" msg="<font color=red>*</font>收货人不能为空"/>
            </li>

            <li>
                <label for="">身份证号码</label>
                <input type="text" value="<?= $edit['idno'] ?>" class="user" name='idno' valType="IDENTITY" msg="<font color=red>*</font>身份证格式不正确"/>
            </li>

            <li>
                <label for="">手机号码</label>
                <input type="text" value="<?= $edit['mobile'] ?>" class="user" name='mobile' valType="MOBILE" msg="<font color=red>*</font>手机格式不正确"/>
            </li>

            <li><label for="">省&nbsp;份</label>
                <select id="s1" name="s_province" valType="required" msg="<font color=red>*</font>省&nbsp;份不能为空"></select>
            </li>

            <li><label for="">市&nbsp;区</label>
                <select id="s2" name="s_city" valType="required" msg="<font color=red>*</font>市&nbsp;区不能为空"></select>
            </li>

            <li><label for="">区/县</label>
                <select id="s3" name="s_county" valType="required" msg="<font color=red>*</font>区/县不能为空">
            </li>

            <li><input type="text"/></li>
            <li><label for="">详细地址</label>
                <textarea type="text" class="user" name='address' valType="required" msg="<font color=red>*</font>详细地址不能为空"/><?=$edit['address']?></textarea>
            </li>

            <li><label for="" class"bel">设为默认地址</label>
                <span class="bel <?php echo ($edit['isdefault'] == 1) ? 'bor' : '' ?> ">
                    <input type="checkbox" name='isdefault' <?php echo ($edit['isdefault'] == 1) ? 'checked' : '' ?> class="select">
                </span>
            </li>
        </ul>

        <ol class="present">
            <li><a href="/member/address">取消</a></li>
            <li><input type='submit' name='submit' value='确认'/></li>
        </ol>
    </form>
</div>
</div>

<script type="text/javascript">
    $(function () {
        $(".edit").click(function () {
            $(this).text(($(this).text() == '完成') ? '编辑' : '完成');
            $(".revamp").slideToggle("slow");
        });
        $(".bel").click(function () {
            $(this).toggleClass("bor");
        });
    });
</script>

<script type="text/javascript" src="../js/area.js"></script>
<script type="text/javascript">
    _init_area();
</script>




