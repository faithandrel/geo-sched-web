<?php

namespace App\Services\Notifications;
use Illuminate\Notifications\Notifiable as LaravelNotifiable;

trait Notifiable
{
    use LaravelNotifiable;

    public function notifyClient(ItemNotificationInterface $instance) 
    {
    	if(!$instance->checkExistingNotification($this)) {
    		$this->notify($instance);
    	}
    }

    public function latestUnreadNotification()
    {
    	$notification = $this->unreadNotifications->sortByDesc('updated_at')->first();

    	return $notification;
    }
}
