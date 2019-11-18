/*
* 公用组件
* author: MoRong
* version: v1.0
*/

;(function(window, document, undefined) {
	
	var Util = function() {

		var hasTouch = 'ontouchstart' in window ? true : false,
			touchStart = hasTouch ? 'touchstart' : 'mousedown',
			touchMove = hasTouch ? 'touchmove' : 'mousemove',
			touchEnd = hasTouch ? 'touchend' : 'mouseup';

		return {
			/*
			* 原生获取id
			*/
			$$: function(id) {
				return (!id) ? null : document.getElementById(id);
			},
			/*
			* 原生移除前后空格
			*/
			trimStr: function(str) {
				return str.replace(/(^\s*)|(\s*$)/g, "");
			},
			/*
			* 原生检测类
			*/
			hasClass: function(ele, cls) {
				return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
			},
			/*
			* 原生添加类
			*/
			addClass: function(ele, cls) {
				if (!this.hasClass(ele, cls)) ele.className += ' '+cls;
			},
			/*
			* 原生移除类
			*/
			removeClass: function(ele, cls) {
				if (this.hasClass(ele, cls)) {
					var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
					ele.className = ele.className.replace(reg, '');
				}
			},
			/*
			* 原生移除全部类
			*/
			removeAllClass: function(eleArr, cls) {
				for (var i = 0, len = eleArr.length; i < len; i++) {
					this.removeClass(eleArr[i], cls);
				}
			},
			/*
			* 原生获取类名
			*/
			getClass: function(node, cls) {
				if (typeof document.getElementsByClassName != 'function') {
					var a = [];
				    var re = new RegExp('(^| )'+cls+'( |$)');
				    var els = node.getElementsByTagName("div");
				    for(var i=0,j=els.length; i<j; i++) {
				    	if (els[i].className) {
					        if (re.test(els[i].className)) a.push(els[i]);
				    	} else if (els[i].getAttribute('class')) {
				    		if (re.test(els[i].getAttribute("class"))) a.push(ele[i]);
				    	}
				    }
				    return a;
				} else {
					return document.getElementsByClassName(cls);
				}
			},
			/*
			* 原生添加事件
			*/
			addHandler: function(element, type, handler) {
				if (element.addEventListener) {
					element.addEventListener(type, handler, false);
				} else if (element.attachEvent) {
					element.attachEvent('on'+type, handler);
				} else {
					element['on'+type] = handler;
				}
			},
			/*
			* 原生移除事件
			*/
			removeHandler: function(element, type, handler) {
				if (element.removeEventListener) {
					element.removeEventListener(type, handler, false);
				} else if (element.detachEvent) {
					element.detachEvent('on'+type, handler);
				} else {
					element['on'+type] = null;
				}
			},
			/*
			* 原生获取事件
			*/
			getEvent: function(event) {
				return event ? event : window.event;
			},
			/*
			* 原生获取目标元素
			*/
			getTarget: function(event) {
				return event.target || event.srcElement;
			},
			/*
			* 原生阻止默认事件
			*/
			preventDefault: function(event) {
				if (event.preventDefault) {
					event.preventDefault();
				} else {
					event.returnValue = false;
				}
			},
			/*
			* 原生创建XMLHttpRequest对象
			*/
			createXHR: function() {
				var xhr;
				if (window.XMLHttpRequest) {
					xhr = new XMLHttpRequest();
				} else {
					xhr = new ActiveXObject("Microsoft.XMLHTTP");
				}
				return xhr;
			},
			/*
			* jsonp跨域请求数据方法
			*/
			jsonp: function(obj, callback) {
				var script = document.createElement('script');
				script.type = 'text/javascript';
				script.src = obj.url+callback;

				script.onerror = function(err) {
					console.log(err + ', 数据请求失败！');
				}

				document.getElementsByTagName('head')[0].appendChild(script);
				if (typeof document.attachEvent == 'undefined') {
					document.getElementsByTagName('head')[0].removeChild(script);
				}
			}, 
			//  检测是否有关注公众号
		    sendAjax: function(obj, callback) {
		      	var url = obj.url,
		      		data = obj.data || '',
		      		type = obj.type || 'post',
		      		dataType = obj.dataType || 'json';

				$.ajax({
					type: type,
					url: url,
					data: data,
					dataType: dataType,
					success: function(data) {
						callback && callback(data);
					}
				});
		 	},
			/*
			* 弹出框
			*/
			promptBox: function(opts) {
				var title = opts.title || '',
					titleInfo = opts.titleInfo || '',	// 标题信息
					info = opts.info || '',				// 信息
					leftBtn = opts.leftBtn || '',		// 左按钮
					rightBtn = opts.rightBtn || '',		// 右按钮
					leftEve = opts.leftEve || '',		// 左按钮事件
					rightEve = opts.rightEve || '',		// 右按钮事件
					timer = opts.timer || 0,			// 时间控制隐藏
					option = opts.option || ['event','close'],	// 默认选项，左按钮事件，右按钮取消
					callback = opts.callback || '',		// 回调函数
					typeHTML = '', strHTML = '';		// 字符串

				if (title == 'ok') {
					var titleok = (titleInfo != '') ? titleInfo : '提交成功'; 
					title = '<h3 class="ok">'+titleok+'</h3>';
				} else if (title == 'note') {
					var titlenote = (titleInfo != '') ? titleInfo : '温馨提示'; 
					title = '<h3 class="note">'+titlenote+'</h3>';
				}

				// 如果左右按钮存在，则使用双按钮类
				if (leftBtn !== '' && rightBtn !== '') {
					typeHTML = '<div class="prompt-double">';
				} else {
					typeHTML = '<div class="prompt-single">';
				}
				// 如果有按钮
				(leftBtn !== '') && (strHTML += '<a class="prompt-'+option[0]+'" href="'+(leftEve  || 'javascript:;')+'">'+leftBtn+'</a>');
				(rightBtn !== '') && (strHTML += '<a class="prompt-'+option[1]+'" href="'+(rightEve  || 'javascript:;')+'">'+rightBtn+'</a>');

				// 插入内容
				var divBox = '<div class="prompt-box"></div>\
					<div class="prompt-main">\
						'+typeHTML+'\
							'+title+'\
							<span>'+info+'</span>\
							<div class="prompt-btn">'+strHTML+'</div>\
						</div>\
					</div>';
				// 如果页面没有该提示框
				if ($('.prompt-box').length < 1) {
					$('body').append(divBox);
				}

				var $box = $('.prompt-box'),
					$main = $('.prompt-main');

				// 如果有内容，则输出
				if (strHTML != '') {
					var $eve = $('.prompt-event'),
						$close = $('.prompt-close');
					// 点击时间按钮
					$eve.on(touchEnd, function(e) {
						e.stopPropagation();
						// 如果设置回调函数，则执行
						callback ? (eleHide(), callback()) : eleHide();
					});
					// 点击关闭按钮
					$close.on(touchEnd, function(e) {
						e.stopPropagation();
						eleHide();
					});
				}
				// 关闭事件
				$box.on(touchEnd, function() {
					eleHide();
				});
				// 如果设置了时间，则自动隐藏
				if (timer !== 0) {
					setTimeout(function() {
						eleHide();
					}, timer);
				}
				// 隐藏元素
				function eleHide() {
					$box.fadeOut(50);
					$main.fadeOut(50);
					setTimeout(function() {
						$box.remove();
						$main.remove();
					}, 50);
				}
			},
			/*
			* 获取url参数
			* 格式 {name:morong,age:21}
			*/
			getLocationURL: function() {
				if (location.search.length > 0) {
					var url = location.search,
						paraString = url.substring(url.indexOf("?")+1, url.length).split("&"),
						urlArr = {};
					// 截取对应字符
					for (var i = 0; i < paraString.length; i++) {
						var j = paraString[i];
						// 设置对应参数
						urlArr[j.substring(0,j.indexOf("="))] = j.substring(j.indexOf("=")+1, j.length);
					}
					return urlArr;	// 返回url参数
				}
			},
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
					setTimeout(function() {
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
		};
	}();
	
	window.Util = Util;

})(window, document);