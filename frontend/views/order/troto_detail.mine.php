<?php $this->title = '订单详情'; ?>
<style type="text/css">
.container { margin:0; padding:0 }

/* 必要布局样式css */
.flexView {
    width: 100%;
    height: 100%;
    margin: 0 auto;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
}

.scrollView {
    width: 100%;
    height: 100%;
    font-size: 0.98rem;
    -webkit-box-flex: 1;
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
    position: relative;
    margin-top: 0;
    margin-bottom: 60px;
}

.navBar {
    height: 44px;
    position: relative;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    z-index: 1002;
    background: #ffffff;
}

.navBar:after {
    content: '';
    position: absolute;
    z-index: 2;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid #e2e2e2;
    -webkit-transform: scaleY(0.5);
    transform: scaleY(0.5);
    -webkit-transform-origin: 0 100%;
    transform-origin: 0 100%;
}

.navBar-item {
    height: 44px;
    min-width: 25%;
    -webkit-box-flex: 0;
    -webkit-flex: 0 0 25%;
    -ms-flex: 0 0 25%;
    flex: 0 0 25%;
    padding: 0 0.9rem;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    font-size: 0.7rem;
    white-space: nowrap;
    overflow: hidden;
    color: #a0a0a0;
    position: relative;
}

.navBar-item:first-child {
    -webkit-box-ordinal-group: 2;
    -webkit-order: 1;
    -ms-flex-order: 1;
    order: 1;
    margin-right: -25%;
    font-size: 0.9rem;
    font-weight: bold;
}

.navBar-item:last-child {
    -webkit-box-ordinal-group: 4;
    -webkit-order: 3;
    -ms-flex-order: 3;
    order: 3;
    -webkit-box-pack: end;
    -webkit-justify-content: flex-end;
    -ms-flex-pack: end;
    justify-content: flex-end;
}

.center {
    -webkit-box-ordinal-group: 3;
    -webkit-order: 2;
    -ms-flex-order: 2;
    order: 2;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    height: 44px;
    width: 50%;
    margin-left: 25%;
}

.center-title {
    text-align: center;
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    display: block;
    text-overflow: ellipsis;
    font-size: 0.95rem;
    color: #333;
}

.icon {
    width: 20px;
    height: 20px;
    display: block;
    border: none;
    float: left;
    background-size: 20px;
    background-repeat: no-repeat;
}

.order-list {
    background-image: url("/images/order_header_bg.png");
    background-size: cover;
    height: 5.5rem;
    position: relative;
    margin-bottom: 30px;
}

.icon-complete {
    margin-right: 5px;
    background-size: 19px;
    height: 19px;
    background-image: url('/images/icon_right.png');
}

.order-complete {
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    align-items: center;
    color: #fff;
    padding: 30px 15px;
    font-size: 0.99rem;
}

.order-process {
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    align-items: center;
    color: #fff;
    padding: 5%;
    /*font-size: 0.85rem;*/
    background: #fff;
    box-shadow: 0 3px 4px #ddd;
    border-radius: 5px;
    margin: 0 15px;
    position: absolute;
    width: 92%;
    bottom: -25px;
}

.order-process-hd {
    -webkit-box-flex: 1;
    -webkit-flex: 1;
    flex: 1;
    min-width: 0;
    color: #333;
}

.order-process-bd {
    text-align: right;
    color: #808080;
    padding-right: 13px;
    position: relative
}

.order-process-bd:after {
    content: " ";
    display: inline-block;
    height: 6px;
    width: 6px;
    border-width: 2px 2px 0 0;
    border-color: #2e2d2d;
    border-style: solid;
    -webkit-transform: matrix(0.71, 0.71, -0.71, 0.71, 0, 0);
    transform: matrix(0.71, 0.71, -0.71, 0.71, 0, 0);
    position: relative;
    top: -2px;
    position: absolute;
    top: 50%;
    margin-top: -4px;
    right: 2px;
    border-radius: 2px;
}

.palace {
    padding: 0;
    position: relative;
    overflow: hidden;
    /* margin-bottom: 15px; */
}

