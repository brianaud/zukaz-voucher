<?php


namespace Zukaz\Coupon\Api\Data;


interface CouponInterface
{
    /**
     * @return integer
     **/
    public function getRuleId();

    /**
     * @return string
     **/
    public function getBusinessId();

    /**
     * @return string
     **/
    public function getCouponCode();

    /**
     * @return string
     **/
    public function getVoucherId();

    /**
     * @return float
     **/
    public function getVoucherValue();

    /**
     * @return string
     **/
    public function getVoucherExpiryDate();

    /**
     * @return string
     **/
    public function getCreatedAt();
}
