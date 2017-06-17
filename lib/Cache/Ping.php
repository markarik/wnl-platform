<?php


namespace Lib\Cache;

use App\Models\User;
use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;

class Ping
{
	use MakesHttpRequests,
		InteractsWithAuthentication,
		CreatesApplication;

	protected $app;

	public function __construct()
	{
		$this->app = $this->createApplication();
	}

	public function ping($url)
	{
		$user = User::find(2);
		$this
			->actingAs($user)
			->get($url);
	}
}