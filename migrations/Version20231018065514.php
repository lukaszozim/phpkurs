<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231018065514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apk_answer DROP FOREIGN KEY FK_1E0F0AD8BD40875E');
        $this->addSql('ALTER TABLE apk_answer DROP FOREIGN KEY FK_1E0F0AD88F54B366');
        $this->addSql('DROP INDEX `primary` ON apk_answer');
        $this->addSql('ALTER TABLE apk_answer ADD CONSTRAINT FK_1E0F0AD8BD40875E FOREIGN KEY (apk_uuid) REFERENCES apk (id)');
        $this->addSql('ALTER TABLE apk_answer ADD CONSTRAINT FK_1E0F0AD88F54B366 FOREIGN KEY (answer_uuid) REFERENCES answer (id)');
        $this->addSql('ALTER TABLE apk_answer ADD PRIMARY KEY (apk_uuid, answer_uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apk_answer DROP FOREIGN KEY FK_1E0F0AD8BD40875E');
        $this->addSql('ALTER TABLE apk_answer DROP FOREIGN KEY FK_1E0F0AD88F54B366');
        $this->addSql('DROP INDEX `PRIMARY` ON apk_answer');
        $this->addSql('ALTER TABLE apk_answer ADD CONSTRAINT FK_1E0F0AD8BD40875E FOREIGN KEY (apk_uuid) REFERENCES answer (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE apk_answer ADD CONSTRAINT FK_1E0F0AD88F54B366 FOREIGN KEY (answer_uuid) REFERENCES apk (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE apk_answer ADD PRIMARY KEY (answer_uuid, apk_uuid)');
    }
}
