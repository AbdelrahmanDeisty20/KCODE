<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BroadcastNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $notificationTitle;
    public string $notificationBody;
    public ?string $actionUrl;
    public string $actionText;
    public string $notificationType;

    public function __construct(
        string $title,
        string $body,
        ?string $actionUrl = null,
        ?string $actionText = null,
        string $type = 'general'
    ) {
        $this->notificationTitle = $title;
        $this->notificationBody  = $body;
        $this->actionUrl         = $actionUrl;
        $this->actionText        = $actionText ?: 'زيارة المتجر والتفاصيل ✨';
        $this->notificationType  = $type;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'KCODE | ' . $this->notificationTitle,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.broadcast_notification',
            with: [
                'title'      => $this->notificationTitle,
                'body'       => $this->notificationBody,
                'actionUrl'  => $this->actionUrl,
                'actionText' => $this->actionText,
                'type'       => $this->notificationType,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

