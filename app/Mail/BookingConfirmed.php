<?php

namespace App\Mail;

use App\Models\McuBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new message instance.
     */
    public function __construct(McuBooking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'MCU Booking Confirmed - ' . $this->booking->booking_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmed',
            with: [
                'booking' => $this->booking,
                'package' => $this->booking->package,
                'provider' => $this->booking->provider,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
