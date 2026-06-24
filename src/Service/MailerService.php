<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendWelcomeEmail(string $email, string $nom): void
    {
        $email = (new Email())
            ->from('noreply@watchshop.com')
            ->to($email)
            ->subject('Bienvenue sur WatchShop !')
            ->html("<h1>Bienvenue {$nom} !</h1><p>Merci de vous être inscrit sur WatchShop.</p>");

        $this->mailer->send($email);
    }

    public function sendOrderConfirmation(string $email, string $nom, int $commandeId, float $total): void
    {
        $email = (new Email())
            ->from('noreply@watchshop.com')
            ->to($email)
            ->subject("Confirmation de votre commande #{$commandeId}")
            ->html("
                <h1>Merci pour votre commande {$nom} !</h1>
                <p>Votre commande #{$commandeId} a bien été enregistrée.</p>
                <p>Total: {$total} €</p>
                <p>Vous serez notifié de l'expédition prochainement.</p>
            ");

        $this->mailer->send($email);
    }
}
