<?php

declare(strict_types=1);

namespace Elshrif\Loyalty\Model;

use Magento\Framework\Model\AbstractModel;
use Elshrif\Loyalty\Api\Data\PointsInterface;
use Elshrif\Loyalty\Model\ResourceModel\Points as PointsResource;


class Points extends AbstractModel implements PointsInterface
{



    public const CACH_TAG = 'elshrif_loyalty_points';

    /**
     * @var string
     */
    protected $_eventPrefix = 'elshrif_loyalty_points';


    protected function _construct(): void
    {

        /**
         * @param string $resourceModel
         */
        $this->_init(PointsResource::class);
    }


    /**
     *
     * {@inheritdoc}
     *
     */

    public function getEntityId(): ?int
    {
        $value = $this->getData(self::ENTITY_ID);
        return  $value !== null ? (int) $value : null;
    }

    /**
     *
     * {@inheritdoc}
     *
     */

    public function getCustomerId(): ?int
    {
        $value = $this->getData(self::CUSTOMER_ID);
        return  $value !== null ? (int) $value : null;
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function getPointsBalance(): int
    {
        return (int) ($this->getData(self::POINTS_BALANCE) ?? 0);
    }


    /**
     *
     * {@inheritdoc}
     *
     */

    public function getTotalEarned(): int
    {
        return (int) ($this->getData(self::TOTAL_EARNED) ?? 0);
    }


    /**
     *
     * {@inheritdoc}
     *
     */

    public function getTotalSpent(): int
    {
        return (int) ($this->getData(self::TOTAL_SPENT) ?? 0);
    }




    /**
     *
     * {@inheritdoc}
     *
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }



    /**
     *
     * {@inheritdoc}
     *
     */

    public function setCustomerId(?int $customerId): PointsInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function setPointsBalance(?int $balance): PointsInterface
    {
        return $this->setData(self::POINTS_BALANCE, $balance);
    }
    /**
     *
     * {@inheritdoc}
     *
     */
    public function setTotalEarend(?int $total): PointsInterface
    {
        return $this->setData(self::TOTAL_EARNED, $total);
    }
    /**
     *
     * {@inheritdoc}
     *
     */
    public function setTotalSpent(?int $total): PointsInterface
    {
        return  $this->setData(self::TOTAL_SPENT, $total);
    }

    /**
     * @return array
     */
    public function getIdentities(): array
    {
        return [self::CACH_TAG . ' ' . $this->getId()];
    }
}
