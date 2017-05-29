<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
		'App\Events\Qna\QuestionPosted' => [
			'App\Listeners\PushToUserNotificationChannel',
        ],
		'App\Events\Qna\AnswerPosted'   => [
			'App\Listeners\PushToUserNotificationChannel',
		],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
