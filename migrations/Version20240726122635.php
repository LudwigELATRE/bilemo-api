<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240726122635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD enterprise_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADA97D1AC3 ON product (enterprise_id)');
        $this->addSql('ALTER TABLE user ADD enterprise_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649A97D1AC3 ON user (enterprise_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADA97D1AC3');
        $this->addSql('DROP INDEX IDX_D34A04ADA97D1AC3 ON product');
        $this->addSql('ALTER TABLE product DROP enterprise_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A97D1AC3');
        $this->addSql('DROP INDEX IDX_8D93D649A97D1AC3 ON user');
        $this->addSql('ALTER TABLE user DROP enterprise_id');
    }
}
