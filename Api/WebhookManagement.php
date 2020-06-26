<?php

namespace Zukaz\Coupon\Api;

use DateTime;

class WebhookManagement implements WebhookManagementInterface
{

    private $_factory;
    private $_collection_factory;
    private $_resource_model;

    public function __construct(
        \Zukaz\Coupon\Model\ResourceModel\Webhook\CollectionFactory $collectionFactory,
        \Zukaz\Coupon\Model\WebhookFactory $factory,
        \Zukaz\Coupon\Model\ResourceModel\Webhook $resourceMode
    ) {
        $this->_factory = $factory;
        $this->_collection_factory = $collectionFactory;
        $this->_resource_model = $resourceMode;
    }

    public function list($filter_business_id = null, $filter_event = null)
    {
        $collectionFac = $this->_collection_factory->create();
        if ($filter_business_id) {
            $collectionFac->addFilter('business_id', $filter_business_id, 'and');
        }
        if ($filter_event) {
            $collectionFac->addFilter('event', $filter_event, 'and');
        }
        $webhooks = $collectionFac->getData();
        $webhooks = array_map(function ($webhook) {
            $webhook['id'] = $webhook['id'] * 1;
            return $webhook;
        }, $webhooks);
        return $webhooks;
    }

    public function delete($filter_business_id, $filter_event)
    {
        $collectionFac = $this->_collection_factory->create();
        if ($filter_business_id) {
            $collectionFac->addFilter('business_id', $filter_business_id, 'and');
        }
        if ($filter_event) {
            $collectionFac->addFilter('event', $filter_event, 'and');
        }
        $webhooks = $collectionFac->getData();
        $collectionFac->walk('delete');
        return $webhooks;
    }

    public function addUpdate($business_id, $event, $url, $secret)
    {
        $collectionFac = $this->_collection_factory->create();
        $collectionFac->addFilter('business_id', $business_id, 'and');
        $collectionFac->addFilter('event', $event, 'and');
        $exists = $collectionFac->getData();
        if (count($exists) == 0) {
            $fac = $this->_factory->create();
        } else {
            $exist = $exists[0];
            $exist_id = $exist['id'];
            $fac = $this->_factory->create();
            $this->_resource_model->load($fac, $exist_id);
        }
        $fac->setData('business_id', $business_id);
        $fac->setData('event', $event);
        $fac->setData('url', $url);
        $fac->setData('secret', $secret);
        $fac->setData('created_at', gmdate('Y-m-d'));
        $this->_resource_model->save($fac);
        return $fac;
    }
}
