(function (e) {
	"use strict";
	var t = e.GreenSockGlobals || e;
	if (!t.TweenLite) {
		var n, r, i, s, o, u = function (e) {
			var n, r = e.split("."),
			i = t;
			for (n = 0; r.length > n; n++) i[r[n]] = i = i[r[n]] || {};
			return i
		},
		a = u("com.greensock"),
		f = 1e-10,
		l = [].slice,
		c = function () {},
		h = function () {
			var e = Object.prototype.toString,
			t = e.call([]);
			return function (n) {
				return n instanceof Array || "object" == typeof n && !!n.push && e.call(n) === t
			}
		} (),
		p = {},
		d = function (n, r, i, s) {
			this.sc = p[n] ? p[n].sc: [],
			p[n] = this,
			this.gsClass = null,
			this.func = i;
			var o = [];
			this.check = function (a) {
				for (var f, l, c, h, v = r.length, m = v; --v > -1;)(f = p[r[v]] || new d(r[v], [])).gsClass ? (o[v] = f.gsClass, m--) : a && f.sc.push(this);
				if (0 === m && i) for (l = ("com.greensock." + n).split("."), c = l.pop(), h = u(l.join("."))[c] = this.gsClass = i.apply(i, o), s && (t[c] = h, "function" == typeof define && define.amd ? define((e.GreenSockAMDPath ? e.GreenSockAMDPath + "/": "") + n.split(".").join("/"), [], function () {
					return h
				}) : "undefined" != typeof module && module.exports && (module.exports = h)), v = 0; this.sc.length > v; v++) this.sc[v].check()
			},
			this.check(!0)
		},
		v = e._gsDefine = function (e, t, n, r) {
			return new d(e, t, n, r)
		},
		m = a._class = function (e, t, n) {
			return t = t ||
			function () {},
			v(e, [], function () {
				return t
			},
			n),
			t
		};
		v.globals = t;
		var g = [0, 0, 1, 1],
		y = [],
		b = m("easing.Ease", function (e, t, n, r) {
			this._func = e,
			this._type = n || 0,
			this._power = r || 0,
			this._params = t ? g.concat(t) : g
		},
		!0),
		w = b.map = {},
		E = b.register = function (e, t, n, r) {
			for (var i, s, o, u, f = t.split(","), l = f.length, c = (n || "easeIn,easeOut,easeInOut").split(","); --l > -1;) for (s = f[l], i = r ? m("easing." + s, null, !0) : a.easing[s] || {},
			o = c.length; --o > -1;) u = c[o],
			w[s + "." + u] = w[u + s] = i[u] = e.getRatio ? e: e[u] || new e
		};
		for (i = b.prototype, i._calcEnd = !1, i.getRatio = function (e) {
			if (this._func) return this._params[0] = e,
			this._func.apply(null, this._params);
			var t = this._type,
			n = this._power,
			r = 1 === t ? 1 - e: 2 === t ? e: .5 > e ? 2 * e: 2 * (1 - e);
			return 1 === n ? r *= r: 2 === n ? r *= r * r: 3 === n ? r *= r * r * r: 4 === n && (r *= r * r * r * r),
			1 === t ? 1 - r: 2 === t ? r: .5 > e ? r / 2 : 1 - r / 2
		},
		n = ["Linear", "Quad", "Cubic", "Quart", "Quint,Strong"], r = n.length; --r > -1;) i = n[r] + ",Power" + r,
		E(new b(null, null, 1, r), i, "easeOut", !0),
		E(new b(null, null, 2, r), i, "easeIn" + (0 === r ? ",easeNone": "")),
		E(new b(null, null, 3, r), i, "easeInOut");
		w.linear = a.easing.Linear.easeIn,
		w.swing = a.easing.Quad.easeInOut;
		var S = m("events.EventDispatcher", function (e) {
			this._listeners = {},
			this._eventTarget = e || this
		});
		i = S.prototype,
		i.addEventListener = function (e, t, n, r, i) {
			i = i || 0;
			var u, a, f = this._listeners[e],
			l = 0;
			for (null == f && (this._listeners[e] = f = []), a = f.length; --a > -1;) u = f[a],
			u.c === t && u.s === n ? f.splice(a, 1) : 0 === l && i > u.pr && (l = a + 1);
			f.splice(l, 0, {
				c: t,
				s: n,
				up: r,
				pr: i
			}),
			this !== s || o || s.wake()
		},
		i.removeEventListener = function (e, t) {
			var n, r = this._listeners[e];
			if (r) for (n = r.length; --n > -1;) if (r[n].c === t) return r.splice(n, 1),
			void 0
		},
		i.dispatchEvent = function (e) {
			var t, n, r, i = this._listeners[e];
			if (i) for (t = i.length, n = this._eventTarget; --t > -1;) r = i[t],
			r.up ? r.c.call(r.s || n, {
				type: e,
				target: n
			}) : r.c.call(r.s || n)
		};
		var x = e.requestAnimationFrame,
		T = e.cancelAnimationFrame,
		N = Date.now ||
		function () {
			return (new Date).getTime()
		},
		C = N();
		for (n = ["ms", "moz", "webkit", "o"], r = n.length; --r > -1 && !x;) x = e[n[r] + "RequestAnimationFrame"],
		T = e[n[r] + "CancelAnimationFrame"] || e[n[r] + "CancelRequestAnimationFrame"];
		m("Ticker", function (e, t) {
			var n, r, i, u, a, f = this,
			l = N(),
			h = t !== !1 && x,
			p = function (e) {
				C = N(),
				f.time = (C - l) / 1e3;
				var t, s = f.time - a;
				(!n || s > 0 || e === !0) && (f.frame++, a += s + (s >= u ? .004 : u - s), t = !0),
				e !== !0 && (i = r(p)),
				t && f.dispatchEvent("tick")
			};
			S.call(f),
			f.time = f.frame = 0,
			f.tick = function () {
				p(!0)
			},
			f.sleep = function () {
				null != i && (h && T ? T(i) : clearTimeout(i), r = c, i = null, f === s && (o = !1))
			},
			f.wake = function () {
				null !== i && f.sleep(),
				r = 0 === n ? c: h && x ? x: function (e) {
					return setTimeout(e, 0 | 1e3 * (a - f.time) + 1)
				},
				f === s && (o = !0),
				p(2)
			},
			f.fps = function (e) {
				return arguments.length ? (n = e, u = 1 / (n || 60), a = this.time + u, f.wake(), void 0) : n
			},
			f.useRAF = function (e) {
				return arguments.length ? (f.sleep(), h = e, f.fps(n), void 0) : h
			},
			f.fps(e),
			setTimeout(function () {
				h && (!i || 5 > f.frame) && f.useRAF(!1)
			},
			1500)
		}),
		i = a.Ticker.prototype = new a.events.EventDispatcher,
		i.constructor = a.Ticker;
		var k = m("core.Animation", function (e, t) {
			if (this.vars = t = t || {},
			this._duration = this._totalDuration = e || 0, this._delay = Number(t.delay) || 0, this._timeScale = 1, this._active = t.immediateRender === !0, this.data = t.data, this._reversed = t.reversed === !0, q) {
				o || s.wake();
				var n = this.vars.useFrames ? I: q;
				n.add(this, n._time),
				this.vars.paused && this.paused(!0)
			}
		});
		s = k.ticker = new a.Ticker,
		i = k.prototype,
		i._dirty = i._gc = i._initted = i._paused = !1,
		i._totalTime = i._time = 0,
		i._rawPrevTime = -1,
		i._next = i._last = i._onUpdate = i._timeline = i.timeline = null,
		i._paused = !1;
		var L = function () {
			o && N() - C > 2e3 && s.wake(),
			setTimeout(L, 2e3)
		};
		L(),
		i.play = function (e, t) {
			return arguments.length && this.seek(e, t),
			this.reversed(!1).paused(!1)
		},
		i.pause = function (e, t) {
			return arguments.length && this.seek(e, t),
			this.paused(!0)
		},
		i.resume = function (e, t) {
			return arguments.length && this.seek(e, t),
			this.paused(!1)
		},
		i.seek = function (e, t) {
			return this.totalTime(Number(e), t !== !1)
		},
		i.restart = function (e, t) {
			return this.reversed(!1).paused(!1).totalTime(e ? -this._delay: 0, t !== !1, !0)
		},
		i.reverse = function (e, t) {
			return arguments.length && this.seek(e || this.totalDuration(), t),
			this.reversed(!0).paused(!1)
		},
		i.render = function () {},
		i.invalidate = function () {
			return this
		},
		i.isActive = function () {
			var e, t = this._timeline,
			n = this._startTime;
			return ! t || !this._gc && !this._paused && t.isActive() && (e = t.rawTime()) >= n && n + this.totalDuration() / this._timeScale > e
		},
		i._enabled = function (e, t) {
			return o || s.wake(),
			this._gc = !e,
			this._active = this.isActive(),
			t !== !0 && (e && !this.timeline ? this._timeline.add(this, this._startTime - this._delay) : !e && this.timeline && this._timeline._remove(this, !0)),
			!1
		},
		i._kill = function () {
			return this._enabled(!1, !1)
		},
		i.kill = function (e, t) {
			return this._kill(e, t),
			this
		},
		i._uncache = function (e) {
			for (var t = e ? this: this.timeline; t;) t._dirty = !0,
			t = t.timeline;
			return this
		},
		i._swapSelfInParams = function (e) {
			for (var t = e.length, n = e.concat(); --t > -1;)"{self}" === e[t] && (n[t] = this);
			return n
		},
		i.eventCallback = function (e, t, n, r) {
			if ("on" === (e || "").substr(0, 2)) {
				var i = this.vars;
				if (1 === arguments.length) return i[e];
				null == t ? delete i[e] : (i[e] = t, i[e + "Params"] = h(n) && -1 !== n.join("").indexOf("{self}") ? this._swapSelfInParams(n) : n, i[e + "Scope"] = r),
				"onUpdate" === e && (this._onUpdate = t)
			}
			return this
		},
		i.delay = function (e) {
			return arguments.length ? (this._timeline.smoothChildTiming && this.startTime(this._startTime + e - this._delay), this._delay = e, this) : this._delay
		},
		i.duration = function (e) {
			return arguments.length ? (this._duration = this._totalDuration = e, this._uncache(!0), this._timeline.smoothChildTiming && this._time > 0 && this._time < this._duration && 0 !== e && this.totalTime(this._totalTime * (e / this._duration), !0), this) : (this._dirty = !1, this._duration)
		},
		i.totalDuration = function (e) {
			return this._dirty = !1,
			arguments.length ? this.duration(e) : this._totalDuration
		},
		i.time = function (e, t) {
			return arguments.length ? (this._dirty && this.totalDuration(), this.totalTime(e > this._duration ? this._duration: e, t)) : this._time
		},
		i.totalTime = function (e, t, n) {
			if (o || s.wake(), !arguments.length) return this._totalTime;
			if (this._timeline) {
				if (0 > e && !n && (e += this.totalDuration()), this._timeline.smoothChildTiming) {
					this._dirty && this.totalDuration();
					var r = this._totalDuration,
					i = this._timeline;
					if (e > r && !n && (e = r), this._startTime = (this._paused ? this._pauseTime: i._time) - (this._reversed ? r - e: e) / this._timeScale, i._dirty || this._uncache(!1), i._timeline) for (; i._timeline;) i._timeline._time !== (i._startTime + i._totalTime) / i._timeScale && i.totalTime(i._totalTime, !0),
					i = i._timeline
				}
				this._gc && this._enabled(!0, !1),
				(this._totalTime !== e || 0 === this._duration) && this.render(e, t, !1)
			}
			return this
		},
		i.progress = i.totalProgress = function (e, t) {
			return arguments.length ? this.totalTime(this.duration() * e, t) : this._time / this.duration()
		},
		i.startTime = function (e) {
			return arguments.length ? (e !== this._startTime && (this._startTime = e, this.timeline && this.timeline._sortChildren && this.timeline.add(this, e - this._delay)), this) : this._startTime
		},
		i.timeScale = function (e) {
			if (!arguments.length) return this._timeScale;
			if (e = e || f, this._timeline && this._timeline.smoothChildTiming) {
				var t = this._pauseTime,
				n = t || 0 === t ? t: this._timeline.totalTime();
				this._startTime = n - (n - this._startTime) * this._timeScale / e
			}
			return this._timeScale = e,
			this._uncache(!1)
		},
		i.reversed = function (e) {
			return arguments.length ? (e != this._reversed && (this._reversed = e, this.totalTime(this._totalTime, !0)), this) : this._reversed
		},
		i.paused = function (e) {
			if (!arguments.length) return this._paused;
			if (e != this._paused && this._timeline) {
				o || e || s.wake();
				var t = this._timeline,
				n = t.rawTime(),
				r = n - this._pauseTime; ! e && t.smoothChildTiming && (this._startTime += r, this._uncache(!1)),
				this._pauseTime = e ? n: null,
				this._paused = e,
				this._active = this.isActive(),
				!e && 0 !== r && this._initted && this.duration() && this.render(t.smoothChildTiming ? this._totalTime: (n - this._startTime) / this._timeScale, !0, !0)
			}
			return this._gc && !e && this._enabled(!0, !1),
			this
		};
		var A = m("core.SimpleTimeline", function (e) {
			k.call(this, 0, e),
			this.autoRemoveChildren = this.smoothChildTiming = !0
		});
		i = A.prototype = new k,
		i.constructor = A,
		i.kill()._gc = !1,
		i._first = i._last = null,
		i._sortChildren = !1,
		i.add = i.insert = function (e, t) {
			var n, r;
			if (e._startTime = Number(t || 0) + e._delay, e._paused && this !== e._timeline && (e._pauseTime = e._startTime + (this.rawTime() - e._startTime) / e._timeScale), e.timeline && e.timeline._remove(e, !0), e.timeline = e._timeline = this, e._gc && e._enabled(!0, !0), n = this._last, this._sortChildren) for (r = e._startTime; n && n._startTime > r;) n = n._prev;
			return n ? (e._next = n._next, n._next = e) : (e._next = this._first, this._first = e),
			e._next ? e._next._prev = e: this._last = e,
			e._prev = n,
			this._timeline && this._uncache(!0),
			this
		},
		i._remove = function (e, t) {
			return e.timeline === this && (t || e._enabled(!1, !0), e.timeline = null, e._prev ? e._prev._next = e._next: this._first === e && (this._first = e._next), e._next ? e._next._prev = e._prev: this._last === e && (this._last = e._prev), this._timeline && this._uncache(!0)),
			this
		},
		i.render = function (e, t, n) {
			var r, i = this._first;
			for (this._totalTime = this._time = this._rawPrevTime = e; i;) r = i._next,
			(i._active || e >= i._startTime && !i._paused) && (i._reversed ? i.render((i._dirty ? i.totalDuration() : i._totalDuration) - (e - i._startTime) * i._timeScale, t, n) : i.render((e - i._startTime) * i._timeScale, t, n)),
			i = r
		},
		i.rawTime = function () {
			return o || s.wake(),
			this._totalTime
		};
		var O = m("TweenLite", function (t, n, r) {
			if (k.call(this, n, r), this.render = O.prototype.render, null == t) throw "Cannot tween a null target.";
			this.target = t = "string" != typeof t ? t: O.selector(t) || t;
			var i, s, o, u = t.jquery || t.length && t !== e && t[0] && (t[0] === e || t[0].nodeType && t[0].style && !t.nodeType),
			a = this.vars.overwrite;
			if (this._overwrite = a = null == a ? F[O.defaultOverwrite] : "number" == typeof a ? a >> 0 : F[a], (u || t instanceof Array || t.push && h(t)) && "number" != typeof t[0]) for (this._targets = o = l.call(t, 0), this._propLookup = [], this._siblings = [], i = 0; o.length > i; i++) s = o[i],
			s ? "string" != typeof s ? s.length && s !== e && s[0] && (s[0] === e || s[0].nodeType && s[0].style && !s.nodeType) ? (o.splice(i--, 1), this._targets = o = o.concat(l.call(s, 0))) : (this._siblings[i] = R(s, this, !1), 1 === a && this._siblings[i].length > 1 && U(s, this, null, 1, this._siblings[i])) : (s = o[i--] = O.selector(s), "string" == typeof s && o.splice(i + 1, 1)) : o.splice(i--, 1);
			else this._propLookup = {},
			this._siblings = R(t, this, !1),
			1 === a && this._siblings.length > 1 && U(t, this, null, 1, this._siblings);
			(this.vars.immediateRender || 0 === n && 0 === this._delay && this.vars.immediateRender !== !1) && this.render( - this._delay, !1, !0)
		},
		!0),
		M = function (t) {
			return t.length && t !== e && t[0] && (t[0] === e || t[0].nodeType && t[0].style && !t.nodeType)
		},
		_ = function (e, t) {
			var n, r = {};
			for (n in e) j[n] || n in t && "x" !== n && "y" !== n && "width" !== n && "height" !== n && "className" !== n && "border" !== n || !(!P[n] || P[n] && P[n]._autoCSS) || (r[n] = e[n], delete e[n]);
			e.css = r
		};
		i = O.prototype = new k,
		i.constructor = O,
		i.kill()._gc = !1,
		i.ratio = 0,
		i._firstPT = i._targets = i._overwrittenProps = i._startAt = null,
		i._notifyPluginsOfEnabled = !1,
		O.version = "1.11.2",
		O.defaultEase = i._ease = new b(null, null, 1, 1),
		O.defaultOverwrite = "auto",
		O.ticker = s,
		O.autoSleep = !0,
		O.selector = e.$ || e.jQuery ||
		function (t) {
			return e.$ ? (O.selector = e.$, e.$(t)) : e.document ? e.document.getElementById("#" === t.charAt(0) ? t.substr(1) : t) : t
		};
		var D = O._internals = {
			isArray: h,
			isSelector: M
		},
		P = O._plugins = {},
		H = O._tweenLookup = {},
		B = 0,
		j = D.reservedProps = {
			ease: 1,
			delay: 1,
			overwrite: 1,
			onComplete: 1,
			onCompleteParams: 1,
			onCompleteScope: 1,
			useFrames: 1,
			runBackwards: 1,
			startAt: 1,
			onUpdate: 1,
			onUpdateParams: 1,
			onUpdateScope: 1,
			onStart: 1,
			onStartParams: 1,
			onStartScope: 1,
			onReverseComplete: 1,
			onReverseCompleteParams: 1,
			onReverseCompleteScope: 1,
			onRepeat: 1,
			onRepeatParams: 1,
			onRepeatScope: 1,
			easeParams: 1,
			yoyo: 1,
			immediateRender: 1,
			repeat: 1,
			repeatDelay: 1,
			data: 1,
			paused: 1,
			reversed: 1,
			autoCSS: 1
		},
		F = {
			none: 0,
			all: 1,
			auto: 2,
			concurrent: 3,
			allOnStart: 4,
			preexisting: 5,
			"true": 1,
			"false": 0
		},
		I = k._rootFramesTimeline = new A,
		q = k._rootTimeline = new A;
		q._startTime = s.time,
		I._startTime = s.frame,
		q._active = I._active = !0,
		k._updateRoot = function () {
			if (q.render((s.time - q._startTime) * q._timeScale, !1, !1), I.render((s.frame - I._startTime) * I._timeScale, !1, !1), !(s.frame % 120)) {
				var e, t, n;
				for (n in H) {
					for (t = H[n].tweens, e = t.length; --e > -1;) t[e]._gc && t.splice(e, 1);
					0 === t.length && delete H[n]
				}
				if (n = q._first, (!n || n._paused) && O.autoSleep && !I._first && 1 === s._listeners.tick.length) {
					for (; n && n._paused;) n = n._next;
					n || s.sleep()
				}
			}
		},
		s.addEventListener("tick", k._updateRoot);
		var R = function (e, t, n) {
			var r, i, s = e._gsTweenID;
			if (H[s || (e._gsTweenID = s = "t" + B++)] || (H[s] = {
				target: e,
				tweens: []
			}), t && (r = H[s].tweens, r[i = r.length] = t, n)) for (; --i > -1;) r[i] === t && r.splice(i, 1);
			return H[s].tweens
		},
		U = function (e, t, n, r, i) {
			var s, o, u, a;
			if (1 === r || r >= 4) {
				for (a = i.length, s = 0; a > s; s++) if ((u = i[s]) !== t) u._gc || u._enabled(!1, !1) && (o = !0);
				else if (5 === r) break;
				return o
			}
			var l, c = t._startTime + f,
			h = [],
			p = 0,
			d = 0 === t._duration;
			for (s = i.length; --s > -1;)(u = i[s]) === t || u._gc || u._paused || (u._timeline !== t._timeline ? (l = l || z(t, 0, d), 0 === z(u, l, d) && (h[p++] = u)) : c >= u._startTime && u._startTime + u.totalDuration() / u._timeScale + f > c && ((d || !u._initted) && 2e-10 >= c - u._startTime || (h[p++] = u)));
			for (s = p; --s > -1;) u = h[s],
			2 === r && u._kill(n, e) && (o = !0),
			(2 !== r || !u._firstPT && u._initted) && u._enabled(!1, !1) && (o = !0);
			return o
		},
		z = function (e, t, n) {
			for (var r = e._timeline, i = r._timeScale, s = e._startTime; r._timeline;) {
				if (s += r._startTime, i *= r._timeScale, r._paused) return - 100;
				r = r._timeline
			}
			return s /= i,
			s > t ? s - t: n && s === t || !e._initted && 2 * f > s - t ? f: (s += e.totalDuration() / e._timeScale / i) > t + f ? 0 : s - t - f
		};
		i._init = function () {
			var e, t, n, r, i = this.vars,
			s = this._overwrittenProps,
			o = this._duration,
			u = i.immediateRender,
			a = i.ease;
			if (i.startAt) {
				if (this._startAt && this._startAt.render( - 1, !0), i.startAt.overwrite = 0, i.startAt.immediateRender = !0, this._startAt = O.to(this.target, 0, i.startAt), u) if (this._time > 0) this._startAt = null;
				else if (0 !== o) return
			} else if (i.runBackwards && 0 !== o) if (this._startAt) this._startAt.render( - 1, !0),
			this._startAt = null;
			else {
				n = {};
				for (r in i) j[r] && "autoCSS" !== r || (n[r] = i[r]);
				if (n.overwrite = 0, n.data = "isFromStart", this._startAt = O.to(this.target, 0, n), i.immediateRender) {
					if (0 === this._time) return
				} else this._startAt.render( - 1, !0)
			}
			if (this._ease = a ? a instanceof b ? i.easeParams instanceof Array ? a.config.apply(a, i.easeParams) : a: "function" == typeof a ? new b(a, i.easeParams) : w[a] || O.defaultEase: O.defaultEase, this._easeType = this._ease._type, this._easePower = this._ease._power, this._firstPT = null, this._targets) for (e = this._targets.length; --e > -1;) this._initProps(this._targets[e], this._propLookup[e] = {},
			this._siblings[e], s ? s[e] : null) && (t = !0);
			else t = this._initProps(this.target, this._propLookup, this._siblings, s);
			if (t && O._onPluginEvent("_onInitAllProps", this), s && (this._firstPT || "function" != typeof this.target && this._enabled(!1, !1)), i.runBackwards) for (n = this._firstPT; n;) n.s += n.c,
			n.c = -n.c,
			n = n._next;
			this._onUpdate = i.onUpdate,
			this._initted = !0
		},
		i._initProps = function (t, n, r, i) {
			var s, o, u, a, f, l;
			if (null == t) return ! 1;
			this.vars.css || t.style && t !== e && t.nodeType && P.css && this.vars.autoCSS !== !1 && _(this.vars, t);
			for (s in this.vars) {
				if (l = this.vars[s], j[s]) l && (l instanceof Array || l.push && h(l)) && -1 !== l.join("").indexOf("{self}") && (this.vars[s] = l = this._swapSelfInParams(l, this));
				else if (P[s] && (a = new P[s])._onInitTween(t, this.vars[s], this)) {
					for (this._firstPT = f = {
						_next: this._firstPT,
						t: a,
						p: "setRatio",
						s: 0,
						c: 1,
						f: !0,
						n: s,
						pg: !0,
						pr: a._priority
					},
					o = a._overwriteProps.length; --o > -1;) n[a._overwriteProps[o]] = this._firstPT;
					(a._priority || a._onInitAllProps) && (u = !0),
					(a._onDisable || a._onEnable) && (this._notifyPluginsOfEnabled = !0)
				} else this._firstPT = n[s] = f = {
					_next: this._firstPT,
					t: t,
					p: s,
					f: "function" == typeof t[s],
					n: s,
					pg: !1,
					pr: 0
				},
				f.s = f.f ? t[s.indexOf("set") || "function" != typeof t["get" + s.substr(3)] ? s: "get" + s.substr(3)]() : parseFloat(t[s]),
				f.c = "string" == typeof l && "=" === l.charAt(1) ? parseInt(l.charAt(0) + "1", 10) * Number(l.substr(2)) : Number(l) - f.s || 0;
				f && f._next && (f._next._prev = f)
			}
			return i && this._kill(i, t) ? this._initProps(t, n, r, i) : this._overwrite > 1 && this._firstPT && r.length > 1 && U(t, this, n, this._overwrite, r) ? (this._kill(n, t), this._initProps(t, n, r, i)) : u
		},
		i.render = function (e, t, n) {
			var r, i, s, o, u = this._time,
			a = this._duration;
			if (e >= a) this._totalTime = this._time = a,
			this.ratio = this._ease._calcEnd ? this._ease.getRatio(1) : 1,
			this._reversed || (r = !0, i = "onComplete"),
			0 === a && (o = this._rawPrevTime, (0 === e || 0 > o || o === f) && o !== e && (n = !0, o > f && (i = "onReverseComplete")), this._rawPrevTime = o = !t || e ? e: f);
			else if (1e-7 > e) this._totalTime = this._time = 0,
			this.ratio = this._ease._calcEnd ? this._ease.getRatio(0) : 0,
			(0 !== u || 0 === a && this._rawPrevTime > f) && (i = "onReverseComplete", r = this._reversed),
			0 > e ? (this._active = !1, 0 === a && (this._rawPrevTime >= 0 && (n = !0), this._rawPrevTime = o = !t || e ? e: f)) : this._initted || (n = !0);
			else if (this._totalTime = this._time = e, this._easeType) {
				var l = e / a,
				c = this._easeType,
				h = this._easePower;
				(1 === c || 3 === c && l >= .5) && (l = 1 - l),
				3 === c && (l *= 2),
				1 === h ? l *= l: 2 === h ? l *= l * l: 3 === h ? l *= l * l * l: 4 === h && (l *= l * l * l * l),
				this.ratio = 1 === c ? 1 - l: 2 === c ? l: .5 > e / a ? l / 2 : 1 - l / 2
			} else this.ratio = this._ease.getRatio(e / a);
			if (this._time !== u || n) {
				if (!this._initted) {
					if (this._init(), !this._initted || this._gc) return;
					this._time && !r ? this.ratio = this._ease.getRatio(this._time / a) : r && this._ease._calcEnd && (this.ratio = this._ease.getRatio(0 === this._time ? 0 : 1))
				}
				for (this._active || !this._paused && this._time !== u && e >= 0 && (this._active = !0), 0 === u && (this._startAt && (e >= 0 ? this._startAt.render(e, t, n) : i || (i = "_dummyGS")), this.vars.onStart && (0 !== this._time || 0 === a) && (t || this.vars.onStart.apply(this.vars.onStartScope || this, this.vars.onStartParams || y))), s = this._firstPT; s;) s.f ? s.t[s.p](s.c * this.ratio + s.s) : s.t[s.p] = s.c * this.ratio + s.s,
				s = s._next;
				this._onUpdate && (0 > e && this._startAt && this._startTime && this._startAt.render(e, t, n), t || n && 0 === this._time && 0 === u || this._onUpdate.apply(this.vars.onUpdateScope || this, this.vars.onUpdateParams || y)),
				i && (this._gc || (0 > e && this._startAt && !this._onUpdate && this._startTime && this._startAt.render(e, t, n), r && (this._timeline.autoRemoveChildren && this._enabled(!1, !1), this._active = !1), !t && this.vars[i] && this.vars[i].apply(this.vars[i + "Scope"] || this, this.vars[i + "Params"] || y), 0 === a && this._rawPrevTime === f && o !== f && (this._rawPrevTime = 0)))
			}
		},
		i._kill = function (e, t) {
			if ("all" === e && (e = null), null == e && (null == t || t === this.target)) return this._enabled(!1, !1);
			t = "string" != typeof t ? t || this._targets || this.target: O.selector(t) || t;
			var n, r, i, s, o, u, a, f;
			if ((h(t) || M(t)) && "number" != typeof t[0]) for (n = t.length; --n > -1;) this._kill(e, t[n]) && (u = !0);
			else {
				if (this._targets) {
					for (n = this._targets.length; --n > -1;) if (t === this._targets[n]) {
						o = this._propLookup[n] || {},
						this._overwrittenProps = this._overwrittenProps || [],
						r = this._overwrittenProps[n] = e ? this._overwrittenProps[n] || {}: "all";
						break
					}
				} else {
					if (t !== this.target) return ! 1;
					o = this._propLookup,
					r = this._overwrittenProps = e ? this._overwrittenProps || {}: "all"
				}
				if (o) {
					a = e || o,
					f = e !== r && "all" !== r && e !== o && ("object" != typeof e || !e._tempKill);
					for (i in a)(s = o[i]) && (s.pg && s.t._kill(a) && (u = !0), s.pg && 0 !== s.t._overwriteProps.length || (s._prev ? s._prev._next = s._next: s === this._firstPT && (this._firstPT = s._next), s._next && (s._next._prev = s._prev), s._next = s._prev = null), delete o[i]),
					f && (r[i] = 1); ! this._firstPT && this._initted && this._enabled(!1, !1)
				}
			}
			return u
		},
		i.invalidate = function () {
			return this._notifyPluginsOfEnabled && O._onPluginEvent("_onDisable", this),
			this._firstPT = null,
			this._overwrittenProps = null,
			this._onUpdate = null,
			this._startAt = null,
			this._initted = this._active = this._notifyPluginsOfEnabled = !1,
			this._propLookup = this._targets ? {}: [],
			this
		},
		i._enabled = function (e, t) {
			if (o || s.wake(), e && this._gc) {
				var n, r = this._targets;
				if (r) for (n = r.length; --n > -1;) this._siblings[n] = R(r[n], this, !0);
				else this._siblings = R(this.target, this, !0)
			}
			return k.prototype._enabled.call(this, e, t),
			this._notifyPluginsOfEnabled && this._firstPT ? O._onPluginEvent(e ? "_onEnable": "_onDisable", this) : !1
		},
		O.to = function (e, t, n) {
			return new O(e, t, n)
		},
		O.from = function (e, t, n) {
			return n.runBackwards = !0,
			n.immediateRender = 0 != n.immediateRender,
			new O(e, t, n)
		},
		O.fromTo = function (e, t, n, r) {
			return r.startAt = n,
			r.immediateRender = 0 != r.immediateRender && 0 != n.immediateRender,
			new O(e, t, r)
		},
		O.delayedCall = function (e, t, n, r, i) {
			return new O(t, 0, {
				delay: e,
				onComplete: t,
				onCompleteParams: n,
				onCompleteScope: r,
				onReverseComplete: t,
				onReverseCompleteParams: n,
				onReverseCompleteScope: r,
				immediateRender: !1,
				useFrames: i,
				overwrite: 0
			})
		},
		O.set = function (e, t) {
			return new O(e, 0, t)
		},
		O.getTweensOf = function (e, t) {
			if (null == e) return [];
			e = "string" != typeof e ? e: O.selector(e) || e;
			var n, r, i, s;
			if ((h(e) || M(e)) && "number" != typeof e[0]) {
				for (n = e.length, r = []; --n > -1;) r = r.concat(O.getTweensOf(e[n], t));
				for (n = r.length; --n > -1;) for (s = r[n], i = n; --i > -1;) s === r[i] && r.splice(n, 1)
			} else for (r = R(e).concat(), n = r.length; --n > -1;)(r[n]._gc || t && !r[n].isActive()) && r.splice(n, 1);
			return r
		},
		O.killTweensOf = O.killDelayedCallsTo = function (e, t, n) {
			"object" == typeof t && (n = t, t = !1);
			for (var r = O.getTweensOf(e, t), i = r.length; --i > -1;) r[i]._kill(n, e)
		};
		var W = m("plugins.TweenPlugin", function (e, t) {
			this._overwriteProps = (e || "").split(","),
			this._propName = this._overwriteProps[0],
			this._priority = t || 0,
			this._super = W.prototype
		},
		!0);
		if (i = W.prototype, W.version = "1.10.1", W.API = 2, i._firstPT = null, i._addTween = function (e, t, n, r, i, s) {
			var o, u;
			return null != r && (o = "number" == typeof r || "=" !== r.charAt(1) ? Number(r) - n: parseInt(r.charAt(0) + "1", 10) * Number(r.substr(2))) ? (this._firstPT = u = {
				_next: this._firstPT,
				t: e,
				p: t,
				s: n,
				c: o,
				f: "function" == typeof e[t],
				n: i || t,
				r: s
			},
			u._next && (u._next._prev = u), u) : void 0
		},
		i.setRatio = function (e) {
			for (var t, n = this._firstPT, r = 1e-6; n;) t = n.c * e + n.s,
			n.r ? t = 0 | t + (t > 0 ? .5 : -.5) : r > t && t > -r && (t = 0),
			n.f ? n.t[n.p](t) : n.t[n.p] = t,
			n = n._next
		},
		i._kill = function (e) {
			var t, n = this._overwriteProps,
			r = this._firstPT;
			if (null != e[this._propName]) this._overwriteProps = [];
			else for (t = n.length; --t > -1;) null != e[n[t]] && n.splice(t, 1);
			for (; r;) null != e[r.n] && (r._next && (r._next._prev = r._prev), r._prev ? (r._prev._next = r._next, r._prev = null) : this._firstPT === r && (this._firstPT = r._next)),
			r = r._next;
			return ! 1
		},
		i._roundProps = function (e, t) {
			for (var n = this._firstPT; n;)(e[this._propName] || null != n.n && e[n.n.split(this._propName + "_").join("")]) && (n.r = t),
			n = n._next
		},
		O._onPluginEvent = function (e, t) {
			var n, r, i, s, o, u = t._firstPT;
			if ("_onInitAllProps" === e) {
				for (; u;) {
					for (o = u._next, r = i; r && r.pr > u.pr;) r = r._next;
					(u._prev = r ? r._prev: s) ? u._prev._next = u: i = u,
					(u._next = r) ? r._prev = u: s = u,
					u = o
				}
				u = t._firstPT = i
			}
			for (; u;) u.pg && "function" == typeof u.t[e] && u.t[e]() && (n = !0),
			u = u._next;
			return n
		},
		W.activate = function (e) {
			for (var t = e.length; --t > -1;) e[t].API === W.API && (P[(new e[t])._propName] = e[t]);
			return ! 0
		},
		v.plugin = function (e) {
			if (! (e && e.propName && e.init && e.API)) throw "illegal plugin definition.";
			var t, n = e.propName,
			r = e.priority || 0,
			i = e.overwriteProps,
			s = {
				init: "_onInitTween",
				set: "setRatio",
				kill: "_kill",
				round: "_roundProps",
				initAll: "_onInitAllProps"
			},
			o = m("plugins." + n.charAt(0).toUpperCase() + n.substr(1) + "Plugin", function () {
				W.call(this, n, r),
				this._overwriteProps = i || []
			},
			e.global === !0),
			u = o.prototype = new W(n);
			u.constructor = o,
			o.API = e.API;
			for (t in s)"function" == typeof e[t] && (u[s[t]] = e[t]);
			return o.version = e.version,
			W.activate([o]),
			o
		},
		n = e._gsQueue) {
			for (r = 0; n.length > r; r++) n[r]();
			for (i in p) p[i].func || e.console.log("GSAP encountered missing dependency: com.greensock." + i)
		}
		o = !1
	}
})(window);
(window._gsQueue || (window._gsQueue = [])).push(function () {
	"use strict";
	window._gsDefine("TimelineLite", ["core.Animation", "core.SimpleTimeline", "TweenLite"], function (e, t, n) {
		var r = function (e) {
			t.call(this, e),
			this._labels = {},
			this.autoRemoveChildren = this.vars.autoRemoveChildren === !0,
			this.smoothChildTiming = this.vars.smoothChildTiming === !0,
			this._sortChildren = !0,
			this._onUpdate = this.vars.onUpdate;
			var n, r, i = this.vars;
			for (r in i) n = i[r],
			o(n) && -1 !== n.join("").indexOf("{self}") && (i[r] = this._swapSelfInParams(n));
			o(i.tweens) && this.add(i.tweens, 0, i.align, i.stagger)
		},
		i = 1e-10,
		s = n._internals.isSelector,
		o = n._internals.isArray,
		u = [],
		a = function (e) {
			var t, n = {};
			for (t in e) n[t] = e[t];
			return n
		},
		f = function (e, t, n, r) {
			e._timeline.pause(e._startTime),
			t && t.apply(r || e._timeline, n || u)
		},
		l = u.slice,
		c = r.prototype = new t;
		return r.version = "1.11.0",
		c.constructor = r,
		c.kill()._gc = !1,
		c.to = function (e, t, r, i) {
			return t ? this.add(new n(e, t, r), i) : this.set(e, r, i)
		},
		c.from = function (e, t, r, i) {
			return this.add(n.from(e, t, r), i)
		},
		c.fromTo = function (e, t, r, i, s) {
			return t ? this.add(n.fromTo(e, t, r, i), s) : this.set(e, i, s)
		},
		c.staggerTo = function (e, t, i, o, u, f, c, p) {
			var d, v = new r({
				onComplete: f,
				onCompleteParams: c,
				onCompleteScope: p
			});
			for ("string" == typeof e && (e = n.selector(e) || e), s(e) && (e = l.call(e, 0)), o = o || 0, d = 0; e.length > d; d++) i.startAt && (i.startAt = a(i.startAt)),
			v.to(e[d], t, a(i), d * o);
			return this.add(v, u)
		},
		c.staggerFrom = function (e, t, n, r, i, s, o, u) {
			return n.immediateRender = 0 != n.immediateRender,
			n.runBackwards = !0,
			this.staggerTo(e, t, n, r, i, s, o, u)
		},
		c.staggerFromTo = function (e, t, n, r, i, s, o, u, a) {
			return r.startAt = n,
			r.immediateRender = 0 != r.immediateRender && 0 != n.immediateRender,
			this.staggerTo(e, t, r, i, s, o, u, a)
		},
		c.call = function (e, t, r, i) {
			return this.add(n.delayedCall(0, e, t, r), i)
		},
		c.set = function (e, t, r) {
			return r = this._parseTimeOrLabel(r, 0, !0),
			null == t.immediateRender && (t.immediateRender = r === this._time && !this._paused),
			this.add(new n(e, 0, t), r)
		},
		r.exportRoot = function (e, t) {
			e = e || {},
			null == e.smoothChildTiming && (e.smoothChildTiming = !0);
			var i, s, o = new r(e),
			u = o._timeline;
			for (null == t && (t = !0), u._remove(o, !0), o._startTime = 0, o._rawPrevTime = o._time = o._totalTime = u._time, i = u._first; i;) s = i._next,
			t && i instanceof n && i.target === i.vars.onComplete || o.add(i, i._startTime - i._delay),
			i = s;
			return u.add(o, 0),
			o
		},
		c.add = function (i, s, u, a) {
			var f, l, c, h, p, d;
			if ("number" != typeof s && (s = this._parseTimeOrLabel(s, 0, !0, i)), !(i instanceof e)) {
				if (i instanceof Array || i && i.push && o(i)) {
					for (u = u || "normal", a = a || 0, f = s, l = i.length, c = 0; l > c; c++) o(h = i[c]) && (h = new r({
						tweens: h
					})),
					this.add(h, f),
					"string" != typeof h && "function" != typeof h && ("sequence" === u ? f = h._startTime + h.totalDuration() / h._timeScale: "start" === u && (h._startTime -= h.delay())),
					f += a;
					return this._uncache(!0)
				}
				if ("string" == typeof i) return this.addLabel(i, s);
				if ("function" != typeof i) throw "Cannot add " + i + " into the timeline; it is not a tween, timeline, function, or string.";
				i = n.delayedCall(0, i)
			}
			if (t.prototype.add.call(this, i, s), this._gc && !this._paused && this._duration < this.duration()) for (p = this, d = p.rawTime() > i._startTime; p._gc && p._timeline;) p._timeline.smoothChildTiming && d ? p.totalTime(p._totalTime, !0) : p._enabled(!0, !1),
			p = p._timeline;
			return this
		},
		c.remove = function (t) {
			if (t instanceof e) return this._remove(t, !1);
			if (t instanceof Array || t && t.push && o(t)) {
				for (var n = t.length; --n > -1;) this.remove(t[n]);
				return this
			}
			return "string" == typeof t ? this.removeLabel(t) : this.kill(null, t)
		},
		c._remove = function (e, n) {
			t.prototype._remove.call(this, e, n);
			var r = this._last;
			return r ? this._time > r._startTime + r._totalDuration / r._timeScale && (this._time = this.duration(), this._totalTime = this._totalDuration) : this._time = this._totalTime = 0,
			this
		},
		c.append = function (e, t) {
			return this.add(e, this._parseTimeOrLabel(null, t, !0, e))
		},
		c.insert = c.insertMultiple = function (e, t, n, r) {
			return this.add(e, t || 0, n, r)
		},
		c.appendMultiple = function (e, t, n, r) {
			return this.add(e, this._parseTimeOrLabel(null, t, !0, e), n, r)
		},
		c.addLabel = function (e, t) {
			return this._labels[e] = this._parseTimeOrLabel(t),
			this
		},
		c.addPause = function (e, t, n, r) {
			return this.call(f, ["{self}", t, n, r], this, e)
		},
		c.removeLabel = function (e) {
			return delete this._labels[e],
			this
		},
		c.getLabelTime = function (e) {
			return null != this._labels[e] ? this._labels[e] : -1
		},
		c._parseTimeOrLabel = function (t, n, r, i) {
			var s;
			if (i instanceof e && i.timeline === this) this.remove(i);
			else if (i && (i instanceof Array || i.push && o(i))) for (s = i.length; --s > -1;) i[s] instanceof e && i[s].timeline === this && this.remove(i[s]);
			if ("string" == typeof n) return this._parseTimeOrLabel(n, r && "number" == typeof t && null == this._labels[n] ? t - this.duration() : 0, r);
			if (n = n || 0, "string" != typeof t || !isNaN(t) && null == this._labels[t]) null == t && (t = this.duration());
			else {
				if (s = t.indexOf("="), -1 === s) return null == this._labels[t] ? r ? this._labels[t] = this.duration() + n: n: this._labels[t] + n;
				n = parseInt(t.charAt(s - 1) + "1", 10) * Number(t.substr(s + 1)),
				t = s > 1 ? this._parseTimeOrLabel(t.substr(0, s - 1), 0, r) : this.duration()
			}
			return Number(t) + n
		},
		c.seek = function (e, t) {
			return this.totalTime("number" == typeof e ? e: this._parseTimeOrLabel(e), t !== !1)
		},
		c.stop = function () {
			return this.paused(!0)
		},
		c.gotoAndPlay = function (e, t) {
			return this.play(e, t)
		},
		c.gotoAndStop = function (e, t) {
			return this.pause(e, t)
		},
		c.render = function (e, t, n) {
			this._gc && this._enabled(!0, !1);
			var r, s, o, a, f, l = this._dirty ? this.totalDuration() : this._totalDuration,
			c = this._time,
			h = this._startTime,
			p = this._timeScale,
			d = this._paused;
			if (e >= l ? (this._totalTime = this._time = l, this._reversed || this._hasPausedChild() || (s = !0, a = "onComplete", 0 === this._duration && (0 === e || 0 > this._rawPrevTime || this._rawPrevTime === i) && this._rawPrevTime !== e && this._first && (f = !0, this._rawPrevTime > i && (a = "onReverseComplete"))), this._rawPrevTime = this._duration || !t || e ? e: i, e = l + 1e-6) : 1e-7 > e ? (this._totalTime = this._time = 0, (0 !== c || 0 === this._duration && (this._rawPrevTime > i || 0 > e && this._rawPrevTime >= 0)) && (a = "onReverseComplete", s = this._reversed), 0 > e ? (this._active = !1, 0 === this._duration && this._rawPrevTime >= 0 && this._first && (f = !0), this._rawPrevTime = e) : (this._rawPrevTime = this._duration || !t || e ? e: i, e = 0, this._initted || (f = !0))) : this._totalTime = this._time = this._rawPrevTime = e, this._time !== c && this._first || n || f) {
				if (this._initted || (this._initted = !0), this._active || !this._paused && this._time !== c && e > 0 && (this._active = !0), 0 === c && this.vars.onStart && 0 !== this._time && (t || this.vars.onStart.apply(this.vars.onStartScope || this, this.vars.onStartParams || u)), this._time >= c) for (r = this._first; r && (o = r._next, !this._paused || d);)(r._active || r._startTime <= this._time && !r._paused && !r._gc) && (r._reversed ? r.render((r._dirty ? r.totalDuration() : r._totalDuration) - (e - r._startTime) * r._timeScale, t, n) : r.render((e - r._startTime) * r._timeScale, t, n)),
				r = o;
				else for (r = this._last; r && (o = r._prev, !this._paused || d);)(r._active || c >= r._startTime && !r._paused && !r._gc) && (r._reversed ? r.render((r._dirty ? r.totalDuration() : r._totalDuration) - (e - r._startTime) * r._timeScale, t, n) : r.render((e - r._startTime) * r._timeScale, t, n)),
				r = o;
				this._onUpdate && (t || this._onUpdate.apply(this.vars.onUpdateScope || this, this.vars.onUpdateParams || u)),
				a && (this._gc || (h === this._startTime || p !== this._timeScale) && (0 === this._time || l >= this.totalDuration()) && (s && (this._timeline.autoRemoveChildren && this._enabled(!1, !1), this._active = !1), !t && this.vars[a] && this.vars[a].apply(this.vars[a + "Scope"] || this, this.vars[a + "Params"] || u)))
			}
		},
		c._hasPausedChild = function () {
			for (var e = this._first; e;) {
				if (e._paused || e instanceof r && e._hasPausedChild()) return ! 0;
				e = e._next
			}
			return ! 1
		},
		c.getChildren = function (e, t, r, i) {
			i = i || -9999999999;
			for (var s = [], o = this._first, u = 0; o;) i > o._startTime || (o instanceof n ? t !== !1 && (s[u++] = o) : (r !== !1 && (s[u++] = o), e !== !1 && (s = s.concat(o.getChildren(!0, t, r)), u = s.length))),
			o = o._next;
			return s
		},
		c.getTweensOf = function (e, t) {
			for (var r = n.getTweensOf(e), i = r.length, s = [], o = 0; --i > -1;)(r[i].timeline === this || t && this._contains(r[i])) && (s[o++] = r[i]);
			return s
		},
		c._contains = function (e) {
			for (var t = e.timeline; t;) {
				if (t === this) return ! 0;
				t = t.timeline
			}
			return ! 1
		},
		c.shiftChildren = function (e, t, n) {
			n = n || 0;
			for (var r, i = this._first, s = this._labels; i;) i._startTime >= n && (i._startTime += e),
			i = i._next;
			if (t) for (r in s) s[r] >= n && (s[r] += e);
			return this._uncache(!0)
		},
		c._kill = function (e, t) {
			if (!e && !t) return this._enabled(!1, !1);
			for (var n = t ? this.getTweensOf(t) : this.getChildren(!0, !0, !1), r = n.length, i = !1; --r > -1;) n[r]._kill(e, t) && (i = !0);
			return i
		},
		c.clear = function (e) {
			var t = this.getChildren(!1, !0, !0),
			n = t.length;
			for (this._time = this._totalTime = 0; --n > -1;) t[n]._enabled(!1, !1);
			return e !== !1 && (this._labels = {}),
			this._uncache(!0)
		},
		c.invalidate = function () {
			for (var e = this._first; e;) e.invalidate(),
			e = e._next;
			return this
		},
		c._enabled = function (e, n) {
			if (e === this._gc) for (var r = this._first; r;) r._enabled(e, !0),
			r = r._next;
			return t.prototype._enabled.call(this, e, n)
		},
		c.duration = function (e) {
			return arguments.length ? (0 !== this.duration() && 0 !== e && this.timeScale(this._duration / e), this) : (this._dirty && this.totalDuration(), this._duration)
		},
		c.totalDuration = function (e) {
			if (!arguments.length) {
				if (this._dirty) {
					for (var t, n, r = 0, i = this._last, s = 999999999999; i;) t = i._prev,
					i._dirty && i.totalDuration(),
					i._startTime > s && this._sortChildren && !i._paused ? this.add(i, i._startTime - i._delay) : s = i._startTime,
					0 > i._startTime && !i._paused && (r -= i._startTime, this._timeline.smoothChildTiming && (this._startTime += i._startTime / this._timeScale), this.shiftChildren( - i._startTime, !1, -9999999999), s = 0),
					n = i._startTime + i._totalDuration / i._timeScale,
					n > r && (r = n),
					i = t;
					this._duration = this._totalDuration = r,
					this._dirty = !1
				}
				return this._totalDuration
			}
			return 0 !== this.totalDuration() && 0 !== e && this.timeScale(this._totalDuration / e),
			this
		},
		c.usesFrames = function () {
			for (var t = this._timeline; t._timeline;) t = t._timeline;
			return t === e._rootFramesTimeline
		},
		c.rawTime = function () {
			return this._paused ? this._totalTime: (this._timeline.rawTime() - this._startTime) * this._timeScale
		},
		r
	},
	!0)
}),
window._gsDefine && window._gsQueue.pop()();
(window._gsQueue || (window._gsQueue = [])).push(function () {
	"use strict";
	window._gsDefine("easing.Back", ["easing.Ease"], function (e) {
		var t, n, r, i = window.GreenSockGlobals || window,
		s = i.com.greensock,
		o = 2 * Math.PI,
		u = Math.PI / 2,
		a = s._class,
		f = function (t, n) {
			var r = a("easing." + t, function () {},
			!0),
			i = r.prototype = new e;
			return i.constructor = r,
			i.getRatio = n,
			r
		},
		l = e.register ||
		function () {},
		c = function (e, t, n, r) {
			var i = a("easing." + e, {
				easeOut: new t,
				easeIn: new n,
				easeInOut: new r
			},
			!0);
			return l(i, e),
			i
		},
		h = function (e, t, n) {
			this.t = e,
			this.v = t,
			n && (this.next = n, n.prev = this, this.c = n.v - t, this.gap = n.t - e)
		},
		p = function (t, n) {
			var r = a("easing." + t, function (e) {
				this._p1 = e || 0 === e ? e: 1.70158,
				this._p2 = 1.525 * this._p1
			},
			!0),
			i = r.prototype = new e;
			return i.constructor = r,
			i.getRatio = n,
			i.config = function (e) {
				return new r(e)
			},
			r
		},
		d = c("Back", p("BackOut", function (e) {
			return (e -= 1) * e * ((this._p1 + 1) * e + this._p1) + 1
		}), p("BackIn", function (e) {
			return e * e * ((this._p1 + 1) * e - this._p1)
		}), p("BackInOut", function (e) {
			return 1 > (e *= 2) ? .5 * e * e * ((this._p2 + 1) * e - this._p2) : .5 * ((e -= 2) * e * ((this._p2 + 1) * e + this._p2) + 2)
		})),
		v = a("easing.SlowMo", function (e, t, n) {
			t = t || 0 === t ? t: .7,
			null == e ? e = .7 : e > 1 && (e = 1),
			this._p = 1 !== e ? t: 0,
			this._p1 = (1 - e) / 2,
			this._p2 = e,
			this._p3 = this._p1 + this._p2,
			this._calcEnd = n === !0
		},
		!0),
		m = v.prototype = new e;
		return m.constructor = v,
		m.getRatio = function (e) {
			var t = e + (.5 - e) * this._p;
			return this._p1 > e ? this._calcEnd ? 1 - (e = 1 - e / this._p1) * e: t - (e = 1 - e / this._p1) * e * e * e * t: e > this._p3 ? this._calcEnd ? 1 - (e = (e - this._p3) / this._p1) * e: t + (e - t) * (e = (e - this._p3) / this._p1) * e * e * e: this._calcEnd ? 1 : t
		},
		v.ease = new v(.7, .7),
		m.config = v.config = function (e, t, n) {
			return new v(e, t, n)
		},
		t = a("easing.SteppedEase", function (e) {
			e = e || 1,
			this._p1 = 1 / e,
			this._p2 = e + 1
		},
		!0),
		m = t.prototype = new e,
		m.constructor = t,
		m.getRatio = function (e) {
			return 0 > e ? e = 0 : e >= 1 && (e = .999999999),
			(this._p2 * e >> 0) * this._p1
		},
		m.config = t.config = function (e) {
			return new t(e)
		},
		n = a("easing.RoughEase", function (t) {
			t = t || {};
			for (var n, r, i, s, o, u, a = t.taper || "none", f = [], l = 0, c = 0 | (t.points || 20), p = c, d = t.randomize !== !1, v = t.clamp === !0, m = t.template instanceof e ? t.template: null, g = "number" == typeof t.strength ? .4 * t.strength: .4; --p > -1;) n = d ? Math.random() : 1 / c * p,
			r = m ? m.getRatio(n) : n,
			"none" === a ? i = g: "out" === a ? (s = 1 - n, i = s * s * g) : "in" === a ? i = n * n * g: .5 > n ? (s = 2 * n, i = .5 * s * s * g) : (s = 2 * (1 - n), i = .5 * s * s * g),
			d ? r += Math.random() * i - .5 * i: p % 2 ? r += .5 * i: r -= .5 * i,
			v && (r > 1 ? r = 1 : 0 > r && (r = 0)),
			f[l++] = {
				x: n,
				y: r
			};
			for (f.sort(function (e, t) {
				return e.x - t.x
			}), u = new h(1, 1, null), p = c; --p > -1;) o = f[p],
			u = new h(o.x, o.y, u);
			this._prev = new h(0, 0, 0 !== u.t ? u: u.next)
		},
		!0),
		m = n.prototype = new e,
		m.constructor = n,
		m.getRatio = function (e) {
			var t = this._prev;
			if (e > t.t) {
				for (; t.next && e >= t.t;) t = t.next;
				t = t.prev
			} else for (; t.prev && t.t >= e;) t = t.prev;
			return this._prev = t,
			t.v + (e - t.t) / t.gap * t.c
		},
		m.config = function (e) {
			return new n(e)
		},
		n.ease = new n,
		c("Bounce", f("BounceOut", function (e) {
			return 1 / 2.75 > e ? 7.5625 * e * e: 2 / 2.75 > e ? 7.5625 * (e -= 1.5 / 2.75) * e + .75 : 2.5 / 2.75 > e ? 7.5625 * (e -= 2.25 / 2.75) * e + .9375 : 7.5625 * (e -= 2.625 / 2.75) * e + .984375
		}), f("BounceIn", function (e) {
			return 1 / 2.75 > (e = 1 - e) ? 1 - 7.5625 * e * e: 2 / 2.75 > e ? 1 - (7.5625 * (e -= 1.5 / 2.75) * e + .75) : 2.5 / 2.75 > e ? 1 - (7.5625 * (e -= 2.25 / 2.75) * e + .9375) : 1 - (7.5625 * (e -= 2.625 / 2.75) * e + .984375)
		}), f("BounceInOut", function (e) {
			var t = .5 > e;
			return e = t ? 1 - 2 * e: 2 * e - 1,
			e = 1 / 2.75 > e ? 7.5625 * e * e: 2 / 2.75 > e ? 7.5625 * (e -= 1.5 / 2.75) * e + .75 : 2.5 / 2.75 > e ? 7.5625 * (e -= 2.25 / 2.75) * e + .9375 : 7.5625 * (e -= 2.625 / 2.75) * e + .984375,
			t ? .5 * (1 - e) : .5 * e + .5
		})),
		c("Circ", f("CircOut", function (e) {
			return Math.sqrt(1 - (e -= 1) * e)
		}), f("CircIn", function (e) {
			return - (Math.sqrt(1 - e * e) - 1)
		}), f("CircInOut", function (e) {
			return 1 > (e *= 2) ? -.5 * (Math.sqrt(1 - e * e) - 1) : .5 * (Math.sqrt(1 - (e -= 2) * e) + 1)
		})),
		r = function (t, n, r) {
			var i = a("easing." + t, function (e, t) {
				this._p1 = e || 1,
				this._p2 = t || r,
				this._p3 = this._p2 / o * (Math.asin(1 / this._p1) || 0)
			},
			!0),
			s = i.prototype = new e;
			return s.constructor = i,
			s.getRatio = n,
			s.config = function (e, t) {
				return new i(e, t)
			},
			i
		},
		c("Elastic", r("ElasticOut", function (e) {
			return this._p1 * Math.pow(2, -10 * e) * Math.sin((e - this._p3) * o / this._p2) + 1
		},
		.3), r("ElasticIn", function (e) {
			return - (this._p1 * Math.pow(2, 10 * (e -= 1)) * Math.sin((e - this._p3) * o / this._p2))
		},
		.3), r("ElasticInOut", function (e) {
			return 1 > (e *= 2) ? -.5 * this._p1 * Math.pow(2, 10 * (e -= 1)) * Math.sin((e - this._p3) * o / this._p2) : .5 * this._p1 * Math.pow(2, -10 * (e -= 1)) * Math.sin((e - this._p3) * o / this._p2) + 1
		},
		.45)),
		c("Expo", f("ExpoOut", function (e) {
			return 1 - Math.pow(2, -10 * e)
		}), f("ExpoIn", function (e) {
			return Math.pow(2, 10 * (e - 1)) - .001
		}), f("ExpoInOut", function (e) {
			return 1 > (e *= 2) ? .5 * Math.pow(2, 10 * (e - 1)) : .5 * (2 - Math.pow(2, -10 * (e - 1)))
		})),
		c("Sine", f("SineOut", function (e) {
			return Math.sin(e * u)
		}), f("SineIn", function (e) {
			return - Math.cos(e * u) + 1
		}), f("SineInOut", function (e) {
			return - .5 * (Math.cos(Math.PI * e) - 1)
		})),
		a("easing.EaseLookup", {
			find: function (t) {
				return e.map[t]
			}
		},
		!0),
		l(i.SlowMo, "SlowMo", "ease,"),
		l(n, "RoughEase", "ease,"),
		l(t, "SteppedEase", "ease,"),
		d
	},
	!0)
}),
window._gsDefine && window._gsQueue.pop()();
(window._gsQueue || (window._gsQueue = [])).push(function () {
	"use strict";
	window._gsDefine("plugins.CSSPlugin", ["plugins.TweenPlugin", "TweenLite"], function (e, t) {
		var n, r, i, s, o = function () {
			e.call(this, "css"),
			this._overwriteProps.length = 0,
			this.setRatio = o.prototype.setRatio
		},
		u = {},
		a = o.prototype = new e("css");
		a.constructor = o,
		o.version = "1.11.2",
		o.API = 2,
		o.defaultTransformPerspective = 0,
		a = "px",
		o.suffixMap = {
			top: a,
			right: a,
			bottom: a,
			left: a,
			width: a,
			height: a,
			fontSize: a,
			padding: a,
			margin: a,
			perspective: a
		};
		var f, l, c, h, p, d, v = /(?:\d|\-\d|\.\d|\-\.\d)+/g,
		m = /(?:\d|\-\d|\.\d|\-\.\d|\+=\d|\-=\d|\+=.\d|\-=\.\d)+/g,
		g = /(?:\+=|\-=|\-|\b)[\d\-\.]+[a-zA-Z0-9]*(?:%|\b)/gi,
		y = /[^\d\-\.]/g,
		b = /(?:\d|\-|\+|=|#|\.)*/g,
		w = /opacity *= *([^)]*)/,
		E = /opacity:([^;]*)/,
		S = /alpha\(opacity *=.+?\)/i,
		x = /^(rgb|hsl)/,
		T = /([A-Z])/g,
		N = /-([a-z])/gi,
		C = /(^(?:url\(\"|url\())|(?:(\"\))$|\)$)/gi,
		k = function (e, t) {
			return t.toUpperCase()
		},
		L = /(?:Left|Right|Width)/i,
		A = /(M11|M12|M21|M22)=[\d\-\.e]+/gi,
		O = /progid\:DXImageTransform\.Microsoft\.Matrix\(.+?\)/i,
		M = /,(?=[^\)]*(?:\(|$))/gi,
		_ = Math.PI / 180,
		D = 180 / Math.PI,
		P = {},
		H = document,
		B = H.createElement("div"),
		j = H.createElement("img"),
		F = o._internals = {
			_specialProps: u
		},
		I = navigator.userAgent,
		q = function () {
			var e, t = I.indexOf("Android"),
			n = H.createElement("div");
			return c = -1 !== I.indexOf("Safari") && -1 === I.indexOf("Chrome") && ( - 1 === t || Number(I.substr(t + 8, 1)) > 3),
			p = c && 6 > Number(I.substr(I.indexOf("Version/") + 8, 1)),
			h = -1 !== I.indexOf("Firefox"),
			/MSIE ([0-9]{1,}[\.0-9]{0,})/.exec(I) && (d = parseFloat(RegExp.$1)),
			n.innerHTML = "<a style='top:1px;opacity:.55;'>a</a>",
			e = n.getElementsByTagName("a")[0],
			e ? /^0.55/.test(e.style.opacity) : !1
		} (),
		R = function (e) {
			return w.test("string" == typeof e ? e: (e.currentStyle ? e.currentStyle.filter: e.style.filter) || "") ? parseFloat(RegExp.$1) / 100 : 1
		},
		U = function (e) {
			window.console && console.log(e)
		},
		z = "",
		W = "",
		X = function (e, t) {
			t = t || B;
			var n, r, i = t.style;
			if (void 0 !== i[e]) return e;
			for (e = e.charAt(0).toUpperCase() + e.substr(1), n = ["O", "Moz", "ms", "Ms", "Webkit"], r = 5; --r > -1 && void 0 === i[n[r] + e];);
			return r >= 0 ? (W = 3 === r ? "ms": n[r], z = "-" + W.toLowerCase() + "-", W + e) : null
		},
		V = H.defaultView ? H.defaultView.getComputedStyle: function () {},
		$ = o.getStyle = function (e, t, n, r, i) {
			var s;
			return q || "opacity" !== t ? (!r && e.style[t] ? s = e.style[t] : (n = n || V(e, null)) ? (e = n.getPropertyValue(t.replace(T, "-$1").toLowerCase()), s = e || n.length ? e: n[t]) : e.currentStyle && (s = e.currentStyle[t]), null == i || s && "none" !== s && "auto" !== s && "auto auto" !== s ? s: i) : R(e)
		},
		J = function (e, t, n, r, i) {
			if ("px" === r || !r) return n;
			if ("auto" === r || !n) return 0;
			var s, o = L.test(t),
			u = e,
			a = B.style,
			f = 0 > n;
			return f && (n = -n),
			"%" === r && -1 !== t.indexOf("border") ? s = n / 100 * (o ? e.clientWidth: e.clientHeight) : (a.cssText = "border:0 solid red;position:" + $(e, "position") + ";line-height:0;", "%" !== r && u.appendChild ? a[o ? "borderLeftWidth": "borderTopWidth"] = n + r: (u = e.parentNode || H.body, a[o ? "width": "height"] = n + r), u.appendChild(B), s = parseFloat(B[o ? "offsetWidth": "offsetHeight"]), u.removeChild(B), 0 !== s || i || (s = J(e, t, n, r, !0))),
			f ? -s: s
		},
		K = function (e, t, n) {
			if ("absolute" !== $(e, "position", n)) return 0;
			var r = "left" === t ? "Left": "Top",
			i = $(e, "margin" + r, n);
			return e["offset" + r] - (J(e, t, parseFloat(i), i.replace(b, "")) || 0)
		},
		Q = function (e, t) {
			var n, r, i = {};
			if (t = t || V(e, null)) if (n = t.length) for (; --n > -1;) i[t[n].replace(N, k)] = t.getPropertyValue(t[n]);
			else for (n in t) i[n] = t[n];
			else if (t = e.currentStyle || e.style) for (n in t)"string" == typeof n && void 0 !== i[n] && (i[n.replace(N, k)] = t[n]);
			return q || (i.opacity = R(e)),
			r = xt(e, t, !1),
			i.rotation = r.rotation,
			i.skewX = r.skewX,
			i.scaleX = r.scaleX,
			i.scaleY = r.scaleY,
			i.x = r.x,
			i.y = r.y,
			St && (i.z = r.z, i.rotationX = r.rotationX, i.rotationY = r.rotationY, i.scaleZ = r.scaleZ),
			i.filters && delete i.filters,
			i
		},
		G = function (e, t, n, r, i) {
			var s, o, u, a = {},
			f = e.style;
			for (o in n)"cssText" !== o && "length" !== o && isNaN(o) && (t[o] !== (s = n[o]) || i && i[o]) && -1 === o.indexOf("Origin") && ("number" == typeof s || "string" == typeof s) && (a[o] = "auto" !== s || "left" !== o && "top" !== o ? "" !== s && "auto" !== s && "none" !== s || "string" != typeof t[o] || "" === t[o].replace(y, "") ? s: 0 : K(e, o), void 0 !== f[o] && (u = new ct(f, o, f[o], u)));
			if (r) for (o in r)"className" !== o && (a[o] = r[o]);
			return {
				difs: a,
				firstMPT: u
			}
		},
		Y = {
			width: ["Left", "Right"],
			height: ["Top", "Bottom"]
		},
		Z = ["marginLeft", "marginRight", "marginTop", "marginBottom"],
		et = function (e, t, n) {
			var r = parseFloat("width" === t ? e.offsetWidth: e.offsetHeight),
			i = Y[t],
			s = i.length;
			for (n = n || V(e, null); --s > -1;) r -= parseFloat($(e, "padding" + i[s], n, !0)) || 0,
			r -= parseFloat($(e, "border" + i[s] + "Width", n, !0)) || 0;
			return r
		},
		tt = function (e, t) { (null == e || "" === e || "auto" === e || "auto auto" === e) && (e = "0 0");
			var n = e.split(" "),
			r = -1 !== e.indexOf("left") ? "0%": -1 !== e.indexOf("right") ? "100%": n[0],
			i = -1 !== e.indexOf("top") ? "0%": -1 !== e.indexOf("bottom") ? "100%": n[1];
			return null == i ? i = "0": "center" === i && (i = "50%"),
			("center" === r || isNaN(parseFloat(r)) && -1 === (r + "").indexOf("=")) && (r = "50%"),
			t && (t.oxp = -1 !== r.indexOf("%"), t.oyp = -1 !== i.indexOf("%"), t.oxr = "=" === r.charAt(1), t.oyr = "=" === i.charAt(1), t.ox = parseFloat(r.replace(y, "")), t.oy = parseFloat(i.replace(y, ""))),
			r + " " + i + (n.length > 2 ? " " + n[2] : "")
		},
		nt = function (e, t) {
			return "string" == typeof e && "=" === e.charAt(1) ? parseInt(e.charAt(0) + "1", 10) * parseFloat(e.substr(2)) : parseFloat(e) - parseFloat(t)
		},
		rt = function (e, t) {
			return null == e ? t: "string" == typeof e && "=" === e.charAt(1) ? parseInt(e.charAt(0) + "1", 10) * Number(e.substr(2)) + t: parseFloat(e)
		},
		it = function (e, t, n, r) {
			var i, s, o, u, a = 1e-6;
			return null == e ? u = t: "number" == typeof e ? u = e: (i = 360, s = e.split("_"), o = Number(s[0].replace(y, "")) * ( - 1 === e.indexOf("rad") ? 1 : D) - ("=" === e.charAt(1) ? 0 : t), s.length && (r && (r[n] = t + o), -1 !== e.indexOf("short") && (o %= i, o !== o % (i / 2) && (o = 0 > o ? o + i: o - i)), -1 !== e.indexOf("_cw") && 0 > o ? o = (o + 9999999999 * i) % i - (0 | o / i) * i: -1 !== e.indexOf("ccw") && o > 0 && (o = (o - 9999999999 * i) % i - (0 | o / i) * i)), u = t + o),
			a > u && u > -a && (u = 0),
			u
		},
		st = {
			aqua: [0, 255, 255],
			lime: [0, 255, 0],
			silver: [192, 192, 192],
			black: [0, 0, 0],
			maroon: [128, 0, 0],
			teal: [0, 128, 128],
			blue: [0, 0, 255],
			navy: [0, 0, 128],
			white: [255, 255, 255],
			fuchsia: [255, 0, 255],
			olive: [128, 128, 0],
			yellow: [255, 255, 0],
			orange: [255, 165, 0],
			gray: [128, 128, 128],
			purple: [128, 0, 128],
			green: [0, 128, 0],
			red: [255, 0, 0],
			pink: [255, 192, 203],
			cyan: [0, 255, 255],
			transparent: [255, 255, 255, 0]
		},
		ot = function (e, t, n) {
			return e = 0 > e ? e + 1 : e > 1 ? e - 1 : e,
			0 | 255 * (1 > 6 * e ? t + 6 * (n - t) * e: .5 > e ? n: 2 > 3 * e ? t + 6 * (n - t) * (2 / 3 - e) : t) + .5
		},
		ut = function (e) {
			var t, n, r, i, s, o;
			return e && "" !== e ? "number" == typeof e ? [e >> 16, 255 & e >> 8, 255 & e] : ("," === e.charAt(e.length - 1) && (e = e.substr(0, e.length - 1)), st[e] ? st[e] : "#" === e.charAt(0) ? (4 === e.length && (t = e.charAt(1), n = e.charAt(2), r = e.charAt(3), e = "#" + t + t + n + n + r + r), e = parseInt(e.substr(1), 16), [e >> 16, 255 & e >> 8, 255 & e]) : "hsl" === e.substr(0, 3) ? (e = e.match(v), i = Number(e[0]) % 360 / 360, s = Number(e[1]) / 100, o = Number(e[2]) / 100, n = .5 >= o ? o * (s + 1) : o + s - o * s, t = 2 * o - n, e.length > 3 && (e[3] = Number(e[3])), e[0] = ot(i + 1 / 3, t, n), e[1] = ot(i, t, n), e[2] = ot(i - 1 / 3, t, n), e) : (e = e.match(v) || st.transparent, e[0] = Number(e[0]), e[1] = Number(e[1]), e[2] = Number(e[2]), e.length > 3 && (e[3] = Number(e[3])), e)) : st.black
		},
		at = "(?:\\b(?:(?:rgb|rgba|hsl|hsla)\\(.+?\\))|\\B#.+?\\b";
		for (a in st) at += "|" + a + "\\b";
		at = RegExp(at + ")", "gi");
		var ft = function (e, t, n, r) {
			if (null == e) return function (e) {
				return e
			};
			var i, s = t ? (e.match(at) || [""])[0] : "",
			o = e.split(s).join("").match(g) || [],
			u = e.substr(0, e.indexOf(o[0])),
			a = ")" === e.charAt(e.length - 1) ? ")": "",
			f = -1 !== e.indexOf(" ") ? " ": ",",
			l = o.length,
			c = l > 0 ? o[0].replace(v, "") : "";
			return l ? i = t ?
			function (e) {
				var t, h, p, d;
				if ("number" == typeof e) e += c;
				else if (r && M.test(e)) {
					for (d = e.replace(M, "|").split("|"), p = 0; d.length > p; p++) d[p] = i(d[p]);
					return d.join(",")
				}
				if (t = (e.match(at) || [s])[0], h = e.split(t).join("").match(g) || [], p = h.length, l > p--) for (; l > ++p;) h[p] = n ? h[0 | (p - 1) / 2] : o[p];
				return u + h.join(f) + f + t + a + ( - 1 !== e.indexOf("inset") ? " inset": "")
			}: function (e) {
				var t, s, h;
				if ("number" == typeof e) e += c;
				else if (r && M.test(e)) {
					for (s = e.replace(M, "|").split("|"), h = 0; s.length > h; h++) s[h] = i(s[h]);
					return s.join(",")
				}
				if (t = e.match(g) || [], h = t.length, l > h--) for (; l > ++h;) t[h] = n ? t[0 | (h - 1) / 2] : o[h];
				return u + t.join(f) + a
			}: function (e) {
				return e
			}
		},
		lt = function (e) {
			return e = e.split(","),
			function (t, n, r, i, s, o, u) {
				var a, f = (n + "").split(" ");
				for (u = {},
				a = 0; 4 > a; a++) u[e[a]] = f[a] = f[a] || f[(a - 1) / 2 >> 0];
				return i.parse(t, u, s, o)
			}
		},
		ct = (F._setPluginRatio = function (e) {
			this.plugin.setRatio(e);
			for (var t, n, r, i, s = this.data, o = s.proxy, u = s.firstMPT, a = 1e-6; u;) t = o[u.v],
			u.r ? t = t > 0 ? 0 | t + .5 : 0 | t - .5 : a > t && t > -a && (t = 0),
			u.t[u.p] = t,
			u = u._next;
			if (s.autoRotate && (s.autoRotate.rotation = o.rotation), 1 === e) for (u = s.firstMPT; u;) {
				if (n = u.t, n.type) {
					if (1 === n.type) {
						for (i = n.xs0 + n.s + n.xs1, r = 1; n.l > r; r++) i += n["xn" + r] + n["xs" + (r + 1)];
						n.e = i
					}
				} else n.e = n.s + n.xs0;
				u = u._next
			}
		},
		function (e, t, n, r, i) {
			this.t = e,
			this.p = t,
			this.v = n,
			this.r = i,
			r && (r._prev = this, this._next = r)
		}),
		ht = (F._parseToProxy = function (e, t, n, r, i, s) {
			var o, u, a, f, l, c = r,
			h = {},
			p = {},
			d = n._transform,
			v = P;
			for (n._transform = null, P = t, r = l = n.parse(e, t, r, i), P = v, s && (n._transform = d, c && (c._prev = null, c._prev && (c._prev._next = null))); r && r !== c;) {
				if (1 >= r.type && (u = r.p, p[u] = r.s + r.c, h[u] = r.s, s || (f = new ct(r, "s", u, f, r.r), r.c = 0), 1 === r.type)) for (o = r.l; --o > 0;) a = "xn" + o,
				u = r.p + "_" + a,
				p[u] = r.data[a],
				h[u] = r[a],
				s || (f = new ct(r, a, u, f, r.rxp[a]));
				r = r._next
			}
			return {
				proxy: h,
				end: p,
				firstMPT: f,
				pt: l
			}
		},
		F.CSSPropTween = function (e, t, r, i, o, u, a, f, l, c, h) {
			this.t = e,
			this.p = t,
			this.s = r,
			this.c = i,
			this.n = a || t,
			e instanceof ht || s.push(this.n),
			this.r = f,
			this.type = u || 0,
			l && (this.pr = l, n = !0),
			this.b = void 0 === c ? r: c,
			this.e = void 0 === h ? r + i: h,
			o && (this._next = o, o._prev = this)
		}),
		pt = o.parseComplex = function (e, t, n, r, i, s, o, u, a, l) {
			n = n || s || "",
			o = new ht(e, t, 0, 0, o, l ? 2 : 1, null, !1, u, n, r),
			r += "";
			var c, h, p, d, g, y, b, w, E, S, T, N, C = n.split(", ").join(",").split(" "),
			k = r.split(", ").join(",").split(" "),
			L = C.length,
			A = f !== !1;
			for (( - 1 !== r.indexOf(",") || -1 !== n.indexOf(",")) && (C = C.join(" ").replace(M, ", ").split(" "), k = k.join(" ").replace(M, ", ").split(" "), L = C.length), L !== k.length && (C = (s || "").split(" "), L = C.length), o.plugin = a, o.setRatio = l, c = 0; L > c; c++) if (d = C[c], g = k[c], w = parseFloat(d), w || 0 === w) o.appendXtra("", w, nt(g, w), g.replace(m, ""), A && -1 !== g.indexOf("px"), !0);
			else if (i && ("#" === d.charAt(0) || st[d] || x.test(d))) N = "," === g.charAt(g.length - 1) ? "),": ")",
			d = ut(d),
			g = ut(g),
			E = d.length + g.length > 6,
			E && !q && 0 === g[3] ? (o["xs" + o.l] += o.l ? " transparent": "transparent", o.e = o.e.split(k[c]).join("transparent")) : (q || (E = !1), o.appendXtra(E ? "rgba(": "rgb(", d[0], g[0] - d[0], ",", !0, !0).appendXtra("", d[1], g[1] - d[1], ",", !0).appendXtra("", d[2], g[2] - d[2], E ? ",": N, !0), E && (d = 4 > d.length ? 1 : d[3], o.appendXtra("", d, (4 > g.length ? 1 : g[3]) - d, N, !1)));
			else if (y = d.match(v)) {
				if (b = g.match(m), !b || b.length !== y.length) return o;
				for (p = 0, h = 0; y.length > h; h++) T = y[h],
				S = d.indexOf(T, p),
				o.appendXtra(d.substr(p, S - p), Number(T), nt(b[h], T), "", A && "px" === d.substr(S + T.length, 2), 0 === h),
				p = S + T.length;
				o["xs" + o.l] += d.substr(p)
			} else o["xs" + o.l] += o.l ? " " + d: d;
			if ( - 1 !== r.indexOf("=") && o.data) {
				for (N = o.xs0 + o.data.s, c = 1; o.l > c; c++) N += o["xs" + c] + o.data["xn" + c];
				o.e = N + o["xs" + c]
			}
			return o.l || (o.type = -1, o.xs0 = o.e),
			o.xfirst || o
		},
		dt = 9;
		for (a = ht.prototype, a.l = a.pr = 0; --dt > 0;) a["xn" + dt] = 0,
		a["xs" + dt] = "";
		a.xs0 = "",
		a._next = a._prev = a.xfirst = a.data = a.plugin = a.setRatio = a.rxp = null,
		a.appendXtra = function (e, t, n, r, i, s) {
			var o = this,
			u = o.l;
			return o["xs" + u] += s && u ? " " + e: e || "",
			n || 0 === u || o.plugin ? (o.l++, o.type = o.setRatio ? 2 : 1, o["xs" + o.l] = r || "", u > 0 ? (o.data["xn" + u] = t + n, o.rxp["xn" + u] = i, o["xn" + u] = t, o.plugin || (o.xfirst = new ht(o, "xn" + u, t, n, o.xfirst || o, 0, o.n, i, o.pr), o.xfirst.xs0 = 0), o) : (o.data = {
				s: t + n
			},
			o.rxp = {},
			o.s = t, o.c = n, o.r = i, o)) : (o["xs" + u] += t + (r || ""), o)
		};
		var vt = function (e, t) {
			t = t || {},
			this.p = t.prefix ? X(e) || e: e,
			u[e] = u[this.p] = this,
			this.format = t.formatter || ft(t.defaultValue, t.color, t.collapsible, t.multi),
			t.parser && (this.parse = t.parser),
			this.clrs = t.color,
			this.multi = t.multi,
			this.keyword = t.keyword,
			this.dflt = t.defaultValue,
			this.pr = t.priority || 0
		},
		mt = F._registerComplexSpecialProp = function (e, t, n) {
			"object" != typeof t && (t = {
				parser: n
			});
			var r, i, s = e.split(","),
			o = t.defaultValue;
			for (n = n || [o], r = 0; s.length > r; r++) t.prefix = 0 === r && t.prefix,
			t.defaultValue = n[r] || o,
			i = new vt(s[r], t)
		},
		gt = function (e) {
			if (!u[e]) {
				var t = e.charAt(0).toUpperCase() + e.substr(1) + "Plugin";
				mt(e, {
					parser: function (e, n, r, i, s, o, a) {
						var f = (window.GreenSockGlobals || window).com.greensock.plugins[t];
						return f ? (f._cssRegister(), u[r].parse(e, n, r, i, s, o, a)) : (U("Error: " + t + " js file not loaded."), s)
					}
				})
			}
		};
		a = vt.prototype,
		a.parseComplex = function (e, t, n, r, i, s) {
			var o, u, a, f, l, c, h = this.keyword;
			if (this.multi && (M.test(n) || M.test(t) ? (u = t.replace(M, "|").split("|"), a = n.replace(M, "|").split("|")) : h && (u = [t], a = [n])), a) {
				for (f = a.length > u.length ? a.length: u.length, o = 0; f > o; o++) t = u[o] = u[o] || this.dflt,
				n = a[o] = a[o] || this.dflt,
				h && (l = t.indexOf(h), c = n.indexOf(h), l !== c && (n = -1 === c ? a: u, n[o] += " " + h));
				t = u.join(", "),
				n = a.join(", ")
			}
			return pt(e, this.p, t, n, this.clrs, this.dflt, r, this.pr, i, s)
		},
		a.parse = function (e, t, n, r, s, o) {
			return this.parseComplex(e.style, this.format($(e, this.p, i, !1, this.dflt)), this.format(t), s, o)
		},
		o.registerSpecialProp = function (e, t, n) {
			mt(e, {
				parser: function (e, r, i, s, o, u) {
					var a = new ht(e, i, 0, 0, o, 2, i, !1, n);
					return a.plugin = u,
					a.setRatio = t(e, r, s._tween, i),
					a
				},
				priority: n
			})
		};
		var yt = "scaleX,scaleY,scaleZ,x,y,z,skewX,rotation,rotationX,rotationY,perspective".split(","),
		bt = X("transform"),
		wt = z + "transform",
		Et = X("transformOrigin"),
		St = null !== X("perspective"),
		xt = function (e, t, n, r) {
			if (e._gsTransform && n && !r) return e._gsTransform;
			var i, s, u, a, f, l, c, h, p, d, v, m, g, y = n ? e._gsTransform || {
				skewY: 0
			}: {
				skewY: 0
			},
			b = 0 > y.scaleX,
			w = 2e-5,
			E = 1e5,
			S = 179.99,
			x = S * _,
			T = St ? parseFloat($(e, Et, t, !1, "0 0 0").split(" ")[2]) || y.zOrigin || 0 : 0;
			for (bt ? i = $(e, wt, t, !0) : e.currentStyle && (i = e.currentStyle.filter.match(A), i = i && 4 === i.length ? [i[0].substr(4), Number(i[2].substr(4)), Number(i[1].substr(4)), i[3].substr(4), y.x || 0, y.y || 0].join(",") : ""), s = (i || "").match(/(?:\-|\b)[\d\-\.e]+\b/gi) || [], u = s.length; --u > -1;) a = Number(s[u]),
			s[u] = (f = a - (a |= 0)) ? (0 | f * E + (0 > f ? -.5 : .5)) / E + a: a;
			if (16 === s.length) {
				var N = s[8],
				C = s[9],
				k = s[10],
				L = s[12],
				O = s[13],
				M = s[14];
				if (y.zOrigin && (M = -y.zOrigin, L = N * M - s[12], O = C * M - s[13], M = k * M + y.zOrigin - s[14]), !n || r || null == y.rotationX) {
					var P, H, B, j, F, I, q, R = s[0],
					U = s[1],
					z = s[2],
					W = s[3],
					X = s[4],
					V = s[5],
					J = s[6],
					K = s[7],
					Q = s[11],
					G = Math.atan2(J, k),
					Y = -x > G || G > x;
					y.rotationX = G * D,
					G && (j = Math.cos( - G), F = Math.sin( - G), P = X * j + N * F, H = V * j + C * F, B = J * j + k * F, N = X * -F + N * j, C = V * -F + C * j, k = J * -F + k * j, Q = K * -F + Q * j, X = P, V = H, J = B),
					G = Math.atan2(N, R),
					y.rotationY = G * D,
					G && (I = -x > G || G > x, j = Math.cos( - G), F = Math.sin( - G), P = R * j - N * F, H = U * j - C * F, B = z * j - k * F, C = U * F + C * j, k = z * F + k * j, Q = W * F + Q * j, R = P, U = H, z = B),
					G = Math.atan2(U, V),
					y.rotation = G * D,
					G && (q = -x > G || G > x, j = Math.cos( - G), F = Math.sin( - G), R = R * j + X * F, H = U * j + V * F, V = U * -F + V * j, J = z * -F + J * j, U = H),
					q && Y ? y.rotation = y.rotationX = 0 : q && I ? y.rotation = y.rotationY = 0 : I && Y && (y.rotationY = y.rotationX = 0),
					y.scaleX = (0 | Math.sqrt(R * R + U * U) * E + .5) / E,
					y.scaleY = (0 | Math.sqrt(V * V + C * C) * E + .5) / E,
					y.scaleZ = (0 | Math.sqrt(J * J + k * k) * E + .5) / E,
					y.skewX = 0,
					y.perspective = Q ? 1 / (0 > Q ? -Q: Q) : 0,
					y.x = L,
					y.y = O,
					y.z = M
				}
			} else if (! (St && !r && s.length && y.x === s[4] && y.y === s[5] && (y.rotationX || y.rotationY) || void 0 !== y.x && "none" === $(e, "display", t))) {
				var Z = s.length >= 6,
				et = Z ? s[0] : 1,
				tt = s[1] || 0,
				nt = s[2] || 0,
				rt = Z ? s[3] : 1;
				y.x = s[4] || 0,
				y.y = s[5] || 0,
				l = Math.sqrt(et * et + tt * tt),
				c = Math.sqrt(rt * rt + nt * nt),
				h = et || tt ? Math.atan2(tt, et) * D: y.rotation || 0,
				p = nt || rt ? Math.atan2(nt, rt) * D + h: y.skewX || 0,
				d = l - Math.abs(y.scaleX || 0),
				v = c - Math.abs(y.scaleY || 0),
				Math.abs(p) > 90 && 270 > Math.abs(p) && (b ? (l *= -1, p += 0 >= h ? 180 : -180, h += 0 >= h ? 180 : -180) : (c *= -1, p += 0 >= p ? 180 : -180)),
				m = (h - y.rotation)* 1,
				g = (p - y.skewX)* 1,
				(void 0 === y.skewX || d > w || -w > d || v > w || -w > v || m > -S && S > m && false | m * E || g > -S && S > g && false | g * E) && (y.scaleX = l, y.scaleY = c, y.rotation = h, y.skewX = p),
				St && (y.rotationX = y.rotationY = y.z = 0, y.perspective = parseFloat(o.defaultTransformPerspective) || 0, y.scaleZ = 1)
			}
			y.zOrigin = T;
			for (u in y) w > y[u] && y[u] > -w && (y[u] = 0);
			return n && (e._gsTransform = y),
			y
		},
		Tt = function (e) {
			var t, n, r = this.data,
			i = -r.rotation * _,
			s = i + r.skewX * _,
			o = 1e5,
			u = (0 | Math.cos(i) * r.scaleX * o) / o,
			a = (0 | Math.sin(i) * r.scaleX * o) / o,
			f = (0 | Math.sin(s) * -r.scaleY * o) / o,
			l = (0 | Math.cos(s) * r.scaleY * o) / o,
			c = this.t.style,
			h = this.t.currentStyle;
			if (h) {
				n = a,
				a = -f,
				f = -n,
				t = h.filter,
				c.filter = "";
				var p, v, m = this.t.offsetWidth,
				g = this.t.offsetHeight,
				y = "absolute" !== h.position,
				E = "progid:DXImageTransform.Microsoft.Matrix(M11=" + u + ", M12=" + a + ", M21=" + f + ", M22=" + l,
				S = r.x,
				x = r.y;
				if (null != r.ox && (p = (r.oxp ? .01 * m * r.ox: r.ox) - m / 2, v = (r.oyp ? .01 * g * r.oy: r.oy) - g / 2, S += p - (p * u + v * a), x += v - (p * f + v * l)), y ? (p = m / 2, v = g / 2, E += ", Dx=" + (p - (p * u + v * a) + S) + ", Dy=" + (v - (p * f + v * l) + x) + ")") : E += ", sizingMethod='auto expand')", c.filter = -1 !== t.indexOf("DXImageTransform.Microsoft.Matrix(") ? t.replace(O, E) : E + " " + t, (0 === e || 1 === e) && 1 === u && 0 === a && 0 === f && 1 === l && (y && -1 === E.indexOf("Dx=0, Dy=0") || w.test(t) && 100 !== parseFloat(RegExp.$1) || -1 === t.indexOf("gradient(" && t.indexOf("Alpha")) && c.removeAttribute("filter")), !y) {
					var T, N, C, k = 8 > d ? 1 : -1;
					for (p = r.ieOffsetX || 0, v = r.ieOffsetY || 0, r.ieOffsetX = Math.round((m - ((0 > u ? -u: u) * m + (0 > a ? -a: a) * g)) / 2 + S), r.ieOffsetY = Math.round((g - ((0 > l ? -l: l) * g + (0 > f ? -f: f) * m)) / 2 + x), dt = 0; 4 > dt; dt++) N = Z[dt],
					T = h[N],
					n = -1 !== T.indexOf("px") ? parseFloat(T) : J(this.t, N, parseFloat(T), T.replace(b, "")) || 0,
					C = n !== r[N] ? 2 > dt ? -r.ieOffsetX: -r.ieOffsetY: 2 > dt ? p - r.ieOffsetX: v - r.ieOffsetY,
					c[N] = (r[N] = Math.round(n - C * (0 === dt || 2 === dt ? 1 : k))) + "px"
				}
			}
		},
		Nt = function () {
			var e, t, n, r, i, s, o, u, a, f, l, c, p, d, v, m, g, y, b, w, E, S, x, T = this.data,
			N = this.t.style,
			C = T.rotation * _,
			k = T.scaleX,
			L = T.scaleY,
			A = T.scaleZ,
			O = T.perspective;
			if (h) {
				var M = 1e-4;
				M > k && k > -M && (k = A = 2e-5),
				M > L && L > -M && (L = A = 2e-5),
				!O || T.z || T.rotationX || T.rotationY || (O = 0)
			}
			if (C || T.skewX) y = Math.cos(C),
			b = Math.sin(C),
			e = y,
			i = b,
			T.skewX && (C -= T.skewX * _, y = Math.cos(C), b = Math.sin(C)),
			t = -b,
			s = y;
			else {
				if (! (T.rotationY || T.rotationX || 1 !== A || O)) return N[bt] = "translate3d(" + T.x + "px," + T.y + "px," + T.z + "px)" + (1 !== k || 1 !== L ? " scale(" + k + "," + L + ")": ""),
				void 0;
				e = s = 1,
				t = i = 0
			}
			l = 1,
			n = r = o = u = a = f = c = p = d = 0,
			v = O ? -1 / O: 0,
			m = T.zOrigin,
			g = 1e5,
			C = T.rotationY * _,
			C && (y = Math.cos(C), b = Math.sin(C), a = l * -b, p = v * -b, n = e * b, o = i * b, l *= y, v *= y, e *= y, i *= y),
			C = T.rotationX * _,
			C && (y = Math.cos(C), b = Math.sin(C), w = t * y + n * b, E = s * y + o * b, S = f * y + l * b, x = d * y + v * b, n = t * -b + n * y, o = s * -b + o * y, l = f * -b + l * y, v = d * -b + v * y, t = w, s = E, f = S, d = x),
			1 !== A && (n *= A, o *= A, l *= A, v *= A),
			1 !== L && (t *= L, s *= L, f *= L, d *= L),
			1 !== k && (e *= k, i *= k, a *= k, p *= k),
			m && (c -= m, r = n * c, u = o * c, c = l * c + m),
			r = (w = (r += T.x) - (r |= 0)) ? (0 | w * g + (0 > w ? -.5 : .5)) / g + r: r,
			u = (w = (u += T.y) - (u |= 0)) ? (0 | w * g + (0 > w ? -.5 : .5)) / g + u: u,
			c = (w = (c += T.z) - (c |= 0)) ? (0 | w * g + (0 > w ? -.5 : .5)) / g + c: c,
			N[bt] = "matrix3d(" + [(0 | e * g) / g, (0 | i * g) / g, (0 | a * g) / g, (0 | p * g) / g, (0 | t * g) / g, (0 | s * g) / g, (0 | f * g) / g, (0 | d * g) / g, (0 | n * g) / g, (0 | o * g) / g, (0 | l * g) / g, (0 | v * g) / g, r, u, c, O ? 1 + -c / O: 1].join(",") + ")"
		},
		Ct = function () {
			var e, t, n, r, i, s, o, u, a, f = this.data,
			l = this.t,
			c = l.style;
			h && (e = c.top ? "top": c.bottom ? "bottom": parseFloat($(l, "top", null, !1)) ? "bottom": "top", t = $(l, e, null, !1), n = parseFloat(t) || 0, r = t.substr((n + "").length) || "px", f._ffFix = !f._ffFix, c[e] = (f._ffFix ? n + .05 : n - .05) + r),
			f.rotation || f.skewX ? (i = f.rotation * _, s = i - f.skewX * _, o = 1e5, u = f.scaleX * o, a = f.scaleY * o, c[bt] = "matrix(" + (0 | Math.cos(i) * u) / o + "," + (0 | Math.sin(i) * u) / o + "," + (0 | Math.sin(s) * -a) / o + "," + (0 | Math.cos(s) * a) / o + "," + f.x + "," + f.y + ")") : c[bt] = "matrix(" + f.scaleX + ",0,0," + f.scaleY + "," + f.x + "," + f.y + ")"
		};
		mt("transform,scale,scaleX,scaleY,scaleZ,x,y,z,rotation,rotationX,rotationY,rotationZ,skewX,skewY,shortRotation,shortRotationX,shortRotationY,shortRotationZ,transformOrigin,transformPerspective,directionalRotation,parseTransform,force3D", {
			parser: function (e, t, n, r, s, o, u) {
				if (r._transform) return s;
				var a, f, l, c, h, p, d, v = r._transform = xt(e, i, !0, u.parseTransform),
				m = e.style,
				g = 1e-6,
				y = yt.length,
				b = u,
				w = {};
				if ("string" == typeof b.transform && bt) l = m.cssText,
				m[bt] = b.transform,
				m.display = "block",
				a = xt(e, null, !1),
				m.cssText = l;
				else if ("object" == typeof b) {
					if (a = {
						scaleX: rt(null != b.scaleX ? b.scaleX: b.scale, v.scaleX),
						scaleY: rt(null != b.scaleY ? b.scaleY: b.scale, v.scaleY),
						scaleZ: rt(null != b.scaleZ ? b.scaleZ: b.scale, v.scaleZ),
						x: rt(b.x, v.x),
						y: rt(b.y, v.y),
						z: rt(b.z, v.z),
						perspective: rt(b.transformPerspective, v.perspective)
					},
					d = b.directionalRotation, null != d) if ("object" == typeof d) for (l in d) b[l] = d[l];
					else b.rotation = d;
					a.rotation = it("rotation" in b ? b.rotation: "shortRotation" in b ? b.shortRotation + "_short": "rotationZ" in b ? b.rotationZ: v.rotation, v.rotation, "rotation", w),
					St && (a.rotationX = it("rotationX" in b ? b.rotationX: "shortRotationX" in b ? b.shortRotationX + "_short": v.rotationX || 0, v.rotationX, "rotationX", w), a.rotationY = it("rotationY" in b ? b.rotationY: "shortRotationY" in b ? b.shortRotationY + "_short": v.rotationY || 0, v.rotationY, "rotationY", w)),
					a.skewX = null == b.skewX ? v.skewX: it(b.skewX, v.skewX),
					a.skewY = null == b.skewY ? v.skewY: it(b.skewY, v.skewY),
					(f = a.skewY - v.skewY) && (a.skewX += f, a.rotation += f)
				}
				for (null != b.force3D && (v.force3D = b.force3D, p = !0), h = v.force3D || v.z || v.rotationX || v.rotationY || a.z || a.rotationX || a.rotationY || a.perspective, h || null == b.scale || (a.scaleZ = 1); --y > -1;) n = yt[y],
				c = a[n] - v[n],
				(c > g || -g > c || null != P[n]) && (p = !0, s = new ht(v, n, v[n], c, s), n in w && (s.e = w[n]), s.xs0 = 0, s.plugin = o, r._overwriteProps.push(s.n));
				return c = b.transformOrigin,
				(c || St && h && v.zOrigin) && (bt ? (p = !0, n = Et, c = (c || $(e, n, i, !1, "50% 50%")) + "", s = new ht(m, n, 0, 0, s, -1, "transformOrigin"), s.b = m[n], s.plugin = o, St ? (l = v.zOrigin, c = c.split(" "), v.zOrigin = (c.length > 2 && (0 === l || "0px" !== c[2]) ? parseFloat(c[2]) : l) || 0, s.xs0 = s.e = m[n] = c[0] + " " + (c[1] || "50%") + " 0px", s = new ht(v, "zOrigin", 0, 0, s, -1, s.n), s.b = l, s.xs0 = s.e = v.zOrigin) : s.xs0 = s.e = m[n] = c) : tt(c + "", v)),
				p && (r._transformType = h || 3 === this._transformType ? 3 : 2),
				s
			},
			prefix: !0
		}),
		mt("boxShadow", {
			defaultValue: "0px 0px 0px 0px #999",
			prefix: !0,
			color: !0,
			multi: !0,
			keyword: "inset"
		}),
		mt("borderRadius", {
			defaultValue: "0px",
			parser: function (e, t, n, s, o) {
				t = this.format(t);
				var u, a, f, l, c, h, p, d, v, m, g, y, b, w, E, S, x = ["borderTopLeftRadius", "borderTopRightRadius", "borderBottomRightRadius", "borderBottomLeftRadius"],
				T = e.style;
				for (v = parseFloat(e.offsetWidth), m = parseFloat(e.offsetHeight), u = t.split(" "), a = 0; x.length > a; a++) this.p.indexOf("border") && (x[a] = X(x[a])),
				c = l = $(e, x[a], i, !1, "0px"),
				-1 !== c.indexOf(" ") && (l = c.split(" "), c = l[0], l = l[1]),
				h = f = u[a],
				p = parseFloat(c),
				y = c.substr((p + "").length),
				b = "=" === h.charAt(1),
				b ? (d = parseInt(h.charAt(0) + "1", 10), h = h.substr(2), d *= parseFloat(h), g = h.substr((d + "").length - (0 > d ? 1 : 0)) || "") : (d = parseFloat(h), g = h.substr((d + "").length)),
				"" === g && (g = r[n] || y),
				g !== y && (w = J(e, "borderLeft", p, y), E = J(e, "borderTop", p, y), "%" === g ? (c = 100 * (w / v) + "%", l = 100 * (E / m) + "%") : "em" === g ? (S = J(e, "borderLeft", 1, "em"), c = w / S + "em", l = E / S + "em") : (c = w + "px", l = E + "px"), b && (h = parseFloat(c) + d + g, f = parseFloat(l) + d + g)),
				o = pt(T, x[a], c + " " + l, h + " " + f, !1, "0px", o);
				return o
			},
			prefix: !0,
			formatter: ft("0px 0px 0px 0px", !1, !0)
		}),
		mt("backgroundPosition", {
			defaultValue: "0 0",
			parser: function (e, t, n, r, s, o) {
				var u, a, f, l, c, h, p = "background-position",
				v = i || V(e, null),
				m = this.format((v ? d ? v.getPropertyValue(p + "-x") + " " + v.getPropertyValue(p + "-y") : v.getPropertyValue(p) : e.currentStyle.backgroundPositionX + " " + e.currentStyle.backgroundPositionY) || "0 0"),
				g = this.format(t);
				if ( - 1 !== m.indexOf("%") != ( - 1 !== g.indexOf("%")) && (h = $(e, "backgroundImage").replace(C, ""), h && "none" !== h)) {
					for (u = m.split(" "), a = g.split(" "), j.setAttribute("src", h), f = 2; --f > -1;) m = u[f],
					l = -1 !== m.indexOf("%"),
					l !== ( - 1 !== a[f].indexOf("%")) && (c = 0 === f ? e.offsetWidth - j.width: e.offsetHeight - j.height, u[f] = l ? parseFloat(m) / 100 * c + "px": 100 * (parseFloat(m) / c) + "%");
					m = u.join(" ")
				}
				return this.parseComplex(e.style, m, g, s, o)
			},
			formatter: tt
		}),
		mt("backgroundSize", {
			defaultValue: "0 0",
			formatter: tt
		}),
		mt("perspective", {
			defaultValue: "0px",
			prefix: !0
		}),
		mt("perspectiveOrigin", {
			defaultValue: "50% 50%",
			prefix: !0
		}),
		mt("transformStyle", {
			prefix: !0
		}),
		mt("backfaceVisibility", {
			prefix: !0
		}),
		mt("userSelect", {
			prefix: !0
		}),
		mt("margin", {
			parser: lt("marginTop,marginRight,marginBottom,marginLeft")
		}),
		mt("padding", {
			parser: lt("paddingTop,paddingRight,paddingBottom,paddingLeft")
		}),
		mt("clip", {
			defaultValue: "rect(0px,0px,0px,0px)",
			parser: function (e, t, n, r, s, o) {
				var u, a, f;
				return 9 > d ? (a = e.currentStyle, f = 8 > d ? " ": ",", u = "rect(" + a.clipTop + f + a.clipRight + f + a.clipBottom + f + a.clipLeft + ")", t = this.format(t).split(",").join(f)) : (u = this.format($(e, this.p, i, !1, this.dflt)), t = this.format(t)),
				this.parseComplex(e.style, u, t, s, o)
			}
		}),
		mt("textShadow", {
			defaultValue: "0px 0px 0px #999",
			color: !0,
			multi: !0
		}),
		mt("autoRound,strictUnits", {
			parser: function (e, t, n, r, i) {
				return i
			}
		}),
		mt("border", {
			defaultValue: "0px solid #000",
			parser: function (e, t, n, r, s, o) {
				return this.parseComplex(e.style, this.format($(e, "borderTopWidth", i, !1, "0px") + " " + $(e, "borderTopStyle", i, !1, "solid") + " " + $(e, "borderTopColor", i, !1, "#000")), this.format(t), s, o)
			},
			color: !0,
			formatter: function (e) {
				var t = e.split(" ");
				return t[0] + " " + (t[1] || "solid") + " " + (e.match(at) || ["#000"])[0]
			}
		}),
		mt("float,cssFloat,styleFloat", {
			parser: function (e, t, n, r, i) {
				var s = e.style,
				o = "cssFloat" in s ? "cssFloat": "styleFloat";
				return new ht(s, o, 0, 0, i, -1, n, !1, 0, s[o], t)
			}
		});
		var kt = function (e) {
			var t, n = this.t,
			r = n.filter || $(this.data, "filter"),
			i = 0 | this.s + this.c * e;
			100 === i && ( - 1 === r.indexOf("atrix(") && -1 === r.indexOf("radient(") && -1 === r.indexOf("oader(") ? (n.removeAttribute("filter"), t = !$(this.data, "filter")) : (n.filter = r.replace(S, ""), t = !0)),
			t || (this.xn1 && (n.filter = r = r || "alpha(opacity=" + i + ")"), -1 === r.indexOf("opacity") ? 0 === i && this.xn1 || (n.filter = r + " alpha(opacity=" + i + ")") : n.filter = r.replace(w, "opacity=" + i))
		};
		mt("opacity,alpha,autoAlpha", {
			defaultValue: "1",
			parser: function (e, t, n, r, s, o) {
				var u = parseFloat($(e, "opacity", i, !1, "1")),
				a = e.style,
				f = "autoAlpha" === n;
				return "string" == typeof t && "=" === t.charAt(1) && (t = ("-" === t.charAt(0) ? -1 : 1) * parseFloat(t.substr(2)) + u),
				f && 1 === u && "hidden" === $(e, "visibility", i) && 0 !== t && (u = 0),
				q ? s = new ht(a, "opacity", u, t - u, s) : (s = new ht(a, "opacity", 100 * u, 100 * (t - u), s), s.xn1 = f ? 1 : 0, a.zoom = 1, s.type = 2, s.b = "alpha(opacity=" + s.s + ")", s.e = "alpha(opacity=" + (s.s + s.c) + ")", s.data = e, s.plugin = o, s.setRatio = kt),
				f && (s = new ht(a, "visibility", 0, 0, s, -1, null, !1, 0, 0 !== u ? "inherit": "hidden", 0 === t ? "hidden": "inherit"), s.xs0 = "inherit", r._overwriteProps.push(s.n), r._overwriteProps.push(n)),
				s
			}
		});
		var Lt = function (e, t) {
			t && (e.removeProperty ? e.removeProperty(t.replace(T, "-$1").toLowerCase()) : e.removeAttribute(t))
		},
		At = function (e) {
			if (this.t._gsClassPT = this, 1 === e || 0 === e) {
				this.t.className = 0 === e ? this.b: this.e;
				for (var t = this.data, n = this.t.style; t;) t.v ? n[t.p] = t.v: Lt(n, t.p),
				t = t._next;
				1 === e && this.t._gsClassPT === this && (this.t._gsClassPT = null)
			} else this.t.className !== this.e && (this.t.className = this.e)
		};
		mt("className", {
			parser: function (e, t, r, s, o, u, a) {
				var f, l, c, h, p, d = e.className,
				v = e.style.cssText;
				if (o = s._classNamePT = new ht(e, r, 0, 0, o, 2), o.setRatio = At, o.pr = -11, n = !0, o.b = d, l = Q(e, i), c = e._gsClassPT) {
					for (h = {},
					p = c.data; p;) h[p.p] = 1,
					p = p._next;
					c.setRatio(1)
				}
				return e._gsClassPT = o,
				o.e = "=" !== t.charAt(1) ? t: d.replace(RegExp("\\s*\\b" + t.substr(2) + "\\b"), "") + ("+" === t.charAt(0) ? " " + t.substr(2) : ""),
				s._tween._duration && (e.className = o.e, f = G(e, l, Q(e), a, h), e.className = d, o.data = f.firstMPT, e.style.cssText = v, o = o.xfirst = s.parse(e, f.difs, o, u)),
				o
			}
		});
		var Ot = function (e) {
			if ((1 === e || 0 === e) && this.data._totalTime === this.data._totalDuration && "isFromStart" !== this.data.data) {
				var t, n, r, i, s = this.t.style,
				o = u.transform.parse;
				if ("all" === this.e) s.cssText = "",
				i = !0;
				else for (t = this.e.split(","), r = t.length; --r > -1;) n = t[r],
				u[n] && (u[n].parse === o ? i = !0 : n = "transformOrigin" === n ? Et: u[n].p),
				Lt(s, n);
				i && (Lt(s, bt), this.t._gsTransform && delete this.t._gsTransform)
			}
		};
		for (mt("clearProps", {
			parser: function (e, t, r, i, s) {
				return s = new ht(e, r, 0, 0, s, 2),
				s.setRatio = Ot,
				s.e = t,
				s.pr = -10,
				s.data = i._tween,
				n = !0,
				s
			}
		}), a = "bezier,throwProps,physicsProps,physics2D".split(","), dt = a.length; dt--;) gt(a[dt]);
		a = o.prototype,
		a._firstPT = null,
		a._onInitTween = function (e, t, u) {
			if (!e.nodeType) return ! 1;
			this._target = e,
			this._tween = u,
			this._vars = t,
			f = t.autoRound,
			n = !1,
			r = t.suffixMap || o.suffixMap,
			i = V(e, ""),
			s = this._overwriteProps;
			var a, h, d, v, m, g, y, b, w, S = e.style;
			if (l && "" === S.zIndex && (a = $(e, "zIndex", i), ("auto" === a || "" === a) && (S.zIndex = 0)), "string" == typeof t && (v = S.cssText, a = Q(e, i), S.cssText = v + ";" + t, a = G(e, a, Q(e)).difs, !q && E.test(t) && (a.opacity = parseFloat(RegExp.$1)), t = a, S.cssText = v), this._firstPT = h = this.parse(e, t, null), this._transformType) {
				for (w = 3 === this._transformType, bt ? c && (l = !0, "" === S.zIndex && (y = $(e, "zIndex", i), ("auto" === y || "" === y) && (S.zIndex = 0)), p && (S.WebkitBackfaceVisibility = this._vars.WebkitBackfaceVisibility || (w ? "visible": "hidden"))) : S.zoom = 1, d = h; d && d._next;) d = d._next;
				b = new ht(e, "transform", 0, 0, null, 2),
				this._linkCSSP(b, null, d),
				b.setRatio = w && St ? Nt: bt ? Ct: Tt,
				b.data = this._transform || xt(e, i, !0),
				s.pop()
			}
			if (n) {
				for (; h;) {
					for (g = h._next, d = v; d && d.pr > h.pr;) d = d._next;
					(h._prev = d ? d._prev: m) ? h._prev._next = h: v = h,
					(h._next = d) ? d._prev = h: m = h,
					h = g
				}
				this._firstPT = v
			}
			return ! 0
		},
		a.parse = function (e, t, n, s) {
			var o, a, l, c, h, p, d, v, m, g, y = e.style;
			for (o in t) p = t[o],
			a = u[o],
			a ? n = a.parse(e, p, o, this, n, s, t) : (h = $(e, o, i) + "", m = "string" == typeof p, "color" === o || "fill" === o || "stroke" === o || -1 !== o.indexOf("Color") || m && x.test(p) ? (m || (p = ut(p), p = (p.length > 3 ? "rgba(": "rgb(") + p.join(",") + ")"), n = pt(y, o, h, p, !0, "transparent", n, 0, s)) : !m || -1 === p.indexOf(" ") && -1 === p.indexOf(",") ? (l = parseFloat(h), d = l || 0 === l ? h.substr((l + "").length) : "", ("" === h || "auto" === h) && ("width" === o || "height" === o ? (l = et(e, o, i), d = "px") : "left" === o || "top" === o ? (l = K(e, o, i), d = "px") : (l = "opacity" !== o ? 0 : 1, d = "")), g = m && "=" === p.charAt(1), g ? (c = parseInt(p.charAt(0) + "1", 10), p = p.substr(2), c *= parseFloat(p), v = p.replace(b, "")) : (c = parseFloat(p), v = m ? p.substr((c + "").length) || "": ""), "" === v && (v = r[o] || d), p = c || 0 === c ? (g ? c + l: c) + v: t[o], d !== v && "" !== v && (c || 0 === c) && (l || 0 === l) && (l = J(e, o, l, d), "%" === v ? (l /= J(e, o, 100, "%") / 100, l > 100 && (l = 100), t.strictUnits !== !0 && (h = l + "%")) : "em" === v ? l /= J(e, o, 1, "em") : (c = J(e, o, c, v), v = "px"), g && (c || 0 === c) && (p = c + l + v)), g && (c += l), !l && 0 !== l || !c && 0 !== c ? void 0 !== y[o] && (p || "NaN" != p + "" && null != p) ? (n = new ht(y, o, c || l || 0, 0, n, -1, o, !1, 0, h, p), n.xs0 = "none" !== p || "display" !== o && -1 === o.indexOf("Style") ? p: h) : U("invalid " + o + " tween value: " + t[o]) : (n = new ht(y, o, l, c - l, n, 0, o, f !== !1 && ("px" === v || "zIndex" === o), 0, h, p), n.xs0 = v)) : n = pt(y, o, h, p, !0, null, n, 0, s)),
			s && n && !n.plugin && (n.plugin = s);
			return n
		},
		a.setRatio = function (e) {
			var t, n, r, i = this._firstPT,
			s = 1e-6;
			if (1 !== e || this._tween._time !== this._tween._duration && 0 !== this._tween._time) if (e || this._tween._time !== this._tween._duration && 0 !== this._tween._time || this._tween._rawPrevTime === -1e-6) for (; i;) {
				if (t = i.c * e + i.s, i.r ? t = t > 0 ? 0 | t + .5 : 0 | t - .5 : s > t && t > -s && (t = 0), i.type) if (1 === i.type) if (r = i.l, 2 === r) i.t[i.p] = i.xs0 + t + i.xs1 + i.xn1 + i.xs2;
				else if (3 === r) i.t[i.p] = i.xs0 + t + i.xs1 + i.xn1 + i.xs2 + i.xn2 + i.xs3;
				else if (4 === r) i.t[i.p] = i.xs0 + t + i.xs1 + i.xn1 + i.xs2 + i.xn2 + i.xs3 + i.xn3 + i.xs4;
				else if (5 === r) i.t[i.p] = i.xs0 + t + i.xs1 + i.xn1 + i.xs2 + i.xn2 + i.xs3 + i.xn3 + i.xs4 + i.xn4 + i.xs5;
				else {
					for (n = i.xs0 + t + i.xs1, r = 1; i.l > r; r++) n += i["xn" + r] + i["xs" + (r + 1)];
					i.t[i.p] = n
				} else - 1 === i.type ? i.t[i.p] = i.xs0: i.setRatio && i.setRatio(e);
				else i.t[i.p] = t + i.xs0;
				i = i._next
			} else for (; i;) 2 !== i.type ? i.t[i.p] = i.b: i.setRatio(e),
			i = i._next;
			else for (; i;) 2 !== i.type ? i.t[i.p] = i.e: i.setRatio(e),
			i = i._next
		},
		a._enableTransforms = function (e) {
			this._transformType = e || 3 === this._transformType ? 3 : 2,
			this._transform = this._transform || xt(this._target, i, !0)
		},
		a._linkCSSP = function (e, t, n, r) {
			return e && (t && (t._prev = e), e._next && (e._next._prev = e._prev), e._prev ? e._prev._next = e._next: this._firstPT === e && (this._firstPT = e._next, r = !0), n ? n._next = e: r || null !== this._firstPT || (this._firstPT = e), e._next = t, e._prev = n),
			e
		},
		a._kill = function (t) {
			var n, r, i, s = t;
			if (t.autoAlpha || t.alpha) {
				s = {};
				for (r in t) s[r] = t[r];
				s.opacity = 1,
				s.autoAlpha && (s.visibility = 1)
			}
			return t.className && (n = this._classNamePT) && (i = n.xfirst, i && i._prev ? this._linkCSSP(i._prev, n._next, i._prev._prev) : i === this._firstPT && (this._firstPT = n._next), n._next && this._linkCSSP(n._next, n._next._next, i._prev), this._classNamePT = null),
			e.prototype._kill.call(this, s)
		};
		var Mt = function (e, t, n) {
			var r, i, s, o;
			if (e.slice) for (i = e.length; --i > -1;) Mt(e[i], t, n);
			else for (r = e.childNodes, i = r.length; --i > -1;) s = r[i],
			o = s.type,
			s.style && (t.push(Q(s)), n && n.push(s)),
			1 !== o && 9 !== o && 11 !== o || !s.childNodes.length || Mt(s, t, n)
		};
		return o.cascadeTo = function (e, n, r) {
			var i, s, o, u = t.to(e, n, r),
			a = [u],
			f = [],
			l = [],
			c = [],
			h = t._internals.reservedProps;
			for (e = u._targets || u.target, Mt(e, f, c), u.render(n, !0), Mt(e, l), u.render(0, !0), u._enabled(!0), i = c.length; --i > -1;) if (s = G(c[i], f[i], l[i]), s.firstMPT) {
				s = s.difs;
				for (o in r) h[o] && (s[o] = r[o]);
				a.push(t.to(c[i], n, s))
			}
			return a
		},
		e.activate([o]),
		o
	},
	!0)
}),
window._gsDefine && window._gsQueue.pop()()