<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\NotificationTransformer;

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

		if (!$user->can('viewMultiple', Notification::class)) {
			return $this->respondForbidden();
		}

		$resource = new Collection($notifications, new NotificationTransformer, 'user_notifications');
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function query(Request $request)
	{
		$user = User::fetch($request->route('id'));

		if (!$user) {
			return $this->respondNotFound();
		}

		$notifications = $user->notifications();
		$notifications = $this->applyFilters($notifications, $request)->get();

		if (!$notifications) {
			return $this->respondNotFound();
		}

		if (!$user->can('viewMultiple', Notification::class)) {
			return $this->respondForbidden();
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

		$notification = Notification::where('notifiable_id', $user->id)
			->where('notifiable_type', 'App\\Models\\User')
			->where('id', $notificationId)
			->first();

		if (!$notification) {
			return $this->respondNotFound();
		}

		if (!$user->can('update', $notification)) {
			return $this->respondForbidden();
		}

		$notification->update($request->all());

		$resource = new Item($notification, new NotificationTransformer, 'user_notifications');
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function patchMany(Request $request)
	{
		$user = User::fetch($request->route('id'));
		$requestData = collect($request->all());

		$notificationsQuery = Notification::whereIn('id', $requestData->keys());
		$notifications = $notificationsQuery->get();

		if (!$user->can('updateMultiple', [ Notification::class, $notifications ])){
			return $this->respondForbidden();
		}

		foreach ($notifications as $notification) {
			$notification->update($requestData->get($notification->id));
		}

		$notifications = $notificationsQuery->get();
		$resource = new Collection($notifications, new NotificationTransformer, 'user_notifications');
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}
}
