<?php
namespace App\Service;

use App\Entity\Client;
use App\Entity\CodePromo;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

class FideliteService
{
    private EntityManagerInterface $em;

    // Niveaux avec seuils et avantages précis
    private array $niveaux = [
        'bronze' => [
            'seuil' => 0,
            'reduction' => 0,
            'points_multiplier' => 1,
            'badge' => '🥉',
            'label' => 'Bronze',
            'couleur' => '#cd7f32',
            'benefits' => ['Points fidélité (x1)', 'Offres exclusives']
        ],
        'argent' => [
            'seuil' => 300,
            'reduction' => 5,
            'points_multiplier' => 1.5,
            'badge' => '🥈',
            'label' => 'Argent',
            'couleur' => '#c0c0c0',
            'benefits' => ['Points fidélité (x1.5)', '5% de réduction', 'Livraison prioritaire', 'Offres exclusives']
        ],
        'or' => [
            'seuil' => 800,
            'reduction' => 10,
            'points_multiplier' => 2,
            'badge' => '🥇',
            'label' => 'Or',
            'couleur' => '#ffd700',
            'benefits' => ['Points fidélité (x2)', '10% de réduction', 'Livraison prioritaire', 'Accès avant-première', 'Cadeau anniversaire']
        ],
        'platine' => [
            'seuil' => 2000,
            'reduction' => 15,
            'points_multiplier' => 3,
            'badge' => '💎',
            'label' => 'Platine',
            'couleur' => '#e5e4e2',
            'benefits' => ['Points fidélité (x3)', '15% de réduction', 'Livraison prioritaire', 'Accès avant-première', 'Service client dédié', 'Cadeau anniversaire', 'Invitations événements']
        ]
    ];

    // Codes promo prédéfinis avec conditions
    private array $codesSpeciaux = [
        'BIENVENUE10' => ['reduction' => 10, 'type' => 'pourcentage', 'min' => 50, 'description' => '10% de réduction sur votre première commande'],
        'FIDELITE15' => ['reduction' => 15, 'type' => 'pourcentage', 'min' => 100, 'description' => '15% de réduction pour nos fidèles clients'],
        'FIDELITE20' => ['reduction' => 20, 'type' => 'pourcentage', 'min' => 150, 'description' => '20% de réduction (10 commandes)'],
        'FIDELITE25' => ['reduction' => 25, 'type' => 'pourcentage', 'min' => 200, 'description' => '25% de réduction (25 commandes)'],
        'FIDELITE30' => ['reduction' => 30, 'type' => 'pourcentage', 'min' => 250, 'description' => '30% de réduction (50 commandes)'],
        'PLATINE50' => ['reduction' => 50, 'type' => 'fixe', 'min' => 300, 'description' => '50€ de réduction (membre Platine)'],
        'ANNIVERSAIRE' => ['reduction' => 20, 'type' => 'pourcentage', 'min' => 0, 'description' => '20% de réduction pour votre anniversaire'],
        'BLACKFRIDAY' => ['reduction' => 30, 'type' => 'pourcentage', 'min' => 200, 'description' => '30% de réduction - Black Friday'],
        'NOEL2024' => ['reduction' => 25, 'type' => 'pourcentage', 'min' => 150, 'description' => '25% de réduction - Noël 2024'],
        'PARRAINAGE' => ['reduction' => 10, 'type' => 'pourcentage', 'min' => 50, 'description' => '10% de réduction pour le parrainage']
    ];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function addPoints(Client $client, float $total): void
    {
        $niveau = $this->getNiveauClient($client);
        $multiplier = $this->niveaux[$niveau]['points_multiplier'];
        
        // Points = montant * multiplicateur
        $points = (int) floor($total * $multiplier);
        $client->setPointsFidelite($client->getPointsFidelite() + $points);
        $client->setTotalAchats($client->getTotalAchats() + $total);
        $client->setNbCommandes($client->getNbCommandes() + 1);
        
        // Vérifier si le client change de niveau
        $ancienNiveau = $client->getNiveau();
        $this->updateNiveau($client);
        $nouveauNiveau = $client->getNiveau();
        
        // Notification de changement de niveau
        if ($ancienNiveau !== $nouveauNiveau) {
            $this->notifierChangementNiveau($client, $ancienNiveau, $nouveauNiveau);
            $this->genererCodePromoNiveau($client, $nouveauNiveau);
        }
        
        // Vérifier les jalons de commandes
        $this->checkJalonsCommandes($client);
        
        // Vérifier les événements spéciaux
        $this->checkEvenementsSpeciaux($client);
        
        $this->em->flush();
    }

    private function updateNiveau(Client $client): void
    {
        $total = $client->getTotalAchats();
        $niveaux = array_reverse($this->niveaux, true);
        
        foreach ($niveaux as $nom => $data) {
            if ($total >= $data['seuil']) {
                $client->setNiveau($nom);
                return;
            }
        }
        $client->setNiveau('bronze');
    }

    private function getNiveauClient(Client $client): string
    {
        return $client->getNiveau() ?? 'bronze';
    }

    private function notifierChangementNiveau(Client $client, string $ancien, string $nouveau): void
    {
        $notif = new Notification();
        $notif->setClient($client);
        $notif->setType('fidelite');
        $notif->setMessage(
            "🎉 Félicitations ! Vous passez au niveau **" . ucfirst($nouveau) . "** ! " .
            "Profitez de " . $this->niveaux[$nouveau]['reduction'] . "% de réduction sur vos prochains achats."
        );
        $this->em->persist($notif);
    }

