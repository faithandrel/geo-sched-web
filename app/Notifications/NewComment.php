<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Services\Notifications\ItemNotificationInterface;

class NewComment extends Notification implements ItemNotificationInterface
{
    use Queueable;

    private $item, $actor;

    private $text    = null;
    private $counter = 1;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($item, $actor)
    {
        $this->item  = $item;
        $this->actor = $actor;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable     
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'item_id'    => $this->item->id,
            'actor_id'   => $this->actor->id,
            'actor_name' => $this->actor->name,
            'counter'    => $this->counter,
            'text'       => $this->counter.' '.$this->getActionText($notifiable).' '.$this->actor->name
        ];
    }


    public function getActionText($notifiable) 
    {   
        $commentText = 'comments';

        if($this->counter == 1) {
            $commentText = 'comment';
        }

        if(is_null($this->text) && $notifiable->id == $this->item->user_id) {
            return 'new '.$commentText.' on your story from';
        } 
        return 'new '.$commentText.' on a story you are following from';
    }

    public function getActorIds($actor_id_string)
    {
        $actor_ids   = explode('|', $actor_id_string);
        $actor_ids[] = $this->actor->id;

        return implode('|', array_unique($actor_ids));
    }

    public function findNotificationsForItem($notifiable) 
    {
        $unreadNotifications = $notifiable->unreadNotifications->all();
        $item_id             = $this->item->id;
        $notifiable_id       = $notifiable->id;
        $notification_type    = static::class;

        $processUnreadNotifications = function($notification) 
                                     use ($item_id, $notifiable_id, $notification_type) {
            if(isset($notification->data['item_id']) &&
               $notification->data['item_id'] == $item_id && 
               $notification->notifiable_id   == $notifiable_id &&
               $notification->type            == $notification_type) {
                    return true;
            }
            return false;
        };

        return array_values(array_filter($unreadNotifications, $processUnreadNotifications));
    }

    public function processNotification($notifiable) 
    {
        $existingNotifications = $this->findNotificationsForItem($notifiable);

        //No existing notif, create new
        if(count($existingNotifications) < 1) {
            return true;
        }
        
        $existingNotification = $existingNotifications[0];

        $this->counter = $existingNotification->data['counter'] + 1;

        $data = $this->toArray($notifiable);

        $data['actor_id'] = $this->getActorIds($existingNotification->data['actor_id']);
        
        if($data['counter'] == 2 && $this->actor->id != $existingNotification->data['actor_id']) {
            $data['text'] .= ' and '.$existingNotification->data['actor_name'];
        }
        else if (strpos($data['actor_id'], '|') != false) {
            $data['text'] .= ' and more';
        }

        //Update existing notif
        $existingNotification->data = $data;
        $existingNotification->save();

        return false;
    }
}
