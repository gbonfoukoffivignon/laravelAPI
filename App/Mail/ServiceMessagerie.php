<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ServiceMessagerie extends Mailable
{
    
use Queueable, SerializesModels;
public string $motDePasse;


/**
 * Create a new message instance.
 */
public function __construct(string $motDePasse)
{
    //
    $this->motDePasse = $motDePasse;
}

/**
 * Get the message envelope.
 */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Service Messagerie',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset-message',        
            with: [ 'motDePasse' => $this->motDePasse]
        
        );
    }
    
    /*
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}


