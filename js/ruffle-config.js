window.RufflePlayer = window.RufflePlayer || {};
window.RufflePlayer.config = window.RufflePlayer.config || {};
window.RufflePlayer.config.autoplay = "on";
window.RufflePlayer.config.unmuteOverlay = "hidden";
window.RufflePlayer.config.splashScreen = false;
window.RufflePlayer.config.preloader = false;

(function () {
	var AD_SLOT_ID = "ads";
	var AD_LINK_URL = "http://vestibular.unipam.edu.br";
	var AD_LINK_CLASS = "ruffle-touch-ad-link";
	var STYLE_ID = "ruffle-touch-ad-link-style";

	function ready(callback) {
		if (document.readyState === "loading") {
			document.addEventListener("DOMContentLoaded", callback);
			return;
		}

		callback();
	}

	function installStyle() {
		var style;
		var target;

		if (document.getElementById(STYLE_ID)) {
			return;
		}

		style = document.createElement("style");
		style.id = STYLE_ID;
		style.type = "text/css";
		style.appendChild(document.createTextNode(
			"#" + AD_SLOT_ID + " {" +
			"position: relative;" +
			"}" +
			"#" + AD_SLOT_ID + " ." + AD_LINK_CLASS + " {" +
			"display: none;" +
			"}" +
			"@media (pointer: coarse), (hover: none) {" +
			"#" + AD_SLOT_ID + " ." + AD_LINK_CLASS + " {" +
			"display: block;" +
			"position: absolute;" +
			"left: 0;" +
			"top: 0;" +
			"width: 255px;" +
			"height: 255px;" +
			"z-index: 10;" +
			"background: transparent;" +
			"touch-action: pan-x pan-y pinch-zoom;" +
			"}" +
			"#" + AD_SLOT_ID + " object," +
			"#" + AD_SLOT_ID + " embed," +
			"#" + AD_SLOT_ID + " ruffle-player," +
			"#" + AD_SLOT_ID + " ruffle-object," +
			"#" + AD_SLOT_ID + " ruffle-embed," +
			"#" + AD_SLOT_ID + " canvas {" +
			"pointer-events: none;" +
			"}" +
			"}"
		));

		target = document.head || document.getElementsByTagName("head")[0] || document.documentElement;
		target.appendChild(style);
	}

	function installTouchLink() {
		var slot = document.getElementById(AD_SLOT_ID);
		var link;

		if (!slot || slot.getElementsByClassName(AD_LINK_CLASS).length > 0) {
			return;
		}

		link = document.createElement("a");
		link.className = AD_LINK_CLASS;
		link.href = AD_LINK_URL;
		link.target = "_blank";
		link.rel = "noopener";
		link.setAttribute("aria-label", "UNIPAM");
		slot.appendChild(link);
	}

	installStyle();
	ready(installTouchLink);
}());
