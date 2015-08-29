<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$mainTable = $installer->getTable("meanbee_personalisedcontent/customer_categories");

if ($installer->tableExists($mainTable)) {
    $installer->getConnection()->dropTable($mainTable);
}

/**
 * Create table 'meanbee_personalisedcontent_customer_categories'
 */
$table = $installer->getConnection()
    ->newTable($mainTable)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'primary' => true,
        'auto_increment' => true,
    ), 'ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Customer ID')
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Category ID')
    ->addColumn('score', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => 0
    ), 'Weighting Score')
    ->addForeignKey(
        $installer->getFkName('meanbee_personalisedcontent/customer_categories', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('meanbee_personalisedcontent/customer_categories', 'category_id', 'catalog/category', 'entity_id'),
        'category_id',
        $installer->getTable('catalog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$installer->getConnection()->createTable($table);

$installer->endSetup();
