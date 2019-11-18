! function(a) {
	var b = {},
		c = {};
	c.attachEvent = function(b, c, d) {
		return "addEventListener" in a ? b.addEventListener(c, d, !1) : void 0
	}, c.fireFakeEvent = function(a, b) {
		return document.createEvent ? a.target.dispatchEvent(c.createEvent(b)) : void 0
	}, c.createEvent = function(b) {
		if (document.createEvent) {
			var c = a.document.createEvent("HTMLEvents");
			return c.initEvent(b, !0, !0), c.eventName = b, c
		}
	}, c.getRealEvent = function(a) {
		return a.originalEvent && a.originalEvent.touches && a.originalEvent.touches.length ? a.originalEvent.touches[0] : a.touches && a.touches.length ? a.touches[0] : a
	};
	var d = [{
		test: ("propertyIsEnumerable" in a || "hasOwnProperty" in document) && (a.propertyIsEnumerable("ontouchstart") || document.hasOwnProperty("ontouchstart")),
		events: {
			start: "touchstart",
			move: "touchmove",
			end: "touchend"
		}
	}, {
		test: a.navigator.msPointerEnabled,
		events: {
			start: "MSPointerDown",
			move: "MSPointerMove",
			end: "MSPointerUp"
		}
	}, {
		test: a.navigator.pointerEnabled,
		events: {
			start: "pointerdown",
			move: "pointermove",
			end: "pointerup"
		}
	}];
	b.options = {
		eventName: "tap",
		fingerMaxOffset: 11
	};
	var e, f, g, h, i = {};
	e = function(a) {
		return c.attachEvent(document.body, h[a], g[a])
	}, g = {
		start: function(a) {
			a = c.getRealEvent(a), i.start = [a.pageX, a.pageY], i.offset = [0, 0]
		},
		move: function(a) {
			return i.start || i.move ? (a = c.getRealEvent(a), i.move = [a.pageX, a.pageY], void(i.offset = [Math.abs(i.move[0] - i.start[0]), Math.abs(i.move[1] - i.start[1])])) : !1
		},
		end: function(d) {
			if (d = c.getRealEvent(d), i.offset[0] < b.options.fingerMaxOffset && i.offset[1] < b.options.fingerMaxOffset && !c.fireFakeEvent(d, b.options.eventName)) {
				if (a.navigator.msPointerEnabled || a.navigator.pointerEnabled) {
					var e = function(a) {
						a.preventDefault(), d.target.removeEventListener("click", e)
					};
					d.target.addEventListener("click", e, !1)
				}
				d.preventDefault()
			}
			i = {}
		},
		click: function(a) {
			return c.fireFakeEvent(a, b.options.eventName) ? void 0 : a.preventDefault()
		}
	}, f = function() {
		for (var a = 0; a < d.length; a++)
			if (d[a].test) return h = d[a].events, e("start"), e("move"), e("end"), !1;
		return c.attachEvent(document.body, "click", g.click)
	}, c.attachEvent(a, "load", f), a.Tap = b
}(window);


