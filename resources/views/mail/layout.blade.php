<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style type="text/css">
		/* BETHINK MAIL STYLEGUIDE */
		html, body {
			color: #242d47;
			font-family: sans-serif;
			font-size: 14px;
			line-height: 1.7em;
		}

		h1 {
			font-size: 2rem;
		}
		h2 {
			font-size: 1.75rem;
		}
		h3 {
			font-size: 1.5rem;
		}
		h4 {
			font-size: 1.25rem;
		}

		a.button,
		a.button:visited,
		a.button:active {
			border: 1px solid #3f9fa7;
			border-radius: 100px;
			color: #3f9fa7;
			display: inline-block;
			font-size: 0.8em;
			font-weight: bold;
			padding: 5px 15px;
			text-transform: uppercase;
		}

		a.button:hover {
			border-color: #366063;
			color: #366063;
		}

		a,
		a:visited,
		a:active {
			color: #3f9fa7;
			text-decoration: none;
		}

		a:hover {
			color: #366063;
			text-decoration: none;
		}

		.has-text-centered {
			text-align: center;
		}
		.has-text-left {
			text-align: left;
		}
		.has-text-right {
			text-align: right;
		}

		.is-centered {
			margin-left: auto;
			margin-right: auto;
		}
	</style>
</head>
<body>
	<div style="padding: 0 20px; max-width: 650px; margin: 0 auto;">
		<div style="padding: 20px 0 5px;">
			<div style="text-align: center;">
				<img src="{{ asset('images/wnl-logo.svg') }}" alt="Logo Więcej niż LEK" style="height: 50px; max-width: 150px;">
			</div>
		</div>
		<div>
			@yield('content')
		</div>
		<p><em>Ekipa Więcej niż LEK</em></p>
		<div style="text-align: center; font-size: 10px; margin-top: 40px; padding: 15px 0; border-top: 1px solid #efefef;">
			<p>Ta wiadomość została wysłana na adres %recipient_email%<br>
			Jeżeli nie chcesz otrzymywać od nas wiadomości, <a href="{{ config('app.url') . '/app/myself/settings' }}">zmień ustawienia swojego konta</a>.</p>
			<p>&copy; by bethink sp. z o.o., Wszystkie prawa zastrzeżone<br>
			ul. Henryka Sienkiewicza 8/1, 60-817 Poznań<br>
			KRS: 0000668811, NIP: 781-194-37-56<br>
		</div>
	</div>
</body>
</html>
