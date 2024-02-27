<?php

use Phinx\Migration\AbstractMigration;

class AddDeveloperIdToOrderDetailTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $db_prefix = $this->getAdapter()->getOption('db_prefix');
        $table = $this->table($db_prefix.'order_detail');
        $table->addColumn('developer_id', 'integer', ['null' => true, 'after' => 'id_order', 'signed' => false, 'limit' => 10])
            ->addForeignKey('developer_id', $db_prefix.'customer', 'id_customer', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION', 'constraint' => 'order_detail_customer'])
            ->save();
        
    }
}
