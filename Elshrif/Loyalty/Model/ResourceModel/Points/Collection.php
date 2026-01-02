<?php
namespace Elshrif\Loyalty\Model\ResourceModel\Points;
use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;
use Elshrif\Loyalty\Model\Points as PointsModel;

use Elshrif\Loyalty\Model\ResourceModel\Points as PointsResource;



class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(PointsModel::class, PointsResource::class);
    }
}