.mt10{margin-top: 10px;}
.palace-grid {
    position: relative;
    float: left;
    padding: 15px 1px 15px 1px;
    width: 19.99999%;
    box-sizing: border-box;
}
.palace-grid-icon {
    width: 50px;
    height: 50px;
    margin: 0 auto;
}
.palace-grid-icon img {
    display: block;
    width: 100%;
    height: 100%;
    border: none;
}
.palace-grid-text {
    display: block;
    text-align: center;
    color: #333333;
    font-size: 13px;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}
.palace-grid-text h2 {
    font-size: 1.25rem;
    font-weight: normal;
}

.divHeight {
    background: #f6f6f6;
    height: 10px;
    width: 100%;
    position: relative;
    overflow: hidden;
}

.footer {
    width: 100%;
    position: relative;
    z-index: 100;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    padding: 12px 5px 12px 5px;
    background-color: #ffffff;
    /* box-shadow: 0 -1px 9px #ddd; */
}

.footer:after {
    content: '';
    position: absolute;
    z-index: 0;
    top: 0;
    left: 0;
    width: 100%;
    height: 1px;
    /* border-top: 1px solid #D9D9D9; */
    -webkit-transform: scaleY(0.5);
    transform: scaleY(0.5);
    -webkit-transform-origin: 0 100%;
    transform-origin: 0 100%;
}

.tabBar-item {
    -webkit-box-flex: 1;
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    color: #979797;
}
.tabBar-item-text {
    display: inline-block;
    color: #252529;
    border: 1px solid #2e2d2d;
    border-radius: 20px;
    padding: 0.35rem 0.6rem;
}
.tabBar-item-active .tabBar-item-text {
    color: #000000;
    border: none;
}

.footer-fixed {
    position: fixed;
    bottom: 0;
    left: 0;
    z-index: 49;
}
.footer:after {
    content: '';
    position: absolute;
    z-index: 0;
    top: 0;
    left: 0;
    width: 100%;
    height: 1px;
    border-top: 1px solid #B2B2B2;
    -webkit-transform: scaleY(0.5);
    transform: scaleY(0.5);
    -webkit-transform-origin: 0 0;
    transform-origin: 0 0;
}

.icon-home { background-image: url('/images/icon_home.png'); }
.icon-me { background-image: url('/images/icon_user.png'); }
.icon-share { background-image: url('/images/icon_share.png'); }
.icon-loan { background-image: url('/images/icon_gift.png'); }
.icon-credit { background-image: url('/images/icon_cart.png'); }
.icon-return { background-image: url('/images/icon_arraow_left.png'); }
.icon-shop { background-image: url('/images/icon_shop.png'); margin-right: 10px; }
.icon-car { background-image: url('/images/icon_car.png'); margin-right: 5px; }

.order-shop {
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    align-items: center;
    color: #fff;
    font-size: 0.85rem;
    background: #fff;
    border-radius: 5px;
    position: relative;
    padding: 15px;
    width: 100%;
}
.order-shop-hd {
    color: #333;
    font-size: 0.85rem;
    position: relative;
    padding-right: 15px;
}
.order-shop-hd:after {
    content: " ";
    display: inline-block;
    height: 6px;
    width: 6px;
    border-width: 2px 2px 0 0;
    border-color: #2e2d2d;
    border-style: solid;
    -webkit-transform: matrix(0.71, 0.71, -0.71, 0.71, 0, 0);
    transform: matrix(0.71, 0.71, -0.71, 0.71, 0, 0);
    position: relative;
    top: -2px;
    position: absolute;
    top: 50%;
    margin-top: -4px;
    right: 2px;
    border-radius: 2px;
}
.order-shop:after {
    content: '';
    position: absolute;
    z-index: 2;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid #dedede;
    -webkit-transform: scaleY(0.5);
    transform: scaleY(0.5);
    -webkit-transform-origin: 0 100%;
    transform-origin: 0 100%;
}
.order-product {
    padding: 15px;
    position: relative;
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    align-items: center;
}
.order-product:after {
    content: '';
    position: absolute;
    z-index: 2;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid #dedede;
    -webkit-transform: scaleY(0.5);
    transform: scaleY(0.5);
    -webkit-transform-origin: 0 100%;
    transform-origin: 0 100%;
}
.order-product-hd {
    margin-right: .8em;
    width: 70px;
    height: 70px;
    line-height: 70px;
    text-align: center;
}
.order-product-hd img {
    width: 100%;
    max-height: 100%;
    vertical-align: top;
    border-radius: 5px;
}
.order-product-bd {
    -webkit-box-flex: 1;
    -webkit-flex: 1;
    flex: 1;
    min-width: 0;
}
.order-product-bd h2 {
    font-weight: 400;
    font-size: 0.98rem;
    color: #333;
    width: auto;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    word-wrap: normal;
    word-wrap: break-word;
    word-break: break-all;
    padding-bottom: 0.4rem;
}
.order-product-bd p {
    color: #808080;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
}
.order-text {
    position: relative;
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    align-items: center;
    padding-top: 0.5rem;
}
.order-text-hd {
    -webkit-box-flex: 1;
    -webkit-flex: 1;
    flex: 1;
    min-width: 0;
    /*font-size: 0.8rem;*/
    color: #333;
}
.order-text-bd em {
    font-style: normal;
    display: inline-block;
    /*font-size: 0.65rem;*/
    color: #252529;
    border: 1px solid #a9a9a9;
    border-radius: 20px;
    padding: 0.2rem 0.5rem;
}
.order-service {
    padding: 15px;
    position: relative;
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    align-items: center;
    color: #333;
}
.order-service:after {
    content: '';
    position: absolute;
    z-index: 2;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid #dedede;
    -webkit-transform: scaleY(0.5);
    transform: scaleY(0.5);
    -webkit-transform-origin: 0 100%;
    transform-origin: 0 100%;
}

