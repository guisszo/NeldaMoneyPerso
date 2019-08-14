<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190814154309 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, user_envoi_id INT DEFAULT NULL, user_retrait_id INT DEFAULT NULL, nomcomplet_expediteur VARCHAR(100) NOT NULL, tel_expediteur VARCHAR(15) NOT NULL, nomcomplet_recepteur VARCHAR(100) NOT NULL, tel_recepteur VARCHAR(15) NOT NULL, code_transaction VARCHAR(50) NOT NULL, montant INT NOT NULL, cnirecepteur VARCHAR(14) DEFAULT NULL, statut VARCHAR(15) NOT NULL, sent_at DATETIME NOT NULL, receved_at DATETIME DEFAULT NULL, commission_env INT DEFAULT NULL, commission_retrait INT DEFAULT NULL, commission_etat INT DEFAULT NULL, commission_neldam INT DEFAULT NULL, INDEX IDX_723705D1DF1A08E5 (user_envoi_id), INDEX IDX_723705D1D99F8396 (user_retrait_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1DF1A08E5 FOREIGN KEY (user_envoi_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1D99F8396 FOREIGN KEY (user_retrait_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE transaction');
    }
}
