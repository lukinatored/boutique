<?php
namespace App\Service;

use App\Entity\Commande;

class ChatVendeurService
{
    private $reponses = [];

    public function __construct()
    {
        $this->reponses = [
            'bonjour' => [
                'reponse' => 'Bonjour ! Comment puis-je vous aider ?',
                'keywords' => ['bonjour', 'salut', 'coucou', 'hello']
            ],
            'livraison' => [
                'reponse' => 'Votre commande est en cours de préparation. Elle sera expédiée sous 2-3 jours ouvrables.',
                'keywords' => ['livraison', 'délai', 'transport', 'colis', 'expédition']
            ],
            'retour' => [
                'reponse' => 'Vous pouvez retourner votre article sous 30 jours. Contactez notre service client pour plus d\'informations.',
                'keywords' => ['retour', 'remboursement', 'annulation', 'échange']
            ],
            'stock' => [
                'reponse' => 'Le stock est mis à jour en temps réel sur notre site. Si un produit est indisponible, nous vous invitons à vous inscrire aux alertes.',
                'keywords' => ['stock', 'disponibilité', 'rupture', 'approvisionnement']
            ],
            'prix' => [
                'reponse' => 'Nos prix sont les plus compétitifs du marché. Nous proposons également des promotions régulières sur notre site.',
                'keywords' => ['prix', 'tarif', 'réduction', 'promo', 'code']
            ],
            'commande' => [
                'reponse' => 'Je peux vous aider avec votre commande. Donnez-moi le numéro de commande pour plus de détails.',
                'keywords' => ['commande', 'achat', 'panier', 'paiement']
            ],
            'garantie' => [
                'reponse' => 'Tous nos produits sont garantis 2 ans. Cette garantie couvre les défauts de fabrication.',
                'keywords' => ['garantie', 'sav', 'réparation', 'défaut']
            ],
            'contact' => [
                'reponse' => 'Vous pouvez nous contacter par email à contact@watchshop.com ou par téléphone au +33 1 23 45 67 89.',
                'keywords' => ['contact', 'email', 'téléphone', 'appeler']
            ],
            'merci' => [
                'reponse' => 'Avec plaisir ! N\'hésitez pas si vous avez d\'autres questions. 😊',
                'keywords' => ['merci', 'merci beaucoup', 'thanks']
            ],
            'default' => [
                'reponse' => 'Je suis là pour vous aider ! Pourriez-vous préciser votre demande ?'
            ]
        ];
    }

    public function getReponse(string $message, Commande $commande): array
    {
        $message = strtolower(trim($message));
        
        // Vérifier si un numéro de commande est mentionné
        if (preg_match('/#?(\d{2,5})/', $message, $matches)) {
            return [
                'reponse' => "Je vois que vous parlez de la commande #{$matches[1]}. Son statut actuel est : **{$commande->getStatut()}**.",
                'type' => 'info'
            ];
        }

        // Vérifier le statut de la commande
        if (strpos($message, 'statut') !== false || strpos($message, 'où en est') !== false) {
            return [
                'reponse' => "Votre commande #{$commande->getId()} est actuellement **{$commande->getStatut()}**.",
                'type' => 'info'
            ];
        }

        // Parcourir les réponses pour trouver une correspondance
        foreach ($this->reponses as $key => $data) {
            if ($key === 'default') continue;
            
            foreach ($data['keywords'] as $keyword) {
                if (strpos($message, $keyword) !== false) {
                    return [
                        'reponse' => $data['reponse'],
                        'type' => 'success'
                    ];
                }
            }
        }

        // Réponse par défaut
        return [
            'reponse' => $this->reponses['default']['reponse'],
            'type' => 'info'
        ];
    }

    public function getReponseAleatoire(string $message, Commande $commande): array
    {
        // Réponses aléatoires pour simuler une vraie conversation
        $reponsesAleatoires = [
            "Merci pour votre message ! Je transmets votre demande au service concerné.",
            "Je comprends votre demande. Nous allons faire le nécessaire rapidement.",
            "Votre satisfaction est notre priorité. Nous revenons vers vous sous 24h.",
            "Je prends note de votre demande. Un conseiller vous contactera.",
            "Nous avons bien reçu votre message. Notre équipe va vous répondre très vite.",
            "Je vous remercie pour votre confiance. Nous traitons votre demande en priorité.",
            "Parfait ! Je vous tiendrai informé dès que possible.",
            "C'est noté ! Nous allons étudier votre demande avec attention."
        ];

        // Si le message est court, réponse plus simple
        if (strlen($message) < 10) {
            return [
                'reponse' => $reponsesAleatoires[array_rand($reponsesAleatoires)],
                'type' => 'info'
            ];
        }

        // Détecter le ton du message
        if (strpos($message, '!') !== false || strpos($message, '?') !== false) {
            return [
                'reponse' => "Je comprends votre {urgent|question}. Notre équipe va traiter votre demande en priorité.",
                'type' => 'warning'
            ];
        }

        return $this->getReponse($message, $commande);
    }
}
