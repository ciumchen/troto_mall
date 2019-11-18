/**
* 获取红包金额
*/


;(function(window, document, undefined) {

	/*************************************
    * 获取领取优惠红包
    * @paran box 遮罩层
    * @paran get 弹出内容
    * @paran money 获取金钱额度
    * @paran total 金钱总额
    * @paran btn 关闭按钮
    * @paran list 数据列表
    * @paran speed 金钱动画速度
    *************************************/
    function GetMoney(opts) {
        this.box = opts.box;
        this.get = opts.get;
        this.money = this.get.find('h2');
        this.total = opts.total;
        this.btn = opts.btn;
        this.speed = opts.speed;
        this.att = opts.att;
        this.close = opts.close;
        this.url = opts.url;
        this.red = opts.red;

        this.init();
    }
    GetMoney.prototype = {
      constructor: GetMoney,
     	isSnid: function() {
			var money = parseFloat(localStorage.getItem('money')).toFixed(2);
			money = (!isNaN(money)) ? money : 0;
			this.showMoney(this.total, money);
      	},
      	showMoney: function(ele, money) {
			var _self = this;
		  	if (money) {
		  		ele.text('0.00元');
				setTimeout(function() {
				    var strMoney = money+'',
				    	i = 0, j = 0,
				    	strInt, strFloat, numInt, numFloat;	// 临时数字
				    // 获取整数部分
				    strInt = strMoney.substring(0, strMoney.indexOf('.'));
				    // 获取小数部分
				    strFloat = strMoney.substring(strMoney.indexOf('.') + 1);

				    // 转为数字
				    numInt = parseInt(strInt);
				    numFloat = parseInt(strFloat) / 100;
				    // console.log(numFloat);

				    // 动画显示金钱增加
				    setTimeout(function() {
				        if (i<=numInt) {
				          if (j < numFloat) j += 0.01;
				          ele.text(parseFloat(i+j).toFixed(2)+'元');
				          i += numInt / 100;
				          // console.log(j);
				          setTimeout(arguments.callee, _self.speed);
				          return;
				        }
				        ele.text(parseFloat(money).toFixed(2)+'元');
				    }, _self.speed);
				}, 180);
		  	}
      	},
      	showAttention: function(flag) {
          var _self = this;
    	  $.ajax({
    	  		type: "post",
    	  		url: _self.url,
    	  		data: '',
    	  		dataType: 'json',
    	  		success: function(data) {
    	  			if (data.status == -100) {
	  					_self.att.show();
	  					_self.box.show();
    	  				if (flag == 1) {
    	  					_self.att.find('h2').show();
    	  					_self.att.find('p').html('长按二维码，关注公众号，激活余额！').css('margin-top','0');
    	  				} else {
    	  					_self.att.find('h2').hide();
    	  					_self.att.find('p').html('抱歉，您还不能使用该功能！<br />请关注云吉良品公众号，激活余额！').css('margin-top','12px');
    	  				}
    	  			} else {
    	  				if (flag == 0) {
	    	  				window.location = $('#setBonusCheck').attr('href');
    	  				}
    	  				_self.att.hide();
    	  				_self.box.hide();
    	  			}

    	  		}
    	  });
    	 	_self.close.on('touchend', function(e) {
	    	  	e.preventDefault();
	    	  	_self.box.hide();
	    	  	_self.att.hide();
    	  	});
      	},
      	init: function() {
			var _self = this,
			    snid = localStorage.getItem('snid'),
			    money = localStorage.getItem('money');
			if (money) {
			    _self.box.show();
			    _self.get.show();
			    this.isSnid();
			    this.btn.on('touchend', function(e) {
			        e.preventDefault();
			        closeEvent();
			 		location.href = $(this).attr('href');
			    });
			    this.box.on('touchend', function(e) {
			    	e.preventDefault();
			        closeEvent();
			        _self.box.fadeOut(200);
			    	_self.get.fadeOut(200);
			    });
			}
			this.red.on('click', function(e) {
				e.preventDefault();
			    _self.showAttention(0);
				return false;
			});
			function closeEvent() {
				if (snid) localStorage.removeItem('snid');
				localStorage.removeItem('money');
			}
      	}
    };

    window.GetMoney = GetMoney;

})(window, document);