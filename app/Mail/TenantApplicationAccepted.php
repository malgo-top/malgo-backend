<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantApplicationAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public $fullName;

    public function __construct($fullName)
    {
        $this->fullName = $fullName;
    }

    public function build()
    {
        return $this->subject('¡Felicitaciones! Has sido seleccionado para el arriendo')
            ->view('emails.tenant_accepted')
            ->with([
                'fullName' => $this->fullName,
            ]);
    }

    // /**
    //  * Get the message envelope.
    //  */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Tenant Application Accepted',
    //     );
    // }

    // /**
    //  * Get the message content definition.
    //  */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    // /**
    //  * Get the attachments for the message.
    //  *
    //  * @return array<int, \Illuminate\Mail\Mailables\Attachment>
    //  */
    // public function attachments(): array
    // {
    //     return [];
    // }
}
