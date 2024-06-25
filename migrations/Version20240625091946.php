<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240625091946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campus DROP FOREIGN KEY FK_9D0968119D1C3019');
        $this->addSql('DROP INDEX IDX_9D0968119D1C3019 ON campus');
        $this->addSql('ALTER TABLE campus DROP participant_id');
        $this->addSql('ALTER TABLE participant ADD campus_id INT NOT NULL, DROP actif');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('CREATE INDEX IDX_D79F6B11AF5D55E1 ON participant (campus_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campus ADD participant_id INT NOT NULL');
        $this->addSql('ALTER TABLE campus ADD CONSTRAINT FK_9D0968119D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9D0968119D1C3019 ON campus (participant_id)');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11AF5D55E1');
        $this->addSql('DROP INDEX IDX_D79F6B11AF5D55E1 ON participant');
        $this->addSql('ALTER TABLE participant ADD actif TINYINT(1) NOT NULL, DROP campus_id');
    }
}
