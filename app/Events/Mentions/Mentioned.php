<?php namespace App\Events\Mentions;

use App\Events\Event;
use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Mentioned extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SerializesModels;

	/**
	 * @var array $mentioned
	 */
	public $mentioned;

	/**
	 * @var User $actor
	 */
	public $actor;

	/**
	 * @var array $payload
	 */
	private $payload;

	/**
	 * Create a new event instance.
	 *
	 * @param array $payload
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
