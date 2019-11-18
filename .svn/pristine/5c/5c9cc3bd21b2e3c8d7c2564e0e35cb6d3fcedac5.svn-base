<?php $this->title = '购物车'; ?>
<?php include "../views/prompt/information.php"; ?>
<!-- 我的购物车 -->
<script type="text/javascript" src="/js/lib/jquery.cookie.js"></script>

<div class="shopping-box">
    <?php if ($getCartData): ?>
        <div class="storage">
            <?php $num = 1; ?>
            <?php foreach ($getCartData as $key => $CartData): ?>
                <ul class="shopping-title car<?php echo ($key) ? $key : $CartData['id']; ?>" style='display:none'>
                    <li style=''><label for=""><input type="checkbox" class="select"></label></li>
                    <li style=''><?php echo ($CartData['name']) ? $CartData['name'] : '暂无仓库' ?>
                    </li>
                </ul>

                <div class="indent-box car<?php echo ($key) ? $key : $CartData['id']; ?>">
                    <i class="edit edit<?= $CartData['c_id'] ?>"
                       onclick="edit(<?= $CartData['c_id'] ?>,<?= $num ?>,<?php echo $CartData['total']; ?>)">编辑</i>
                    <ol class="indent">
                        <li amount="<?= $CartData['marketprice'] * $CartData['total']; ?>">
                            <label class="marg <?= ($CartData['selected'] == 1 ? 'bor' : '') ?>" goodsid="<?= $CartData['id'] ?>">
                                <input type="checkbox" class="select">
                            </label>
                        </li>
                        <li>
                            <a href="/goods/detail?id=<?= $CartData['id'] ?>">
                                <img src="<?php echo $CartData['thumb']; ?>" alt="">
                            </a>
                        </li>

                        <li>
                            <dl>
                                <dd><h3><?php echo $CartData['title']; ?></h3></dd>
                                <dd><span></span>日本直购</dd>
                                <dd>￥<a href=""><?php echo $CartData['marketprice'] * $CartData['total']; ?></a>
                                    <s>￥<?php echo $CartData['originalprice'] * $CartData['total']; ?></s>
                                    <i>x<?php echo $CartData['total']; ?></i>
                                </dd>
                            </dl>
                            <div class="amend amend<?= $CartData['c_id'] ?>">
                            <?php if ($CartData['maxbuy']==0) :?>
                                <div class="number">
                                    <span class="decrease" onclick="reduction(<?= $num ?>)"></span>
                                    <input id="res<?= $num ?>" type="text" value="<?php echo $CartData['total']; ?>"
                                           maxlength="2" style='border:0px'>
                                    <span class="increase" onclick="Totaljia(<?php echo $CartData['g_total']; ?>,<?= $num ?>)"></span>
                                </div>
                            <?php endif; ?>
                                <a href="javascript:;" class="delete"
                                   onclick="delCart(<?php echo $CartData['id'] ?>,<?php echo $uid ?>)"><span
                                        class="icon-bin"></span>删除</a>
                            </div>
                        </li>
                    </ol>
                </div>
                <?php $num++; ?>
            <?php endforeach; ?>
            <ul class="statistics" style='display:none'>
                <li style='display:none'>活动优惠：- ￥60.00</li>
                <li>本仓总计（不含税）：￥<i class="states" style="font-style:normal"><?= $sum ?></i></li>
            </ul>
        </div>
    <?php else: ?>
        <div class="mainbody">
            <img src="/images/shopping-cart.jpg">
            <p>购物车没有商品哦，快去买买买吧~</p>
            <a href="/home/index">立即选购</a>
        </div>
    <?php endif; ?>
</div>

<!-- 合计、结算 -->
<div class="settle">
    <label for="" id="lab">
        <input type="checkbox" class="select" id="sel-all">
    </label>
    <ul>
        <li>
            <h1>全选</h1>
            <p>已选 <span id="sel-num">0</span> 件货品</p>
        </li>
        <li>
            <h1>合计：￥<i id="SumTotal" style="font-style:normal"><?= $sum ?></i></h1>
        </li>
        <li>
            <button id="btn_checkout" onclick="checkout();">结算</button>
        </li>
    </ul>
