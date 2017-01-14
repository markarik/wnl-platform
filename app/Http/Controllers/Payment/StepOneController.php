<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StepOneController extends Controller
{
    public function index() {

        return view('payment.step1');
    }
}
