<?php


namespace Zukaz\Coupon\Plugin;

class SaleRulesPlugin
{
    protected $_ruleEasCollectionFac;
    public function __construct(
        \Zukaz\Coupon\Model\ResourceModel\RuleEA\CollectionFactory $collectionFactory
    ) {
        $this->_ruleEasCollectionFac = $collectionFactory;
    }
    public function afterDelete(
        \Magento\SalesRule\Model\ResourceModel\Rule\Interceptor $subjectOne,
        \Magento\SalesRule\Model\ResourceModel\Rule\Interceptor $subjectTwo,
        \Magento\SalesRule\Model\Rule $rule
    ) {
        $eaCollection = $this->_ruleEasCollectionFac->create();
        $eaCollection->addFilter('rule_id', $rule->getId());
        $eaCollection->walk('delete');
    }
}
