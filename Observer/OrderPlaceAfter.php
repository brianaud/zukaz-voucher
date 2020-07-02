<?php

namespace Zukaz\Coupon\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;

class OrderPlaceAfter implements ObserverInterface
{
    protected $_logger;
    private $_collection_factory;
    private $_httpClient_factory;
    private $_rule_eas_fac;
    private $_json;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Zukaz\Coupon\Model\ResourceModel\Webhook\CollectionFactory $collectionFactory,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Zukaz\Coupon\Model\ResourceModel\RuleEA\CollectionFactory $ruleEACollectionFactory,
        \Magento\Framework\Serialize\Serializer\Json $json
    ) {
        $this->_logger = $logger;
        $this->_collection_factory = $collectionFactory;
        $this->_httpClient_factory = $httpClientFactory;
        $this->_rule_eas_fac = $ruleEACollectionFactory;
        $this->_json = $json;
    }

    private function isZukazCoupon($coupon_code)
    {
        return strpos(strtoupper($coupon_code), 'ZK-') == 0;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $data = $order->getData();
        $this->_logger->info($this->_json->serialize($order->getData()));

        if(!isset( $data['coupon_code'])){
            return;
        }

        $coupon_code = $data['coupon_code'];

        if (!$this->isZukazCoupon($coupon_code)) {
            return;
        }

        $applied_rule_ids = $data['applied_rule_ids'];

        if (!$applied_rule_ids) {
            return;
        }
        $applied_rule_ids = explode(',', $applied_rule_ids);
        $rule_eas = $this->_rule_eas_fac->create();
        foreach ($applied_rule_ids as $applied_rule_id) {
            $rule_eas->addFilter('rule_id', $applied_rule_id, 'or');
        }
        $eas = $rule_eas->getData();
        if (count($eas) == 0) {
            return;
        }
        $ea = $eas[0];
        $voucher_expiry_date = date_create_from_format('Y-m-d H:i:s', $ea['voucher_expiry_date']);
        $ea['voucher_expiry_date'] = $voucher_expiry_date->format('Y-m-d');
        $business_id = $ea['business_id'];

        $collectionFac = $this->_collection_factory->create();
        $collectionFac->addFilter('event', 'OrderPlaceAfter', 'and');
        $collectionFac->addFilter('business_id', $business_id, 'and');
        $webhooks = $collectionFac->getData();

        $this->_logger->info($this->_json->serialize($webhooks));

        if (count($webhooks) == 0) {
            return;
        }
        foreach ($webhooks as $webhook) {
            $client = $this->_httpClient_factory->create();
            $client->setUri($webhook['url']);
            $this->_logger->info($webhook['url']);
            $client->setMethod(\Zend_Http_Client::POST);
            $client->setHeaders(\Zend_Http_Client::CONTENT_TYPE, 'application/json');
            $client->setHeaders('Accept', 'application/json');
            $send_data = [
                'order_data' => $data,
                'zukaz_data' => $ea
            ];
            $json_data = $this->_json->serialize($send_data);
            $sign = hash_hmac('sha256', $json_data, $webhook['secret']);
            $sign = base64_encode($sign);
            $client->setHeaders('Authorization', $sign);
            $client->setRawData($json_data, 'application/json');
            $client->request();
            $this->_logger->info($this->_json->serialize($webhook));
        }
        //throw new BadMessageException('break');
    }
}
