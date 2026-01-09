<?php

namespace Elshrif\Loyalty\Model\ResourceModel\Points;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Elshrif\Loyalty\Model\Points as PointsModel;

use Elshrif\Loyalty\Model\ResourceModel\Points as PointsResource;



class Collection extends AbstractCollection
{

    /**
     * for indexing
     */
    protected $_idFieldName = 'entity_id';


    protected $_eventPrefix = 'elshrif_loyalty_Points_collection';

    protected $_eventObject = 'points_collection';

    /**
     * @param string $modelClass
     * @param string $resourceClass
     *
     */

    protected function _construct()
    {
        $this->_init(PointsModel::class, PointsResource::class);
    }

    /**
     *
     * @param int|array $customerIds
     * @return $this
     */
    public function addCustomerFilter($customerIds): self
    {
        if (is_array($customerIds)) {
            $this->addFieldToFilter('customer_id', ['in' => $customerIds]);
        } else {
            $this->addFieldToFilter('customer_id', $customerIds);
        }
        return $this;
    }

    /**
     * @return int
     *
     */

    public function getTotalPointsBalance(): int
    {
        $this->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns(['total' => new \Zend_Db_Expr('SUM(points_balance)')]);
        return (int) $this->getConnection()->fetchOne($this->getSelect());
    }
}
