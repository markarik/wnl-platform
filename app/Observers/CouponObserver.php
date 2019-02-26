<?php


namespace App\Observers;


use App\Jobs\SyncCouponUpdate;
use App\Models\Coupon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Bus\DispatchesJobs;


class CouponObserver
{
	use DispatchesJobs;

	public function created(Coupon $coupon)
	{
		\Log::debug("IM HERE....");
		if (empty(env('APP_COUPONS_SYNC_SOURCE'))) {
			return;
		}

		if (empty($coupon->studyBuddy)) {
			$couponToCreate = $coupon->toArray();
			unset($couponToCreate['id']);

			\Log::debug('COUPON SHOULD BE CREATED' . $coupon->id);

			$headers = [
				'Accept' => 'application/json',
				'Host' => env('APP_COUPONS_SYNC_HOST'),
//				'X-BETHINK-COUPON-SYNC-TOKEN' => env('APP_COUPONS_SYNC_TOKEN'),
			];

			$client = new Client();
			try {
				$client->request('POST', env('APP_COUPONS_SYNC_URL') . "/api/v1/coupons", [
					'headers' => $headers,
					'json' => [
						'coupon' => $couponToCreate
					]
				]);
			} catch (RequestException $exception) {
				$coupon->removeObservableEvents(['deleted']);
				$coupon->delete();
				\Log::debug('COUPON SHOULD BE DELETED' . $coupon->id);
			}
		}
	}

	public function updated(Coupon $coupon) {
		if (empty($coupon->studyBuddy)) {
			$couponToUpdate = $coupon->toArray();
			unset($couponToUpdate['id']);

			$this->dispatch(new SyncCouponUpdate($couponToUpdate));
		}
	}

	public function deleted(Coupon $coupon) {
		\Log::debug('in deleted observable');
		if (empty(env('APP_COUPONS_SYNC_SOURCE'))) {
			return;
		}

		if (empty($coupon->studyBuddy)) {
			$couponToCreate = $coupon->toArray();
			unset($couponToCreate['id']);

			$headers = [
				'Accept' => 'application/json',
				'Host' => env('APP_COUPONS_SYNC_HOST'),
				'X-BETHINK-COUPON-SYNC-TOKEN' => env('APP_COUPONS_SYNC_TOKEN'),
			];

			$client = new Client();
			$client->request('DELETE', env('APP_COUPONS_SYNC_URL') . "/api/v1/coupons/{$coupon->code}", [
				'headers' => $headers,
			]);
		}
	}
}
