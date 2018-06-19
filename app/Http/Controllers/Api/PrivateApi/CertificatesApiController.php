<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CertificatesApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.certificates');
	}

	public function getAvailableCertificates() {
		$user = Auth::user();

		$orders = $user->orders()
			->where('paid', 1)
			->get();

		$paidOrders = $orders->filter(function($order) {
			return $order->product->lessons()->count() > 0;
		});

		return $this->json(['orders' => $paidOrders->toArray()]);
	}

	public function getParticipationCertificate($orderId)
	{
		$user = Auth::user();

		$order = Order::where('id', $orderId)
			->first();

		if (!$order) {
			$this->respondNotFound("Order not found");
		}

		if (!$user->can('view', $order)) {
			return $this->respondForbidden("User not allowed to view order details");
		}

		$file = Storage::get('participation_certificate.jpg');
		$img = Image::make($file);

		$img->text($order->id, 1740, 915, function($font) {
			$fontFile = base_path('resources/fonts/Roboto_Mono/RobotoMono-Light.ttf');
			$font->file($fontFile);
			$font->size(48);
		});

		$img->text(
			sprintf("%s - %s r.",
				$order->product->course_start->format('j.m.Y'),
				$order->product->course_end->format('j.m.Y')
			), 1794, 1372, function($font) {
			$fontFile = base_path('resources/fonts/Rubik/Rubik-Light.ttf');
			$font->file($fontFile);
			$font->size(54);
		});

		$img->text($order->user->profile->fullName, 1754, 1150, function($font) {
			$fontFile = base_path('resources/fonts/Rubik/Rubik-Medium.ttf');
			$font->file($fontFile);
			$font->size(86);
			$font->align('center');
		});

		$img->text($order->product->course_start->format('j / m / Y') . ' r., Poznań', 2365, 1650, function($font) {
			$fontFile = base_path('resources/fonts/Rubik/Rubik-Medium.ttf');
			$font->file($fontFile);
			$font->size(61);
			$font->align('center');
		});

		$imgPath = "{$order->id}.jpg";
		Storage::put($imgPath, $img->encode('jpg')->__toString());
		return response()->download(Storage::path($imgPath))->deleteFileAfterSend(true);
	}
}
