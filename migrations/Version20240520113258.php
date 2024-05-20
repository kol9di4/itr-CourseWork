<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240520113258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item_attribute_integer_field (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, custom_item_attribute_id INT NOT NULL, value INT NOT NULL, INDEX IDX_E1567D3F126F525E (item_id), INDEX IDX_E1567D3F8BF3B7B6 (custom_item_attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_attribute_string_field (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, custom_item_attribute_id INT NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_76051DC126F525E (item_id), INDEX IDX_76051DC8BF3B7B6 (custom_item_attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item_attribute_integer_field ADD CONSTRAINT FK_E1567D3F126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE item_attribute_integer_field ADD CONSTRAINT FK_E1567D3F8BF3B7B6 FOREIGN KEY (custom_item_attribute_id) REFERENCES custom_item_attribute (id)');
        $this->addSql('ALTER TABLE item_attribute_string_field ADD CONSTRAINT FK_76051DC126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE item_attribute_string_field ADD CONSTRAINT FK_76051DC8BF3B7B6 FOREIGN KEY (custom_item_attribute_id) REFERENCES custom_item_attribute (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item_attribute_integer_field DROP FOREIGN KEY FK_E1567D3F126F525E');
        $this->addSql('ALTER TABLE item_attribute_integer_field DROP FOREIGN KEY FK_E1567D3F8BF3B7B6');
        $this->addSql('ALTER TABLE item_attribute_string_field DROP FOREIGN KEY FK_76051DC126F525E');
        $this->addSql('ALTER TABLE item_attribute_string_field DROP FOREIGN KEY FK_76051DC8BF3B7B6');
        $this->addSql('DROP TABLE item_attribute_integer_field');
        $this->addSql('DROP TABLE item_attribute_string_field');
    }
}
