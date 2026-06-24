<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260623144852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produits ADD galerie JSON DEFAULT NULL, DROP description_detaillee, DROP images, DROP materiaux, DROP mouvement, DROP boitier, DROP bracelet, DROP eau_resistance, DROP diametre, DROP epaisseur');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produits ADD description_detaillee LONGTEXT DEFAULT NULL, ADD materiaux JSON DEFAULT NULL, ADD mouvement VARCHAR(50) DEFAULT NULL, ADD boitier VARCHAR(50) DEFAULT NULL, ADD bracelet VARCHAR(50) DEFAULT NULL, ADD eau_resistance INT DEFAULT NULL, ADD diametre VARCHAR(20) DEFAULT NULL, ADD epaisseur VARCHAR(20) DEFAULT NULL, CHANGE galerie images JSON DEFAULT NULL');
    }
}
