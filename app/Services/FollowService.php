<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\FileUploader;

class FollowService
{

    public function toggleFollow($astroId, $userId)
    {
        $user = User::with(['followedAstros'])->findOrFail($userId);
        $isFollowing = $user->followedAstros()->where('astro_id', $astroId)->exists();

        if ($isFollowing) {
            // Unfollow
            $user->followedAstros()->detach($astroId);
            return ['message' => 'Unfollowed successfully', 'following' => false];
        } else {
            // Follow
            $user->followedAstros()->attach($astroId);
            return ['message' => 'Followed successfully', 'following' => true];
        }
    }
}