</div>

<script>
    $(function () {
        $('ol.indent li input.select').click(function () {
            var label = $(this).parent();
            var goodsid = label.attr('goodsid');

            if (label.hasClass('bor')) {
                label.removeClass('bor');
                updateCartSelected(goodsid, 0);
            } else {
                label.addClass('bor');
                updateCartSelected(goodsid, 1);
            }

            updateCartSelNum();
        });

        //全选按钮
        $('#lab').click(function () {
            var btn = $(this);

            if (btn.hasClass('bor')) {
                $('ol.indent li label').removeClass('bor');
            } else {
                $('ol.indent li label').addClass('bor');
            }

            updateCartAll();

            updateCartSelNum();
        });

        updateCartSelNum();
    });

    function edit(id, num, totals) {
        $(".edit" + id).html(($(".edit" + id).text() == '完成') ? '编辑' : "<i onclick='editSubmit(" + id + "," + num + "," + totals + ")'>完成</i>");
        $(".amend" + id).toggle();
    }

    function editSubmit(c_id, num, totals) {
        var total = $("#res" + num).val();
        if (total != totals) {
            $.get("/cart/editcart", {op: "edit", c_id: c_id, total: total},
                function (result) {
                    if (result.update_msg == false) {
                        alert('请勿重复修改相同值');
                    } else {
                        history.go(0);
                    }
                }, 'json');
        }
    }

    function reduction(nums) {
        var str = $("#res" + nums).val();
        var num = parseInt(str) - parseInt("1");
        if (num >= 1) {
            $("#res" + nums).val(num);
        }
    }
    function Totaljia(total, nums) {
        var str = $("#res" + nums).val();
        var num = parseInt(str) + parseInt("1");
        if (num <= total) {
            $("#res" + nums).val(num);
        }
    }

    function checkout() {
        var num = $('label.bor').length;

        if (num === 0) {
            alert('请选择结算商品！');
            return false;
        }

        window.location.href = '/order/confirm';
    }

    /*删除购物车*/
    function delCart(id, uid) {
        if (uid != 0) {
            var product_id = id;
            var str = '.car' + id;
            $.get("/cart/addcart?id=" + product_id + "&op=del", function (msg) {
                $(str).remove();
                $('.SumTotal').text(msg.sumTotal);
                if (msg.sumTotal == 0) {

                    $('.mainbody').css("display", 'block');
                    $('.storage').css("display", 'none');
                }
            }, "JSON");

        } else {
            $('.car' + id).remove();
            var cart = JSON.parse($.cookie('cart'));
            var cart_rest = [];

            $.each(cart, function (i, item) {
                if (item.id == id) {
                    return;
                }

                cart_rest.push(item);
            });
            $.cookie('cart', JSON.stringify(cart_rest), {path: '/', expires: 7});
            history.go(0);
        }
    }

    function updateCartSelNum() {
        var sel_num = $('ol.indent li label.bor').length;
        var all_num = $('ol.indent').length;

        all_items_toggle(sel_num === all_num);

        $('#sel-num').html(sel_num);

        updateAmount();
    }

    function updateAmount() {
        var items = $('ol.indent li label.bor');
        var amount = 0;

        $.each(items, function (idx, goods) {
            amount += ($(goods).parent().attr('amount') * 1);
        });

        $('#SumTotal').html(amount.toFixed(2));
    }

    function all_items_toggle(all_selected) {
        if (all_selected) {
            $('#lab').addClass('bor');
        } else {
            $('#lab').removeClass('bor');
        }
    }

    function updateCartSelected(id, selected) {
        $.get("/cart/selected", {
            id: id,
            selected: selected
        }, function (msg) {
            //todo
        }, "JSON");
    }

    function updateCartAll() {
        var is_selected = $('#lab').hasClass('bor') ? 0 : 1;
        var items = $('ol.indent li label');

        $.each(items, function (idx, goods) {
            var goodsid = $(goods).attr('goodsid');
            updateCartSelected(goodsid, is_selected)
        });
    }
</script>