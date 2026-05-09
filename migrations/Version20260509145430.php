<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260509145430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE review (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, rating INTEGER NOT NULL, comment CLOB DEFAULT NULL, created_at DATETIME NOT NULL, user_id INTEGER DEFAULT NULL, product_id INTEGER DEFAULT NULL, CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_794381C64584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_794381C6A76ED395 ON review (user_id)');
        $this->addSql('CREATE INDEX IDX_794381C64584665A ON review (product_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__order AS SELECT id, total_amount, order_status, tracking_number, created_at, user_id, reference_number, payment_mode FROM "order"');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, total_amount DOUBLE PRECISION NOT NULL, order_status VARCHAR(255) NOT NULL, tracking_number VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, user_id INTEGER NOT NULL, reference_number VARCHAR(20) DEFAULT NULL, payment_mode VARCHAR(255) NOT NULL, CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO "order" (id, total_amount, order_status, tracking_number, created_at, user_id, reference_number, payment_mode) SELECT id, total_amount, order_status, tracking_number, created_at, user_id, reference_number, payment_mode FROM __temp__order');
        $this->addSql('DROP TABLE __temp__order');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON "order" (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE review');
        $this->addSql('CREATE TEMPORARY TABLE __temp__order AS SELECT id, total_amount, order_status, tracking_number, created_at, reference_number, payment_mode, user_id FROM "order"');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, total_amount DOUBLE PRECISION NOT NULL, order_status VARCHAR(255) NOT NULL, tracking_number VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, reference_number VARCHAR(20) DEFAULT NULL, payment_mode VARCHAR(255) DEFAULT \'GCash\' NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO "order" (id, total_amount, order_status, tracking_number, created_at, reference_number, payment_mode, user_id) SELECT id, total_amount, order_status, tracking_number, created_at, reference_number, payment_mode, user_id FROM __temp__order');
        $this->addSql('DROP TABLE __temp__order');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON "order" (user_id)');
    }
}
