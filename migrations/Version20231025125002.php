<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231025125002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ac_activities CHANGE context context JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE payload payload JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE pr_preview_links CHANGE options options JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE publication_dimension_content CHANGE availableLocales availableLocales JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE shadowLocales shadowLocales JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE templateData templateData JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE se_role_settings CHANGE value value JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE tr_trash_items CHANGE restoreData restoreData JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE restoreOptions restoreOptions JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE we_analytics CHANGE content content JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ac_activities CHANGE context context JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE payload payload JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE publication_dimension_content CHANGE availableLocales availableLocales JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE shadowLocales shadowLocales JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', CHANGE templateData templateData JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE se_role_settings CHANGE value value JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE we_analytics CHANGE content content JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE tr_trash_items CHANGE restoreData restoreData JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE restoreOptions restoreOptions JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE pr_preview_links CHANGE options options JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
