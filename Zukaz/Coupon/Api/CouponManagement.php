<?php

namespace Zukaz\Coupon\Api;

//use Magento\Framework\Api\Filter;
//use Magento\Framework\Api\FilterBuilder;
//use Magento\Framework\Api\SearchCriteriaBuilder;

use http\Exception\BadQueryStringException;
use Magento\Framework\Exception\NoSuchEntityException;

class CouponManagement implements CouponManagementInterface
{

    protected $_rule_resource;
    protected $_rule_fac;
    protected $_rule_collection_fac;
    protected $_coupon_repository;
    protected $_ea_collection_fac;
    protected $_website_repository;
    protected $_cus_group_repository;
    protected $_rule_ea_resource;
    protected $_rule_ea_factory;
    protected $_date_time;
    protected $_timezone;

    public function __construct(
        \Magento\Store\Model\WebsiteRepository $websiteRepository,
        \Magento\Customer\Model\ResourceModel\Group\Collection $groupRegistry,
        \Magento\SalesRule\Model\ResourceModel\Rule $rule_resource,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\SalesRule\Model\CouponRepository $coupon_repository,
        \Zukaz\Coupon\Model\ResourceModel\RuleEA\CollectionFactory $ruleEACollectionFactory,
        \Zukaz\Coupon\Model\ResourceModel\RuleEA $ruleEA,
        \Zukaz\Coupon\Model\RuleEAFactory $ruleEAFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->_rule_resource = $rule_resource;
        $this->_rule_fac = $ruleFactory;
        $this->_coupon_repository = $coupon_repository;
        $this->_ea_collection_fac = $ruleEACollectionFactory;
        $this->_website_repository = $websiteRepository;
        $this->_cus_group_repository = $groupRegistry;
        $this->_rule_ea_factory = $ruleEAFactory;
        $this->_rule_ea_resource = $ruleEA;
        $this->_rule_collection_fac = $ruleCollectionFactory;
        $this->_date_time = $dateTime;
        $this->_timezone = $timezone;
    }

    public function list($filter_business_id = null, $filter_voucher_id = null)
    {
        $collection = $this->_ea_collection_fac->create();
        if ($filter_business_id) {
            $collection->addFilter('business_id', $filter_business_id, 'and');
        }
        if ($filter_voucher_id) {
            $collection->addFilter('voucher_id', $filter_voucher_id, 'and');
        }
        $rs = array_map(function ($item) {
            $date = date_create_from_format('Y-m-d H:i:s', $item['voucher_expiry_date']);
            $item['rule_id'] = $item['rule_id'] * 1;
            $item['voucher_value'] = $item['voucher_value'] * 1.0;
            $item['voucher_expiry_date'] = $date->format('Y-m-d');
            return $item;
        }, $collection->getData());
        return $rs;
    }

    public function delete($delete_by_field_name, $delete_by_field_value)
    {
        $field_names = ['rule_id', 'voucher_id', 'coupon_code'];
        if (!in_array($delete_by_field_name, $field_names)) {
            throw new BadQueryStringException('delete_by_field_name must be one of: rule_id, voucher_id, coupon_code');
        }
        $rule_eas = $this->_ea_collection_fac->create();
        $rule_eas->addFilter($delete_by_field_name, $delete_by_field_value);
        $rule_eas = $rule_eas->getData();
        if (count($rule_eas) == 0) {
            return [];
        }
        $rules = $this->_rule_collection_fac->create();
        foreach ($rule_eas as $rule_ea) {
            $rules->addFilter('main_table.rule_id', $rule_ea['rule_id'], 'or');
        }
        $rules->walk('delete');
        return $rule_eas;
    }

    public function add($business_id, $coupon_code, $voucher_id, $voucher_value, $voucher_expiry_date)
    {
        $voucher_value = $voucher_value * 1.0;
        $rule = $this->_rule_fac->create();
        $rule->setName('Zukaz coupon #'.$voucher_id);
        $rule->setDescription('Zukaz coupon $'.number_format($voucher_value, 2));
        $rule->setIsActive(1);
        $websites = $this->_website_repository->getList();
        $websites = array_filter($websites, function ($website) {
            return $website->getCode() != 'admin';
        });
        $website_ids = [];
        foreach ($websites as $website) {
            $website_ids[] = $website->getId();
        }
        if (count($website_ids) == 0) {
            throw new NoSuchEntityException(__('No websites'));
        }
        $website_ids = implode(',', $website_ids);
        $rule->setWebsiteIds($website_ids);
        $cus_groups = $this->_cus_group_repository->toOptionArray();
        $cus_group_ids = [];
        foreach ($cus_groups as $cus_group) {
            $cus_group_ids[] = $cus_group['value'];
        }
        if (count($cus_group_ids) == 0) {
            throw new NoSuchEntityException(__('No customer groups'));
        }
        $time = $this->_timezone->scopeTimeStamp();
        $today = (new \DateTime())->setTimestamp($time);
        $cus_group_ids = implode(',', $cus_group_ids);
        $rule->setCustomerGroupIds($cus_group_ids);
        $rule->setCouponType(\Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC);
        $rule->setCouponCode($coupon_code);
        $rule->setUsesPerCoupon(1);
        $rule->setUsesPerCustomer(1);
        $rule->setFromDate($today->format('Y-m-d'));
        $end = $this->_timezone->scopeDate(null, date_create_from_format('Y-m-d', $voucher_expiry_date));
        $rule->setToDate($end->format('Y-m-d'));
        $rule->setSimpleAction('by_fixed');
        $rule->setDiscountAmount($voucher_value * 1.0);
        $this->_rule_resource->save($rule);
        $rule_ea = $this->_rule_ea_factory->create();
        $rule_ea->setId($rule->getRuleId());
        $rule_ea->setData('business_id', $business_id);
        $rule_ea->setData('coupon_code', $coupon_code);
        $rule_ea->setData('voucher_id', $voucher_id);
        $rule_ea->setData('voucher_value', $voucher_value);
        $rule_ea->setData('voucher_expiry_date', $voucher_expiry_date);
        $rule_ea->setData('created_at', gmdate('Y-m-d'));
        try {
            $this->_rule_ea_resource->save($rule_ea);
            return $rule_ea;
        } catch (\Exception $exception) {
            $this->_rule_resource->delete($rule);
            throw $exception;
        }
    }
}
