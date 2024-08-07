<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddWlsFieldsToCustomerTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $db_prefix = $this->getAdapter()->getOption('db_prefix');
        $table = $this->table($db_prefix.'customer');
        $table
            ->addColumn('wls_id', 'string')
            ->addColumn('wls_url', 'string')
            ->save();
    }
}
