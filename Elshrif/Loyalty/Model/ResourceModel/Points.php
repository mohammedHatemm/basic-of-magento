<?php

namespace Elshrif\Loyalty\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Points extends AbstractDb
{


    /**
     *
     *
     *  @param string $tableName
     * @param string $idFieldName
     */
    protected function _construct()
    {
        $this->_init("elshrif_loyalty_points", "entity_id");
    }


    /**
     * @param Points $model
     * @param int $customerId
     * @return $this
     */

    public function loadByCustomerId(
        \Elshrif\Loyalty\Model\Points $model,
        int $customerId
    ): self {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('customer_id = ?', $customerId);
        $data = $connection->fetchRow($select);
        if ($data) {
            $model->setData($data);
        }
        return $this;
    }

    /**
     * @param int $customerId
     * @return bool
     */
    public function customerHasPoints(int $customerId): bool
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), ['entity_id'])
            ->where('customer_id = ? ', $customerId);
        return (bool) $connection->fetchOne($select);
    }
}
