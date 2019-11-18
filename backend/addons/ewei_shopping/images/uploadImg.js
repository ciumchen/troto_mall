! function (t, e) {
    function i(t) {
        this._form = t._form, this.upfile = t.upfile, this.desc = t.desc, this.pro = t.pro, this._type = t._type, this.btn =
            t.btn, this.load = t.load, this.fileImg = t.fileImg, this._type = t._type, this.typeHide = t.typeHide, this
            .imgUpload = t.imgUpload, this.url = t.url, this.oInfo = t.oInfo, this.add = t.add, this.num = t.num, this.reduce =
            t.reduce, this.fileFilter = [], this.imgUrl = [], this.init()
    }
    i.prototype = {
        constructor: i,
        tabType: function () {
            var t = this;
            t._type.find("li").on("touchend", function () {
                $(this).addClass("on").siblings().removeClass("on"), t.typeHide.val($(this).attr("data-type"))
            })
        },
        getIndex: function (t, e) {
            e = e || [];
            for (var i = 0, n = e.length; n > i; i++) if (e[i] == t) return i
        },
        uploadfile: function (t) {
            function e(t, o) {
                var a = new XMLHttpRequest;
                a.onreadystatechange = function () {
                    if (4 == a.readyState && 200 == a.status) {
                        var n = JSON.parse(a.responseText);
                        if (200 == n.status) {
                            if ("add" == t) {
                                i.imgUrl.push(n.path), console.log(i.imgUrl);
                                var o = document.createElement("li");
                                if (o.innerHTML = '<i></i><img src="/attachment/' + n.path + '" width="100%">', i.fileImg
                                    .appendChild(o), i.fileImg.children.length > 0) for (var l = i.fileImg.getElementsByTagName(
                                        "i"), s = 0, d = l.length; d > s; s++) l[s].onclick = function () {
                                            var t = i.getIndex(this, l);
                                            e("del", t), i.fileFilter.splice(t, 1), i.imgUrl.splice(t, 1), console.log(
                                                i.imgUrl), this.parentNode.parentNode.removeChild(this.parentNode)
                                }
                            }
                            i.load.hide()
                        }
                    }
                }, a.onprogress = function (t) {
                    if (t.lengthComputable) {
                        var e = 100 * (t.loaded / t.total).toFixed(2) + "%";
                        console.log(e)
                    }
                };
                var l = new FormData;
                l.append("image", n[0]), l.append("flag", t), "del" == t && l.append("del", o), a.open("post", i.imgUpload, !
                    0), a.send(l)
            }
            var i = this,
                n = t.target.files;
            if (-1 == n[0].type.indexOf("image")) return Util.promptBox({
                    title: "note",
                    info: "请选择图片上传！",
                    leftBtn: "我知道了"
                }), i.load.hide(), !1;
            if (this.fileFilter.length >= 3) return Util.promptBox({
                    title: "note",
                    info: "最多只能三张图片！",
                    leftBtn: "我知道了"
                }), i.load.hide(), !1;
            this.fileFilter.push(n);
            var o = (n[0].name, Math.floor(n[0].size / 1024));
            return o > 2048 ? (Util.promptBox({
                title: "note",
                info: "上传大小不能超过2M",
                leftBtn: "我知道了"
            }), i.load.hide(), !1) : void e("add")
        },
        afterNumChange: function () {
            var t, e = this,
                i = this.num.attr("data-max");
            this.add.on("touchend", function () {
                t = e.num.val(), i > t && (t++, e.num.val(t))
            }), this.reduce.on("touchend", function () {
                t = e.num.val(), t > 1 && (t--, e.num.val(t))
            })
        },
        checkInfo: function () {
            var t = this;
            return "" == this.desc.val() ? (Util.promptBox({
                title: "note",
                info: "请填写问题描述信息",
                leftBtn: "我知道了"
            }), !1) : 0 == t.fileFilter.length ? (Util.promptBox({
                title: "note",
                info: "请最少选择一张图片",
                leftBtn: "我知道了"
            }), !1) : !0
        },
        submitInfo: function () {
            var t = this,
                e = this.oInfo.attr("data-order"),
                i = this.oInfo.attr("data-goodsId"),
                n = this.typeHide.val(),
                o = this.num.val(),
                a = this.desc.val();
            $.ajax({
                type: "post",
                url: t.url,
                data: {
                    order: e,
                    id: i,
                    type: n,
                    num: o,
                    desc: a,
                    imgUrl: t.imgUrl
                },
                dataType: "json",
                success: function (e) {
                    t.load.hide(), 200 == e.status ? Util.promptBox({
                        title: "ok",
                        info: "工作人员将在2个工作日内处理您的请求，请耐心等候",
                        leftBtn: "我知道了",
                        callback: function () {
                            location.href = e.link
                        }
                    }) : Util.promptBox({
                        title: "note",
                        info: e.msg,
                        leftBtn: "确认"
                    })
                }
            })
        },
        uploadBtn: function () {
            var t = this;
            this.btn.addEventListener("touchend", function () {
                return t.checkInfo() ? (t.load.show().find("span").text("正在提交信息..."), void t.submitInfo()) : !1
            }, !1)
        },
        init: function () {
            var t = this;
            this.tabType(), this.uploadBtn(), this.afterNumChange(), this.upfile.addEventListener("change", function (e) {
                t.load.show().find("span").text("正在上传图片..."), t.uploadfile(e)
            }, !1)
        }
    }, t.UploadImg = i
}(window);