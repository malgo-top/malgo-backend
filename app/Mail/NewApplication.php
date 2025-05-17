<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewApplication extends Mailable
{
    use Queueable, SerializesModels;

    public $property;

    public function __construct($property)
    {
        $this->property = $property;
    }

    public function build()
    {
        return $this->subject("Nueva Aplicacion Recibida - Malgo")
                    ->view('emails.new_application');
    }

   
}
