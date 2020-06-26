<?php

namespace Zukaz\Coupon\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('zukaz_sale_rules_eas')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('zukaz_sale_rules_eas')
            )
                ->addColumn(
                    'rule_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => false,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ]
                )
                ->addColumn(
                    'business_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    [
                        'nullable' => false,
                    ]
                )
                ->addColumn(
                    'coupon_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    [
                        'nullable' => false,
                    ]
                )
                ->addColumn(
                    'voucher_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    [
                        'nullable' => false,
                    ]
                )
                ->addColumn(
                    'voucher_value',
                    \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    null,
                    [
                        'nullable' => false,
                    ]
                )
                ->addColumn(
                    'voucher_expiry_date',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                    ]
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE]
                )
                ->addIndex(
                    $installer->getIdxName(
                        'zukaz_sale_rules_eas',
                        ['coupon_code'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['coupon_code'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addIndex(
                    $installer->getIdxName(
                        'zukaz_sale_rules_eas',
                        ['voucher_id'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['voucher_id'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )
            ;
            $installer->getConnection()->createTable($table);
        }
        if (!$installer->tableExists('zukaz_webhooks')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('zukaz_webhooks')
            )
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ]
                )
                ->addColumn(
                    'business_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    [
                        'nullable' => false,
                    ]
                )
                ->addColumn(
                    'event',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    [
                        'nullable' => false,
                    ]
                )
                ->addColumn(
                    'url',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                    ]
                )
                ->addColumn(
                    'secret',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                    ]
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE]
                )
            ;
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
