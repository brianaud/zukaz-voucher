<?php

namespace Zukaz\Coupon\Model\ResourceModel\RuleEA;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'rule_id';
    protected $_eventPrefix = 'zukaz_sale_rules_eas_collection';
    protected $_eventObject = 'sale_rules_eas_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Zukaz\Coupon\Model\RuleEA::class, \Zukaz\Coupon\Model\ResourceModel\RuleEA::class);
    }

}
