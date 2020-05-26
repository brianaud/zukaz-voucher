<?php

namespace Zukaz\Coupon\Model\ResourceModel\Webhook;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'zukaz_webhooks_collection';
    protected $_eventObject = 'webhooks_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Zukaz\Coupon\Model\Webhook::class, \Zukaz\Coupon\Model\ResourceModel\Webhook::class);
    }

}
