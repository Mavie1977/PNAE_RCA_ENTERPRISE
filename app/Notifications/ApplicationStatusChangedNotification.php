<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Application $application,
        private readonly string $oldStatus,
        private readonly string $newStatus,
        private readonly ?string $comment = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (
            filled($notifiable->email)
            && config('mail.default') !== 'log'
        ) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'application_status_changed',
            'title' => 'Mise à jour de votre demande',
            'message' => sprintf(
                'La demande %s est passée du statut « %s » au statut « %s ».',
                $this->application->reference,
                $this->statusLabel($this->oldStatus),
                $this->statusLabel($this->newStatus),
            ),
            'application_id' => $this->application->id,
            'reference' => $this->application->reference,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'comment' => $this->comment,
            'url' => route(
                'citizen.applications.show',
                $this->application,
            ),
            'created_at' => now()->toIso8601String(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(
                'Mise à jour de la demande ' .
                $this->application->reference
            )
            ->greeting(
                'Bonjour ' . ($notifiable->name ?? '')
            )
            ->line(
                sprintf(
                    'Votre demande %s est maintenant au statut « %s ».',
                    $this->application->reference,
                    $this->statusLabel($this->newStatus),
                )
            )
            ->when(
                filled($this->comment),
                fn (MailMessage $message) => $message->line(
                    'Observation : ' . $this->comment
                )
            )
            ->action(
                'Consulter ma demande',
                route(
                    'citizen.applications.show',
                    $this->application,
                )
            )
            ->line(
                'Plateforme Nationale d’Administration Électronique.'
            );
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'soumise' => 'Soumise',
            'en_attente' => 'En attente',
            'en_traitement' => 'En traitement',
            'validee' => 'Validée',
            'rejetee' => 'Rejetée',
            'terminee' => 'Terminée',
            default => ucfirst(
                str_replace('_', ' ', $status)
            ),
        };
    }
}