<?php

// app/Notifications/UserRegisteredNotification.php

namespace App\Notifications;

use App\Channels\PusherChannel;
use Illuminate\Notifications\Notification;
// use App\Channels\PusherChannel;

class UserRegisteredNotification extends Notification
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return [PusherChannel::class]; // Use the custom channel here
    }

    public function toPusher($notifiable)
    {
        return [
            'message' => 'Welcome ' . $this->user->name . ', you have successfully registered.',
        ];
    }

    public function event()
    {
        return 'UserRegistered'; // The event name for Pusher
    }
}
