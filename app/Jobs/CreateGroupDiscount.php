<?php

namespace App\Jobs;

use App\Mail\StudyGroup;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class CreateGroupDiscount implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	const SLUG_PATTERN = 'group-%s';
	const CODE_PATTERN = '%s-%s';

	private $mailCollection;
	private $couponAttributes;
	private $emailIsCode;

	/**
	 * Create a new job instance.
	 *
	 * @param Collection $mailCollection
	 * @param array $couponAttributes
	 * @param bool $emailIsCode
	 */
	public function __construct($mailCollection, $couponAttributes, $emailIsCode = true)
	{
		$this->mailCollection = $mailCollection;
		$this->couponAttributes = $couponAttributes;
		$this->emailIsCode = $emailIsCode;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$now = Carbon::now();
		$groupSign = strtoupper(str_random(4));
		$slug = sprintf(self::SLUG_PATTERN, $groupSign);

		foreach ($this->mailCollection as $email) {
			$code = $this->emailIsCode ? $email : $this->code($groupSign);

			$coupon = Coupon::create([
				'name' => $this->couponAttributes['name'],
				'type' => $this->couponAttributes['type'],
				'value' => $this->couponAttributes['value'],
				'code' => $code,
				'slug' => $slug,
				'created_at' => $now,
				'updated_at' => $now,
				'times_usable' => 1,
				'expires_at' => $this->couponAttributes['expires'],
				'coupon_type' => Coupon::TYPE_GROUP
			]);

			Mail::to($email)->send(new StudyGroup($coupon));
		}
	}

	private function code($groupSign)
	{
		return sprintf(self::CODE_PATTERN, $groupSign, strtoupper(str_random(4)));
	}
}
