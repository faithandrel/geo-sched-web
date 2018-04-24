<?php 

namespace App\Services\Notifications;

use FCM;
use Log;
use Carbon\Carbon;
use LaravelFCM\Message\PayloadNotificationBuilder;
use App\Models\User;
use App\Models\Item;

class FCMNotifier {

	public static function notifyUsers() {
		$instance = new static;
		$allUsers = User::all();

		foreach ($allUsers as $user) {
			//Log::info($user->name.' '.json_encode($instance::checkLastActive($user)));
			if( $instance::checkLastActive($user) &&
				$instance::checkLastNotified($user) && 
				!is_null($user->device_token) ) {
					$instance::notifyUser($user);
					Log::info($user->name);
				//Log::info($result);
			}
		}
	}

	public static function notifyUser($user) {
		$instance = new static;

		$notification = $user->latestUnreadNotification();
		//TODO: error handling for send
		$result = $instance::send($notification, $user->device_token);

		$user->last_notified = Carbon::now();
		$user->save();
	}

	public static function send($notification, $token) {
		$body = Item::find($notification->data['item_id'])->title;

		$notificationBuilder = new PayloadNotificationBuilder();
  		$notificationBuilder->setTitle($notification->data['text'])
                  ->setBody($body)
                  ->setSound('default')
                  ->setTag($notification->id);

        $notification = $notificationBuilder->build();

		$result = FCM::sendTo($token, null, $notification);

		return $result;
	}

	public static function checkLastActive($user) {
		$now 		= Carbon::now();
		$lastActive = $user->last_active;

		$minutesPassed = $now->diffInMinutes($lastActive);

		return $minutesPassed > config('fcm.lastActiveMinutes') ? true : false;
	}

	public static function checkLastNotified($user) {
		$now 		  = Carbon::now();
		$lastNotified = $user->last_notified;

		if(is_null($lastNotified)) {
			return true;
		}

		$minutesPassed = $now->diffInMinutes($lastNotified);

		return $minutesPassed > config('fcm.lastNotifiedMinutes') ? true : false;
	}

}