<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailController extends AbstractController{

    
    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }

    public function sendEmail($receiver, $sender, $subject, $contnent)
    {
        /* Akcja wysyłania maila bez docelowej konfiguracji

        $email = (new Email())
            ->from($receiver)
            ->to($sender)
            ->subject($subject)
            ->html($contnent);

        $this->mailer->send($email);

        return Response('Email sent');
        */
    }
}