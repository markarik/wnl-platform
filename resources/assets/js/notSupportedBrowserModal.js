(function () {
	var supportedBrowsers = {
			'chrome': 55,
			'firefox': 52,
			'ios': 10.1,
			'safari': 10,
			'chrome-ios': 55
		},
		detectedBrowser = parseUserAgent(window.navigator.userAgent),
		cookieName = 'wnl_supported_browser',
		cookieSet = getCookie(cookieName);

	if (!detectedBrowser) {
		return !cookieSet && showModal();
	}

	if (!supportedBrowsers[detectedBrowser.name] || supportedBrowsers[detectedBrowser.name] > detectedBrowser.version) {
		return !cookieSet && showModal();
	}

	function showModal() {
		var modal = document.getElementById('notSupportedBrowserModal'),
			background = document.getElementById('notSupportedBrowserModalBackground'),
			closeButton = document.getElementById('notSupportedBrowserModalClose');

		modal.classList.add('is-active');
		closeButton.addEventListener('click', function() {
			modal.classList.remove('is-active');
		});
		background.addEventListener('click', function() {
			modal.classList.remove('is-active');
		});
		setCookie(cookieName, true);
	}

	function parseUserAgent(userAgentString) {
		var browsers = supportedBrowsersRules();

		if (!userAgentString) {
			return {};
		}

		var detected = browsers.map(function (browser) {
			var match = browser.rule.exec(userAgentString);
			var version = match && match[1].split(/[._]/).slice(0, 3);

			if (version && version.length < 3) {
				version = version.concat(version.length === 1 ? [0, 0] : [0]);
			}

			return match && {
				name: browser.name,
				// only take the major version
				version: version[0]
			};
		}).filter(Boolean)[0] || {};

		return detected;
	}

	function supportedBrowsersRules() {
		return buildRules([
			['chrome', /(?!Chrom.*OPR)Chrom(?:e|ium)\/([0-9\.]+)(:?\s|$)/],
			['firefox', /Firefox\/([0-9\.]+)(?:\s|$)/],
			['ios', /Version\/([0-9\._]+).*Mobile.*Safari.*/],
			['safari', /Version\/([0-9\._]+).*Safari/],
			['chrome-ios', /CriOS\/([0-9\.]+).*Mobile.*Safari.*/]
		]);
	}

	function buildRules(ruleTuples) {
		return ruleTuples.map(function (tuple) {
			return {
				name: tuple[0],
				rule: tuple[1]
			};
		});
	}

	function setCookie(cname, cvalue, exdays) {
		var d = new Date(), expires;
		d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
		expires = 'expires='+d.toUTCString();
		document.cookie = cname + '=' + cvalue + ';' + expires + ';path=/';
	}

	function getCookie(cname) {
		var name = cname + '=';
		var ca = document.cookie.split(';');
		for(var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return '';
	}
}());
