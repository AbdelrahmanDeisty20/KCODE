<?php

namespace App\Mail;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CouponMail extends Mailable
{
    use Queueable, SerializesModels;

    public Coupon $coupon;
    public ?User $user;

    public function __construct(Coupon $coupon, ?User $user = null)
    {
        $this->coupon = $coupon;
        $this->user   = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎁 كود خصم خاص بك من KCODE: {$this->coupon->code}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.coupon',
            with: [
                'coupon'   => $this->coupon,
                'userName' => $this->user?->name ?? 'عميلنا العزيز',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
