<?php

// app/Notifications/PusherNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Pusher\Pusher;

class PusherNotification extends Notification
{
    use Queueable;

    protected $data;
    protected $channel;
    protected $event;

    public function __construct($data, $channel, $event)
    {
        $this->data = $data;
        $this->channel = $channel;
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['pusher'];
    }

    public function toPusher($notifiable)
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

        $pusher->trigger($this->channel, $this->event, $this->data);
    }
}
