<div id="fb-root"></div>
<script>
	window.fbAsyncInit = function () {
		FB.init({
			xfbml: true,
			version: 'v3.2'
		});
	};

	(function (d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s);
		js.id = id;
		js.src = 'https://connect.facebook.net/pl_PL/sdk/xfbml.customerchat.js';
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>

<div class="fb-customerchat"
		 attribution=setup_tool
		 page_id="1107802549311729"
		 theme_color="#0D9EA0"
		 logged_in_greeting="Cześ! Chętnie odpowiemy na Twoje pytania."
		 logged_out_greeting="Cześ! Chętnie odpowiemy na Twoje pytania.">
</div>
