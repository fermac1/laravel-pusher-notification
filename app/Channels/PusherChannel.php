<?php
// app/Channels/PusherChannel.php

namespace App\Channels;

use Pusher\Pusher;
use Illuminate\Notifications\Notification;

class PusherChannel
{
    public function send($notifiable, Notification $notification)
    {
        $options = [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,
        ];

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        // Get the data to send
        $data = $notification->toPusher($notifiable);

        // Trigger the Pusher event
        $pusher->trigger('user.' . $notifiable->id, $notification->event(), $data);
    }
}
