<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        protected Appointment $appointment,
        protected ?string $previousStatus = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Appointment Status Updated')
            ->line('Your appointment status has been updated to: ' . $this->appointment->status)
            ->line('Doctor: ' . $this->appointment->doctor->user->name)
            ->line('Date: ' . $this->appointment->appointment_date->format('M d, Y'))
            ->line('Time: ' . $this->appointment->appointment_date->format('h:i A'))
            ->action('View Appointment', url('/appointments/' . $this->appointment->id));
    }

    public function getAppointment(): Appointment
    {
        return $this->appointment;
    }
}
