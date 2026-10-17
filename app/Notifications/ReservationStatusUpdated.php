<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationStatusUpdated extends Notification
{
    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $r = $this->reservation->loadMissing(['room.property.landlord']);
        $approved = $r->status === 'approved';
        $property = $r->room->property;

        $mail = (new MailMessage)
            ->subject('Your reservation was '.$r->status.': '.$property->name)
            ->greeting('Hello '.$notifiable->name.',')
            ->line($approved
                ? "Good news! Your request for a {$r->room->room_type} room at {$property->name} was approved."
                : "Sorry, your request for a {$r->room->room_type} room at {$property->name} was declined.")
            ->line('Move-in date: '.$r->move_in_date->format('M d, Y'));

        if ($approved) {
            $mail->line('Landlord: '.$property->landlord->name.($property->landlord->phone ? ' · '.$property->landlord->phone : ''));
        }

        return $mail->action('View my reservations', url('/student/reservations'));
    }
}
