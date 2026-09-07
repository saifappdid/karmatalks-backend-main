<?php

namespace App\Notifications;

use Firebase;
use Illuminate\Bus\Queueable;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;

class SendNotification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $firebase;
    public function __construct()
    {
        $this->firebase = app(Factory::class);
    }
    public function sendNotification(string $token, string $title, string $body, array $data = [], string $image = null)
    {

        $firebase = $this->firebase->withServiceAccount(base_path('/firebase.json'));
        $messaging = $firebase->createMessaging();

        $notificationPayload = [
            'title' => $title,
            'body' => $body,
        ];
        if ($image) {
            $notificationPayload['image'] = $image;
        }
        $notification = Notification::fromArray($notificationPayload);

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification)
            ->withHighestPossiblePriority('high');
        if ($data) {
            $message = $message->withData($data);
        }
        try {
            $messaging->send($message);
        } catch (\Throwable $th) {
            \Log::error('Firebase Notification Error: ' . $th->getMessage());
            return $th->getMessage();
        }
    }

    public function sendNotificationToTokens(array $deviceTokens, string $title, string $body, array $data = [], string $image = null)
    {
        $firebase = (new Factory)->withServiceAccount(base_path('/firebase.json'));
        $messaging = $firebase->createMessaging();

        $notificationPayload = [
            'title' => $title,
            'body' => $body,
        ];

        if ($image) {
            $notificationPayload['image'] = $image;
        }

        $notification = Notification::fromArray($notificationPayload);

        foreach ($deviceTokens as $token) {
            try {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification($notification)
                    ->withHighestPossiblePriority('high');

                if (!empty($data)) {
                    $message = $message->withData($data);
                }

                $messaging->send($message);
            } catch (\Throwable $e) {
                \Log::error("FCM token send failed for {$token}: " . $e->getMessage());
            }
        }
    }
}
