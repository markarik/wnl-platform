<?php namespace App\Http\Controllers\Api\PrivateApi\User;

use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Notification;
use League\Fractal\Resource\Collection;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\NotificationTransformer;
use League\Fractal\Resource\Item;
use Symfony\Component\HttpFoundation\Request;

class UserNotificationApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.user-notifications');
	}
	
	public function get($id)
	{
		$user = User::fetch($id);

		$notifications = $user->notifications;

		if (!$user || !$notifications) {
			return $this->respondNotFound();
		}

		if (Auth::user()->id !== $user->id) {
			return $this->respondUnauthorized();
		}

		$resource = new Collection($notifications, new NotificationTransformer, 'user_notifications');
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function patch(Request $request)
	{
		$user = User::fetch($request->route('id'));
		$notificationId = $request->route('notificationId');

		if (!$user) {
			return $this->respondNotFound();
		}

		if (Auth::user()->id !== $user->id) {
			return $this->respondUnauthorized();
		}

		$notification = Notification::where('notifiable_id', $user->id)
			->where('notifiable_type', 'App\\Models\\User')
			->where('id', $notificationId)
			->first();

		$notification->update([
			'read_at' => Carbon::now(),
			]);

		$resource = new Item($notification, new NotificationTransformer, 'user_notifications');
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}
}

