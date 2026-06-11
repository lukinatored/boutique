<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260611081646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE acheter (produit_id INT NOT NULL, commande_id INT NOT NULL, quantite SMALLINT NOT NULL, prix NUMERIC(10, 2) NOT NULL, INDEX IDX_6E0E9118F347EFB (produit_id), INDEX IDX_6E0E911882EA2E54 (commande_id), PRIMARY KEY(produit_id, commande_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, produit_id INT NOT NULL, note SMALLINT NOT NULL, commentaire VARCHAR(500) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_8F91ABF019EB6921 (client_id), INDEX IDX_8F91ABF0F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE caracteristique (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_D14FBE8B6C6E55B5 (nom), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_497DD6346C6E55B5 (nom), INDEX IDX_497DD634727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, adresse VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C7440455E7927C74 (email), INDEX IDX_C7440455D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, total NUMERIC(10, 2) NOT NULL, statut VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_6EEAA67D19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE departement (id INT AUTO_INCREMENT NOT NULL, region_id INT NOT NULL, nom VARCHAR(50) NOT NULL, code VARCHAR(10) NOT NULL, INDEX IDX_C1765B6398260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, produit_id INT NOT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_E01FBE6AF347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livraison (id INT AUTO_INCREMENT NOT NULL, commande_id INT NOT NULL, adresse VARCHAR(255) NOT NULL, statut VARCHAR(50) NOT NULL, date_expedition DATE NOT NULL, UNIQUE INDEX UNIQ_A60C9F1F82EA2E54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE marque (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, logo VARCHAR(255) DEFAULT NULL, description VARCHAR(500) DEFAULT NULL, pays_origine VARCHAR(100) DEFAULT NULL, annee_creation INT DEFAULT NULL, UNIQUE INDEX UNIQ_5A6F91CE6C6E55B5 (nom), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE montre (id INT AUTO_INCREMENT NOT NULL, marque_id INT NOT NULL, categorie_id INT NOT NULL, produit_id INT NOT NULL, mouvement VARCHAR(50) DEFAULT NULL, materiau VARCHAR(50) DEFAULT NULL, eau_resistance INT DEFAULT NULL, INDEX IDX_B61A93A44827B9B2 (marque_id), INDEX IDX_B61A93A4BCF5E72D (categorie_id), UNIQUE INDEX UNIQ_B61A93A4F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pack (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, prix NUMERIC(10, 2) NOT NULL, prix_promo NUMERIC(10, 2) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pack_produits (pack_id INT NOT NULL, produits_id INT NOT NULL, INDEX IDX_90A29B9D1919B217 (pack_id), INDEX IDX_90A29B9DCD11A2CF (produits_id), PRIMARY KEY(pack_id, produits_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prix_caracteristique (id INT AUTO_INCREMENT NOT NULL, valeur_caracteristique_id INT NOT NULL, produit_id INT NOT NULL, supplement NUMERIC(10, 2) DEFAULT NULL, INDEX IDX_112E24104B95F011 (valeur_caracteristique_id), INDEX IDX_112E2410F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produits (id INT AUTO_INCREMENT NOT NULL, stock SMALLINT NOT NULL, prix NUMERIC(10, 2) NOT NULL, nom VARCHAR(100) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_F62F1766C6E55B5 (nom), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_57698A6A6C6E55B5 (nom), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE valeur_caracteristique (id INT AUTO_INCREMENT NOT NULL, caracteristique_id INT NOT NULL, valeur VARCHAR(100) NOT NULL, INDEX IDX_B90C98CB1704EEB7 (caracteristique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ville (id INT AUTO_INCREMENT NOT NULL, departement_id INT NOT NULL, nom VARCHAR(100) NOT NULL, code_postal VARCHAR(10) NOT NULL, INDEX IDX_43C3D9C3CCF9E01E (departement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acheter ADD CONSTRAINT FK_6E0E9118F347EFB FOREIGN KEY (produit_id) REFERENCES produits (id)');
        $this->addSql('ALTER TABLE acheter ADD CONSTRAINT FK_6E0E911882EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF019EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0F347EFB FOREIGN KEY (produit_id) REFERENCES produits (id)');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD634727ACA70 FOREIGN KEY (parent_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE departement ADD CONSTRAINT FK_C1765B6398260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AF347EFB FOREIGN KEY (produit_id) REFERENCES produits (id)');
        $this->addSql('ALTER TABLE livraison ADD CONSTRAINT FK_A60C9F1F82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE montre ADD CONSTRAINT FK_B61A93A44827B9B2 FOREIGN KEY (marque_id) REFERENCES marque (id)');
        $this->addSql('ALTER TABLE montre ADD CONSTRAINT FK_B61A93A4BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE montre ADD CONSTRAINT FK_B61A93A4F347EFB FOREIGN KEY (produit_id) REFERENCES produits (id)');
        $this->addSql('ALTER TABLE pack_produits ADD CONSTRAINT FK_90A29B9D1919B217 FOREIGN KEY (pack_id) REFERENCES pack (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pack_produits ADD CONSTRAINT FK_90A29B9DCD11A2CF FOREIGN KEY (produits_id) REFERENCES produits (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prix_caracteristique ADD CONSTRAINT FK_112E24104B95F011 FOREIGN KEY (valeur_caracteristique_id) REFERENCES valeur_caracteristique (id)');
        $this->addSql('ALTER TABLE prix_caracteristique ADD CONSTRAINT FK_112E2410F347EFB FOREIGN KEY (produit_id) REFERENCES produits (id)');
        $this->addSql('ALTER TABLE valeur_caracteristique ADD CONSTRAINT FK_B90C98CB1704EEB7 FOREIGN KEY (caracteristique_id) REFERENCES caracteristique (id)');
        $this->addSql('ALTER TABLE ville ADD CONSTRAINT FK_43C3D9C3CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acheter DROP FOREIGN KEY FK_6E0E9118F347EFB');
        $this->addSql('ALTER TABLE acheter DROP FOREIGN KEY FK_6E0E911882EA2E54');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF019EB6921');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0F347EFB');
        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY FK_497DD634727ACA70');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455D60322AC');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D19EB6921');
        $this->addSql('ALTER TABLE departement DROP FOREIGN KEY FK_C1765B6398260155');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AF347EFB');
        $this->addSql('ALTER TABLE livraison DROP FOREIGN KEY FK_A60C9F1F82EA2E54');
        $this->addSql('ALTER TABLE montre DROP FOREIGN KEY FK_B61A93A44827B9B2');
        $this->addSql('ALTER TABLE montre DROP FOREIGN KEY FK_B61A93A4BCF5E72D');
        $this->addSql('ALTER TABLE montre DROP FOREIGN KEY FK_B61A93A4F347EFB');
        $this->addSql('ALTER TABLE pack_produits DROP FOREIGN KEY FK_90A29B9D1919B217');
        $this->addSql('ALTER TABLE pack_produits DROP FOREIGN KEY FK_90A29B9DCD11A2CF');
        $this->addSql('ALTER TABLE prix_caracteristique DROP FOREIGN KEY FK_112E24104B95F011');
        $this->addSql('ALTER TABLE prix_caracteristique DROP FOREIGN KEY FK_112E2410F347EFB');
        $this->addSql('ALTER TABLE valeur_caracteristique DROP FOREIGN KEY FK_B90C98CB1704EEB7');
        $this->addSql('ALTER TABLE ville DROP FOREIGN KEY FK_43C3D9C3CCF9E01E');
        $this->addSql('DROP TABLE acheter');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE caracteristique');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE departement');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE livraison');
        $this->addSql('DROP TABLE marque');
        $this->addSql('DROP TABLE montre');
        $this->addSql('DROP TABLE pack');
        $this->addSql('DROP TABLE pack_produits');
        $this->addSql('DROP TABLE prix_caracteristique');
        $this->addSql('DROP TABLE produits');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE valeur_caracteristique');
        $this->addSql('DROP TABLE ville');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
