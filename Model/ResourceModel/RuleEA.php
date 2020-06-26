<?php

namespace Zukaz\Coupon\Model\ResourceModel;

class RuleEA extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('zukaz_sale_rules_eas', 'rule_id');
        $this->_isPkAutoIncrement = false;
    }

}
