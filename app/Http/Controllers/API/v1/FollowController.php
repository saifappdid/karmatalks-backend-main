<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Astro;
use App\Services\FollowService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    use ResponseTrait;
    public function toggleFollow(Request $request, FollowService $followService)
    {
        $request->validate([
            'astro_id' => 'required|exists:astros,id',
        ]);

        $user = $request->user();
        $astroId = $request->astro_id;
        $order = $followService->toggleFollow($astroId, $user->id);
        return $this->success($order, $order['message']);
    }

    public function getFollowers(Request $request)
    {
        $request->validate([
            'astro_id' => 'required|exists:astros,id',
        ]);
        $astro = Astro::findOrFail($request->astro_id);
        $followers = $astro->followers()->with('user')->get()->map(function ($follower) {
            return [
                'id' => $follower->id,
                'user_id' => $follower->user_id,
                'user_name' => $follower->user ? $follower->user->name : 'N/A',
                'followed_at' => $follower->created_at ? $follower->created_at->toDateTimeString() : 'N/A',
            ];
        });
        return $this->success($followers, 'Followers retrieved successfully');
    }
}
