<?php

namespace Zukaz\Coupon\Model;

class RuleEA extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'zukaz_rule_eas';

    protected $_cacheTag = 'zukaz_rule_eas';

    protected $_eventPrefix = 'zukaz_rule_eas';

    protected function _construct()
    {
        $this->_init(\Zukaz\Coupon\Model\ResourceModel\RuleEA::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}
