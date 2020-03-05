<?php

namespace App\Components\Entities;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class EventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    /** @var array|null $user */
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param array $event
     * @param array|null $user
     */
    public function __construct(array $event, array $user = null)
    {
        $this->event = $event;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if($this->event['anonymous']) {
            return $this->eventEmail();
        }
        /** @var \App\User $notifiable */
        return $notifiable->id === $this->event['created_by'] ? $this->emailToCreator() : $this->eventEmail();
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
            //
        ];
    }

    abstract public function eventEmail();

    abstract public function emailToCreator();
}
