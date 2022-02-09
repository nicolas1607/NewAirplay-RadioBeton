<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220209110724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE playlist_has_disc (id INT AUTO_INCREMENT NOT NULL, disc_id INT DEFAULT NULL, playlist_id INT DEFAULT NULL, INDEX IDX_A22E4802C38F37CA (disc_id), INDEX IDX_A22E48026BBD148 (playlist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE playlist_has_disc ADD CONSTRAINT FK_A22E4802C38F37CA FOREIGN KEY (disc_id) REFERENCES disc (id)');
        $this->addSql('ALTER TABLE playlist_has_disc ADD CONSTRAINT FK_A22E48026BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id)');
        $this->addSql('DROP TABLE disc_playlist');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE disc_playlist (disc_id INT NOT NULL, playlist_id INT NOT NULL, INDEX IDX_369E6BB1C38F37CA (disc_id), INDEX IDX_369E6BB16BBD148 (playlist_id), PRIMARY KEY(disc_id, playlist_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE disc_playlist ADD CONSTRAINT FK_369E6BB16BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE disc_playlist ADD CONSTRAINT FK_369E6BB1C38F37CA FOREIGN KEY (disc_id) REFERENCES disc (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE playlist_has_disc');
    }
}
