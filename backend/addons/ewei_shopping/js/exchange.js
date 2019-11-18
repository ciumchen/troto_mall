;(function(window, document, undefined) {

	// 显示规则及入口导航
	function exchangeRule() {
		var $rule = $('.exchange-rule'),
			$ruleBtn = $('.rule-ok-btn'),
			$link = $('.exchange-link');
		$ruleBtn.on('touchend', function() {
			$(this).hide();
			$rule.slideUp(150);
			setTimeout(function() {
				$link.show();
			}, 80);
		});
	}
	window.exchangeRule = exchangeRule;

	// 分成转换余额
	function exBalance(obj) {
		var $input = $('.balance-input'),
			$money = $('.balance-money'),
			$balanceBtn = $('.balance-btn'),
			max = $money.attr('data-max');
		// 转换为数字类型
		(max == '') ? max = parseFloat(0).toFixed(2) : max = parseFloat(max).toFixed(2);

		$input.on('keyup', function(e) {
			var $this = $(this);
			if (e.keyCode != 8) {
				$this.val($this.val().replace(/[^0-9.]/g,''));
				var val = $this.val();
				(val == '') ? val = parseFloat(0).toFixed(2) : val = parseFloat(val).toFixed(2);
				if (max - val > 0) {
					$money.text('￥'+val);
				} else {
					$money.text('￥'+max);
					$this.val(max);
				}
			}
		});
		$balanceBtn.on('touchend', function() {
			var money = $input.val();
			if (money == '') {
				Util.promptBox({
					title: 'note',
					info: '转换金额不能为空！',
					leftBtn: '我知道了'
				});
				return false;
			} else if (max - money < 0) {
				money = max;
				$input.val(max);
			}
			if (money == '0.00') {
				Util.promptBox({
					title: 'note',
					info: '可提现分成不足！',
					leftBtn: '我知道了'
				});
				return false;
			}
			money = parseFloat(money).toFixed(2);
			$('.loading').show();
			$.ajax({
				type: 'post',
				url: obj.url,
				data: {money: money},
				dataType: 'json',
				success: function(data) {
					$('.loading').hide();
					if (data.status == 200) {
						Util.promptBox({
							title: 'ok',
							titleInfo: obj.info,
							leftBtn: '确定',
							callback: function()　{
								location.href = data.link;
							}
						});
					}
				}
			});
		});
	}
	window.exBalance = exBalance;

	// 添加银行卡
	function AddBankCardInfo(opts) {
		this.btn = opts.btn;
		this.bankName = opts.bankName;
		this.subName = opts.subName;
		this.bankAc = opts.bankAc;
		this.acName = opts.acName;
		this.mobile = opts.mobile;
		this.bankType = opts.bankType;
		this.url = opts.url;

		this.init();
	}
	AddBankCardInfo.prototype = {
		constructor: AddBankCardInfo,
		// 设置只允许输入数字
		checkNumber: function() {
			this.bankAc.on('keyup', function(event) {
				var $this = $(this), 
					val, valArr = [], account = '';
				if (event.keyCode != 8) {
					$this.val($this.val().replace(/[^0-9\s]/g, '').replace(/(\d{4})(?=[^\s])/,'$1 '));
				}
				
			});
			this.mobile.on('keyup', function() {
				var $this = $(this);
				$this.val($this.val().replace(/[^0-9]/g, ''));
			});
		},
		// 验证银行卡信息
		checkBankCard: function() {
			var that = this,
				bankName = $.trim(this.bankName.val()),
				subName = $.trim(this.subName.val()),
				bankAc = $.trim(this.bankAc.val()),
				acName = $.trim(this.acName.val()),
				mobile = $.trim(this.mobile.val()),
				type = this.bankType.val(),
				promptInfo = '';

			if (bankName == '') {
				promptInfo = '开户银行不能为空！';
			} else if (subName == '') {
				promptInfo = '支行名称不能为空！';
			} else if (bankAc == '') {
				promptInfo = '银行账户不能为空！';
			} else if (acName == '') {
				promptInfo = '开户姓名不能为空！';
			} else if (mobile == '') {
				promptInfo = '联系电话不能为空！';
			} 
			// 银行卡不为空是判断
			if (bankAc != '' && promptInfo == '') {
				// 判断银行账户是否为数字
				var bankReg = /^\d{16,19}$/;
				bankAc = Number(bankAc.replace(/[^0-9]/g, ''));
				if (!bankReg.test(bankAc)) {
					promptInfo = '请输入正确的银行账户号码！';
					flag = false;
				}
			} 
			if (mobile != '' && promptInfo == '') {			// 不为空是判断
				// 判断手机号码是否为数字
				var mobileReg = /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;
				if (!mobileReg.test(mobile)) {
					promptInfo = '请输入正确的手机号码！';
				}
			}

			if (promptInfo != '') {
				Util.promptBox({
					title: 'note',
					info: promptInfo,
					leftBtn: '我知道了'
				});
				return false;
			}
			return {
				bankName: bankName,
				subName: subName,
				bankAc: bankAc,
				acName: acName,
				mobile: mobile,
				type: type
			};
		},
		// 保存银行卡信息
		saveBankCardInfo: function() {
			var that = this, data;
			this.btn.on('touchend', function() {
				data = that.checkBankCard();
				if (data != false) {
					$.ajax({
						type: 'post',
						url: that.url,
						data: data,
						dataType: 'json',
						success: function(data) {
							if (data.status == 200) {
								Util.promptBox({
									title: 'ok',
									info: '银行卡添加成功！',
									leftBtn: '我知道了',
									callback: function() {
										location.href = data.link;
									}
								});
							}
						}
					});
				} 
			});
		},
		init: function() {
			this.checkNumber();
			this.saveBankCardInfo();
		}
	};
	window.AddBankCardInfo = AddBankCardInfo;

	// 删除银行卡列表
	function bancardListDel(obj) {
		var $list = $('.bankcard-list > div'),
			$del = $('.bankcard-del');
		$del.on('touchend', function() {
			var $this = $(this);
			Util.promptBox({
				title: '',
				info: '删除此银行卡信息',
				leftBtn: '删除',
				rightBtn: '取消',
				callback: function() {
					var $div = $this.parent('div'),
						id = $div.attr('data-id');
					$.ajax({
						type: 'post',
						url: obj.url,
						data: {id: id, type: 'del'},
						dataType: 'json',
						success: function(data) {
							if (data.status == 200) {
								$div.remove();
							}
						}
					});
				}
			});
		});
	}
	window.bancardListDel = bancardListDel;

	// 切换进度
	function tabInquire(obj) {
		var $nav = $('.inquire-nav li'),
			$con = $('.inquire-con li');
		$nav.on('touchend', function() {
			var index = $(this).index();
			$(this).addClass(obj.on).siblings().removeClass(obj.on);
			$con.eq(index).addClass(obj.on).siblings().removeClass(obj.on);
		});
	}
	window.tabInquire = tabInquire;

	return {
		rule: exchangeRule,
		balance: exBalance,
		add: AddBankCardInfo,
		list: bancardListDel
	};

})(window, document);