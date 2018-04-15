<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Models\Item;
use App\Models\User;

class ViewCount extends Notification
{
    use Queueable;

    private $item, $counter;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Item $item, $counter)
    {
        $this->item    = $item;
        $this->counter = $counter;
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
            'counter'    => $this->counter,
            'text'       => $this->getActionText($notifiable)
        ];
    }

    /**
     * Returns text for the notification 
     * @return string
     */
    public function getActionText() 
    {   
        return 'Your story now has '.$this->counter.' views';
    }
}
