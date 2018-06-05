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
			font-size: 8pt;
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
			padding: 0 15px 50px;
			width: 50%;
		}

		.has-text-right {
			text-align: right;
		}

		.has-text-centered {
			text-align: center;
		}

		table {
			border-spacing: 0;
			border-collapse: collapse;
			margin: 0 0 30px;
			width: 100%;
		}

		th, td {
			border: 1px solid #efefef;
			padding: 15px;
		}

		th {
			background: #efefef;
		}

		.no-border {
			border: none;
		}
	</style>
</head>
<body>
	<div class="columns">
		{{-- Invoice data --}}
		<div class="column">
			@yield('invoice-data')
		</div>

		{{-- Logo --}}
		<div class="column has-text-right">
			<img src="{{ asset('/images/wnl-logo@2x.png') }}" alt="Logo Więcej niż LEK" style="width: 400px;">
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
				NIP: 781-194-37-56<br>
				REGON: 366-802-351
			</address>
		</div>

		{{-- Buyer --}}
		<div class="column has-text-right">
			<strong>Nabywca</strong>
			<address>
				@yield('buyer')
			</address>
		</div>
	</div>

	<div class="columns">
		<div class="columns has-text-centered">
			<h2>PRZED KOREKTĄ</h2>
		</div>
	</div>

	{{-- Orders --}}
	<div class="columns">
		<h3>@yield('before-orders-title', '')</h3>
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
			@yield('before-orders-list')
		</table>
	</div>

	<table>
		<tr class="no-border">
			<td style="width: 50%;" class="no-border">
				{{-- Orders summary --}}
				@yield('settlement', '')
			</td>
			<td style="width: 50%;" class="no-border">
				<div class="has-text-right">
					@yield('summary')
				</div>
			</td>
		</tr>
		{{--<tr class="no-border">
			<td style="width: 50%; vertical-align: top;" class="no-border">
				 Advances
				@yield('advances', '')
			</td>
			<td style="width: 50%; vertical-align: top;" class="no-border">
				 Settlement
				@yield('settlement', '')
			</td>
		</tr>--}}
	</table>
	<div style="page-break-after: always;"></div>
	<div class="columns">
		<div class="columns has-text-centered">
			<h2>PO KOREKCIE</h2>
		</div>
	</div>

	<div class="columns">
		<h3>@yield('after-orders-title', '')</h3>
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
			@yield('after-orders-list')
		</table>
	</div>

	<table>
		<tr class="no-border">
			<td style="width: 50%;" class="no-border">
				@yield('afterCorrectionSettlement')
			</td>
			<td style="width: 50%;" class="no-border">
				<div class="has-text-right">
					@yield('afterCorrectionSummary')
				</div>
			</td>
		</tr>
	</table>

	<table>
		<tr class="no-border">
			<td style="width: 50%;" class="no-border">
				@yield('taxDifference')
			</td>
			<td style="width: 50%;" class="no-border">
				<div class="has-text-right">

				</div>
			</td>
		</tr>
	</table>

	@yield('final', '')
	<div class="column">
		<p><strong>Uwagi:</strong></p>
		@yield('notes', '')
	</div>
</body>
</html>
