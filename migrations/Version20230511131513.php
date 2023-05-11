<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230511131513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE players ADD club_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE players ADD CONSTRAINT FK_264E43A6DF2AB4E5 FOREIGN KEY (club_id_id) REFERENCES club (id)');
        $this->addSql('CREATE INDEX IDX_264E43A6DF2AB4E5 ON players (club_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE players DROP FOREIGN KEY FK_264E43A6DF2AB4E5');
        $this->addSql('DROP INDEX IDX_264E43A6DF2AB4E5 ON players');
        $this->addSql('ALTER TABLE players DROP club_id_id');
    }
}
