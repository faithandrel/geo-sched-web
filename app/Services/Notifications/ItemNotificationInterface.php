<?php 

namespace App\Services\Notifications;

use App\Models\Item;
use App\Models\User;

interface ItemNotificationInterface { //TODO: change name to Updatable

	public function __construct(Item $item, User $actor);

	public function checkExistingNotification($notifiable);

}