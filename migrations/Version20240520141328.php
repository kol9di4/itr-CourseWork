<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240520141328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, user_id INT NOT NULL, text LONGTEXT NOT NULL, date_time_add DATETIME NOT NULL, INDEX IDX_9474526C126F525E (item_id), INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE custom_item_attribute (id INT AUTO_INCREMENT NOT NULL, item_collection_id INT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_DC45CCD1BDE5FE26 (item_collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, item_collection_id INT NOT NULL, name VARCHAR(255) NOT NULL, date_add DATETIME NOT NULL, INDEX IDX_1F1B251EBDE5FE26 (item_collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_tag (item_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_E49CCCB1126F525E (item_id), INDEX IDX_E49CCCB1BAD26311 (tag_id), PRIMARY KEY(item_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_attribute_integer_field (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, custom_item_attribute_id INT NOT NULL, value INT NOT NULL, INDEX IDX_E1567D3F126F525E (item_id), INDEX IDX_E1567D3F8BF3B7B6 (custom_item_attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_attribute_string_field (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, custom_item_attribute_id INT NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_76051DC126F525E (item_id), INDEX IDX_76051DC8BF3B7B6 (custom_item_attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_collection (id INT AUTO_INCREMENT NOT NULL, image_id INT NOT NULL, category_id INT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_add DATETIME NOT NULL, UNIQUE INDEX UNIQ_41FC4D383DA5256D (image_id), INDEX IDX_41FC4D3812469DE2 (category_id), INDEX IDX_41FC4D38A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, user_id INT NOT NULL, type SMALLINT NOT NULL, INDEX IDX_AC6340B3126F525E (item_id), INDEX IDX_AC6340B3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE custom_item_attribute ADD CONSTRAINT FK_DC45CCD1BDE5FE26 FOREIGN KEY (item_collection_id) REFERENCES item_collection (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EBDE5FE26 FOREIGN KEY (item_collection_id) REFERENCES item_collection (id)');
        $this->addSql('ALTER TABLE item_tag ADD CONSTRAINT FK_E49CCCB1126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_tag ADD CONSTRAINT FK_E49CCCB1BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_attribute_integer_field ADD CONSTRAINT FK_E1567D3F126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE item_attribute_integer_field ADD CONSTRAINT FK_E1567D3F8BF3B7B6 FOREIGN KEY (custom_item_attribute_id) REFERENCES custom_item_attribute (id)');
        $this->addSql('ALTER TABLE item_attribute_string_field ADD CONSTRAINT FK_76051DC126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE item_attribute_string_field ADD CONSTRAINT FK_76051DC8BF3B7B6 FOREIGN KEY (custom_item_attribute_id) REFERENCES custom_item_attribute (id)');
        $this->addSql('ALTER TABLE item_collection ADD CONSTRAINT FK_41FC4D383DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE item_collection ADD CONSTRAINT FK_41FC4D3812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE item_collection ADD CONSTRAINT FK_41FC4D38A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C126F525E');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE custom_item_attribute DROP FOREIGN KEY FK_DC45CCD1BDE5FE26');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EBDE5FE26');
        $this->addSql('ALTER TABLE item_tag DROP FOREIGN KEY FK_E49CCCB1126F525E');
        $this->addSql('ALTER TABLE item_tag DROP FOREIGN KEY FK_E49CCCB1BAD26311');
        $this->addSql('ALTER TABLE item_attribute_integer_field DROP FOREIGN KEY FK_E1567D3F126F525E');
        $this->addSql('ALTER TABLE item_attribute_integer_field DROP FOREIGN KEY FK_E1567D3F8BF3B7B6');
        $this->addSql('ALTER TABLE item_attribute_string_field DROP FOREIGN KEY FK_76051DC126F525E');
        $this->addSql('ALTER TABLE item_attribute_string_field DROP FOREIGN KEY FK_76051DC8BF3B7B6');
        $this->addSql('ALTER TABLE item_collection DROP FOREIGN KEY FK_41FC4D383DA5256D');
        $this->addSql('ALTER TABLE item_collection DROP FOREIGN KEY FK_41FC4D3812469DE2');
        $this->addSql('ALTER TABLE item_collection DROP FOREIGN KEY FK_41FC4D38A76ED395');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3126F525E');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3A76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE custom_item_attribute');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE item_tag');
        $this->addSql('DROP TABLE item_attribute_integer_field');
        $this->addSql('DROP TABLE item_attribute_string_field');
        $this->addSql('DROP TABLE item_collection');
        $this->addSql('DROP TABLE `like`');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
