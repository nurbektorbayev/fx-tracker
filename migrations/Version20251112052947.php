<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251112052947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema for currency_pairs and exchange_rates';
    }

    public function up(Schema $schema): void
    {
        // currency_pairs
        $currencyPairs = $schema->createTable('currency_pairs');
        $currencyPairs->addColumn('id', 'integer', ['autoincrement' => true]);
        $currencyPairs->addColumn('base_currency', 'string', ['length' => 3]);
        $currencyPairs->addColumn('target_currency', 'string', ['length' => 3]);
        $currencyPairs->addColumn('active', 'boolean', ['default' => true]);
        $currencyPairs->addColumn('created_at', 'datetime_immutable');
        $currencyPairs->setPrimaryKey(['id']);
        $currencyPairs->addUniqueIndex(
            ['base_currency', 'target_currency'],
            'uniq_currency_pair'
        );

        // exchange_rates
        $exchangeRates = $schema->createTable('exchange_rates');
        $exchangeRates->addColumn('id', 'bigint', ['autoincrement' => true]);
        $exchangeRates->addColumn('pair_id', 'integer');
        $exchangeRates->addColumn('rate', 'decimal', ['precision' => 18, 'scale' => 8]);
        $exchangeRates->addColumn('provider', 'string', ['length' => 32]);
        $exchangeRates->addColumn('fetched_at', 'datetime_immutable');
        $exchangeRates->addColumn('valid_at', 'datetime_immutable');
        $exchangeRates->setPrimaryKey(['id']);
        $exchangeRates->addIndex(['pair_id', 'valid_at'], 'idx_pair_valid_at');
        $exchangeRates->addForeignKeyConstraint(
            'currency_pairs',
            ['pair_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'fk_exchange_rates_pair'
        );
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('exchange_rates');
        $schema->dropTable('currency_pairs');
    }
}
