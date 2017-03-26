<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>@yield('title')</title>

	<!-- Styles -->
	<style>
		body {
			color: #242d47;
			font-family: 'DejaVu Sans', sans-serif;
		}

		.columns {
			padding: 20px;
		}

		.columns:after {
			content: "";
			display: table;
			clear: both;
		}

		.column {
			box-sizing: border-box;
			float: left;
			padding: 0 15px;
			width: 50%;
		}

		table {
			border-spacing: 0;
			border-collapse: collapse;
			margin: 10px 0;
			width: 100%;
		}

		th, td {
			border: 1px solid #efefef;
			padding: 5px;
		}

		th {
			background: #efefef;
		}

		td.hidden {
			border: 0;
		}
	</style>
</head>
<body>
	<div class="columns">
		{{-- Logo --}}
		<div class="column">
			<div style="width: 200px;">
				<img src="{{ asset('/images/wnl-logo@2x.png') }}" alt="Logo Więcej niż LEK">
			</div>
		</div>

		{{-- Identifier --}}
		<div class="column">
			@yield('invoice-data')
		</div>
	</div>

	<div class="columns">
		{{-- Seller --}}
		<div class="column">
			<strong>Sprzedawca</strong>
			<address>
				bethink sp. z o.o.<br>
				ul. Henryka Sienkiewicza 8/1<br>
				60-817 Poznań<br>
				NIP: 7811943756<br>
				KRS: 0000668811
			</address>
		</div>

		{{-- Buyer --}}
		<div class="column">
			<strong>Nabywca</strong>
			<address>
				@yield('buyer')
			</address>
		</div>
	</div>

	{{-- Orders --}}
	<div class="columns">
		<h3>@yield('orders-title', '')</h3>
		<table>
			<tr>
				<th>Lp</th>
				<th>Nazwa</th>
				<th>Jedn.</th>
				<th>Ilość</th>
				<th>Cena brutto</th>
				<th>Stawka VAT</th>
				<th>Wartość netto</th>
				<th>Wartość brutto</th>
			</tr>
			@yield('orders-list')
		</table>
	</div>

	{{-- Orders summary --}}
	<div class="columns">
		<div class="column pull-right">
			@yield('orders-summary', '')
		</div>
	</div>

	<div class="columns">
		{{-- Advances --}}
		<div class="column">
			@yield('advances', '')
		</div>

		{{-- Settlement --}}
		<div class="column">
			@yield('settlement', '')
		</div>
	</div>

	<div class="columns">
		{{-- Payment details --}}
		<div class="column">
			@yield('payment-details', '')

			<p><strong>Uwagi:</strong></p>
			@yield('notes', '')
		</div>

		{{-- Summary --}}
		<div class="column">
			@yield('summary')
		</div>
	</div>
</body>
</html>