.icon-service {
    background-image: url('/images/icon_services.png');
    position: absolute;
    top: 0;
    left: 0;
}

.order-service-hd {
    min-width: 0;
    font-size: 0.8rem;
    color: #333;
    text-align: center;
    width: 100px;
    margin: 0 auto;
    position: relative;
}

.fk-list {
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    align-items: center;
    padding: 15px;
    position: relative;
    /*font-size: 0.8rem;*/
    color: #333;
    margin-bottom: 15px;
}

.fk-list-hd {
    -webkit-box-flex: 1;
    -webkit-flex: 1;
    flex: 1;
    min-width: 0;
}

.fk-list-hd em {
    font-style: normal;
}

.fk-list-bd em {
    font-style: normal;
    color: #f93824;
}

.show-box {
    /* padding-top: 0.8rem; */
    display: none;
    width: 100%;
    font-size: 0.98rem;
}
.show-box .order-history-item{ padding: 5px 15px; }
.show-box p {
    color: #333333;
    padding-bottom: 0.2rem;
    line-height: 1.5;
}
.show-button {
    width: 120px;
    /* height: 20px; */
    margin: 0 auto;
    position: relative;
    font-style: normal;
    /* display: inline-block; */
    font-size: 0.65rem;
    color: #252529;
    border: 1px solid #a9a9a9;
    border-radius: 20px;
    padding: 0.2rem 0.5rem;
    text-align: center;
}
.show-button a {
    display: block;
    width: 80px;
    /* height: 20px; */
    /* margin-top: 10px; */
}
.show-button span {
    display: block;
    margin: 0 auto;
    position: relative;
    font-size: 0.8rem;
}

.fa-angle-right {
    background-size: 15px;
    position: absolute;
    top: -2px;
    right: 10px;
    background-image: url('/images/icon_arraow_down.png');
}
.fa-angle-down {
    background-size: 15px;
    position: absolute;
    top: 2px;
    right: 10px;
    background-image: url('/images/icon_arraow_up.png');
}

.order-up-flex {
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    align-items: center;
    padding: 2px 15px;
    color: #333;
    font-weight: bold;
}
.order-up-flex-hd {
    -webkit-box-flex: 1;
    -webkit-flex: 1;
    flex: 1;
    min-width: 0;
}

.show-box a {
    padding: 15px;
    display: block;
    position: relative;
}
.show-box a:after {
    content: '';
    position: absolute;
    z-index: 2;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid #dedede;
    -webkit-transform: scaleY(0.5);
    transform: scaleY(0.5);
    -webkit-transform-origin: 0 100%;
    transform-origin: 0 100%;
}

.fk-list:after {
    content: '';
    position: absolute;
    z-index: 2;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid #dedede;
    -webkit-transform: scaleY(0.5);
    transform: scaleY(0.5);
    -webkit-transform-origin: 0 100%;
    transform-origin: 0 100%;
}

