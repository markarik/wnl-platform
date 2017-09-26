(function () {
	var modal = document.getElementById('notSupportedBrowserModal'),
		supportedBrowsers = {
			'chrome': 55,
			'firefox': 52,
			'ios': 10,
			'safari': 10
		},
		detectedBrowser = parseUserAgent(window.navigator.userAgent),
		closeButton = document.getElementById('notSupportedBrowserModalClose');

	if (!detectedBrowser) {
		modal.classList.add('is-active')
		closeButton.addEventListener('click', function() {
			modal.classList.remove('is-active')
		});

		return;
	}

	if (!supportedBrowsers[detectedBrowser.name] || supportedBrowsers[detectedBrowser.name] > detectedBrowser.version) {
		modal.classList.add('is-active')
		closeButton.addEventListener('click', function() {
			modal.classList.remove('is-active');
		});
	}

	function parseUserAgent(userAgentString) {
		var browsers = supportedBrowsersRules();

		if (!userAgentString) {
			return null;
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
		}).filter(Boolean)[0] || null;

		return detected;
	}

	function supportedBrowsersRules() {
		return buildRules([
			['chrome', /(?!Chrom.*OPR)Chrom(?:e|ium)\/([0-9\.]+)(:?\s|$)/],
			['firefox', /Firefox\/([0-9\.]+)(?:\s|$)/],
			['ios', /Version\/([0-9\._]+).*Mobile.*Safari.*/],
			['safari', /Version\/([0-9\._]+).*Safari/]
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
}())
