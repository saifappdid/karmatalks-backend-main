<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Promocode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;

class PromocodeController extends Controller
{
    use ResponseTrait;
    public function getPromocodes(Request $request)
    {
        $user = auth('sanctum')->user();
        $promocodes = Promocode::where(['status' => 'active'])
            ->whereDate('start', '<=', now())
            ->whereDate('end', '>=', now()->endOfDay())
            ->get();
        return $this->success($promocodes, 'Promocodes retrieved successfully');
    }
    public function validatePromocode(Request $request)
    {
        $request->validate([
            'promocode' => 'required|string',
        ]);
        $promocode = Promocode::whereDate('end', '>=', date('Y-m-d'))
            ->where('status', 'active')
            ->where('code', $request->promocode)
            ->first();
        if ($promocode) {
            return $this->success($promocode, 'Valid promocode');
        } else {
            return $this->error('Invalid promocode', 422);
        }
    }
}