.shop-list {
    position: relative;
    overflow: hidden;
    padding-left: 8px;
    background: #f2f2f5;
    padding-top: 8px;
}
.shop-list-item {
    width: 31%;
    float: left;
    position: relative;
    z-index: 0;
    padding: 0.32rem 0;
    font-size: 0.28rem;
    margin-right: 2%;
    border-radius: 4px;
}
.shop-list-selected .shop-list-item {
    width: 48%;
    padding: 0;
    overflow: hidden;
    margin-bottom: 8px;
    background: #fff;
}
.shop-list-item-hd { position: relative; }
.shop-list-item-hd img {
    width: 100%;
    height: auto;
    display: block;
    border: none;
}
.shop-list-selected .shop-list-item-tag {
    padding: 1px 4px;
    color: #fff;
    font-size: 0.65rem;
    /* position: absolute; */
    /* left: 0; */
    /* bottom: 39px; */
    width: auto;
    text-align: center;
    background-color: #e4163a;
    border-radius: 20px;
}

.shop-list-item-bd {
    text-align: center;
    font-size: 0.9rem;
    color: #333;
    padding: 0.5rem 0 0.01rem 0;
    font-weight: bold;
}

.shop-list-selected .shop-list-item-bd {
    text-align: left;
    padding-left: 10px;
    padding-right: 10px;
    padding-bottom: 10px;
}

.shop-list-selected .shop-list-item h3 {
    color: #333;
    font-size: 0.8rem;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    font-weight: normal;
    text-align: left;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}

