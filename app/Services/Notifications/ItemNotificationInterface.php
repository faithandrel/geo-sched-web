<?php 

namespace App\Services\Notifications;

interface ItemNotificationInterface {

	public function processNotification($notifiable);

}