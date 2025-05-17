<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class newDocument extends Mailable
{
    use Queueable, SerializesModels;

    public $property;

    public function __construct($property)
    {
        $this->property = $property;
    }

    public function build()
    {
        return $this->subject("Nuevo Documento Requerido Recibido")
                    ->view('emails.new_document');
    }

   
}
