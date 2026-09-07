<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AstroTimeReportController extends Controller
{
    public function index()
    {
        return view('content.reports.astro_time_report.index');
    }
}
