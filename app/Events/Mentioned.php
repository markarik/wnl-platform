<?php namespace App\Events;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class Mentioned extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SerializesModels;

	/**
	 * @var User
	 */
	public $mentioned;

	/**
	 * @var
	 */
	public $actor;

	/**
	 * @var
	 */
	private $payload;

	/**
	 * Create a new event instance.
	 *
	 * @param $payload
	 */
	public function __construct(array $payload)
	{
		parent::__construct();
		$this->payload = $payload;
		$this->mentioned = $payload['mentioned_users'];
	}

	public function transform()
	{
		$actor = $this->payload['actor'];

		$this->data['event'] = 'mentioned';

		if (!empty($this->payload['context'])) {
			$this->data['context'] = $this->payload['context'];
		}

		$this->data['actors'] = [
			'id'           => $actor->id,
			'first_name'   => $actor->profile->first_name,
			'last_name'    => $actor->profile->last_name,
			'full_name'    => $actor->profile->full_name,
			'display_name' => $actor->profile->display_name,
			'avatar'       => $actor->profile->avatar_url,
		];

		$this->data['referer'] = $this->referer;

		if ($this->payload['subject']['type'] === 'chat_message') {
			$this->transformSubjectForChatMessage();
			$this->data['objects'] = $this->payload['objects'];
		} else {
			$this->transformSubject();
		}

	}

	protected function transformSubject()
	{
		$resourcePlural = str_plural($this->payload['subject']['type']);
		$resourceId = $this->payload['subject']['id'];
		$model = ApiController::getResourceModel($resourcePlural);
		$subject = $model::find($resourceId);
		$subjecType = snake_case(class_basename($subject));

		$this->data['subject'] = [
			'type' => $subjecType,
			'id'   => $subject->id,
			'text' => $subject->text ?? null,
		];
	}

	protected function transformSubjectForChatMessage()
	{
		$payloadSubject = $this->payload['subject'];
		$this->data['subject'] = $payloadSubject;
		$this->data['subject']['text'] = strip_tags($payloadSubject['text']) ?? null;
	}
}