.shop-list-selected .special-etc {
    color: #e82440;
    font-weight: bold;
    /* padding-top: 10px; */
    font-size: 0.9rem;
}
.shop-list-selected .special-etc {
    color: #e82440;
    font-weight: bold;
    /* padding-top: 10px; */
    font-size: 0.9rem;
}
.shop-list-selected .special-etc .special-etc-bd {
    text-decoration: line-through;
    color: #999;
    font-weight: normal;
    font-size: 0.7rem;
}
.me-box { padding-top:20px; }
.me-box-img img {
    width: 100%;
    height: auto;
    display: block;
    border: none;
}
</style>
<div class="row" style="margin:0; padding:0">
<section class="flexView">
    <header class="navBar navBar-fixed">
        <a href="javascript:;" class="navBar-item"><i class="icon icon-return"></i></a>
        <div class="center"><span class="center-title" style="font-size: 15px;">订单详情</span></div>
        <a href="javascript:;" class="navBar-item"><i class="icon icon-share"></i></a>
    </header>
    <section class="scrollView">
        <div class="col-xs-12 blk order-item" data-orderid="">
        <div class="row" style="border-bottom: 1px dotted #ccc;margin-left:0px;width:100%;">
            <div class="col-xs-5">
                智能柜下单
            </div>
            <div class="col-xs-7">下单时间：<span class="item-act"></span></div>
        </div>
        <div class="row mtb10">
          <div class="col-xs-4"><img src="https://files.1card1.cn/Platform/1095601/20190709/a46c44f5e2464129b24135d383634b7d.jpg" style="height: 100px;width: 100px;"></div>
          <div class="col-xs-8">
            <div class="row">
              <div class="col-xs-12">
                <ul class="list-group">
                  <li class="list-group-item-text txtbold">三力12R22.5/L812A</li>
                  <li class="list-group-item-text txtbold"><span>12/R22.5&nbsp;&nbsp;&nbsp;&nbsp;三力</span><span style="padding-left:15px"></span></li>
                </ul>
              </div>
              <!-- <div class="col-xs-6 txtbold">￥8946.00</div>
              <div class="col-xs-6"><button class="btn btn-primary btnserve" onclick="window.location.href='/member/handout?uid=98056'">申请售后</button></div> -->
            </div>
          </div>
        </div>
        <div class="row" style="border-top: 1px dotted #ccc; margin-left: 0px; width: 100%; padding-top: 5px;">
          <div class="col-xs-6">共条轮胎</div>
          <div class="col-xs-6" style="color:#f00">订单总额￥</div>
        </div>
    </div>
        <div class="order-service">
            <div class="order-service-hd"></div>
        </div>
        <div class="divHeight"></div>
        <div class="divHeight"></div>
        <div class="fk-list">
            <div class="fk-list-hd">订单编号: <em>700890888933</em></div>
            <div class="fk-list-bd">实付款: <em>￥199.20</em></div>
        </div>
        <div class="show-box">
            <div class="order-history-item">下单时间：2019-08-22 18:20:22</div>
            <div class="order-history-item">支付方式：微信在线支付</div>
            <div class="order-history-item">服务方式：自助下单 自助取胎安装</div>
            <div class="order-history-item">支付时间：2019-08-22 18:20:52</div>
            <div class="order-history-item">支付时间：2019-08-22 18:20:52</div>
            <div class="order-history-item">取胎详细：用户<span style="color:#f00">下单人微信昵称</span>于2019-08-22 18:30:51，在柜(南山蛇口港装载仓)自助完成取胎</div>
            <div class="order-history-item">完成时间：2019-08-22 18:30:52</div>
            <div class="divHeight"></div>
            <div class="order-up">
                <div class="order-up-flex">
                    <div class="order-up-flex-hd">商品总额</div>
                    <div class="order-up-flex-bd">￥199.20</div>
                </div>
                <div class="order-up-flex">
                    <div class="order-up-flex-hd">优惠</div>
                    <div class="order-up-flex-bd">-￥0.00</div>
                </div>
                <div class="order-up-flex">
                    <div class="order-up-flex-hd">服务费</div>
                    <div class="order-up-flex-bd">+￥0.00</div>
                </div>
            </div>
        </div>
        <div class="show-button">
            <a class="" href="javascript:void(0);" id="show-btn">
                <span id="show-open">展开订单完整信息</span>
                <i class="icon fa fa-angle-right"></i>
            </a>
        </div>
        <div class="me-box">
            <div class="me-box-img">
                <img src="http://www.17sucai.com/preview/1268063/2018-08-22/Order/images/icon-banner.png" alt="">
            </div>
            <div class="shop-list shop-list-selected">
                <a href="javascript:;" class="shop-list-item">
                    <div class="shop-list-item-hd"><img src="/uploads/lt/2BDZywIC.png"></div>
                    <div class="shop-list-item-bd">
                        <h3>
                            <span class="shop-list-item-tag">自营</span>
                            康威斯12.00R20/CPD68
                        </h3>
                        <p class="special-etc">
                            <span class="special-etc-hd">￥598</span>
                            <span class="special-etc-bd">￥898</span>
                        </p>
                    </div>
                </a>
                <a href="javascript:;" class="shop-list-item">
                    <div class="shop-list-item-hd"><img src="/uploads/lt/2BDZywIC.png"></div>
                    <div class="shop-list-item-bd">
                        <h3>
                            <span class="shop-list-item-tag">自营</span>
                            康威斯12.00R20/CPD68
                        </h3>
                        <p class="special-etc">
                            <span class="special-etc-hd">￥598</span>
                            <span class="special-etc-bd">￥898</span>
                        </p>
                    </div>
                </a>
            </div>
        </div>
    </section>
    <footer class="footer footer-fixed">
        <a href="javascript:;" class="tabBar-item"><span class="tabBar-item-text">删除订单</span></a>
        <a href="javascript:;" class="tabBar-item "><span class="tabBar-item-text">申请售后</span></a>
        <a href="javascript:;" class="tabBar-item "><span class="tabBar-item-text">再次购买</span></a>
        <!-- <a href="javascript:;" class="tabBar-item tabBar-item-active"><span class="tabBar-item-text">评价晒单</span></a> -->
    </footer>
</section>
<?php include dirname(__DIR__).'/layouts/TrotoBottomNavBar.php'; ?>
</div>
<script type="text/javascript">
    $(function view_details_click() {
        $("#show-btn").bind('click', function() {
            if ($(".show-box").is(":hidden")) {
                $(".show-box").show();
                $(this).find("#show-open").text(" 收起订单完整信息");
                $(this).find(".fa").removeClass("fa-angle-right").addClass("fa-angle-down");
            } else {
                $(".show-box").hide();
                $(this).find("#show-open").text(" 展开订单完整信息");
                $(this).find(".fa").removeClass("fa-angle-down").addClass("fa-angle-right");
            }
        });

    });
</script>