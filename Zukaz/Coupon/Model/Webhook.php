<?php

namespace Zukaz\Coupon\Model;

class Webhook extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'zukaz_webhooks';

    protected $_cacheTag = 'zukaz_webhooks';

    protected $_eventPrefix = 'zukaz_webhooks';

    protected function _construct()
    {
        $this->_init(\Zukaz\Coupon\Model\ResourceModel\Webhook::class);
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
