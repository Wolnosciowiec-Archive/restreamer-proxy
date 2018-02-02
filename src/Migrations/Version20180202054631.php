<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180202054631 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $libraryElement = $schema->createTable('restreamer_library_element');
        $libraryElement->addColumn('id', 'string', [
            'null' => false,
            'length' => 128,
        ]);

        $source = $schema->createTable('restreamer_source');
        $source->addColumn('id', 'string', [
            'null' => false,
            'length' => 128
        ]);

        $source->addColumn('url', 'string', [
            'length' => 254
        ]);

        $source->addColumn('order_num', 'integer', [
            'length' => 4
        ]);

        $source->addColumn('library_element_id', 'string', [
            'length' => 128
        ]);

        $source->addUniqueIndex(['url', 'library_element_id', 'order_num'], 'unique_url_per_library_element_at_order_position');
        $source->addForeignKeyConstraint($libraryElement, ['library_element_id'], ['id']);
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('restreamer_source');
        $schema->dropTable('restreamer_library_element');
    }
}
