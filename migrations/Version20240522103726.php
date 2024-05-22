<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522103726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item_attribute_boolean_field (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, custom_item_attribute_id INT NOT NULL, value TINYINT(1) NOT NULL, INDEX IDX_E63A3670126F525E (item_id), INDEX IDX_E63A36708BF3B7B6 (custom_item_attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_attribute_date_field (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, custom_item_attribute_id INT NOT NULL, value DATE NOT NULL, INDEX IDX_9096E29A126F525E (item_id), INDEX IDX_9096E29A8BF3B7B6 (custom_item_attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_attribute_text_field (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, custom_item_attribute_id INT NOT NULL, value LONGTEXT NOT NULL, INDEX IDX_7CD2B04B126F525E (item_id), INDEX IDX_7CD2B04B8BF3B7B6 (custom_item_attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item_attribute_boolean_field ADD CONSTRAINT FK_E63A3670126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE item_attribute_boolean_field ADD CONSTRAINT FK_E63A36708BF3B7B6 FOREIGN KEY (custom_item_attribute_id) REFERENCES custom_item_attribute (id)');
        $this->addSql('ALTER TABLE item_attribute_date_field ADD CONSTRAINT FK_9096E29A126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE item_attribute_date_field ADD CONSTRAINT FK_9096E29A8BF3B7B6 FOREIGN KEY (custom_item_attribute_id) REFERENCES custom_item_attribute (id)');
        $this->addSql('ALTER TABLE item_attribute_text_field ADD CONSTRAINT FK_7CD2B04B126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE item_attribute_text_field ADD CONSTRAINT FK_7CD2B04B8BF3B7B6 FOREIGN KEY (custom_item_attribute_id) REFERENCES custom_item_attribute (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item_attribute_boolean_field DROP FOREIGN KEY FK_E63A3670126F525E');
        $this->addSql('ALTER TABLE item_attribute_boolean_field DROP FOREIGN KEY FK_E63A36708BF3B7B6');
        $this->addSql('ALTER TABLE item_attribute_date_field DROP FOREIGN KEY FK_9096E29A126F525E');
        $this->addSql('ALTER TABLE item_attribute_date_field DROP FOREIGN KEY FK_9096E29A8BF3B7B6');
        $this->addSql('ALTER TABLE item_attribute_text_field DROP FOREIGN KEY FK_7CD2B04B126F525E');
        $this->addSql('ALTER TABLE item_attribute_text_field DROP FOREIGN KEY FK_7CD2B04B8BF3B7B6');
        $this->addSql('DROP TABLE item_attribute_boolean_field');
        $this->addSql('DROP TABLE item_attribute_date_field');
        $this->addSql('DROP TABLE item_attribute_text_field');
    }
}
