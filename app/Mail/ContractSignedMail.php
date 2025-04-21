<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractSignedMail extends Mailable
{
    use Queueable, SerializesModels;


    public $fullName;
    public $email;
    public $password;

    public function __construct($fullName, $email, $password)
    {
        $this->fullName = $fullName;
        $this->email = $email;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Felicitaciones, su contrato ha sido firmado')
            ->view('emails.contract_signed')
            ->with([
                'fullName' => $this->fullName,
                'email' => $this->email,
                'password' => $this->password,
                'loginUrl' => url('/login'), // Or your system's login URL
            ]);
    }

    // /**
    //  * Get the message envelope.
    //  */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Contract Signed Mail',
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