(function($){
	$.fn.mdater = function(config){
		var defaults = {
			maxDate : null,
			minDate : new Date(1970, 0, 1),
			util: null
		};
		var option = $.extend(defaults, config);
		var input = this;

		//通用函数
		var F = {
			//计算某年某月有多少天
			getDaysInMonth : function(year, month){
			    return new Date(year, month+1, 0).getDate();
			},
			//计算某月1号是星期几
			getWeekInMonth : function(year, month, day){
				var day = day || 1;
				return new Date(year, month, day).getDay();
			},
			getMonth : function(m){
				return ['一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二'][m];
			},
			//计算年某月的最后一天日期
			getLastDayInMonth : function(year, month){
				return new Date(year, month, this.getDaysInMonth(year, month));
			}
		}

		//为$扩展一个方法，以配置的方式代理事件
		$.fn.delegates = function(configs) {
		    el = $(this[0]);
		    for (var name in configs) {
		        var value = configs[name];
		        if (typeof value == 'function') {
		            var obj = {};
		            obj.tap = value;
		            value = obj;
		        };
		        for (var type in value) {
		            el.delegate(name, type, value[type]);
		        }
		    }
		    return this;
		}

		var mdater = {
			value : {
				year : '',
				month : '',
				date : '',
				dateArr: [],
			},
			lastCheckedDate : '',
			flag: false,
			init : function(){
				this.renderHTML();
				this.initListeners();
			},
			renderHTML : function(){
				// var $html = $('<div class="md_panel"><div class="md_head"><div class="md_selectarea"><a class="md_prev change_year" href="javascript:void(0);">&lt;</a> <a class="md_headtext yeartag" href="javascript:void(0);"></a> <a class="md_next change_year" href="javascript:void(0);">&gt;</a></div><div class="md_selectarea"><a class="md_prev change_month" href="javascript:void(0);">&lt;</a> <a class="md_headtext monthtag" href="javascript:void(0);">月</a> <a class="md_next change_month" href="javascript:void(0);">&gt;</a></div></div><div class="md_body"><ul class="md_weekarea"><li>日</li><li>一</li><li>二</li><li>三</li><li>四</li><li>五</li><li>六</li></ul><ul class="md_datearea in"></ul></div><div class="md_foot"><a href="javascript:void(0);" class="md_ok">客房预订</a><p>温馨提示：亲，若微信预订系统开小差，可尝试退出微信，重新登陆！</p></div>');
				var $html = $('<div class="md_panel"><div class="md_head"><div class="md_selectarea"><a class="md_headtext yeartag" href="javascript:void(0);"></a></div><div class="md_selectarea"><a class="md_prev change_month" href="javascript:void(0);">&lt;</a><a class="md_headtext monthtag" href="javascript:void(0);">月</a> <a class="md_next change_month" href="javascript:void(0);">&gt;</a></div></div><div class="md_body"><ul class="md_weekarea"><li>日</li><li>一</li><li>二</li><li>三</li><li>四</li><li>五</li><li>六</li></ul><ul class="md_datearea in"></ul></div><div class="md_foot"><a href="javascript:void(0);" class="md_ok">温泉抢订</a><p>温馨提示：亲，若微信预订系统开小差，可尝试退出微信，重新登陆！</p></div>');
				$('.md_mask').html($html);
			},
			_showPanel : function(container){
				this.refreshView();
				// $('.md_panel, .md_mask').addClass('show');
				$('.md_panel').addClass('show');
			},
			_hidePanel : function(){
				$('.md_panel, .md_mask').removeClass('show');
			},
			_changeMonth : function(add, checkDate){

				//先把已选择的日期保存下来
				this.saveCheckedDate();

				var monthTag = $('.md_selectarea').find('.monthtag'),
					num = ~~monthTag.data('month')+add;
				//月份变动发生了跨年
				if(num>11){
					num = 0;
					this.value.year++;
					$('.yeartag').text(this.value.year).data('year', this.value.year);
				}
				else if(num<0){
					num = 11;
					this.value.year--;
					$('.yeartag').text(this.value.year).data('year', this.value.year);
				}

				var nextMonth = F.getMonth(num)+'月';
				monthTag.text(nextMonth).data('month', num);
				this.value.month = num;
				if(checkDate){
					this.value.date = checkDate;
				}
				else{
					//如果有上次选择的数据，则进行赋值
					this.setCheckedDate();
				}
				this.updateDate(add);
			},
			_changeYear : function(add){
				//先把已选择的日期保存下来
				this.saveCheckedDate();

				var yearTag = $('.md_selectarea').find('.yeartag'),
					num = ~~yearTag.data('year')+add;
				yearTag.text(num+'年').data('year', num);
				this.value.year = num;
				
				this.setCheckedDate();

				this.updateDate(add);
			},
			//保存上一次选择的数据
			saveCheckedDate : function(){
				if(this.value.date){
					this.lastCheckedDate = {
						year : this.value.year,
						month : this.value.month,
						date : this.value.date
					}
				}
			},
			//将上一次保存的数据恢复到界面
			setCheckedDate : function(){
				if(this.lastCheckedDate && this.lastCheckedDate.year==this.value.year && this.lastCheckedDate.month==this.value.month){
					this.value.date = this.lastCheckedDate.date;
				}
				else{
					this.value.date = '';
				}
			},
			//根据日期得到渲染天数的显示的HTML字符串
			getDateStr : function(y, m, d, date){
				var dayStr = '';
				//计算1号是星期几，并补上上个月的末尾几天
				var week = F.getWeekInMonth(y, m);
				var lastMonthDays = F.getDaysInMonth(y, m-1);
				for(var j=week-1; j>=0; j--){
					var no = '';
					no = noClick(y,m-1,lastMonthDays-j);
					dayStr += '<li class="'+no+' prevdate" data-day="'+(lastMonthDays-j)+'"><i>'+(lastMonthDays-j)+'</i></li>';
				}
				//再补上本月的所有天;
				var currentMonthDays = F.getDaysInMonth(y, m);
				//判断是否超出允许的日期范围
				var startDay = 1, 
					endDay = currentMonthDays, 
					thisDate = new Date(y, m, d),
					firstDate = new Date(y, m, 1);
					lastDate =  new Date(y, m, currentMonthDays),
					minDateDay = option.minDate.getDate();
					

				if(option.minDate>lastDate){
					startDay = currentMonthDays+1;
				}
				else if(option.minDate>=firstDate && option.minDate<=lastDate){
					startDay = minDateDay;
				}

				if(option.maxDate){
					var maxDateDay = option.maxDate.getDate();
					if(option.maxDate<firstDate){
						endDay = startDay-1;
					}
					else if(option.maxDate>=firstDate && option.maxDate<=lastDate){
						endDay = maxDateDay;
					}
				}
				

				// 获取房间剩余数量
				var dateArr = [];
				dateArr = this.getRoomAjax(y, m+1);
				// console.log(dateArr);

				var year = date.getFullYear(),
					month = date.getMonth(),
					day = date.getDate();
				console.log(day);

				//将日期按允许的范围分三段拼接
				for(var i=1; i<startDay; i++){
					dayStr += '<li class="disabled" data-day="'+i+'"><i>'+i+'</i></li>';
				}
				for(var j=startDay, k=startDay; j<=endDay; j++){
					var current = '', no = '';
					if(year==this.value.year && month==this.value.month && day==j){
						current = 'active';
						console.log(day, j);
					}
					no = noClick(y,m,j);
					try {
						var sm = (m+1 < 10) ? '0'+(m+1) : (m+1);
							sj = (j < 10) ? '0'+j : j;
						if (dateArr[k-1].date == y+'-'+sm+'-'+sj) {
							// console.log(dateArr[k-1].num);
							var num = '';
							if (typeof dateArr[k-1].num == 'number') {
								num = '剩'+dateArr[k-1].num;
							} else {
								if (dateArr[k-1].num == '') {
									no = 'no';
								}
								num = dateArr[k-1].num;
							}
							dayStr += '<li class="'+no+' '+current+'" data-day="'+j+'"><i>'+j+'</i><span>'+num+'</span></li>';
						} else {
							dayStr += '<li class="'+no+' '+current+'" data-day="'+j+'"><i>'+j+'</i><span>待抢</span></li>';
						}
					} catch(e) {
						// 捕获错误，跳过
						dayStr += '<li class="'+no+' '+current+'" data-day="'+j+'"><i>'+j+'</i><span>剩0</span></li>';
					}
					if (k<endDay) k++;
				}
				for(var k=endDay+1; k<=currentMonthDays; k++){
					dayStr += '<li class="disabled" data-day="'+k+'">'+k+'</li>';
				}

				//再补上下个月的开始几天
				var nextMonthStartWeek = (currentMonthDays + week) % 7;
				if(nextMonthStartWeek!==0){
					for(var i=1; i<=7-nextMonthStartWeek; i++){
						var no = '';
						no = noClick(y,m+1,i);
						// console.log(F.getWeekInMonth(y,m+1,i));
						dayStr += '<li class="'+no+' nextdate" data-day="'+i+'"><i>'+i+'</i></li>';
					}
				}

				function noClick(y,m,j) {
					if (F.getWeekInMonth(y,m,j) == 6) {
						return 'no';
					}
					return '';
				}
				return dayStr;
			},
			updateDate : function(add){

				if (this.flag) return false;
				this.flag = true;
				var that = this;
				var dateArea = $('.md_datearea.in');
				if(add == 1){
					var c1 = 'out_left';
					var c2 = 'out_right';
				}
				else{
					var c1 = 'out_right';
					var c2 = 'out_left';	
				}
				var newDateArea = $('<ul class="md_datearea '+c2+'"></ul>'),
					y = this.value.year,
					m = this.value.month,
					d = this.value.dateArr;
				var dd = new Date();
				// newDateArea.html(this.getDateStr(dd.getFullYear(), dd.getMonth(), dd.getDate()));
				newDateArea.html(this.getDateStr(y, m, d, dd));
				$('.md_body').append(newDateArea);
				setTimeout(function(){
					that.flag = false;
					newDateArea.removeClass(c2).addClass('in');
					dateArea.removeClass('in').addClass(c1);
				}, 0);
				
				setTimeout(function() {
					$('.md_datearea').find('li').each(function() {
						var $this = $(this),
							day = $this.attr('data-day');
						for (var i=0,len=d.length; i<len; i++) {
							if ((y+'-'+(m+1)+'-'+day) == d[i]) {
								if (!$this.hasClass('prevdate') && !$this.hasClass('nextdate') && !$this.hasClass('active') && !$this.hasClass('no')) {
									$this.addClass('current');
								}
							}
						}
					});
				}, 200);
				
			},
			//每次调出panel前，对界面进行重置
			refreshView : function(){
				/*var initVal = input.val(),
					date = null;
				if(initVal){
					var arr = initVal.split('-');
					date = new Date(arr[0], arr[1]-1 , arr[2]);
				}
				else{
					date = new Date();
				}*/
				var date = new Date(),
					y = this.value.year = date.getFullYear(),
					m = this.value.month = date.getMonth(),
					d = this.value.date = date.getDate();
				$('.yeartag').text(y).data('year', y);
				$('.monthtag').text(F.getMonth(m)+'月').data('month', m);
				var dayStr = this.getDateStr(y, m, d, date);
				$('.md_datearea').html(dayStr);
			},
			// 获取房间剩余数量
			getRoomAjax: function(year, month) {
				var dataArr = [];
				$.ajax({
					type: 'post',
					url: '/home/Meilinhu/AjHotSpring',
					data: {year: year, month: month},
					async: false,
					dataType: 'json',
					success: function(data) {
						if (data) {
							dataArr = data;
						}
					}
				});
				return dataArr;
			},
			// 发送获取Ajax
			sendAjax: function(dateStr) {
				$.ajax({
					type: 'post',
					url: '/home/Meilinhu/AjDateBook',
					data: {date: dateStr},
					dataType: 'json',
					success: function(data) {
						if (data) {
							// localStorage.setItem('date', data.date);
							location.href = data.link;
						} else {
							location.href = '/reservation/reservation.html';
							option.util.promptBox('预订失败，请重新预订！', '重新预订');
						}
					}
				});
			},
			initListeners : function(){
				var panel = $('.md_panel'),
					mask = $('.md_mask'),
					_this = this;

				_this._showPanel();	

				mask.on('tap', function(){
					_this._hidePanel();
				});

				panel.delegates({
					'.change_month' : function(){
						var add = $(this).hasClass('md_next') ? 1 : -1;
						_this._changeMonth(add);
					},
					'.change_year' : function(){
						return false;
						var add = $(this).hasClass('md_next') ? 1 : -1;
						_this._changeYear(add);	
					},
					'.out_left, .out_right' : {
						'webkitTransitionEnd' : function(){
							$(this).remove();
						}
					},
					'.md_datearea li' : function(){
						var $this = $(this);
						if($this.hasClass('disabled')){
							return;
						}

						//判断是否点击的是前一月或后一月的日期
						var add = 0;
						if($this.hasClass('nextdate')){
							add = 1;
						}
						else if($this.hasClass('prevdate')){
							add = -1;
						}

						if(add !== 0){
							_this._changeMonth(add, _this.value.date);
						}
						else{
							// 如果是当前日或周六，则不允许点击
							if ($this.hasClass('no') || $this.hasClass('active')) return false;
							var txt = $this.find('span').text(),
								num = parseInt(txt.substring(txt.indexOf('剩')+1));
							if (typeof num == 'number') {
								if (num == 0) {
									option.util.promptBox('糟糕！来晚了，没有房间可以预订了！', '明天再来');
									return false;
								}
							}
							if (txt == '抢完') {
								option.util.promptBox('糟糕！来晚了，没有房间可以预订了！', '明天再来');		
								return false;
							} else if (txt == '待抢') {
								option.util.promptBox('亲爱的，抢订还没开始呢！请耐心等待！', '好，明白了');		
								return false;
							}

							if (!$this.hasClass('current') && !check) {
								$this.addClass('current');
							} else {
								$this.removeClass('current');
							}

							_this.value.date = $this.data('day');
							var dateArr = _this.value.dateArr,
								year = (_this.value.year),
								month = (_this.value.month+1),
								flag = false,
								check = false;
							if ($this.hasClass('nextdate')) {
								(month == 12) ? (month = 1, year++) : month++;
							} else if ($this.hasClass('prevdate')) {
								(month == 0)? (month = 12, year--) : month--;
							}
							!function checkDateArr() {
								for (var i=0,len=dateArr.length; i<len; i++) {
									if (dateArr[i] == year+'-'+month+'-'+$this.data('day')) {
										dateArr.splice(i, 1);
										flag = true;
										check = true;
									}
								}
							}();
							if (!flag) {
								_this.value.dateArr.push(year+'-'+month+'-'+$this.data('day'));
							}
							console.log(dateArr);

						}
						
					},
					'.md_ok' : function(){

						var date, hours;
						// 每天10点后开放预订
						$.ajax({
							type: 'get',
							url: '/home/api/getTime',
							async: false,
							dataType: 'json',
							success: function(data) {
								hours = data;
							}
						});
						if (hours < 10) {
							option.util.promptBox('未到预订时间，请在10点后预订！', '好，明白了');		
							return false;
						}

						var monthValue = ~~_this.value.month + 1;
						if(monthValue < 10){
							monthValue = '0' + monthValue;
						}
						var dateValue = _this.value.date;
						var dateArr = _this.value.dateArr;
						if(dateValue === ''){
							dateValue = _this.value.date = 1;
						}
						if(dateValue < 10){
							dateValue = '0' + dateValue;
						}
						input.val(dateArr);

						if (dateArr.length != 0) {
							// _this.sendAjax(dateArr.join(','));
							$('#date').val(dateArr.join(','));
							$('.reservation-form').submit();
						} else {
							option.util.promptBox('亲爱的，你还没选择日期呢！', '去选日期');
						}

					},
					'.md_cancel' : function(){
						_this._hidePanel();
					}
				});

			}
		}
		mdater.init();
	}
});