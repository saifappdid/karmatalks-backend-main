<?php

namespace App\Services;

use App\Helpers\Agora\RtcTokenBuilder;

class AgoraService
{

    public function getAgoraToken($channelName)
    {
        $appID = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');
        $channelName = $channelName;
        $uid = rand(1, 999999);

        $expireTimeInSeconds = 3600;
        $privilegeExpiredTs = now()->timestamp + $expireTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUid(
            $appID,
            $appCertificate,
            $channelName,
            $uid,
            1,
            $privilegeExpiredTs
        );
        $data = [
            'token' => $token,
            'channel_name' => $channelName,
            'uid' => $uid,
            'expire_at' => $privilegeExpiredTs,
        ];
        return $data;
    }
}
