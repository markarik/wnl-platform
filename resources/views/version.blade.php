@extends('layouts.guest')

@section('content')

	<div class="container">
		<section class="section">
			<div class="columns">
				<div class="is-half column">
					<h5><?= config('app.name') ?></h5>
					<table class="table is-striped">
						<tr>
							<td>Version:</td>
							<td><?= config('app.version') ?></td>
						</tr>
						<tr>
							<td>Laravel framework version:</td>
							<td><?= $laravel::VERSION ?></td>
						</tr>
						<tr>
							<td>Environment:</td>
							<td><?= env('APP_ENV') ?></td>
						</tr>
						<tr>
							<td>App URL:</td>
							<td><?= env('APP_URL') ?></td>
						</tr>
					</table>
				</div>
			</div>
		</section>
	</div>
@endsection
