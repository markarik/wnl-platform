<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\NotificationTransformer;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

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

	public function queryForUser(Request $request) {
		$user = User::fetch($request->route('id'));

		if (!$user) {
			return $this->respondNotFound();
		}

		$notificationsQuery = $user->notifications();

		$notificationsQuery->where('channel', $request->get('channel'));

		if ($request->has('unread')) {
			$operator = $request->get('unread') ? '=' : '<>';
			$notificationsQuery->where('read_at', $operator, null);
		}

		if ($request->has('older_than')) {
			$olderThan = Carbon::createFromTimestamp($request->get('older_than'));
			$notificationsQuery->where('created_at', '<', $olderThan);
		}

		if ($request->has('limit')) {
			$notificationsQuery->limit($request->get('limit'));
		}

		$notifications = $notificationsQuery->orderBy('created_at', 'desc')->get();

		return $this->transformAndRespond($notifications);
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
