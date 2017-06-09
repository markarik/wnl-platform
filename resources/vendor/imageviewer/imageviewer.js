/*
 ImageViewer v 1.1.3
 Author: Sudhanshu Yadav
 Copyright (c) 2015 to Sudhanshu Yadav - ignitersworld.com , released under the MIT license.
 Demo on: http://ignitersworld.com/lab/imageViewer.html
 */

!function (i, e, t, n) {
	"use strict";
	function a(i, e, t, n) {
		return i /= n, i--, -t * (i * i * i * i - 1) + e
	}

	function o(i) {
		return i.complete && ("undefined" == typeof i.naturalWidth || 0 !== i.naturalWidth)
	}

	function s(i, e) {
		this.container = i, this.onStart = e.onStart || h, this.onMove = e.onMove || h, this.onEnd = e.onEnd || h, this.sliderId = e.sliderId || "slider" + Math.ceil(1e6 * Math.random())
	}

	function r(e, t) {
		var n = this;
		e.is("#iv-container") && (n._fullPage = !0), n.container = e, t = n.options = i.extend({}, r.defaults, t), n.zoomValue = 100, e.find(".snap-view").length || e.prepend(v), e.addClass("iv-container"), "static" == e.css("position") && e.css("position", "relative"), n.snapView = e.find(".iv-snap-view"), n.snapImageWrap = e.find(".iv-snap-image-wrap"), n.imageWrap = e.find(".iv-image-wrap"), n.snapHandle = e.find(".iv-snap-handle"), n.zoomHandle = e.find(".iv-zoom-handle"), n._viewerId = "iv" + Math.floor(1e6 * Math.random())
	}

	var h = function () {
	}, m  = i("body"), c = i(e), l = i(t), d = 15, u = 5;
	!function () {
		for (var i = 0, t = ["ms", "moz", "webkit", "o"], n = 0; n < t.length && !e.requestAnimationFrame; ++n)e.requestAnimationFrame = e[t[n] + "RequestAnimationFrame"], e.cancelAnimationFrame = e[t[n] + "CancelAnimationFrame"] || e[t[n] + "CancelRequestAnimationFrame"];
		e.requestAnimationFrame || (e.requestAnimationFrame = function (t, n) {
			var a = (new Date).getTime(), o = Math.max(0, 16 - (a - i)), s = e.setTimeout(function () {
				t(a + o)
			}, o);
			return i = a + o, s
		}), e.cancelAnimationFrame || (e.cancelAnimationFrame = function (i) {
			clearTimeout(i)
		})
	}();
	var v = '<div class="iv-loader"></div> <div class="iv-snap-view"><div class="iv-snap-image-wrap"><div class="iv-snap-handle"></div></div><div class="iv-zoom-slider"><div class="iv-zoom-handle"></div></div></div><div class="iv-image-view" ><div class="iv-image-wrap" ></div></div>';
	i(function () {
		m.length || (m = i("body")), m.append('<div id="iv-container">' + v + '<div class="iv-close"></div><div>')
	}), s.prototype.init = function () {
		var i = this, e = (this.container, "." + this.sliderId);
		return this.container.on("touchstart" + e + " mousedown" + e, function (t) {
			t.preventDefault();
			var n = ("touchstart" == t.type ? "touchmove" : "mousemove") + e, a = ("touchstart" == t.type ? "touchend" : "mouseup") + e, o = t.originalEvent, s = o.clientX || o.touches[0].clientX, r = o.clientY || o.touches[0].clientY, h = i.onStart(t, {
				x: s,
				y: r
			});
			if (h !== !1) {
				var m = function (e) {
					e.preventDefault(), o = e.originalEvent;
					var t = o.clientX || o.touches[0].clientX, n = o.clientY || o.touches[0].clientY;
					i.onMove(e, {dx: t - s, dy: n - r, mx: t, my: n})
				}, c  = function () {
					l.off(n, m), l.off(a, c), i.onEnd()
				};
				l.on(n, m), l.on(a, c)
			}
		}), this
	}, r.prototype = {
		constructor: r, _init: function () {
			function i(i) {
				t.snapView && (x || e.zoomValue <= 100 || !e.loaded || (clearTimeout(z), x = !0, e.snapView.css("opacity", 1), i || (z = setTimeout(function () {
					e.snapView.css("opacity", 0), x = !1
				}, 4e3))))
			}

			var e        = this, t = e.options, n = !1, o = this.container, r = "." + e._viewerId, h = this.snapHandle, m = this.snapImageWrap, v = this.imageWrap, f = new s(m, {
				sliderId: e._viewerId,
				onStart: function () {
					if (!e.loaded)return !1;
					var i = h[0].style;
					this.curHandleTop = parseFloat(i.top), this.curHandleLeft = parseFloat(i.left), this.handleWidth = parseFloat(i.width), this.handleHeight = parseFloat(i.height), this.width = m.width(), this.height = m.height(), clearInterval(p.slideMomentumCheck), cancelAnimationFrame(p.sliderMomentumFrame)
				},
				onMove: function (i, t) {
					var n = this.curHandleLeft + 100 * t.dx / this.width, a = this.curHandleTop + 100 * t.dy / this.height;
					n = Math.max(0, n), n = Math.min(100 - this.handleWidth, n), a = Math.max(0, a), a = Math.min(100 - this.handleHeight, a);
					var o = e.containerDim, s = e.imageDim.w * (e.zoomValue / 100), r = e.imageDim.h * (e.zoomValue / 100), m = s < o.w ? (o.w - s) / 2 : -s * n / 100, c = r < o.h ? (o.h - r) / 2 : -r * a / 100;
					h.css({top: a + "%", left: n + "%"}), e.currentImg.css({left: m, top: c})
				}
			}).init(), p = e._imageSlider = new s(v, {
				sliderId: e._viewerId, onStart: function (i, t) {
					if (!e.loaded)return !1;
					if (!n) {
						var a = this;
						f.onStart(), a.imgWidth = e.imageDim.w * e.zoomValue / 100, a.imgHeight = e.imageDim.h * e.zoomValue / 100, a.positions = [t, t], a.startPosition = t, e._clearFrames(), a.slideMomentumCheck = setInterval(function () {
							a.currentPos && (a.positions.shift(), a.positions.push({
								x: a.currentPos.mx,
								y: a.currentPos.my
							}))
						}, 50)
					}
				}, onMove: function (i, e) {
					n || (this.currentPos = e, f.onMove(i, {
						dx: -e.dx * f.width / this.imgWidth,
						dy: -e.dy * f.height / this.imgHeight
					}))
				}, onEnd: function () {
					function i() {
						60 >= s && (e.sliderMomentumFrame = requestAnimationFrame(i)), r += a(s, t / 3, -t / 3, 60), h += a(s, o / 3, -o / 3, 60), f.onMove(null, {
							dx: -(r * f.width / e.imgWidth),
							dy: -(h * f.height / e.imgHeight)
						}), s++
					}

					if (!n) {
						var e = this, t = this.positions[1].x - this.positions[0].x, o = this.positions[1].y - this.positions[0].y;
						if (Math.abs(t) > 30 || Math.abs(o) > 30) {
							var s = 1, r = e.currentPos.dx, h = e.currentPos.dy;
							i()
						}
					}
				}
			}).init(), g = 0;
			v.on("mousewheel" + r + " DOMMouseScroll" + r, function (n) {
				if (t.zoomOnMouseWheel && e.loaded) {
					e._clearFrames();
					var a = Math.max(-1, Math.min(1, n.originalEvent.wheelDelta || -n.originalEvent.detail)), s = e.zoomValue * (100 + a * d) / 100;
					if (s >= 100 && s <= t.maxZoom ? g = 0 : g += Math.abs(a), !(g > u)) {
						n.preventDefault();
						var r = o.offset(), h = (n.pageX || n.originalEvent.pageX) - r.left, m = (n.pageY || n.originalEvent.pageY) - r.top;
						e.zoom(s, {x: h, y: m}), i()
					}
				}
			}), v.on("touchstart" + r, function (i) {
				if (e.loaded) {
					var t = i.originalEvent.touches[0], a = i.originalEvent.touches[1];
					if (t && a) {
						n     = !0;
						var s = o.offset(), r = Math.sqrt(Math.pow(a.pageX - t.pageX, 2) + Math.pow(a.pageY - t.pageY, 2)), h = e.zoomValue, m = {
							x: (a.pageX + t.pageX) / 2 - s.left,
							y: (a.pageY + t.pageY) / 2 - s.top
						}, c  = function (i) {
							i.preventDefault();
							var t = i.originalEvent.touches[0], n = i.originalEvent.touches[1], a = Math.sqrt(Math.pow(n.pageX - t.pageX, 2) + Math.pow(n.pageY - t.pageY, 2)), o = h + (a - r) / 2;
							e.zoom(o, m)
						}, d  = function () {
							l.off("touchmove", c), l.off("touchend", d), n = !1
						};
						l.on("touchmove", c), l.on("touchend", d)
					}
				}
			});
			var w, M = 0;
			v.on("click" + r, function (i) {
				0 == M ? (M = Date.now(), w = {
					x: i.pageX,
					y: i.pageY
				}) : Date.now() - M < 500 && Math.abs(i.pageX - w.x) < 50 && Math.abs(i.pageY - w.y) < 50 ? (e.zoomValue == t.zoomValue ? e.zoom(200) : e.resetZoom(), M = 0) : M = 0
			});
			var z, x, y = e.snapView.find(".iv-zoom-slider");
			new s(y, {
				sliderId: e._viewerId, onStart: function (i) {
					return e.loaded ? (this.leftOffset = y.offset().left, this.handleWidth = e.zoomHandle.width(), void this.onMove(i)) : !1
				}, onMove: function (i, n) {
					var a = (i.pageX || i.originalEvent.touches[0].pageX) - this.leftOffset - this.handleWidth / 2;
					a = Math.max(0, a), a = Math.min(e._zoomSliderLength, a);
					var o = 100 + (t.maxZoom - 100) * a / e._zoomSliderLength;
					e.zoom(o)
				}
			}).init();
			v.on("touchmove" + r + " mousemove" + r, function () {
				i()
			});
			var D = {};
			D["mouseenter" + r + " touchstart" + r] = function () {
				x = !1, i(!0)
			}, D["mouseleave" + r + " touchend" + r] = function () {
				x = !1, i()
			}, e.snapView.on(D), t.refreshOnResize && c.on("resize" + r, function () {
				e.refresh()
			}), e._fullPage && (o.on("touchmove" + r + " mousewheel" + r + " DOMMouseScroll" + r, function (i) {
				i.preventDefault()
			}), o.find(".iv-close").on("click" + r, function () {
				e.hide()
			}))
		}, zoom: function (i, e) {
			function t() {
				l++, 20 > l && (n._zoomFrame = requestAnimationFrame(t));
				var h = a(l, s, i - s, 20), d = h / s, g = n.imageDim.w * h / 100, w = n.imageDim.h * h / 100, M = -((e.x - m) * d - e.x), z = -((e.y - c) * d - e.y);
				M = Math.min(M, u), z = Math.min(z, v), f > M + g && (M = f - g), p > z + w && (z = p - w), r.css({
					height: w + "px",
					width: g + "px",
					left: M + "px",
					top: z + "px"
				}), n.zoomValue = h, n._resizeHandle(g, w, M, z), n.zoomHandle.css("left", (h - 100) * n._zoomSliderLength / (o - 100) + "px")
			}

			i = Math.round(Math.max(100, i)), i = Math.min(this.options.maxZoom, i), e = e || {
					x: this.containerDim.w / 2,
					y: this.containerDim.h / 2
				};
			var n = this, o = this.options.maxZoom, s = this.zoomValue, r = this.currentImg, h = this.containerDim, m = parseFloat(r.css("left")), c = parseFloat(r.css("top"));
			n._clearFrames();
			var l = 0, h = n.containerDim, d = n.imageDim, u = (h.w - d.w) / 2, v = (h.h - d.h) / 2, f = h.w - u, p = h.h - v;
			t()
		}, _clearFrames: function () {
			clearInterval(this._imageSlider.slideMomentumCheck), cancelAnimationFrame(this._imageSlider.sliderMomentumFrame), cancelAnimationFrame(this._zoomFrame)
		}, resetZoom: function () {
			this.zoom(this.options.zoomValue)
		}, _calculateDimensions: function () {
			var i          = this, e = i.currentImg, t = i.container, n = i.snapView, a = e.width(), o = e.height(), s = t.width(), r = t.height(), h = n.innerWidth(), m = n.innerHeight();
			i.containerDim = {w: s, h: r};
			var c, l, d    = a / o;
			c = a > o && r >= s || d * r > s ? s : d * r, l = c / d, i.imageDim = {w: c, h: l}, e.css({
				width: c + "px",
				height: l + "px",
				left: (s - c) / 2 + "px",
				top: (r - l) / 2 + "px",
				"max-width": "none",
				"max-height": "none"
			});
			var u = c > l ? h : c * m / l, v = l > c ? m : l * h / c;
			i.snapImageDim = {w: u, h: v}, i.snapImg.css({
				width: u,
				height: v
			}), i._zoomSliderLength = h - i.zoomHandle.outerWidth()
		}, refresh: function () {
			this.loaded && (this._calculateDimensions(), this.resetZoom())
		}, _resizeHandle: function (i, e, t, n) {
			var a = this.currentImg, o = i || this.imageDim.w * this.zoomValue / 100, s = e || this.imageDim.h * this.zoomValue / 100, r = Math.max(100 * -(t || parseFloat(a.css("left"))) / o, 0), h = Math.max(100 * -(n || parseFloat(a.css("top"))) / s, 0), m = Math.min(100 * this.containerDim.w / o, 100), c = Math.min(100 * this.containerDim.h / s, 100);
			this.snapHandle.css({top: h + "%", left: r + "%", width: m + "%", height: c + "%"})
		}, show: function (i, e) {
			this._fullPage && (this.container.show(), i && this.load(i, e))
		}, hide: function () {
			this._fullPage && this.container.hide()
		}, options: function (i, e) {
			return e ? void(this.options[i] = e) : this.options[i]
		}, destroy: function (i, e) {
			var t = "." + this._viewerId;
			return this._fullPage ? (container.off(t), container.find('[class^="iv"]').off(t)) : this.container.remove('[class^="iv"]'), c.off(t), null
		}, load: function (e, t) {
			function n() {
				a.loaded = !0, a.zoomValue = 100, h.show(), a.snapImg.show(), a.refresh(), a.resetZoom(), s.find(".iv-loader").hide()
			}

			var a = this, s = this.container;
			s.find(".iv-snap-image,.iv-large-image").remove();
			var r = this.container.find(".iv-snap-image-wrap");
			r.prepend('<img class="iv-snap-image" src="' + e + '" />'), this.imageWrap.prepend('<img class="iv-large-image" src="' + e + '" />'), t && this.imageWrap.append('<img class="iv-large-image" src="' + t + '" />');
			var h = this.currentImg = this.container.find(".iv-large-image");
			this.snapImg = this.container.find(".iv-snap-image"), a.loaded = !1, s.find(".iv-loader").show(), h.hide(), a.snapImg.hide(), o(h[0]) ? n() : i(h[0]).on("load", n)
		}
	}, r.defaults = {
		zoomValue: 100,
		snapView: !0,
		maxZoom: 500,
		refreshOnResize: !0,
		zoomOnMouseWheel: !0
	}, e.ImageViewer = function (e, t) {
		var n, a, o;
		e && ("string" == typeof e || e instanceof Element || e[0] instanceof Element) || (t = e, e = i("#iv-container")), e = i(e), e.is("img") ? (n = e, a = n[0].src, o = n.attr("high-res-src") || n.attr("data-high-res-src"), e = n.wrap('<div class="iv-container" style="display:inline-block; overflow:hidden"></div>').parent(), n.css({
			opacity: 0,
			position: "relative",
			zIndex: -1
		})) : (a = e.attr("src") || e.attr("data-src"), o = e.attr("high-res-src") || e.attr("data-high-res-src"));
		var s = new r(e, t);
		return s._init(), a && s.load(a, o), s
	}, i.fn.ImageViewer = function (t) {
		return this.each(function () {
			var n = i(this), a = e.ImageViewer(n, t);
			n.data("ImageViewer", a)
		})
	}
}(window.jQuery, window, document);