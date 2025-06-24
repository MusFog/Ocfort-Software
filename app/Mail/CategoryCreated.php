<?php

namespace App\Mail;

use App\DTOs\ReportData;
use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CategoryCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $category;

    /**
     * Create a new message instance.
     */
    public function __construct($user, string $category)
    {
        $this->user = $user;
        $this->category = $category;
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.category.notification',
            with: [
                'name' => $this->user->name,
                'category' => $this->category
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
