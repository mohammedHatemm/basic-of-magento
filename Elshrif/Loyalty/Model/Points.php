<?php
declare(strict_types=1);
namespace Elshrif\Loyalty\Model;
use Magento\Catalog\Model\AbstractModel;
use Elshrif\Loyalty\Model\ResourceModel\Points as PointsResource;




class Points extends AbstractModel
{
   public const ENTITY_ID = 'entity_id';
   public const CUSTOMER_ID = 'customer_id';
   public const POINTS_BALANCE = 'points_balance';
   public const TOTAL_EARNINGS = 'total_earnings';
   public const TOTAL_SPENT = 'total_spent';
   public const UPDATED_AT = 'updated_at';

   protected function _construct()
   {
       $this->_init(PointsResource::class);

   }


   // getter function
    public function getEntityId(): ?int
    {
        $value = $this->getData(self::ENTITY_ID);
        return  $value!==null ? (int) $value : null;

    }

    public function getCustomerId(): ?int
    {
        $value = $this->getData(self::CUSTOMER_ID);
        return  $value !== null ? (int) $value : null;
    }
    public function getPointsBalance(): ?int
    {
        return (int) $this->getData(self::POINTS_BALANCE ?? 0);
    }

    public function getTotalEarnings(): ?int
    {
        return (int) $this->getData(self::TOTAL_EARNINGS ?? 0);
    }

    public function getTotalSpent(): ?int
    {
        return (int) $this->getData(self::TOTAL_SPENT) ?? 0;
    }

    // setter function

    public function setCustomerId(?int $customerId): Points
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }
    public function setPointsBalance(?int $pointsBalance): Points
    {
        return $this->setData(self::POINTS_BALANCE, $pointsBalance);
    }
    public function setTotalEarned(?int $total): Points
    {
        return $this->setData(self::TOTAL_EARNINGS, $total);

    }
    public function setTotalSpent(?int $total): Points
    {
        return  $this->setData(self::TOTAL_SPENT, $total);
    }


}

