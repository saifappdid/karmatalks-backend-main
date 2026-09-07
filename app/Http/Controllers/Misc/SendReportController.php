<?php

namespace App\Http\Controllers\Misc;

use App\Http\Controllers\Controller;
use App\Mail\SendReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendReportController extends Controller
{
    public function send(Request $request)
    {
        $from = $request->from;
        $error = $request->error;
        Mail::to('raiyan.appdid@gmail.com')->send(new SendReport($from, $error));
    }
}
