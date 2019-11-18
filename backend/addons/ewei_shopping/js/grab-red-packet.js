/*
* 抢红包功能
*/

;(function(window, document, undefined) {

	function GetMonth(opts) {
		this.ele = opts.ele;
		this.list = opts.list;
		this.btn = opts.btn;
		this.load = opts.load;
		this.speed = opts.speed;
		this.timer = '';

		this.init();
	}
	GetMonth.prototype.showMonth = function() {
		var that = this;
		$.ajax({
			type: 'post',
			url: 'getMonth.php',
			dataType: 'json',
			success: function(data) {
				if (data.status == 200) {

					that.ele.text(data.month+'元');
					that.showDataList(data.dataList);

				} else if (data.status == -200) {
					clearTimeout(that.timer);
					//location.reload();
				}
			}
		});
	};
	// 处理数据列表
	GetMonth.prototype.showDataList = function(dataList) {
		if (dataList.length != 0) {
			var strHtml = '<li>\
				<img src="'+dataList.img+'" alt="'+dataList.name+'" />\
				<div class="grab-list-info">\
					<h2>'+dataList.name+'</h2>\
					<p>'+dataList.time+'</p>\
				</div>\
				<span>'+dataList.month+'</span>\
			</li>';
			if (this.list.find('li').length == 10) {
				this.list.find('li').eq(0).remove();
			}
			this.list.appendChild(strHtml);
		}
	};
	GetMonth.prototype.grabRedBtn = function() {
		var that = this;
		this.btn.on('touchend', function(e) {
			e.preventDefault();
			that.load.show();
			$.ajax({
				type: 'post',
				url: 'grab.php',
				dataType: 'json',
				success: function(data) {
					alert(data.msg);
					// if (data.status == 200) {
					// 	alert(data.msg);
					// }
				}
			});
		});
	};
	// 倒计时请求数据	
	GetMonth.prototype.countdown = function() {
		var that = this;
		this.showMonth();
		// 循环取数据
		this.timer = setTimeout(function() {
			that.showMonth();
			setTimeout(arguments.callee, that.speed);
		}, this.speed);
	};
	GetMonth.prototype.init = function() {
		//this.countdown();
		this.grabRedBtn();
	};
	new GetMonth({
		ele: $('.show-month span'),
		list: $('.show-list ul'),
		btn: $('.grab-red-packet-btn'),
		load: $('.grab-loading'),
		speed: 500
	});

})(window, document);