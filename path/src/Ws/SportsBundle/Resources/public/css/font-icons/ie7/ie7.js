/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referring to this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'ws-icon\'">' + entity + '</span>' + html;
	}
	var icons = {
		'ws-icon-arrow-up-right': '&#xe600;',
		'ws-icon-relaxation': '&#x22;',
		'ws-icon-volley': '&#x23;',
		'ws-icon-velo': '&#x24;',
		'ws-icon-ultimate': '&#x25;',
		'ws-icon-tree': '&#x26;',
		'ws-icon-tennis': '&#x27;',
		'ws-icon-surf': '&#x28;',
		'ws-icon-squash': '&#x29;',
		'ws-icon-ski': '&#x2a;',
		'ws-icon-skate': '&#x2b;',
		'ws-icon-running': '&#x2c;',
		'ws-icon-rugby': '&#x2d;',
		'ws-icon-roller': '&#x2e;',
		'ws-icon-rando': '&#x2f;',
		'ws-icon-pingpong': '&#x30;',
		'ws-icon-patin': '&#x31;',
		'ws-icon-laser': '&#x33;',
		'ws-icon-karting': '&#x35;',
		'ws-icon-horse': '&#x36;',
		'ws-icon-handball': '&#x37;',
		'ws-icon-FootUS': '&#x39;',
		'ws-icon-foot': '&#x3a;',
		'ws-icon-fitness': '&#x3b;',
		'ws-icon-circus': '&#x3d;',
		'ws-icon-bowls': '&#x3e;',
		'ws-icon-bmx': '&#x40;',
		'ws-icon-billard': '&#x41;',
		'ws-icon-basketball': '&#x42;',
		'ws-icon-baseball': '&#x43;',
		'ws-icon-badminton': '&#x44;',
		'ws-icon-phone': '&#x45;',
		'ws-icon-location': '&#x46;',
		'ws-icon-location2': '&#x47;',
		'ws-icon-tags': '&#x48;',
		'ws-icon-tag': '&#x49;',
		'ws-icon-alarm': '&#x4a;',
		'ws-icon-stopwatch': '&#x4b;',
		'ws-icon-calendar': '&#x4c;',
		'ws-icon-compass': '&#x4d;',
		'ws-icon-map': '&#x4e;',
		'ws-icon-map2': '&#x4f;',
		'ws-icon-calendar2': '&#x50;',
		'ws-icon-bubble': '&#x51;',
		'ws-icon-bubbles': '&#x52;',
		'ws-icon-bubbles2': '&#x53;',
		'ws-icon-bubble2': '&#x54;',
		'ws-icon-bubbles3': '&#x55;',
		'ws-icon-bubbles4': '&#x56;',
		'ws-icon-user': '&#x57;',
		'ws-icon-users': '&#x58;',
		'ws-icon-thumbs-up': '&#x59;',
		'ws-icon-heart': '&#x5a;',
		'ws-icon-heart2': '&#x5b;',
		'ws-icon-star': '&#x5c;',
		'ws-icon-star2': '&#x5d;',
		'ws-icon-happy': '&#x5e;',
		'ws-icon-happy2': '&#x5f;',
		'ws-icon-smiley': '&#x60;',
		'ws-icon-smiley2': '&#x61;',
		'ws-icon-thumbs-up2': '&#x62;',
		'ws-icon-loupe': '&#x64;',
		'ws-icon-paintball': '&#x21;',
		'ws-icon-flying': '&#xe000;',
		'ws-icon-swimming': '&#xe003;',
		'ws-icon-busy': '&#xe004;',
		'ws-icon-neutral': '&#xe005;',
		'ws-icon-smiley3': '&#xe006;',
		'ws-icon-menu': '&#xe007;',
		'ws-icon-cog': '&#xe008;',
		'ws-icon-exit': '&#xe009;',
		'ws-icon-lightning': '&#xe00a;',
		'ws-icon-envelop': '&#xe00b;',
		'ws-icon-library': '&#xe00c;',
		'ws-icon-book': '&#xe00d;',
		'ws-icon-pacman': '&#xe00e;',
		'ws-icon-enter': '&#xe00f;',
		'ws-icon-quill': '&#xe010;',
		'ws-icon-home': '&#xe011;',
		'ws-icon-blocked': '&#xe012;',
		'ws-icon-cancel-circle': '&#xe013;',
		'ws-icon-checkmark-circle': '&#xe014;',
		'ws-icon-spam': '&#xe015;',
		'ws-icon-close': '&#xe016;',
		'ws-icon-checkmark': '&#xe017;',
		'ws-icon-checkmark2': '&#xe018;',
		'ws-icon-info': '&#xe019;',
		'ws-icon-info2': '&#xe01a;',
		'ws-icon-arrow-left': '&#xe01b;',
		'ws-icon-arrow-right': '&#xe01c;',
		'ws-icon-arrow-up': '&#xe01d;',
		'ws-icon-arrow-down': '&#xe01e;',
		'ws-icon-loop': '&#xe01f;',
		'ws-icon-office': '&#xe020;',
		'ws-icon-martial-art': '&#xe021;',
		'ws-icon-target': '&#xe022;',
		'ws-icon-fighting': '&#xe001;',
		'ws-icon-athle': '&#xe023;',
		'ws-icon-danse': '&#xe024;',
		'ws-icon-gym': '&#xe025;',
		'ws-icon-climbing': '&#xe002;',
		'ws-icon-voile': '&#xe026;',
		'ws-icon-bowling': '&#xe027;',
		'ws-icon-board-games': '&#xe028;',
		'ws-icon-video-games': '&#xe029;',
		'ws-icon-golf': '&#xe02a;',
		'ws-icon-kayak': '&#xe02b;',
		'ws-icon-plus-alt': '&#x63;',
		'ws-icon-hockey': '&#x32;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/ws-icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());