<?php

namespace App\Service;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

/**
 * Mailer Service to send emails
 */
class MailerService
{
    /**
     * Construct service & init dependencies
     *
     * @param MailerInterface $mailer
     */
    public function __construct(private MailerInterface $mailer)
    {
    }

    /**
     * Send email with params
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @param string $text
     *
     * @return void
     */
    public function sendEmail(string $to = '', string $subject = '', string $content = '', string $text = '',  $attachment = null, string $attachmentName = ''): void
    {
        $email = (new Email())
            ->to($to)
            ->subject($subject)
            ->text($text)
            ->html($content)
            ->attach($attachment ?? null, $attachmentName);

        $this->mailer->send($email);
    }
}
