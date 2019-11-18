/**
 * 
 */
var Util = {
	countDown : function(e) {
		var endtime = new Date($(e).attr("time")).getTime(); // 结束时间
		var nowtime = new Date().getTime();// 获取当日时间
		var intDiff = parseInt((endtime - nowtime) / 1000);// 倒计时总秒数量
		window.setInterval(function() {
			var day = 0, hour = 0, minute = 0, second = 0;// 时间默认值
			if (intDiff > 0) {
				day = Math.floor(intDiff / (60 * 60 * 24));
				hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
				minute = Math.floor(intDiff / 60) - (day * 24 * 60)
						- (hour * 60);
				second = Math.floor(intDiff) - (day * 24 * 60 * 60)
						- (hour * 60 * 60) - (minute * 60);
			} else {
				
			}
			if (hour <= 9) {
				hour = '0' + hour;
			}
			if (minute <= 9)
				minute = '0' + minute;
			if (second <= 9)
				second = '0' + second;
			$(e).html(
					'<i>&nbsp;' + hour + '</i>' + ':' + '<i>' + minute + '</i>'
							+ ':' + '<i>' + second + '</i>');
			intDiff--;
		}, 1000);
	},
	dialog : {
		showLoading : function(e) {
			var t = ' <div class="l-overlay"><div class="l-pop" id="l-loading"><div class="loading-spinner"></div><div class="msg">'
					+ (e ? e : "正在加载...") + "</div></div></div>";
			$(t).appendTo($("body"));
		},
		hideLoading : function() {
			$(".l-overlay").remove();
		},
		showMessage : function(e) {
			var t = '<div class="l-pop l-message" id="l-message"><div class="msg">'
					+ (e ? e : "加载异常,请稍候再试.") + "</div></div>";
			$("#l-message").remove();
			var n = $(t).appendTo($("body"));
			n.css({
				"margin-left" : n.width() / 2 * -1,
				"margin-top" : n.height() / 2 * -1 - 60
			}), setTimeout(function() {
				$("#l-message").remove();
			}, 1500);
		},
		showMsg : function(e, u) {
			var t = '<div class="l-pop l-message" id="l-message"><div class="msg">'
					+ (e ? e : "加载异常,请稍候再试.") + "</div></div>";
			$("#l-message").remove();
			var n = $(t).appendTo($("body"));
			n.css({
				"margin-left" : n.width() / 2 * -1,
				"margin-top" : n.height() / 2 * -1 - 60
			}), setTimeout(function() {
				if (u) {
					window.location.href = u;
				} else {
					location.reload();
				}
			}, 1000);
		},
		showBuyMsg : function(e) {
			var t = '<div class="loading-mask" onclick="return Util.dialog.showBuyClose()"></div><div class="popup"><div class="popup-img">成功添加到购物车！</div><div class="popup-isg"><a href="flow.php" class="popup-isg-left">去结算</a><a href="/mobile/" class="popup-isg-right" onclick="return Util.dialog.showBuyClose()">我再逛逛</a></div></div>';
			$(t).appendTo($("body"));
		},
		showCustom : function(Message, Mfooter, callback){
			var Custom = {'left':[],'right':[],'center':[]};
			var isg = '';
			var url = 'javascript:;';
			if(Message == undefined || typeof(Message) == 'object'){
				Message = '异常数据,请联系站点管理员！';
			}

			if(typeof Mfooter == 'object'){
				if(typeof Mfooter.left != 'undefined'){
					var ok = Mfooter.left['ok'] || "去结算";
					isg+='<a href="'+url+'" class="popup-isg-left">'+ ok +'</a>';
				}
				if(typeof Mfooter.right != 'undefined'){
					var not = Mfooter.right['not'] || "我再逛逛";
					isg+='<a href="'+url+'" class="popup-isg-right" onclick="return Util.dialog.showBuyClose()">'+not+'</a>';	
				}
				if(typeof Mfooter.center != 'undefined'){
					if(typeof Mfooter['center'][1] != 'undefined'){
						url = Mfooter['center'][1];
					}
					isg+='<a href="'+url+'" class="popup-isg-center" onclick="return Util.dialog.showBuyClose()">'+Mfooter['center'][0]+'</a>';	
				}
			}

			var t = '<div class="loading-mask" onclick="return Util.dialog.showBuyClose()"></div><div class="popup"><div class="popup-img">'+Message+'</div><div class="popup-isg">'+isg+'</div></div>';
			$(t).appendTo($("body"));
			callback && callback(".popup-isg-left");		// 如果有回调函数，则执行
		},
		showBuyClose : function(e) {
			$(".loading-mask").remove();
			$(".popup").remove();
		},
		showSiginMsg : function(e) {
			var t = '<div class="loading-mask" onclick="return Util.dialog.showBuyClose()"></div><div class="popup"><div class="popup-img">您已签到15天,<br />恭喜获得精美小礼品</div><div class="popup-isg"><a href="flow.php">去领取</a></div></div>';
			$(t).appendTo($("body"));
		}
	}
}
function addToCart() {
	var goods = new Object();
	var spec_arr = new Array();
	var fittings_arr = new Array();
	var number = document.getElementById('goods_number').value;

	goods.quick = 0;
	goods.spec = 0;
	goods.goods_id = goodsId;
	goods.number = number;
	goods.parent = (typeof (parentId) == "undefined") ? 0 : parseInt(parentId);
	$.post('flow.php?step=add_to_cart', 'goods=' + JSON.stringify(goods),
			function(res) {
				if (res.error > 0) {
					alert(res.message);
				}
			}, 'JSON')
}