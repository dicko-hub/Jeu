<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191130091524 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE personnage DROP FOREIGN KEY FK_23A0E6612469DE3');
        $this->addSql('DROP INDEX IDX_23A0E6612469DE3 ON personnage');
        $this->addSql('ALTER TABLE personnage CHANGE salle salle_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE personnage ADD CONSTRAINT FK_6AEA486DDC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
        $this->addSql('CREATE INDEX IDX_6AEA486DDC304035 ON personnage (salle_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE personnage DROP FOREIGN KEY FK_6AEA486DDC304035');
        $this->addSql('DROP INDEX IDX_6AEA486DDC304035 ON personnage');
        $this->addSql('ALTER TABLE personnage CHANGE salle_id salle INT DEFAULT NULL');
        $this->addSql('ALTER TABLE personnage ADD CONSTRAINT FK_23A0E6612469DE3 FOREIGN KEY (salle) REFERENCES salle (id)');
        $this->addSql('CREATE INDEX IDX_23A0E6612469DE3 ON personnage (salle)');
    }
}
