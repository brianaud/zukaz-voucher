<?php

namespace Zukaz\Coupon\Api;

use Zukaz\Coupon\Api\Data\CouponInterface;

interface CouponManagementInterface
{
    /**
     * @param string $filter_business_id
     * @param string $filter_voucher_id
     * @return CouponInterface[]
     */
    public function list($filter_business_id = null, $filter_voucher_id = null);

    /**
     * @param string $delete_by_field_name
     * @param string $delete_by_field_value
     * @return CouponInterface[]
     */
    public function delete($delete_by_field_name, $delete_by_field_value);

    /**
     * @param string $business_id
     * @param string $coupon_code
     * @param string $voucher_id
     * @param float $voucher_value
     * @param string $voucher_expiry_date
     * @return CouponInterface
     */
    public function add($business_id, $coupon_code, $voucher_id, $voucher_value, $voucher_expiry_date);
}
