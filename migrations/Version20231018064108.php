<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231018064108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE apk_answer (answer_uuid INT NOT NULL, apk_uuid INT NOT NULL, INDEX IDX_1E0F0AD88F54B366 (answer_uuid), INDEX IDX_1E0F0AD8BD40875E (apk_uuid), PRIMARY KEY(answer_uuid, apk_uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE apk_answer ADD CONSTRAINT FK_1E0F0AD88F54B366 FOREIGN KEY (answer_uuid) REFERENCES apk (id)');
        $this->addSql('ALTER TABLE apk_answer ADD CONSTRAINT FK_1E0F0AD8BD40875E FOREIGN KEY (apk_uuid) REFERENCES answer (id)');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFBFE9FF1B3');
        $this->addSql('ALTER TABLE response_answer DROP FOREIGN KEY FK_B1A66D0AAA334807');
        $this->addSql('ALTER TABLE response_answer DROP FOREIGN KEY FK_B1A66D0AFBF32840');
        $this->addSql('DROP TABLE response');
        $this->addSql('DROP TABLE response_answer');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE response (id INT AUTO_INCREMENT NOT NULL, apk_id INT NOT NULL, UNIQUE INDEX UNIQ_3E7B0BFBFE9FF1B3 (apk_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE response_answer (response_id INT NOT NULL, answer_id INT NOT NULL, INDEX IDX_B1A66D0AAA334807 (answer_id), INDEX IDX_B1A66D0AFBF32840 (response_id), PRIMARY KEY(response_id, answer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFBFE9FF1B3 FOREIGN KEY (apk_id) REFERENCES apk (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE response_answer ADD CONSTRAINT FK_B1A66D0AAA334807 FOREIGN KEY (answer_id) REFERENCES answer (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE response_answer ADD CONSTRAINT FK_B1A66D0AFBF32840 FOREIGN KEY (response_id) REFERENCES response (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE apk_answer DROP FOREIGN KEY FK_1E0F0AD88F54B366');
        $this->addSql('ALTER TABLE apk_answer DROP FOREIGN KEY FK_1E0F0AD8BD40875E');
        $this->addSql('DROP TABLE apk_answer');
    }
}
