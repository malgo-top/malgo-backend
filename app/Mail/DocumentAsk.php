<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentAsk extends Mailable
{
    use Queueable, SerializesModels;

    public $fullName;
    public $link;
    public $customMessage; // Renamed to avoid conflict

    public function __construct($fullName, $link, $customMessage)
    {
        $this->fullName = $fullName;
        $this->link = $link;
        $this->customMessage = $customMessage;
    }

    public function build()
    {
        return $this->subject("Documentación Requerida para Evaluar Tu Solicitud de Arrendamiento")
                    ->view('emails.document_ask')
                    ->with([
                        'fullName' => $this->fullName,
                        'link' => $this->link,
                        'customMessage' => $this->customMessage, // Send to Blade as "message"
                    ]);
    }
}
