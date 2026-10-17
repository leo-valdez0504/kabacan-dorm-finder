<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReservationRequest extends Notification
{
    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $r = $this->reservation->loadMissing(['student', 'room.property']);

        $mail = (new MailMessage)
            ->subject('New reservation request: '.$r->room->property->name)
            ->greeting('Hello '.$notifiable->name.',')
            ->line("{$r->student->name} requested a {$r->room->room_type} room at {$r->room->property->name}.")
            ->line('Move-in date: '.$r->move_in_date->format('M d, Y'))
            ->line('Student email: '.$r->student->email);

        if ($r->message) {
            $mail->line('Message: "'.$r->message.'"');
        }

        return $mail->action('Review request', url('/landlord/reservations'));
    }
}
