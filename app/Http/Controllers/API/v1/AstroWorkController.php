<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Services\WorkService;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;

class AstroWorkController extends Controller
{
    use ResponseTrait;
    public function getWorkTimes(Request $request, WorkService $workService)
    {
        $request->validate([
            'astro_id' => 'required|exists:astros,id',
            'date' => 'required|date',
            'price_id' => 'nullable|exists:astro_prices,id',
        ]);
        $data = $workService->getWorkTimes($request);
        return $this->success($data);
    }
    public function getInternationalWorkTimes(Request $request, WorkService $workService)
    {
        $request->validate([
            'astro_id' => 'required|exists:astros,id',
            'date' => 'required|date',
            'price_id' => 'nullable|exists:astro_prices,id',
        ]);
        $data = $workService->geInternationalWorkTimes($request);
        return $this->success($data);
    }
}
