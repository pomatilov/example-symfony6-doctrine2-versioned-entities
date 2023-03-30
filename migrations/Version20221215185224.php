<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221215185224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE com_company (id BIGINT NOT NULL, full_name VARCHAR(256) NOT NULL, tax_id BIGINT DEFAULT NULL, registration_reason_code BIGINT DEFAULT NULL, registration_number BIGINT DEFAULT NULL, registration_date DATE DEFAULT NULL, idd BIGINT DEFAULT NULL, vfrom TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, vto TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, cax BIGINT NOT NULL, eax BIGINT DEFAULT NULL, iax BIGINT DEFAULT NULL, is_valid VARCHAR(1) NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tech_comm VARCHAR(1024) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN com_company.id IS \'ID версии записи\'');
        $this->addSql('COMMENT ON COLUMN com_company.full_name IS \'Наименование компании\'');
        $this->addSql('COMMENT ON COLUMN com_company.tax_id IS \'ИНН\'');
        $this->addSql('COMMENT ON COLUMN com_company.registration_reason_code IS \'КПП\'');
        $this->addSql('COMMENT ON COLUMN com_company.registration_number IS \'ОГРН\'');
        $this->addSql('COMMENT ON COLUMN com_company.registration_date IS \'Дата регистрации\'');
        $this->addSql('COMMENT ON COLUMN com_company.idd IS \'ID записи\'');
        $this->addSql('COMMENT ON COLUMN com_company.vfrom IS \'Дата начала версии\'');
        $this->addSql('COMMENT ON COLUMN com_company.vto IS \'Дата окончания версии\'');
        $this->addSql('COMMENT ON COLUMN com_company.cax IS \'ID транзакции, в рамках которой запись была создана\'');
        $this->addSql('COMMENT ON COLUMN com_company.eax IS \'ID транзакции, в рамках которой запись была изменена\'');
        $this->addSql('COMMENT ON COLUMN com_company.iax IS \'ID транзакции, в рамках которой запись была инвалидирована\'');
        $this->addSql('COMMENT ON COLUMN com_company.is_valid IS \'Статус валидности записи\'');
        $this->addSql('COMMENT ON COLUMN com_company.created IS \'Дата создания записи\'');
        $this->addSql('COMMENT ON COLUMN com_company.tech_comm IS \'Технический комментарий\'');
        $this->addSql('CREATE TABLE com_transaction (id BIGINT NOT NULL, session_id VARCHAR(255) NOT NULL, name VARCHAR(128) NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tech_comm VARCHAR(1024) DEFAULT NULL, state VARCHAR(32) NOT NULL, state_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN com_transaction.id IS \'ID транзакции\'');
        $this->addSql('COMMENT ON COLUMN com_transaction.session_id IS \'ID сессии\'');
        $this->addSql('COMMENT ON COLUMN com_transaction.name IS \'Наименование действия, в рамках которого происходит транзакция\'');
        $this->addSql('COMMENT ON COLUMN com_transaction.created IS \'Дата создания записи\'');
        $this->addSql('COMMENT ON COLUMN com_transaction.tech_comm IS \'Технический комментарий\'');
        $this->addSql('COMMENT ON COLUMN com_transaction.state IS \'Статус записи\'');
        $this->addSql('COMMENT ON COLUMN com_transaction.state_date IS \'Дата последнего изменения статуса записи\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE com_company');
        $this->addSql('DROP TABLE com_transaction');
    }
}
