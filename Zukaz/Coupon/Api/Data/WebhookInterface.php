<?php


namespace Zukaz\Coupon\Api\Data;


interface WebhookInterface
{
    /**
     * @return string
     **/
    public function getBusinessId();
    /**
     * @return string
     **/
    public function getEvent();
    /**
     * @return string
     **/
    public function getUrl();
    /**
     * @return string
     **/
    public function getSecret();
    /**
     * @return string
     **/
    public function getCreatedAt();
}
