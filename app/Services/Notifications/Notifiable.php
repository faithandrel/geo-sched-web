<?php

namespace App\Services\Notifications;
use Illuminate\Notifications\Notifiable as LaravelNotifiable;

trait Notifiable
{
    use LaravelNotifiable;

    public function notifyClient(ItemNotificationInterface $instance) 
    {
    	if($instance->processNotification($this)) {
    		$this->notify($instance);
    	}
    }
}
