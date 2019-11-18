;(function(window, undefined) {

	function UploadImg(opts) {
		this._form = opts._form;
		this.upfile = opts.upfile;
		this.desc = opts.desc;
		this.pro = opts.pro;
		this._type = opts._type;
		this.btn = opts.btn;
		this.load = opts.load;
		this.fileImg = opts.fileImg;
		this._type = opts._type;
		this.typeHide = opts.typeHide;
		this.imgUpload = opts.imgUpload;
		this.url = opts.url;
		this.oInfo = opts.oInfo;
		this.add = opts.add;
		this.num = opts.num;
		this.reduce = opts.reduce;
		this.fileFilter = [];
		this.imgUrl = [];

		this.init();
	}
	UploadImg.prototype = {
		constructor: UploadImg,
		tabType: function() {
			var that = this;
			that._type.find('li').on('touchend', function() {
				$(this).addClass('on').siblings().removeClass('on');
				that.typeHide.val($(this).attr('data-type'));
			});
		},
		getIndex: function(elem,arr){
			arr = arr || [];
			for(var i=0,len=arr.length; i < len; i++){		
				if(arr[i]==elem){
					return i;
				}
			}
		},
		//上传获取，添加节点
		uploadfile: function(e) {
			var that = this;
			//	var filelist=e.dataTransfer.files;
			//e.target.files 要么是点击上传获取信息，要么是拖拽上传获取信息
			var filelist = e.target.files;
			if(filelist[0].type.indexOf('image') == -1){
				Util.promptBox({
					title: 'note',
					info: '请选择图片上传！',
					leftBtn: '我知道了'
				});
				that.load.hide();
				return false;
			}
			if (this.fileFilter.length >= 3) {
				Util.promptBox({
					title: 'note',
					info: '最多只能三张图片！',
					leftBtn: '我知道了'
				});
				that.load.hide();
				return false;
			}
			this.fileFilter.push(filelist);
			
			//获取图片的名称
			var filename = filelist[0].name,
				filesize = Math.floor((filelist[0].size/1024));
			if(filesize > 1024*2){
				Util.promptBox({
					title: 'note',
					info: '上传大小不能超过2M',
					leftBtn: '我知道了'
				});
				that.load.hide();
				return false;
			}
			uploadImg('add');
			function uploadImg(flag, index) {
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange=function(){
					if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
						//alert(xmlhttp.responseText);
						//上传完成清空预览容器
						//that.fileImg.innerHTML='';
						//that.fileFilter=[]; //上传完之后清空数组
						//console.log(that.fileFilter.length);
						//console.log(that.fileFilter);
						var data = JSON.parse(xmlhttp.responseText);
						if (data.status == 200) {
							if (flag == 'add') {
								that.imgUrl.push(data.path);
								console.log(that.imgUrl);
								var div = document.createElement('li');
									div.innerHTML = '<i></i><img src="/attachment/'+data.path+'" width="100%">';
									that.fileImg.appendChild(div);
								if(that.fileImg.children.length > 0){
									var dela = that.fileImg.getElementsByTagName('i');
									for(var i=0,len=dela.length; i < len; i++){
										dela[i].onclick = function(){
											//由于循环，会永远导致i为最大值，所以要获取索引值来删除
											var index = that.getIndex(this, dela);
											uploadImg('del', index);
											that.fileFilter.splice(index,1);  //注意splice实现删除，会导致数组索引值重新排列
											that.imgUrl.splice(index, 1);
											console.log(that.imgUrl);
											this.parentNode.parentNode.removeChild(this.parentNode);
										}				
									}
								}
							}
							that.load.hide();
						}
						// console.log(data);
						// console.log(xmlhttp.responseText);
					}
				}
				xmlhttp.onprogress=function(event){
					//event.lengthComputable 表示进度条信息是否可用的布尔值,position表示已接收到的位置，totalsize表示根据content-length响应头部确定的预期字节数;
					if(event.lengthComputable){
						var percent=(event.loaded / event.total).toFixed(2)*100+'%';
						console.log(percent);
					}
				}
				var fileload=new FormData();
				fileload.append("image",filelist[0]);
				fileload.append("flag", flag);
				if (flag == 'del') {
					fileload.append("del", index);
				}
				xmlhttp.open("post",that.imgUpload,true);
				xmlhttp.send(fileload);
			}
		},
		// 选择数量改变事件
		afterNumChange: function() {
			var that = this,
				max = this.num.attr('data-max'),
				num;
			this.add.on('touchend', function() {
				num = that.num.val();
				if (num < max) {
					num++;
					that.num.val(num);
				}
			});
			this.reduce.on('touchend', function() {
				num = that.num.val();
				if (num > 1) {
					num--;
					that.num.val(num);
				}
			});
		},
		// 提交信息验证
		checkInfo: function () {
			var that = this;
			if (this.desc.val() == '') {
				Util.promptBox({
					title: 'note',
					info: '请填写问题描述信息',
					leftBtn: '我知道了'
				});
				return false;
			} else if (that.fileFilter.length == 0) {
				Util.promptBox({
					title: 'note',
					info: '请最少选择一张图片',
					leftBtn: '我知道了'
				});
				return false;
			}
			return true;
		},
		// 提交信息
		submitInfo: function() {
			var that = this,
				order = this.oInfo.attr('data-order'),
				goodsId = this.oInfo.attr('data-goodsId'),
				type = this.typeHide.val(),
				num = this.num.val(),
				desc = this.desc.val();
			/*
			* 提交售后信息
			* 参数：
			* order: 订单号
			* id: 申请售后产品id
			* type: 服务类型 (值：换货、退货)
			* num: 售后数量
			* desc: 售后服务
			* imgUrl: 售后产品图片地址
			*/
			$.ajax({
				type: 'post',
				url: that.url,
				data: {order: order, id: goodsId, type: type, num: num, desc: desc, imgUrl: that.imgUrl},
				dataType: 'json',
				success: function(data) {
					that.load.hide();
					if (data.status == 200) {
						Util.promptBox({
							title: 'ok',
							info: '工作人员将在2个工作日内处理您的请求，请耐心等候',
							leftBtn: '我知道了',
							callback: function() {
								location.href = data.link;
							}
						});
					}else{
						Util.promptBox({
							title: 'note',
							info: data.msg,
							leftBtn: '确认'
						});
					}
				}
			});
		},
		// 上传
		uploadBtn: function() {
			var that = this;
			//ajax上传图片
			this.btn.addEventListener('touchend',function(){
				if (!that.checkInfo()) {
					return false;
				}
				that.load.show().find('span').text('正在提交信息...');
				that.submitInfo();
				
			},false);
		},
		init: function() {
			var that = this;
			this.tabType();
			this.uploadBtn();
			this.afterNumChange();		// 选择数量
			this.upfile.addEventListener("change",function(e){
				that.load.show().find('span').text('正在上传图片...');
				that.uploadfile(e);
			},false);
		}
	};

	window.UploadImg = UploadImg;

})(window);