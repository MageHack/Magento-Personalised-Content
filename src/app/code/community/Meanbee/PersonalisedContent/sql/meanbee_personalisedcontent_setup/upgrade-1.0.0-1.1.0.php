<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$mainTable = $installer->getTable("meanbee_personalisedcontent/cms_block_personalisation");

if ($installer->tableExists($mainTable)) {
    $installer->getConnection()->dropTable($mainTable);
}

/**
 * Create table 'meanbee_personalisedcontent_cms_block_personalisation'
 */
$table = $installer->getConnection()
    ->newTable($mainTable)
    ->addColumn('block_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false
    ), 'Block ID')
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Category ID')
    ->addForeignKey(
        $installer->getFkName('meanbee_personalisedcontent/cms_block_personalisation', 'block_id', 'cms/block', 'block_id'),
        'block_id',
        $installer->getTable('cms/block'),
        'block_id'
    )
    ->addForeignKey(
        $installer->getFkName('meanbee_personalisedcontent/cms_block_personalisation', 'category_id', 'catalog/category', 'entity_id'),
        'category_id',
        $installer->getTable('catalog/category'),
        'entity_id'
    );

$installer->getConnection()->createTable($table);

$installer->endSetup();
