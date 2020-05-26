<?php

namespace Zukaz\Coupon\Api;

interface WebhookManagementInterface
{
    /**
     * @param string $filter_business_id
     * @param string $filter_event
     * @return array
     */
    public function list($filter_business_id = null, $filter_event = null);

    /**
     * @param string $filter_business_id
     * @param string $filter_event
     * @return \Zukaz\Coupon\Api\Data\WebhookInterface
     */
    public function delete($filter_business_id, $filter_event);

    /**
     * @param string $business_id
     * @param string $event
     * @param string $url
     * @param string $secret
     * @return \Zukaz\Coupon\Api\Data\WebhookInterface
     */
    public function addUpdate($business_id, $event, $url, $secret);
}
