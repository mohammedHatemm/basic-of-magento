<?php
declare(strict_types=1);
namespace Elshrif\Loyalty\Points\Collection;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Elshrif\Loyalty\Model\Points as PointsModel;
use Elshrif\HelloWorld\Model\ResourceModel\Points as pointResource;


class Collection extends AbstractCollection
{
    public function _construct(){
        $this->_init(PointsModel::Class, PointsResource::class);
    }
}
