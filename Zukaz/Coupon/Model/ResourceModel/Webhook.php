<?php

namespace Zukaz\Coupon\Model\ResourceModel;

class Webhook extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('zukaz_webhooks', 'id');
    }

}
