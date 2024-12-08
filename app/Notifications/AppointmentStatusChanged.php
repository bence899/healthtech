<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentStatusChanged extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private Appointment $appointment,
        private string $previousStatus
    ) {}

    /**
     * Get the appointment.
     */
    public function getAppointment(): Appointment
    {
        return $this->appointment;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusChange = ucfirst($this->previousStatus) . ' → ' . ucfirst($this->appointment->status);
        
        return (new MailMessage)
            ->subject('Appointment Status Updated')
            ->line("Your appointment status has been updated: {$statusChange}")
            ->line("Appointment Details:")
            ->line("Doctor: " . $this->appointment->doctor->user->name)
            ->line("Date: " . $this->appointment->appointment_date->format('M d, Y h:i A'))
            ->line("Reason: " . $this->appointment->reason_for_visit);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
