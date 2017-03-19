/*
 * PHPWind WindEditor Plugin
 * @Copyright 	: Copyright 2011, phpwind.com
 * @Descript	: bbcode插件
 * @Author		: chaoren1641@gmail.com
 * @Depend		: jquery.js(1.7 or later)
 * $Id: windeditor.js 4472 2012-02-19 10:41:01Z chris.chencq $			:
 */
;(function ( $, window, undefined ) {

	var WindEditor = window.WindEditor,
		browser = $.browser,
		ie = browser.msie,
		ie6 = ie && browser.version < 7,
		mozilla = browser.mozilla,
		webkit = browser.webkit,
		opera = browser.opera;

	var pluginName = 'bbcode';

	//grb转16进制
	function formatColor(color) {
		if (String(color).slice(0, 3) === 'rgb') {
            var ar = color.slice(4, -1).split(','),
                r = parseInt(ar[0]),
                g = parseInt(ar[1]),
                b = parseInt(ar[2]);
            return ['#', r < 16 ? '0' : '', r.toString(16), g < 16 ? '0' : '', g.toString(16), b < 16 ? '0' : '', b.toString(16)].join('');
        }
	    return color;
	}

	function p2br(txt) {
		txt=txt.replace(/^(\s*)<(p|div)>/ig, '$1');
		txt=txt.replace(/<(p|div)>/ig, "<br />");
		txt=txt.replace(/<\/(p|div)>/ig, '');
		return txt;
	}

	function isNum(s) {
        if (s != null && s != '') { return !isNaN(s);}
        else { return false; }
    }

	/**
	 * html转UBB
	 */
	var mapSize1 = {'10':1, '12':2, '16':3, '18':4, '24':5, '32':6, '48':7};
    //var mapSize2 = ['x-small', 'small', 'medium', 'large', 'x-large', 'xx-large', '-webkit-xxx-large'];
    var mapSize2 = {
    	'xx-small':[1,10],
    	'x-small':[2,12],
    	'small':[3,16],
    	'medium':[4,18],
    	'large':[5,24],
    	'x-large':[6,32],
    	'xx-large':[7,48]
    }
    var mapSize3 = {
    	'1':['x-small',10],
    	'2':['small',12],
    	'3':['medium',16],
    	'4':['large',18],
    	'5':['x-large',24],
    	'6':['xx-large',32],
    	'7':['-webkit-xxx-large',48]
    }
    var regSrc = /\s+src\s*=\s*(["']?)\s*(.+?)\s*\1(\s|$)/i;
    var regWidth = /\s*width\s*[:=]\s*(["']?)\s*(\d+(?:\.\d+)?%?);?\s*\1(\s|$)?/i;
    var regHeight = /\s+height\s*=\s*(["']?)\s*(\d+(?:\.\d+)?%?)\s*\1(\s|$)/i;
    var regCp = /\s+cellpadding\s*=\s*(["']?)\s*(.+?)\s*\1(\s|$)/i;
    var regBg = /(?:background|background-color|bgcolor)\s*[:=]\s*(["']?)\s*((rgb\s*\(\s*\d{1,3}%?,\s*\d{1,3}%?\s*,\s*\d{1,3}%?\s*\))|(#[0-9a-f]{3,6})|((?!initial)[a-z]{1,20}))\s*\1/i;
    var regBc = /(?:border-color|bordercolor)\s*[:=]\s*(["']?)\s*((rgb\s*\(\s*\d{1,3}%?,\s*\d{1,3}%?\s*,\s*\d{1,3}%?\s*\))|(#[0-9a-f]{3,6})|([a-z]{1,20}))\s*\1/i;
    var regAg = /(?:border-color|align)\s*[:=]\s*(["']?)\s*((rgb\s*\(\s*\d{1,3}%?,\s*\d{1,3}%?\s*,\s*\d{1,3}%?\s*\))|(#[0-9a-f]{3,6})|([a-z]{1,20}))\s*\1/i;
    var regBw = /\s+border\s*=\s*(["']?)\s*(\d+(?:\.\d+)?)\s*\1(\s|$)/i;

	function html2ubb(s) {
	    function rep(re, str) {
	        s = s.replace(re, str);
	    }
	    var para;//是否有首行缩进
	    if ( s.match(/text-indent\:[\s]?2em/i) ) { para = true; }
	    rep(/<(script|style)(\s+[^>]*?)?>[\s\S]*?<\/\1>/ig, '');
	    rep(/<!--[\s\S]*?-->/ig, '');
	    rep(/<(\/?)(b|u|i|strike)(\s+[^>]*?)?>/ig, '[$1$2]');
	    rep(/<(\/?)strong(\s+[^>]*?)?>/ig, '[$1b]');
	    rep(/<(\/?)em(\s+[^>]*?)?>/ig, '[$1i]');
	    rep(/<(\/?)(s|del)(\s+[^>]*?)?>/ig, '[$1strike]');
	    rep(/<(\/?)(sup|sub)(\s+[^>]*?)?>/ig, '[$1$2]');
	    rep(/<hr[^>]*?\/?>/ig, '[hr]');
	    rep(/<blockquote\s+[^>]*?class=\"blockquote\"[^>]*?>[\n]*?([\s\S]+?)[\n]*?<\/blockquote>/ig, function(all,txt) {
			return '[quote]' + p2br(txt) + '[/quote]';
		});
		rep(/<(\/?)blockquote(\s+[^>]*?)?>/ig, '[$1blockquote]');


		//过滤html5标签
		rep(/<(\/?)(abbr|article|aside|canvas|details|figcaption|figure|footer|header|hgroup|mark|meter|nav|output|progress|section|summary)(\s+[^>]*?)?>/ig, '[$1p]');

	    for (var i = 0; i < 6; i++) {
	        rep(/<(span)(?:\s+[^>]*?)?\s+style\s*=\s*"((?:[^"]*?;)*\s*(?:font-weight|text-decoration|font-style|font-family|font-size|color|background|background-color)\s*:[^"]*)"(?: [^>]+)?>(((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S])*?<\/\1>)*?<\/\1>)*?)<\/\1>/ig, function (all, tag, style, content) {
	            var bold = style.match(/(?:^|;)\s*font-weight\s*:\s*bold/i),
	                underline = style.match(/(?:^|;)\s*text-decoration\s*:[^;]*underline/i),
	                strike = style.match(/(?:^|;)\s*text-decoration\s*:[^;]*line-through/i),
	                italic = style.match(/(?:^|;)\s*font-style\s*:\s*italic/i),
	                fontface = style.match(/(?:^|;)\s*font-family\s*:\s*\'?([^;'&]+)\'?/i),
	                size = style.match(/(?:^|;)\s*font-size\s*:\s*\'?([^;']+)\'?/i),
	                color = style.match(/(?:^|;)\s*color\s*:\s*([^;]+)/i),
	                back = style.match(/(?:^|;)\s*(?:background|background-color)\s*:\s*(?!transparent)([^;]+)/i),
	                str = content;
	            if (fontface) {
	                str = '[font=' + fontface[1] + ']' + str + '[/font]';
	            }
	            if (italic) {
	                str = '[i]' + str + '[/i]';
	            }
	            if (strike) {
	                str = '[strike]' + str + '[/strike]';
	            }
	            if (underline) {
	                str = '[u]' + str + '[/u]';
	            }
	            if (bold) {
	                str = '[b]' + str + '[/b]';
	            }
	            if (color) {
	                str = '[color=' + formatColor(color[1]) + ']' + str + '[/color]';
	            }
	            if (back) {
	                str = '[backcolor=' + formatColor(back[1]) + ']' + str + '[/backcolor]';
	            }
	            if (size) {
	                if (size[1].toLowerCase().indexOf('px') > -1) {
	                    size = mapSize1[parseInt(size[1])];
	                } else if (size[1].toLowerCase().indexOf('pt') > -1) {
	                    size = Math.ceil(parseInt(size[1]) / 10) + 1;
	                } else if ($.inArray(size[1],mapSize2)) {
	                    size = mapSize2[size[1]] ? mapSize2[size[1]][0] : '';
	                }
	                if (size) {
	                    str = '[size=' + size + ']' + str + '[/size]';
	                }
	            }
	            return str;
	        });
	    }
	    if(webkit) {
			rep(/<div>\s*<br\s*\/>\s*<\/div>/ig,'\r\n');
		}
	    for (i = 0; i < 3; i++) {
	        rep(/<(div|p)(?:\s+[^>]*?)?[\s"';]\s*(?:text-)?align\s*[=:]\s*(["']?)\s*(left|center|right)\s*\2[^>]*>(((?!<\1(\s+[^>]*?)?>)[\s\S])+?)<\/\1>/ig, '[align=$3]$4[/align]');
	    }
	    for (i = 0; i < 3; i++) {
	        rep(/<(center)(?:\s+[^>]*?)?>(((?!<\1(\s+[^>]*?)?>)[\s\S])*?)<\/\1>/ig, '[align=center]$2[/align]');
	    }
	    for (i = 0; i < 3; i++) {
	        rep(/<(p|div)(?:\s+[^>]*?)?\s+style\s*=\s*"((?:[^"]*?;)*\s*color\s*:[^"]*)"(?: [^>]+)?>(((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S])*?<\/\1>)*?<\/\1>)*?)<\/\1>/ig, function (all, tag, style, content) {
	            var color = style.match(/(?:^|;)\s*color\s*:\s*([^;]+)/i),
	                str;
	            if (color) {str = '[color=' + formatColor(color[1]) + ']' + content + '[/color]\r\n';}
	            return str;
	        });
	    }
	    rep(/<a(?:\s+[^>]*?)?\s+href=(["'])\s*(.+?)\s*\1[^>]*>\s*([\s\S]*?)\s*<\/a>/ig, function (all, q, url, text) {
	        if (!(url && text)) {
	            return '';
	        }
	        var tag = 'url',
	            str;
	        if (url.match(/^mailto:/i)) {
	            tag = 'email';
	            url = url.replace(/mailto:(.+?)/i, '$1');
	        }
	        str = '[' + tag;
	        if (url != text) {str += '=' + url;}
	        return str + ']' + text + '[/' + tag + ']';
	    });
		rep(/<img((\s+\w+\s*=\s*(["'])?.*?\3)*)\s*\/?>/ig,function(all,attr) {
			var src = attr.match(/\s+src\s*=\s*(["']?)\s*(.+?)\s*\1(\s|$)/i);
			if(src[2] && src[2].indexOf("chrome://livemargins")>-1){
				return '';
			}
			var url = attr.match(regSrc);
			if(!url) {return '';}
			return '[img]'+url[2]+'[/img]';
		});
	    rep(/<embed((?:\s+[^>]*?)?(?:\s+type\s*=\s*"\s*application\/x-shockwave-flash\s*"|\s+classid\s*=\s*"\s*clsid:d27cdb6e-ae6d-11cf-96b8-4445535400000\s*")[^>]*?)\/>/ig, function (all, attr) {
	        var url = attr.match(regSrc),
	            w = attr.match(regWidth),
	            h = attr.match(regHeight),
	            str = '[flash';
	        if (!url) {
	            return '';
	        }
	        if (w && h) {
	            str += '=' + w[2] + ',' + h[2];
	        }
	        str += ']' + url[2];
	        return str + '[/flash]';
	    });
	    rep(/<embed((?:\s+[^>]*?)?(?:\s+type\s*=\s*"\s*application\/x-mplayer2\s*"|\s+classid\s*=\s*"\s*clsid:6bf52a52-394a-11d3-b153-00c04f79faa6\s*")[^>]*?)\/>/ig, function (all, attr) {
	        var url = attr.match(regSrc),
	            w = attr.match(regWidth),
	            h = attr.match(regHeight),
	            p = attr.match(/\s+autostart\s*=\s*(["']?)\s*(.+?)\s*\1(\s|$)/i),
	            str = '[media',
	            auto = '0';
	        if (!url) {
	            return '';
	        }
	        if (p) {
				if (p[2] == 'true') { auto = '1';}
			}
	        if (w && h) str += '=' + w[2] + ',' + h[2] + ',' + auto;
	        str += ']' + url[2];
	        return str + '[/media]';
	    });
	    rep(/<table(\s+[^>]*?)?>/ig, function (all, attr) {
	        var str = '[table';
	        if (attr) {//console.log(regBg)
	            var w = attr.match(regWidth),
	                b = attr.match(regBg),
	                c = attr.match(regBc),
	                s = attr.match(regBw),
	                p = attr.match(regCp);
	                a = attr.match(regAg);
	            if (w) {
	                str += '=' + w[2];
	                if (s && s[2] == '1') {s = null;}
	                if (b || c || s) {
	                    str += ',' + (b ? formatColor(b[2]) : '#ffffff');
	                    str += ',' + (c ? (formatColor(c[2]) == 'initial' ? '#dddddd' : formatColor(c[2])) : '');
	                    str += ',' + (s ? s[2] : 1);
	                }
	                if(p){
	                	str += ',' + p[2] || 0;
	                }

	                if(a) {
	                	str += ',' + a[2];
	                }
	            }
	        }
	        return str + ']';
	    });

	    rep(/<tr(\s+[^>]*?)?>/ig, function (all, attr) {
	        var str = '[tr';
	        return str + ']';
	    });
	    rep(/<(?:th|td)(\s+[^>]*?)?>/ig, function (all, attr) {
	        var str = '[td';
	        if (attr) {
	            var col = attr.match(/\s+colspan\s*=\s*(["']?)\s*(\d+)\s*\1(\s|$)/i),
	                row = attr.match(/\s+rowspan\s*=\s*(["']?)\s*(\d+)\s*\1(\s|$)/i),
	                w = attr.match(regWidth);
	            col = col ? col[2] : 1;
	            row = row ? row[2] : 1;
	            if (col > 1 || row > 1 || w) { str += '=' + col + ',' + row;}
	            if (w && w[2]) { str += ',' + w[2];}
	        }
	        return str + ']';
	    });
	    rep(/<\/(table|tr)>/ig, '[/$1]');
	    rep(/<\/(th|td)>/ig, '[/td]');
	    rep(/<ul(\s+[^>]*?)?>([\s\S]*?)<\/ul>/ig, function (all, attr, context) {
	        var t, tag;
	        if (attr && attr.match(/align="?([^\s"]*?)"?/ig)) {
	            tag = /align="?([^\s"]*?)"?/ig.exec(attr)[1];
	        } else if (attr && attr.match(/text-align\s*:\s*([^\s;]*?);/ig)) {
	            tag = /text-align\s*:\s*([^\s;]*?);/ig.exec(attr)[1];
	        }
	        if (tag) {
	            return '[align=' + tag + '][list]' + context.replace(/<li(\s+[^>]*?)?>([\s\S]*?)[\n]*?<\/li>/ig, "[li]$2[/li]") + '[/list][/align]';
	        }
	        if (attr) {
	            t = attr.match(/\s+type\s*=\s*(["']?)\s*(.+?)\s*\1(\s|$)/i);
	        }
	        return '[list' + (t ? '=' + t[2] : '') + ']' + context.replace(/<li(\s+[^>]*?)?>([\s\S]*?)[\n]*?<\/li>/ig, "[li]$2[/li]") + '[/list]';
	    });
	    rep(/<ol(\s+[^>]*?)?>([\s\S]*?)<\/ol>/ig, function (all, attr, context) {
	        var tag;
	        if (attr && attr.match(/align="?([^\s"]*?)"?/ig)) {
	            tag = /align="?([^\s"]*?)"?/ig.exec(attr)[1];
	        } else if (attr && attr.match(/text-align\s*:\s*([^\s;]*?);/ig)) {
	            tag = /text-align\s*:\s*([^\s;]*?);/ig.exec(attr)[1];
	        }
	        if (tag) {
	            return '[align=' + tag + '][list=1]' + context.replace(/<li(\s+[^>]*?)?>([\s\S]*?)[\n]*?<\/li>/ig, '[li]$2[/li]') + '[/list][/align]';
	        }else {
	            return '[list=1]' + context.replace(/<li(\s+[^>]*?)?>([\s\S]*?)[\n]*?<\/li>/ig, '[li]$2[/li]') + '[/list]';
	        }
	    });

	    rep(/<h([1-6])(\s+[^>]*?)?>/ig, function (all, n) {
	        return '\n\n[size=' + (7 - n) + '][b]';
	    });
	    rep(/<\/h[1-6]>/ig, '[/b][/size]\n\n');
	    rep(/<address(\s+[^>]*?)?>/ig, '\n[i]');
	    rep(/<\/address>/ig, '[i]\n');

	    for (i = 0; i < 3; i++) {
			rep(/([\s\S])<(div|p)(?:\s+[^>]*?)?>(((?!<\2(\s+[^>]*?)?>)[\s\S]|<\2(\s+[^>]*?)?>((?!<\2(\s+[^>]*?)?>)[\s\S]|<\2(\s+[^>]*?)?>((?!<\2(\s+[^>]*?)?>)[\s\S])*?<\/\2>)*?<\/\2>)*?)<\/\2>/ig, "$1\n$3");
	    }
	    rep(/<br[^\/>]*?\/?>/ig, "\n"); /*if(B.UA.gecko>0)*/
	    //FF下使用
	    //if(para) {sUBB = '[paragraph]'+sUBB;}
	    //rep(/((\s|&nbsp;)*\r?\n){3,}/g,"\n\n");//限制最多2次换行
	    //rep(/^((\s|&nbsp;)*\r?\n)+/g,'');//清除开头换行
	    //rep(/((\s|&nbsp;)*\r?\n)+$/g,'');//清除结尾换行
	    rep(/<[^<>]+?>/g, ''); //删除所有HTML标签
	    rep(/&lt;/ig, '<');
	    rep(/&gt;/ig, '>');
	    rep(/&nbsp;/ig, ' ');
	    rep(/&amp;/ig, '&');
	    if(para) { s = '[paragraph]' + s; }
	    //换行
	    rep(/<br \/>/gi,"\n");
		rep(/<br\/>/gi,"\n");
		rep(/<br>/gi,"\n");
		rep(/<p\/>/gi,"");
		rep(/&nbsp;|\u00a0/gi," ");
		//ie下 blockquote后第一个换行
		rep(/\[blockquote\]\n/ig, '[blockquote]');
	    return s;
	}

	/**
	 * ubb转HTML
	 */
	function ubb2html(s) {
	    function rep(re, str) {
	        s = s.replace(re, str);
	    }
	    var para;//是否有首行综进
		if (s.indexOf('[paragraph]') > -1){
			s = s.replace('[paragraph]', '');
			para = true;
		}
	    rep(/&/ig, '&amp;');
		rep(/[<>]/g,function(c) { return {'<':'&lt;','>':'&gt;'}[c]; });
	    rep(/\[(b|u|i|strike)\]\s*?\[\/(b|u|i|strike)\]/ig, '');
	    if (mozilla) { //firefox  font-weight; 0526新增color
	        rep(/\[(\/?)(b|u|i|strike)\]/ig, function (all, pre, tag) {
	            if (pre) { return '</span>';}
	            var str = '<span style="';
	            switch (tag) {
	            case 'b':
	                str += 'font-weight: bold;';
	                break;
	            case 'u':
	                str += 'text-decoration: underline;';
	                break;
	            case 'i':
	                str += 'font-style: italic;';
	                break;
	            case 'strike':
	                str += 'text-decoration: line-through;';
	            }
	            str += '">';
	            return str;
	        });
	        rep(/\[color=([\s\S]*?)\]/ig, '<span style="color:$1">');
	        rep(/\[\/color\]/ig, '</span>');
	    } else { //other  strong em u del
	        rep(/\[(\/?)(b|u|i|strike)\]/ig, '<$1$2>');
	    }

	    rep(/\[(\/?)(sup|sub)\]/ig, '<$1$2>');
	    rep(/\[color\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]/ig, '<font color="$1">');
	    rep(/\[size\s*=\s*(\d+?)\s*\]/ig, '<font size="$1">');
	    rep(/\[font\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]/ig, '<font face="$1">');
	    rep(/\[\/(color|size|font)\]/ig, '</font>');
	    rep(/\[backcolor\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]/ig, '<span style="background-color:$1;">');
	    rep(/\[\/backcolor\]/ig, '</span>');
		for (var i = 0; i < 3; i++) {
			rep(/\[align\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\](((?!\[align(?:\s+[^\]]+)?\])[\s\S])*?)\[\/align\]/ig, '<div align="$1">$2</div>');
	    }
	    rep(/\[img\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/img\]/ig, '<img src="$1" alt="" onload="if(this.width > 500){this.width = 500;}"/>');

	    rep(/\[img\s*=([^,\]]*)(?:\s*,\s*(\d*%?)\s*,\s*(\d*%?)\s*)?(?:,?\s*(\w+))?\s*\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*)?\s*\[\/img\]/ig, function (all, alt, p1, p2, p3, src) {
	        var str = '<img src="' + src + '" alt="' + alt + '"',
	            a = p3 ? p3 : (!isNum(p1) ? p1 : '');
	        if (isNum(p1)) { str += ' width="' + p1 + '"'; }
	        if (isNum(p2)) { str += ' height="' + p2 + '"'; }
	        if (a) { str += ' align="' + a + '"';}
	        str += ' />';
	        return str;
	    });
	    rep(/\[hr]/ig, "<hr>");

	    rep(/\[url\]\s*(((?!")[\s\S])*?)(?:"[\s\S]*?)?\s*\[\/url\]/ig, '<a href="$1">$1</a>');
	    rep(/\[url\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]\s*([\s\S]*?)\s*\[\/url\]/ig, '<a href="$1">$2</a>');
	    rep(/\[email\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/email\]/ig, '<a href="mailto:$1">$1</a>');
	    rep(/\[email\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]\s*([\s\S]+?)\s*\[\/email\]/ig, '<a href="mailto:$1">$2</a>');
	    rep(/\[quote\]([\s\S]*?)\[\/quote\]/ig, '<blockquote class="blockquote">$1</blockquote>');
	    rep(/\[(\/?)(blockquote)\]/ig, '<$1$2>');
	    //rep(/\[blockquote\]([\s\S]*?)\[\/blockquote\]/ig, '<blockquote>$1</blockquote>');
	    rep(/\[media\s*(?:=\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*(\d+)\s*)?)?\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/media\]/ig, function (all, w, h, play, url) {
	        if (!w) { w = 480;}
	        if (!h) {h = 400;}
	        return '<embed type="application/x-mplayer2" src="' + url + '" enablecontextmenu="false" autostart="' + (play == '1' ? 'true' : 'false') + '" width="' + w + '" height="' + h + '"/>';
	    });
	    rep(/\[table\s*(?:=\s*(\d{1,4}%?)\s*(?:,\s*([^\]"]+){1,3}(?:"[^\]]*?)?)?)?\s*\]/ig, function (all, w, o) {
	        var str = '<table',b, c, s, g, p;
	        if (o) {
	            o = o.split(',');
	            b = o[0], c = o[1], s = o[2], p = o[3], g = o[4];
	        }
	        str += ' width="' + (w ? w : '100%') + '"';
	        if (b) { str += ' bgcolor="' + b + '"';}
	        if (c) { str += ' bordercolor="' + c + '"';}
	        str += ' border="' + (s ? s : 1) + '"';
	        if(p){
	        	str += ' cellpadding="'+ p +'"';
	        }
	        if(g){
	        	str += ' align="'+ g +'"';
	        }
	        return str + '>';
	    });
	    rep(/\[tr\s*(?:=\s*([^\]"]+?)(?:"[^\]]*?)?)?\s*\]/ig, function (all, bg) {
	        return '<tr' + (bg ? ' bgcolor="' + bg + '"' : '') + '>';
	    });
	    rep(/\[td\s*(?:=\s*(\d{1,2})\s*,\s*(\d{1,2})\s*(?:,\s*(\d{1,4}\.?\d{1,2}%?))?)?\s*\]/ig, function (all, col, row, w) {
	    	var styleStr = '';
	    	if(w){
	    		styleStr +='width:' + w;
	    	}
	        return '<td' + (col > 1 ? ' colspan="' + col + '"' : '') + (row > 1 ? ' rowspan="' + row + '"' : '') + ' style="' + styleStr + '">&nbsp;';
	    });

	    rep(/\[\/(table|tr|td)\]/ig, '</$1>');
	    rep(/\[list\s*(?:=\s*([^\]"]+?)(?:"[^\]]*?)?)?\s*\]?([\s\S]*?)\[\/list\]/ig, function (all, type, context) {
	        var tag = type ? 'ol' : 'ul';
	        var str = '<' + tag + '>' + context.replace(/\[li\]\[\/li\]/, '<li></li>').replace(/\[li\]((?:(?!\[\/li\]|\[\/list\]|\[list\s*(?:=[^\]]+)?\])[\s\S])+)\[\/li\]/ig, '<li>$1</li>') + '</' + tag + '>';
	        return str;
	    });
	    rep(/<(\w+)(\s+[^>]*)?>([\s\S]+?)<\/\1>/ig, function (all, tag, attr, text) {
	        return '<' + tag + (attr ? attr : '') + '>' + text.replace(/\r?\n/g, '<br />') + '</' + tag + '>';
	    });

		if(para) {
			var style = para ? ' style="text-indent: 2em"' : '';
			s = '<div'+style+'>' + s.replace(/\r?\n/g, '</div><div'+style+'>') +'</div>';
			s.replace(/<div>\s*<\/div>/ig,'<div'+style+'>&nbsp;</div>');
			s = '<div id="partIndent" style="text-indent: 2em">' + s +'</div>';
		}
		rep(/\r?\n/ig,"<br />");
		//rep(/\r/ig,"<br />");
	    rep(/(^|<\/?\w+(?:\s+[^>]*?)?>)([^<$]+)/ig, function (all, tag, text) {
	        return tag + text.replace(/[\t ]/g, function (c) {
	            return {
	                '\t': '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
	                ' ': '&nbsp;'
	            }[c];
	        });
	    });
	    return s;
	}

	WindEditor.plugin(pluginName,function() {
		var _self = this;
		var editorDoc = _self.editorDoc = _self.iframe[0].contentWindow.document,
			editorToolbar = _self.toolbar;


		function wysiwyg() {
			try {
				//alert(_self.codeContainer.val())
				//某些情况下textarea默认没有值，比如载入草稿，这时候需要js来
				/*if($.trim(_self.codeContainer.val()) === '') {
					_self.codeContainer.val(editorDoc.body.innerHTML);
				}*/
				editorDoc.body.innerHTML = ubb2html(_self.codeContainer.val());
			}catch(e) {
				$.error('ubb转html程序出错：'+ e);
			}
		}

		//如果是可见即所得模式，则把ubb转换为可见即所得
		if(_self.viewMode === 'default') {
			try {
				editorDoc.body.innerHTML = ubb2html(_self.codeContainer.val());
			}catch(e) {
				$.error('ubb转html程序出错：'+ e);
			}
		}

		$(_self).on('afterSetValue.' + pluginName,function() {
			try{
				_self.codeContainer.val( html2ubb(_self.codeContainer.val()) );
			}catch(e) {
				$.error('html转ubb程序出错：'+ e);
			}
		});

		$(_self).on('setContenting.' + pluginName,function() {
			wysiwyg();
		});

	});


})( jQuery, window);