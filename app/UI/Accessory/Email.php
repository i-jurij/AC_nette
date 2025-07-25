<?php

namespace App\UI\Accessory;

class Email
{
    public string $from = ''; // 'John <john@example.com>'
    public string $to = ''; // 'peter@example.com'
    public string $subject = '';
    public string $body = '';

    public function __construct()
    {
    }

    public function setEmail(): \Nette\Mail\Message
    {
        $mail = new \Nette\Mail\Message();

        return $mail->setFrom($this->from)
            ->addTo($this->to)
            ->setSubject($this->subject)
            ->setBody($this->body);
    }

    public function sendEmail(): void
    {
        $mailer = new \Nette\Mail\SendmailMailer();
        $mailer->send($this->setEmail());
    }
}
