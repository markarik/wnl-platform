DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>@yield('title')</title>
</head>
<body>
	<div class="columns">
		{{-- Logo --}}
		<div class="column">
			<div style="width: 300px;">
				<img src="{{ asset('/images/wnl-logo.svg') }}" alt="Logo Więcej niż LEK">
			</div>
		</div>

		{{-- Identifier --}}
		<div class="column">
			@yield('identifier')
		</div>
	</div>

	<div class="columns">
		{{-- Seller --}}
		<div class="column">
			<strong>bethink sp. z o.o.</strong><br>
			ul. Henryka Sienkiewicza 8/1<br>
			60-817 Poznań<br>
			NIP: 7811943756<br>
			KRS: 0000668811
		</div>

		{{-- Buyer --}}
		<div class="column">
			@yield('buyer')
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
