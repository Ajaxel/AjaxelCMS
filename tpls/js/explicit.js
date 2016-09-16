(function () {
	var c = {
		publisher_id: null,
		zone_id: null,
		file_base_path: null,
		referrer: null,
		lrefid: null,
		cburl: null,
		fwurl: null
	};
	function g(h) {
		switch (h.type) {
		case "image":
			e(h);
			break;
		case "text":
			f(h);
			break;
		case "flash":
			a(h);
			break
		}
	}
	function e(i) {
		var k = d(i),
		j = b(i),
		h;
		h = '<a href="' + k + '" target="_blank">';
		h += '<img src="' + j + '" width="' + i.width + '" height="' + i.height + '" border="0">';
		h += "</a>";
		document.write(h)
	}
	function a(i) {
		var k = d(i),
		j = b(i),
		h;
		h = "<OBJECT";
		h += ' width="' + i.width + '"';
		h += ' height="' + i.height + '">';
		h += '<PARAM name="movie" value="' + j + "?" + i.flash_var_name + "=" + encodeURIComponent(k) + '"></PARAM>';
		h += '<PARAM name="quality" value="high"></PARAM>';
		h += '<PARAM name="wmode" value="transparent"></PARAM>';
		h += '<EMBED src="' + j + "?" + i.flash_var_name + "=" + encodeURIComponent(k) + '" type="application/x-shockwave-flash" wmode="transparent"width="' + i.width + '" height="' + i.height + '" ></EMBED>';
		h += "</OBJECT>";
		document.write(h)
	}
	function f(i) {
		var j = d(i),
		h = '<a href="' + j + '" target="_blank">' + i.label + "</a>";
		document.write(h)
	}
	function d(j) {
		var i = j.url + c.publisher_id + "&bid=" + j.ad_id + "&zid=" + c.zone_id;
		var h = j.url_args;
		if (c.lrefid) {
			i += "&lrefid=" + c.lrefid
		}
		if (c.cburl) {
			i += "&cburl=" + escape(c.cburl)
		}
		if (c.referrer) {
			i += "&referrer=" + escape(c.referrer)
		}
		if (c.fwurl) {
			i += "&fwurl=" + escape(c.fwurl)
		}
		return i
	}
	function b(i) {
		var h = (("https:" == document.location.protocol) ? "https://": "http://"),
		j = h + c.file_base_path + i.file_path;
		return j
	}
	c.publisher_id = "81663";
	c.zone_id = "1";
	c.file_base_path = "uk.orvillemedia.com/ads/banners";
	c.referrer = "";
	c.lrefid = 0;
	c.cburl = "";
	c.fwurl = "";
	g({
		type: 'image',
		ad_id: 9860,
		setup_id: 81240,
		file_path: '/81240/500x500.jpg',
		url: 'http://uk.ultlink.com/sad/m/orv_samsungs6edge_survey_2015_05/?pid=',
		width: 500,
		height: 500,
		flash_var_name: 'clickTAG',
		label: ""
	})
})();