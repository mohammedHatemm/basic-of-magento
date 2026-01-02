<?php
namespace Elshrif\Loyalty\Model\ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
class Points extends AbstractDb
{
    protected function _construct()
    {
        $this->_init("elshrif_loyalty_points", "entity_id");
    }
}
