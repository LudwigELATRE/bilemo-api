<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240829095036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A5A792BA');
        $this->addSql('DROP INDEX IDX_8D93D649A5A792BA ON user');
        $this->addSql('ALTER TABLE user ADD enterprise_id INT DEFAULT NULL, DROP enterprise_uuid');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649A97D1AC3 ON user (enterprise_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A97D1AC3');
        $this->addSql('DROP INDEX IDX_8D93D649A97D1AC3 ON user');
        $this->addSql('ALTER TABLE user ADD enterprise_uuid VARCHAR(255) DEFAULT NULL, DROP enterprise_id');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A5A792BA FOREIGN KEY (enterprise_uuid) REFERENCES enterprise (uuid)');
        $this->addSql('CREATE INDEX IDX_8D93D649A5A792BA ON user (enterprise_uuid)');
    }
}