    private function genererCodePromoNiveau(Client $client, string $niveau): void
    {
        $code = strtoupper($niveau) . '_' . $client->getId() . '_' . date('Ymd');
        $reduction = $this->niveaux[$niveau]['reduction'];
        
        $codePromo = new CodePromo();
        $codePromo->setCode($code);
        $codePromo->setReduction($reduction);
        $codePromo->setType('pourcentage');
        $codePromo->setDateExpiration((new \DateTime())->modify('+30 days'));
        $codePromo->setNbUtilisationsMax(1);
        $codePromo->setMontantMin(max(50, $reduction * 5));
        $codePromo->setActif(true);
        $this->em->persist($codePromo);
        
        $notif = new Notification();
        $notif->setClient($client);
        $notif->setType('promo');
        $notif->setMessage(
            "🎁 Code promo généré : **" . $code . "** - " . $reduction . "% de réduction !"
        );
        $this->em->persist($notif);
    }

    private function checkJalonsCommandes(Client $client): void
    {
        $nbCommandes = $client->getNbCommandes();
        
        // Commandes spéciales
        $jalons = [
            1 => ['code' => 'BIENVENUE10', 'message' => '🎉 Votre première commande !'],
            5 => ['code' => 'FIDELITE15', 'message' => '🌟 5 commandes ! Vous êtes un fidèle client.'],
            10 => ['code' => 'FIDELITE20', 'message' => '🏆 10 commandes ! Vous méritez une récompense.'],
            25 => ['code' => 'FIDELITE25', 'message' => '💎 25 commandes ! Vous êtes un client exceptionnel.'],
            50 => ['code' => 'FIDELITE30', 'message' => '👑 50 commandes ! Vous êtes un client VIP.']
        ];

        if (isset($jalons[$nbCommandes])) {
            $this->genererCodePromoSpecial($client, $jalons[$nbCommandes]['code'], $jalons[$nbCommandes]['message']);
        }
    }

    private function checkEvenementsSpeciaux(Client $client): void
    {
        // Anniversaire du client (simulé)
        $mois = date('m');
        if ($mois == date('m', strtotime($client->getCreatedAt()->format('Y-m-d')))) {
            $this->genererCodePromoSpecial($client, 'ANNIVERSAIRE', '🎂 Joyeux anniversaire !');
        }
        
        // Black Friday (novembre)
        if (date('m') == 11 && date('d') >= 20 && date('d') <= 27) {
            $this->genererCodePromoSpecial($client, 'BLACKFRIDAY', '🛍️ Black Friday !');
        }
        
        // Noël (décembre)
        if (date('m') == 12 && date('d') >= 15 && date('d') <= 25) {
            $this->genererCodePromoSpecial($client, 'NOEL2024', '🎄 Noël !');
        }
    }

    private function genererCodePromoSpecial(Client $client, string $codeBase, string $message): void
    {
        $code = $codeBase . '_' . $client->getId();
        $data = $this->codesSpeciaux[$codeBase] ?? null;
        
        if (!$data) return;
        
        // Vérifier si le code existe déjà
        $existing = $this->em->getRepository(CodePromo::class)->findOneBy(['code' => $code]);
        if ($existing) return;
        
        $codePromo = new CodePromo();
        $codePromo->setCode($code);
        $codePromo->setReduction($data['reduction']);
        $codePromo->setType($data['type']);
        $codePromo->setDateExpiration((new \DateTime())->modify('+15 days'));
        $codePromo->setNbUtilisationsMax(1);
        $codePromo->setMontantMin($data['min']);
        $codePromo->setActif(true);
        $this->em->persist($codePromo);
        
        $notif = new Notification();
        $notif->setClient($client);
        $notif->setType('promo');
        $notif->setMessage($message . " Code promo : **" . $code . "** - " . $data['reduction'] . "% de réduction !");
        $this->em->persist($notif);
    }

    public function getAvantagesNiveau(string $niveau): array
    {
        return $this->niveaux[$niveau] ?? $this->niveaux['bronze'];
    }

    public function getProgression(Client $client): array
    {
        $total = $client->getTotalAchats();
        $niveauActuel = $client->getNiveau() ?? 'bronze';
        
        // Trouver le prochain niveau
        $prochain = null;
        $progress = 100;
        
        foreach ($this->niveaux as $nom => $data) {
            if ($nom === $niveauActuel) {
                // Trouver le niveau suivant
                $found = false;
                foreach ($this->niveaux as $nextNom => $nextData) {
                    if ($found) {
                        $prochain = ['nom' => $nextNom, 'seuil' => $nextData['seuil']];
                        break;
                    }
                    if ($nextNom === $niveauActuel) {
                        $found = true;
                    }
                }
                break;
            }
        }

        if ($prochain) {
            $seuilActuel = $this->niveaux[$niveauActuel]['seuil'];
            $seuilProchain = $prochain['seuil'];
            $progress = min(100, (($total - $seuilActuel) / ($seuilProchain - $seuilActuel)) * 100);
        }

        return [
            'actuel' => $niveauActuel,
            'prochain' => $prochain,
            'progress' => $progress,
            'total' => $total
        ];
    }

    public function getCodesPromoClient(Client $client): array
    {
        return $this->em->getRepository(CodePromo::class)->findBy([
            'actif' => true
        ]);
    }
}
