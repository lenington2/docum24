<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class DocumentiCondivisi extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $oggetto,
        public string $descrizione,
        public string $senderName,
        public string $zipPath,
        public string $zipName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->oggetto);
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->buildHtml(),
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->zipPath)
                ->as($this->zipName)
                ->withMime('application/zip'),
        ];
    }

    private function buildHtml(): string
    {
        $desc = nl2br(e($this->descrizione));
        $name = e($this->senderName);

        return <<<HTML
        <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="background: #F53003; color: white; padding: 16px 24px; border-radius: 12px 12px 0 0;">
                <h2 style="margin: 0; font-size: 18px;">📎 Documenti condivisi</h2>
            </div>
            <div style="background: #f9fafb; padding: 24px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 12px 12px;">
                <p style="color: #374151; margin: 0 0 16px;"><strong>{$name}</strong> ti ha inviato dei documenti:</p>
                <div style="background: white; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 16px;">
                    {$desc}
                </div>
                <p style="color: #6b7280; font-size: 13px; margin: 0;">I documenti sono allegati come file ZIP.</p>
            </div>
            <p style="color: #9ca3af; font-size: 11px; text-align: center; margin-top: 16px;">Inviato tramite Docum24</p>
        </div>
        HTML;
    }
}
